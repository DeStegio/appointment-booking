<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Service;
use App\Services\AvailabilityService;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_slots_page_loads_for_active_provider()
    {
        $provider = User::where('role','provider')->where('is_active',true)->firstOrFail();
        $service  = $provider->services()->firstOrFail();

        // Find first date within 14 days with at least one slot
        $svc = app(AvailabilityService::class);
        $date = null; $firstSlot = null; $slots = [];
        for ($i = 1; $i <= 14; $i++) {
            $d = now()->addDays($i)->toDateString();
            $slots = $svc->getSlots($provider->id, $service->id, $d);
            if (!empty($slots)) { $date = $d; $firstSlot = $slots[0]; break; }
        }
        $this->assertNotNull($date, 'No available date with slots in next 14 days');

        $resp = $this->get(route('appointments.slots', ['provider'=>$provider->id, 'service'=>$service->id, 'date'=>$date]));
        $resp->assertStatus(200)->assertSee('Slots');
    }

    public function test_customer_can_book_an_available_slot()
    {
        $customer = User::where('role','customer')->firstOrFail();
        $provider = User::where('role','provider')->where('is_active',true)->firstOrFail();
        $service  = $provider->services()->firstOrFail();

        // Find first available date/slot using the service to avoid brittle parsing
        $svc = app(AvailabilityService::class);
        $date = null; $startAt = null; $slots = [];
        for ($i = 1; $i <= 14; $i++) {
            $d = now()->addDays($i)->toDateString();
            $slots = $svc->getSlots($provider->id, $service->id, $d);
            if (!empty($slots)) { $date = $d; $startAt = $slots[0]; break; }
        }
        $this->assertNotNull($date, 'No available date with slots in next 14 days');

        // Optional: still load the page and ensure it renders
        $slotsResp = $this->get(route('appointments.slots', ['provider'=>$provider->id, 'service'=>$service->id, 'date'=>$date]));
        $slotsResp->assertStatus(200);
        if (!$startAt) {
            // Fallback: try to scrape if needed
            if (preg_match('/data-start=\"([^\"]+)\"/', $slotsResp->getContent(), $m)) {
                $startAt = $m[1];
            }
        }

        $this->assertNotEmpty($startAt, 'No slot found to book');

        $this->actingAs($customer);
        $resp = $this->post(route('appointments.store'), [
            'provider_id'=>$provider->id,
            'service_id'=>$service->id,
            'start_at'   =>$startAt,
        ]);
        $resp->assertRedirect();
        $this->assertDatabaseHas('appointments', [
            'provider_id'=>$provider->id,
            'customer_id'=>$customer->id,
            'service_id' =>$service->id,
            'start_at'   =>$startAt,
        ]);
    }

    public function test_double_booking_is_blocked()
    {
        $customer = User::where('role','customer')->firstOrFail();
        $provider = User::where('role','provider')->where('is_active',true)->firstOrFail();
        $service  = $provider->services()->firstOrFail();

        // Find first available date/slot using the service
        $svc = app(AvailabilityService::class);
        $date = null; $startAt = null; $slots = [];
        for ($i = 1; $i <= 14; $i++) {
            $d = now()->addDays($i)->toDateString();
            $slots = $svc->getSlots($provider->id, $service->id, $d);
            if (!empty($slots)) { $date = $d; $startAt = $slots[0]; break; }
        }
        $this->assertNotNull($date, 'No available date with slots in next 14 days');

        // Optional: still load the page and ensure it renders for that date
        $slotsResp = $this->get(route('appointments.slots', ['provider'=>$provider->id, 'service'=>$service->id, 'date'=>$date]));
        $slotsResp->assertStatus(200);

        $this->actingAs($customer)->post(route('appointments.store'), [
            'provider_id'=>$provider->id,
            'service_id'=>$service->id,
            'start_at'   =>$startAt,
        ])->assertRedirect();

        // 2nd attempt (same slot) should fail (unique + guard)
        $this->actingAs($customer)->post(route('appointments.store'), [
            'provider_id'=>$provider->id,
            'service_id'=>$service->id,
            'start_at'   =>$startAt,
        ])->assertSessionHasErrors();
    }
}

