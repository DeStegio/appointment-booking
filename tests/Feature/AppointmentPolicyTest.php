<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Appointment;
use Carbon\Carbon;

class AppointmentPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    protected function makePendingAppointment()
    {
        $provider = User::where('role','provider')->firstOrFail();
        $customer = User::where('role','customer')->firstOrFail();
        $service  = $provider->services()->firstOrFail();
        $start = Carbon::now()->addDays(1)->setTime(11,0,0);
        $end   = (clone $start)->addMinutes($service->duration_minutes ?? 30);

        return Appointment::create([
            'provider_id'=>$provider->id,
            'customer_id'=>$customer->id,
            'service_id' =>$service->id,
            'start_at'   =>$start,
            'end_at'     =>$end,
            'status'     =>'pending',
        ]);
    }

    public function test_provider_can_confirm_and_complete()
    {
        $appt = $this->makePendingAppointment();
        $provider = User::find($appt->provider_id);

        $this->actingAs($provider)
            ->patch(route('appointments.confirm', $appt))
            ->assertRedirect();

        $this->assertDatabaseHas('appointments', ['id'=>$appt->id,'status'=>'confirmed']);

        $this->actingAs($provider)
            ->patch(route('appointments.complete', $appt))
            ->assertRedirect();

        $this->assertDatabaseHas('appointments', ['id'=>$appt->id,'status'=>'completed']);
    }

    public function test_customer_cannot_confirm_but_can_cancel_own()
    {
        $appt = $this->makePendingAppointment();
        $customer = User::find($appt->customer_id);

        $this->actingAs($customer)
            ->patch(route('appointments.confirm', $appt))
            ->assertStatus(403);

        $this->actingAs($customer)
            ->patch(route('appointments.cancel', $appt))
            ->assertRedirect();

        $this->assertDatabaseHas('appointments', ['id'=>$appt->id,'status'=>'cancelled']);
    }
}

