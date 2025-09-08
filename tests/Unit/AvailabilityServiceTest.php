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

        // Find first date within 14 days with at least one slot
        $svc = app(AvailabilityService::class);
        $date = null; $firstSlot = null; $slots = [];
        for ($i = 1; $i <= 14; $i++) {
            $d = now()->addDays($i)->toDateString();
            $slots = $svc->getSlots($provider->id, $service->id, $d);
            if (!empty($slots)) { $date = $d; $firstSlot = $slots[0]; break; }
        }
        $this->assertNotNull($date, 'No available date with slots in next 14 days');
        $this->assertIsArray($slots);
        $this->assertNotEmpty($slots);
    }
}
