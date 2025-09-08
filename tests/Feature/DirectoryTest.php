<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\User;
use App\Services\AvailabilityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DirectoryTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_lists_only_active_providers()
    {
        $activeProvider = User::where('role', 'provider')->where('is_active', true)->firstOrFail();
        $inactive = User::factory()->create([
            'name' => 'Inactive Provider',
            'email' => 'inactive@example.com',
            'role' => 'provider',
            'is_active' => false,
        ]);

        $resp = $this->get(route('providers.index'));
        $resp->assertStatus(200);
        $resp->assertSee($activeProvider->name);
        $resp->assertDontSee('Inactive Provider');
    }

    public function test_provider_show_works_and_lists_services()
    {
        $provider = User::where('role', 'provider')->where('is_active', true)->firstOrFail();
        $service = $provider->services()->where('is_active', true)->first();
        $resp = $this->get(route('providers.show', ['provider' => $provider->slug]));
        $resp->assertStatus(200)->assertSee($provider->name);
        if ($service) {
            $resp->assertSee($service->name);
        }
    }

    public function test_slots_endpoint_returns_json()
    {
        $provider = User::where('role', 'provider')->where('is_active', true)->firstOrFail();
        $service = $provider->services()->where('is_active', true)->firstOrFail();
        $date = now()->toDateString();
        $resp = $this->get(route('providers.service.slots', ['provider' => $provider->slug, 'service' => $service->slug, 'date' => $date]));
        $resp->assertStatus(200)->assertJsonStructure(['date', 'slots']);
    }

    public function test_booking_as_guest_redirects_to_login()
    {
        $provider = User::where('role', 'provider')->where('is_active', true)->firstOrFail();
        $service = $provider->services()->where('is_active', true)->firstOrFail();

        // Find a near-future slot
        $availability = app(AvailabilityService::class);
        $startAt = null; $date = null;
        for ($i = 1; $i <= 14; $i++) {
            $d = now()->addDays($i)->toDateString();
            $slots = $availability->getSlots($provider->id, $service->id, $d);
            if (!empty($slots)) { $date = $d; $startAt = $slots[0]; break; }
        }
        $this->assertNotNull($startAt, 'No available slots to test booking as guest');

        $resp = $this->post(route('appointments.store'), [
            'provider_id' => $provider->id,
            'service_id' => $service->id,
            'start_at' => $startAt,
        ]);
        $resp->assertStatus(302);
        $resp->assertRedirect(route('login'));
    }
}

