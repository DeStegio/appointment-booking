<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Services\AvailabilityService;
use Carbon\Carbon;

class AvailabilityServiceTest extends TestCase
{
    use RefreshDatabase;
    protected $seed = true;

    public function test_returns_some_slots_for_workday()
    {
        $provider = User::where('role','provider')->firstOrFail();
        $service  = $provider->services()->firstOrFail();

        // Pick next weekday (Mon-Fri)
        $date = Carbon::now()->next(Carbon::MONDAY)->toDateString();

        $svc = app(AvailabilityService::class);
        $slots = $svc->getSlots($provider->id, $service->id, $date);

        $this->assertIsArray($slots);
        $this->assertNotEmpty($slots);
    }
}

