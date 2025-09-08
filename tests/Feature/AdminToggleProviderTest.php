<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class AdminToggleProviderTest extends TestCase
{
    use RefreshDatabase;
    protected $seed = true;

    public function test_admin_can_disable_provider_and_it_disappears_from_discovery()
    {
        $admin = User::where('role','admin')->firstOrFail();
        $provider = User::where('role','provider')->firstOrFail();

        $this->actingAs($admin)
             ->patch(route('admin.providers.toggle', $provider))
             ->assertRedirect();

        $provider->refresh();
        $this->assertFalse((bool)$provider->is_active);

        $resp = $this->get(route('providers.index'));
        $resp->assertStatus(200)->assertDontSee($provider->name);
    }
}

