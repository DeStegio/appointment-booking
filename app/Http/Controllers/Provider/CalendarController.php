<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\ProviderSchedule;
use App\Models\Service;
use App\Models\TimeOff;
use App\Models\User;
use App\Services\AvailabilityService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CalendarController extends Controller
{
    public function day(Request $request, AvailabilityService $availability)
    {
        // 1) Provider ID
        $providerId = (int) Auth::id();

        // 2) Date parsing
        $date = Carbon::parse($request->input('date', now()->toDateString()))->startOfDay();

        // 3) Services for provider; select requested or first
        $services = Service::query()
            ->where('provider_id', $providerId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $service = $request->integer('service_id')
            ? $services->firstWhere('id', (int) $request->input('service_id'))
            : $services->first();

        // 4) Weekday in app timezone (0=Sun..6=Sat)
        $weekday = (int) $date->copy()->timezone(config('app.timezone'))->dayOfWeek;

        // 5) Provider schedules for weekday
        $schedules = ProviderSchedule::query()
            ->where('provider_id', $providerId)
            ->where('weekday', $weekday)
            ->orderBy('start_time')
            ->get();

        // 6) Appointments for the day
        $startOfDay = $date->copy();
        $endOfDay = $date->copy()->endOfDay();
        $appointments = Appointment::query()
            ->where('provider_id', $providerId)
            ->whereBetween('start_at', [$startOfDay, $endOfDay])
            ->with(['customer', 'service'])
            ->orderBy('start_at')
            ->get();

        // 7) Time offs overlapping the day
        $timeOffs = TimeOff::query()
            ->where('provider_id', $providerId)
            ->where(function ($q) use ($startOfDay, $endOfDay) {
                $q->whereBetween('start_at', [$startOfDay, $endOfDay])
                  ->orWhereBetween('end_at', [$startOfDay, $endOfDay])
                  ->orWhere(function ($qq) use ($startOfDay, $endOfDay) {
                      $qq->where('start_at', '<=', $startOfDay)->where('end_at', '>=', $endOfDay);
                  });
            })
            ->orderBy('start_at')
            ->get();

        // 8) Available slots for selected service
        $slots = [];
        if ($service) {
            $slots = $availability->getSlots($providerId, (int) $service->id, $date->toDateString());
        }

        // 9) Prev/Next/Today URLs
        $queryBase = function ($d) use ($service) {
            return [
                'date' => $d->toDateString(),
                'service_id' => $service?->id,
            ];
        };
        $prevUrl = route('calendar.day', $queryBase($date->copy()->subDay()));
        $nextUrl = route('calendar.day', $queryBase($date->copy()->addDay()));
        $todayUrl = route('calendar.day', $queryBase(now()))
            ;

        // 10) View
        return view('provider.calendar.day', compact(
            'date', 'services', 'service', 'schedules', 'timeOffs', 'appointments', 'slots', 'prevUrl', 'nextUrl', 'todayUrl'
        ));
    }
}

