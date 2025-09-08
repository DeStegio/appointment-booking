<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Service;
use App\Services\AvailabilityService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AppointmentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:customer']);
    }

    public function index(Request $request)
    {
        $customerId = (int) Auth::id();

        $upcoming = Appointment::query()
            ->with(['provider', 'service'])
            ->upcomingForCustomer($customerId)
            ->paginate(10, ['*'], 'upcoming_page');

        $past = Appointment::query()
            ->with(['provider', 'service'])
            ->where('customer_id', $customerId)
            ->where('start_at', '<', Carbon::now())
            ->orderBy('start_at', 'desc')
            ->paginate(10, ['*'], 'past_page');

        return view('customer.appointments.index', compact('upcoming', 'past'));
    }

    public function show(Appointment $appointment)
    {
        $this->authorize('view', $appointment);
        $appointment->load(['provider', 'service']);
        return view('customer.appointments.show', compact('appointment'));
    }

    public function edit(Appointment $appointment, Request $request, AvailabilityService $availability)
    {
        $this->authorize('reschedule', $appointment);
        $date = (string) $request->input('date', Carbon::today()->toDateString());
        $slots = $availability->getSlots((int) $appointment->provider_id, (int) $appointment->service_id, $date);
        $service = $appointment->service;
        return view('customer.appointments.edit', compact('appointment', 'date', 'slots', 'service'));
    }

    public function update(Request $request, Appointment $appointment, AvailabilityService $availability)
    {
        $this->authorize('reschedule', $appointment);

        $data = $request->validate([
            'date' => ['required', 'date'],
            'start_at' => ['required', 'date'],
        ]);

        $tz = config('app.timezone');
        $startAt = Carbon::parse((string) $data['start_at'], $tz);
        $service = Service::findOrFail((int) $appointment->service_id);
        $duration = (int) ($service->duration_minutes ?? 30);
        if ($duration <= 0) { $duration = 30; }
        $endAt = (clone $startAt)->addMinutes($duration);

        // Validate selected slot is still available
        $date = Carbon::parse((string) $data['date'], $tz)->toDateString();
        $slots = $availability->getSlots((int) $appointment->provider_id, (int) $appointment->service_id, $date);
        if (!in_array($startAt->format('Y-m-d H:i:s'), $slots, true)) {
            return back()->withErrors(['start_at' => 'Slot is no longer available'])->withInput();
        }

        try {
            DB::transaction(function () use ($appointment, $startAt, $endAt) {
                $appointment->start_at = $startAt->format('Y-m-d H:i:s');
                $appointment->end_at = $endAt->format('Y-m-d H:i:s');
                $appointment->save();
            });
        } catch (\Illuminate\Database\QueryException $e) {
            $msg = (string) $e->getMessage();
            if (stripos($msg, 'appointments_provider_start_at_unique') !== false || stripos($msg, 'unique') !== false) {
                return back()->withErrors(['start_at' => 'Slot is no longer available'])->withInput();
            }
            throw $e;
        }

        return redirect()->route('my.appointments.show', $appointment)->with('status', 'Appointment rescheduled.');
    }

    public function cancel(Appointment $appointment)
    {
        $this->authorize('cancel', $appointment);
        if ($appointment->isPast()) {
            return back()->withErrors('Cannot cancel a past appointment.');
        }

        $appointment->status = 'cancelled';
        $appointment->save();

        return redirect()->route('my.appointments.index')->with('status', 'Appointment cancelled.');
    }
}

