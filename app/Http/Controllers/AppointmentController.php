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
        if (!$provider->is_active) {
            abort(404);
        }
        if ((int) $service->provider_id !== (int) $provider->id) {
            abort(404);
        }
        if (!$service->is_active) {
            abort(404);
        }

        $validated = $request->validate([
            'date' => ['required', 'date_format:Y-m-d'],
        ]);

        $date = $validated['date'];
        $slots = $availability->getSlots($provider->id, $service->id, $date);

        return view('appointments.slots', compact('provider', 'service', 'date', 'slots'));
    }

    public function store(\App\Http\Requests\StoreAppointmentRequest $request, AvailabilityService $availability)
    {
        $data = $request->validated();

        $service = Service::findOrFail((int) $data['service_id']);
        $providerId = (int) $data['provider_id'];
        $provider = User::findOrFail($providerId);
        $tz = config('app.timezone');
        $startAt = Carbon::createFromFormat('Y-m-d H:i:s', $data['start_at'], $tz);
        $duration = (int) ($service->duration_minutes ?? 30);
        if ($duration <= 0) {
            $duration = 30;
        }
        $endAt = (clone $startAt)->addMinutes($duration);

        // Verify provider-service relationship
        if ((int) $service->provider_id !== $providerId) {
            return back()->withErrors(['service_id' => 'Service does not belong to the selected provider.'])->withInput();
        }

        // Ensure provider is active
        if (!$provider->is_active) {
            return back()->withErrors('Provider is not accepting bookings.')->withInput();
        }

        // Availability check (defense-in-depth)
        $date = $startAt->format('Y-m-d');
        $slots = $availability->getSlots($providerId, $service->id, $date);
        if (!in_array($startAt->format('Y-m-d H:i:s'), $slots, true)) {
            return back()->withErrors('That slot is no longer available. Please pick another.')->withInput();
        }

        try {
            DB::transaction(function () use ($providerId, $service, $startAt, $endAt, $data) {
                Appointment::create([
                    'provider_id' => $providerId,
                    'customer_id' => Auth::id(),
                    'service_id' => $service->id,
                    'start_at' => $startAt->format('Y-m-d H:i:s'),
                    'end_at' => $endAt->format('Y-m-d H:i:s'),
                    'status' => 'pending',
                    'notes' => $data['notes'] ?? null,
                ]);
            });
        } catch (\Illuminate\Database\QueryException $e) {
            // Handle unique (provider_id, start_at) violation gracefully
            $message = (string) $e->getMessage();
            if (stripos($message, 'provider_start_unique') !== false
                || stripos($message, 'unique') !== false) {
                return back()->withErrors('That slot is no longer available. Please pick another.')->withInput();
            }
            throw $e; // rethrow other DB errors
        }

        return redirect()->route('dashboard')->with('status', 'Appointment requested.');
    }

    public function confirm(Appointment $appointment)
    {
        $this->authorize('confirm', $appointment);

        if (!in_array(strtolower((string) $appointment->status), ['pending'], true)) {
            return back()->withErrors('Invalid state transition.');
        }

        $appointment->status = 'confirmed';
        $appointment->save();

        return back()->with('status', 'Appointment confirmed.');
    }

    public function complete(Appointment $appointment)
    {
        $this->authorize('complete', $appointment);

        $appointment->status = 'completed';
        $appointment->save();

        return back()->with('status', 'Appointment completed');
    }

    public function cancel(Appointment $appointment)
    {
        $this->authorize('cancel', $appointment);

        if (!in_array(strtolower((string) $appointment->status), ['pending', 'confirmed'], true)) {
            return back()->withErrors('Invalid state transition.');
        }

        $appointment->status = 'cancelled';
        $appointment->save();

        return back()->with('status', 'Appointment cancelled.');
    }
}
