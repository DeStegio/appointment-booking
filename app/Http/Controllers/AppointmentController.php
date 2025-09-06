<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\User;
use App\Models\Appointment;
use App\Services\AvailabilityService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AppointmentController extends Controller
{
    public function slots(User $provider, Service $service, Request $request, AvailabilityService $availability)
    {
        // Ensure the service belongs to the provider to avoid mismatches
        if ((int) $service->provider_id !== (int) $provider->id) {
            abort(404);
        }

        $validated = $request->validate([
            'date' => ['required', 'date_format:Y-m-d'],
        ]);

        $date = $validated['date'];
        $slots = $availability->getSlots($provider->id, $service->id, $date);

        return view('appointments.slots', compact('provider', 'service', 'date', 'slots'));
    }

    public function store(Request $request, AvailabilityService $availability)
    {

        $validated = $request->validate([
            'provider_id' => ['required', 'exists:users,id'],
            'service_id' => ['required', 'exists:services,id'],
            'start_at' => ['required', 'date_format:Y-m-d H:i:s'],
            'notes' => ['nullable', 'string'],
        ]);

        $service = Service::findOrFail((int) $validated['service_id']);
        $providerId = (int) $validated['provider_id'];
        $startAt = Carbon::createFromFormat('Y-m-d H:i:s', $validated['start_at']);
        $duration = (int) ($service->duration_minutes ?? 30);
        if ($duration <= 0) {
            $duration = 30;
        }
        $endAt = (clone $startAt)->addMinutes($duration);

        // Verify provider-service relationship
        if ((int) $service->provider_id !== $providerId) {
            return back()->withErrors(['service_id' => 'Service does not belong to the selected provider.'])->withInput();
        }

        // Availability check
        $date = $startAt->format('Y-m-d');
        $slots = $availability->getSlots($providerId, $service->id, $date);
        if (!in_array($startAt->format('Y-m-d H:i:s'), $slots, true)) {
            return back()->withErrors(['start_at' => 'Selected time is no longer available.'])->withInput();
        }

        DB::transaction(function () use ($providerId, $service, $startAt, $endAt, $validated) {
            Appointment::create([
                'provider_id' => $providerId,
                'customer_id' => Auth::id(),
                'service_id' => $service->id,
                'start_at' => $startAt->format('Y-m-d H:i:s'),
                'end_at' => $endAt->format('Y-m-d H:i:s'),
                'status' => 'pending',
                'notes' => $validated['notes'] ?? null,
            ]);
        });

        return redirect()->route('dashboard')->with('status', 'Appointment requested.');
    }
}
