<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\User;
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
        // Optional short-circuit: if provider inactive, no slots
        $provider = User::find($providerId);
        if (!$provider || !(bool) $provider->is_active) {
            return [];
        }
        $tz = config('app.timezone');
        $date = CarbonImmutable::createFromFormat('Y-m-d', $dateYmd, $tz);
        if ($date) {
            $date = $date->startOfDay();
        }
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

        // Enforce booking window: date <= today + booking_window_days
        $windowDays = (int) config('appointments.booking_window_days', 60);
        $today = CarbonImmutable::now($tz)->startOfDay();
        $lastBookableDay = $today->addDays($windowDays);
        if ($date->greaterThan($lastBookableDay)) {
            return [];
        }

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

        $leadMinutes = (int) config('appointments.lead_time_minutes', 120);
        $bufferMinutes = (int) config('appointments.slot_buffer_minutes', 0);
        $now = CarbonImmutable::now($tz);
        $earliestStart = $now->addMinutes($leadMinutes);
        $slots = [];

        foreach ($schedules as $schedule) {
            $interval = (int) ($schedule->slot_interval_minutes ?? 30);
            if ($interval <= 0) {
                $interval = 30;
            }

            // Build schedule window on the given date
            $scheduleStart = CarbonImmutable::parse($dateYmd.' '.$schedule->start_time, $tz);
            $scheduleEnd = CarbonImmutable::parse($dateYmd.' '.$schedule->end_time, $tz);

            // Latest start time to fit duration
            $latestStart = $scheduleEnd->subMinutes($duration);
            if ($latestStart->lessThan($scheduleStart)) {
                continue; // duration does not fit in this schedule
            }

            // Align the first cursor to interval grid and lead time
            $cursor = $scheduleStart;
            if ($earliestStart->greaterThan($cursor)) {
                // Round up earliestStart to the next interval tick relative to scheduleStart
                $diffMinutes = $scheduleStart->diffInMinutes($earliestStart, false);
                if ($diffMinutes > 0) {
                    $rounds = (int) ceil($diffMinutes / $interval);
                    $cursor = $scheduleStart->addMinutes($rounds * $interval);
                }
            }

            for (; !$cursor->greaterThan($latestStart); $cursor = $cursor->addMinutes($interval)) {
                $slotStart = $cursor;
                $slotEnd = $slotStart->addMinutes($duration);

                // Exclude past slots
                if ($slotStart->lt($earliestStart)) {
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
                $blockedByAppt = $appointments->first(function ($ap) use ($slotStart, $slotEnd, $bufferMinutes) {
                    $apStart = $ap->start_at instanceof Carbon ? $ap->start_at : Carbon::parse((string) $ap->start_at);
                    $apEnd = $ap->end_at instanceof Carbon ? $ap->end_at : Carbon::parse((string) $ap->end_at);
                    if ($bufferMinutes > 0) {
                        $apStart = (clone $apStart)->subMinutes($bufferMinutes);
                        $apEnd = (clone $apEnd)->addMinutes($bufferMinutes);
                    }
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
