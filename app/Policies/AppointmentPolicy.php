<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;
use Carbon\Carbon;

class AppointmentPolicy
{
    /**
     * View an appointment: provider owner or customer (admins handled via Gate::before)
     */
    public function view(User $user, Appointment $appointment): bool
    {
        return (int) $appointment->provider_id === (int) $user->id
            || (int) $appointment->customer_id === (int) $user->id;
    }

    /**
     * Confirm: provider owner and appointment is pending.
     */
    public function confirm(User $user, Appointment $appointment): bool
    {
        return (int) $appointment->provider_id === (int) $user->id
            && strcasecmp((string) $appointment->status, 'pending') === 0;
    }

    /**
     * Complete: provider owner, appointment confirmed, and start_at <= now()+1 day.
     */
    public function complete(User $user, Appointment $appointment): bool
    {
        if ((int) $appointment->provider_id !== (int) $user->id) {
            return false;
        }
        if (strcasecmp((string) $appointment->status, 'confirmed') !== 0) {
            return false;
        }
        $start = $appointment->start_at instanceof \DateTimeInterface
            ? Carbon::instance($appointment->start_at)
            : Carbon::parse((string) $appointment->start_at);
        return $start->lessThanOrEqualTo(Carbon::now()->addDay());
    }

    /**
     * Cancel:
     *  - Provider owner: always allowed
     *  - Customer: only if owns, start_at in future, and status in [pending, confirmed]
     */
    public function cancel(User $user, Appointment $appointment): bool
    {
        if ((int) $appointment->provider_id === (int) $user->id) {
            return true; // provider may always cancel
        }

        if ((int) $appointment->customer_id !== (int) $user->id) {
            return false;
        }

        $status = strtolower((string) $appointment->status);
        if (!in_array($status, ['pending', 'confirmed'], true)) {
            return false;
        }

        $start = $appointment->start_at instanceof \DateTimeInterface
            ? Carbon::instance($appointment->start_at)
            : Carbon::parse((string) $appointment->start_at);

        return $start->isFuture();
    }

    /**
     * Reschedule: customer owns, start_at in future, and status in [pending, confirmed]
     */
    public function reschedule(User $user, Appointment $appointment): bool
    {
        if ((int) $appointment->customer_id !== (int) $user->id) {
            return false;
        }

        $status = strtolower((string) $appointment->status);
        if (!in_array($status, ['pending', 'confirmed'], true)) {
            return false;
        }

        $start = $appointment->start_at instanceof \DateTimeInterface
            ? Carbon::instance($appointment->start_at)
            : Carbon::parse((string) $appointment->start_at);

        return $start->isFuture();
    }
}
