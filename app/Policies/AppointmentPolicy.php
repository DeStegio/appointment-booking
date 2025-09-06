<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;

class AppointmentPolicy
{
    /**
     * View an appointment: provider owner, customer, or admin (via Gate::before)
     */
    public function view(User $user, Appointment $appointment): bool
    {
        return (int) $appointment->provider_id === (int) $user->id
            || (int) $appointment->customer_id === (int) $user->id;
    }

    /**
     * Confirm: only the provider owner.
     */
    public function confirm(User $user, Appointment $appointment): bool
    {
        return (int) $appointment->provider_id === (int) $user->id;
    }

    /**
     * Complete: provider owner and appointment is confirmed.
     */
    public function complete(User $user, Appointment $appointment): bool
    {
        return (int) $appointment->provider_id === (int) $user->id
            && strcasecmp((string) $appointment->status, 'confirmed') === 0;
    }

    /**
     * Cancel: provider owner or the customer.
     */
    public function cancel(User $user, Appointment $appointment): bool
    {
        return (int) $appointment->provider_id === (int) $user->id
            || (int) $appointment->customer_id === (int) $user->id;
    }
}

