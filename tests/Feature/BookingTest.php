<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Service;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_slots_page_loads_for_active_provider()
    {
        $provider = User::where('role','provider')->where('is_active',true)->firstOrFail();
        $service  = $provider->services()->firstOrFail();

        $date = now()->addDays(1)->toDateString();
        $resp = $this->get(route('appointments.slots', ['provider'=>$provider->id, 'service'=>$service->id, 'date'=>$date]));
        $resp->assertStatus(200)->assertSee('Slots');
    }

    public function test_customer_can_book_an_available_slot()
    {
        $customer = User::where('role','customer')->firstOrFail();
        $provider = User::where('role','provider')->where('is_active',true)->firstOrFail();
        $service  = $provider->services()->firstOrFail();

        $date = now()->addDays(1)->toDateString();
        $slotsResp = $this->get(route('appointments.slots', ['provider'=>$provider->id, 'service'=>$service->id, 'date'=>$date]));
        $slotsResp->assertStatus(200);
        preg_match('/data-start="([^"]+)"/', $slotsResp->getContent(), $m);
        $this->assertNotEmpty($m, 'No slot found on page');
        $startAt = $m[1];

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

        $date = now()->addDays(2)->toDateString();
        $slotsResp = $this->get(route('appointments.slots', ['provider'=>$provider->id, 'service'=>$service->id, 'date'=>$date]));
        $slotsResp->assertStatus(200);
        preg_match('/data-start="([^"]+)"/', $slotsResp->getContent(), $m);
        $this->assertNotEmpty($m, 'No slot found on page');
        $startAt = $m[1];

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

