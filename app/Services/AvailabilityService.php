<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\ProviderSchedule;
use App\Models\Service;
use App\Models\TimeOff;
use Carbon\Carbon;
use Carbon\CarbonImmutable;

class AvailabilityService
{
    /**
     * Get available slot start times for a provider/service on a date.
     *
     * @param int $providerId
     * @param int $serviceId
     * @param string $dateYmd format Y-m-d
     * @return array<int, string> Array of 'Y-m-d H:i:s' slot start times
     */
    public function getSlots(int $providerId, int $serviceId, string $dateYmd): array
    {
        $date = CarbonImmutable::createFromFormat('Y-m-d', $dateYmd)->startOfDay();
        if (!$date) {
            return [];
        }

        $weekday = (int) $date->dayOfWeek; // 0=Sun..6=Sat

        $service = Service::find($serviceId);
        if (!$service) {
            return [];
        }
        $duration = (int) ($service->duration_minutes ?? 30);
        if ($duration <= 0) {
            $duration = 30;
        }

        // Load schedules for the provider on that weekday
        $schedules = ProviderSchedule::query()
            ->where('provider_id', $providerId)
            ->where('weekday', $weekday)
            ->orderBy('start_time')
            ->get();

        if ($schedules->isEmpty()) {
            return [];
        }

        $dayStart = $date;
        $dayEnd = $date->endOfDay();

        // Preload time offs and appointments overlapping that day
        $timeOffs = TimeOff::query()
            ->where('provider_id', $providerId)
            ->where(function ($q) use ($dayStart, $dayEnd) {
                $q->where('start_at', '<', $dayEnd)
                  ->where('end_at', '>', $dayStart);
            })
            ->get(['start_at', 'end_at']);

        $appointments = Appointment::query()
            ->where('provider_id', $providerId)
            ->where('status', '!=', 'cancelled')
            ->where(function ($q) use ($dayStart, $dayEnd) {
                $q->where('start_at', '<', $dayEnd)
                  ->where('end_at', '>', $dayStart);
            })
            ->get(['start_at', 'end_at']);

        $now = Carbon::now();
        $slots = [];

        foreach ($schedules as $schedule) {
            $interval = (int) ($schedule->slot_interval_minutes ?? 30);
            if ($interval <= 0) {
                $interval = 30;
            }

            // Build schedule window on the given date
            $scheduleStart = CarbonImmutable::parse($dateYmd.' '.$schedule->start_time)->seconds(0);
            $scheduleEnd = CarbonImmutable::parse($dateYmd.' '.$schedule->end_time)->seconds(0);

            // Latest start time to fit duration
            $latestStart = $scheduleEnd->subMinutes($duration);
            if ($latestStart->lessThan($scheduleStart)) {
                continue; // duration does not fit in this schedule
            }

            for ($cursor = $scheduleStart; !$cursor->greaterThan($latestStart); $cursor = $cursor->addMinutes($interval)) {
                $slotStart = $cursor;
                $slotEnd = $slotStart->addMinutes($duration);

                // Exclude past slots
                if ($slotStart->lt($now)) {
                    continue;
                }

                // Overlaps with any time off?
                $blockedByTimeOff = $timeOffs->first(function ($to) use ($slotStart, $slotEnd) {
                    $toStart = $to->start_at instanceof Carbon ? $to->start_at : Carbon::parse((string) $to->start_at);
                    $toEnd = $to->end_at instanceof Carbon ? $to->end_at : Carbon::parse((string) $to->end_at);
                    return $toStart < $slotEnd && $toEnd > $slotStart; // overlap
                });
                if ($blockedByTimeOff) {
                    continue;
                }

                // Overlaps with any appointment?
                $blockedByAppt = $appointments->first(function ($ap) use ($slotStart, $slotEnd) {
                    $apStart = $ap->start_at instanceof Carbon ? $ap->start_at : Carbon::parse((string) $ap->start_at);
                    $apEnd = $ap->end_at instanceof Carbon ? $ap->end_at : Carbon::parse((string) $ap->end_at);
                    return $apStart < $slotEnd && $apEnd > $slotStart; // overlap
                });
                if ($blockedByAppt) {
                    continue;
                }

                $slots[] = $slotStart->format('Y-m-d H:i:s');
            }
        }

        // Ensure sorted and unique
        $slots = array_values(array_unique($slots));
        sort($slots);

        return $slots;
    }
}

