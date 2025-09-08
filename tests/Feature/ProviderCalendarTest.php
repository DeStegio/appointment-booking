<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProviderCalendarTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_provider_can_load_day_view(): void
    {
        $provider = User::providers()->first();
        $this->assertNotNull($provider, 'Seeded provider not found');

        $resp = $this->actingAs($provider)->get(route('calendar.day'));
        $resp->assertStatus(200);
        $resp->assertSee('Day View');
        $resp->assertSee('Working hours');
    }

    public function test_service_selector_and_slots_render(): void
    {
        $provider = User::providers()->first();
        $this->assertNotNull($provider, 'Seeded provider not found');

        $service = Service::where('provider_id', $provider->id)->first();
        $this->assertNotNull($service, 'Provider has no service');

        $date = Carbon::now(config('app.timezone'))->toDateString();

        $resp = $this->actingAs($provider)->get(route('calendar.day', [
            'date' => $date,
            'service_id' => $service->id,
        ]));

        $resp->assertStatus(200);
        $resp->assertSee('Available slots');
    }
}

