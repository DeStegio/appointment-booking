<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\ProviderSchedule;
use App\Models\Service;
use App\Models\User;
use App\Services\AvailabilityService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerAppointmentsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Use MySQL connection as defined in env; migrations run by RefreshDatabase
    }

    protected function createProviderAndSetup(): array
    {
        $provider = User::factory()->create([
            'role' => 'provider',
            'is_active' => true,
        ]);
        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $service = Service::create([
            'provider_id' => $provider->id,
            'name' => 'Consultation',
            'duration_minutes' => 30,
            'price' => 50,
            'is_active' => true,
        ]);

        // Create provider schedule for the next two weeks weekdays 09:00-17:00
        $tz = config('app.timezone');
        $today = Carbon::now($tz)->startOfDay();
        for ($i = 0; $i < 7; $i++) {
            $weekday = $i; // 0=Sun..6=Sat
            ProviderSchedule::create([
                'provider_id' => $provider->id,
                'weekday' => $weekday,
                'start_time' => '09:00:00',
                'end_time' => '17:00:00',
                'slot_interval_minutes' => 30,
            ]);
        }

        return [$provider, $customer, $service];
    }

    public function test_customer_can_load_my_appointments_index()
    {
        [$provider, $customer, $service] = $this->createProviderAndSetup();

        // Create past and upcoming appointments for the customer
        $tz = config('app.timezone');
        $pastStart = Carbon::now($tz)->subDays(2)->setTime(10, 0, 0);
        $pastEnd = (clone $pastStart)->addMinutes(30);
        Appointment::create([
            'provider_id' => $provider->id,
            'customer_id' => $customer->id,
            'service_id' => $service->id,
            'start_at' => $pastStart,
            'end_at' => $pastEnd,
            'status' => 'completed',
        ]);

        $upStart = Carbon::now($tz)->addDays(1)->setTime(11, 0, 0);
        $upEnd = (clone $upStart)->addMinutes(30);
        Appointment::create([
            'provider_id' => $provider->id,
            'customer_id' => $customer->id,
            'service_id' => $service->id,
            'start_at' => $upStart,
            'end_at' => $upEnd,
            'status' => 'pending',
        ]);

        $resp = $this->actingAs($customer)->get(route('my.appointments.index'));
        $resp->assertStatus(200);
        $resp->assertSee('Upcoming');
        $resp->assertSee('Past');
        $resp->assertSee('Consultation');
    }

    public function test_customer_can_cancel_upcoming_appointment()
    {
        [$provider, $customer, $service] = $this->createProviderAndSetup();

        // Find a slot tomorrow
        $availability = app(AvailabilityService::class);
        $tz = config('app.timezone');
        $found = false;
        for ($d = 1; $d <= 3 && !$found; $d++) {
            $date = Carbon::now($tz)->addDays($d)->toDateString();
            $slots = $availability->getSlots($provider->id, $service->id, $date);
            if (!empty($slots)) {
                $startAt = Carbon::parse($slots[0], $tz);
                $endAt = (clone $startAt)->addMinutes(30);
                $appointment = Appointment::create([
                    'provider_id' => $provider->id,
                    'customer_id' => $customer->id,
                    'service_id' => $service->id,
                    'start_at' => $startAt,
                    'end_at' => $endAt,
                    'status' => 'pending',
                ]);
                $found = true;
            }
        }
        $this->assertTrue($found, 'No available slots found for testing');

        $resp = $this->actingAs($customer)->patch(route('my.appointments.cancel', $appointment));
        $resp->assertRedirect();

        $appointment->refresh();
        $this->assertSame('cancelled', $appointment->status);
    }

    public function test_customer_can_reschedule_to_another_available_slot()
    {
        [$provider, $customer, $service] = $this->createProviderAndSetup();
        $availability = app(AvailabilityService::class);
        $tz = config('app.timezone');

        $date = null; $start1 = null; $start2 = null;
        for ($d = 1; $d <= 14; $d++) {
            $candidateDate = Carbon::now($tz)->addDays($d)->toDateString();
            $slots = $availability->getSlots($provider->id, $service->id, $candidateDate);
            if (count($slots) >= 2) {
                $date = $candidateDate;
                $start1 = $slots[0];
                $start2 = $slots[1];
                break;
            }
        }
        $this->assertNotNull($date, 'No date with two available slots found');

        $appointment = Appointment::create([
            'provider_id' => $provider->id,
            'customer_id' => $customer->id,
            'service_id' => $service->id,
            'start_at' => Carbon::parse($start1, $tz),
            'end_at' => Carbon::parse($start1, $tz)->addMinutes(30),
            'status' => 'pending',
        ]);

        $resp = $this->actingAs($customer)->patch(route('my.appointments.update', $appointment), [
            'date' => $date,
            'start_at' => $start2,
        ]);
        $resp->assertRedirect();

        $appointment->refresh();
        $this->assertSame($start2, Carbon::parse($appointment->start_at, $tz)->format('Y-m-d H:i:s'));
    }
}
