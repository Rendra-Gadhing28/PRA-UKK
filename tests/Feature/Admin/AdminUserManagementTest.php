<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_cannot_access_admin_user_management(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get(route('admin.users.index'));

        // Non-admin will be redirected to user dashboard with error
        $response->assertRedirect(route('user.dashboard'));
    }

    public function test_admin_can_view_user_management_screen(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('admin.users.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.users.index');
        $response->assertViewHas('users');
    }

    public function test_admin_can_toggle_user_active_status(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user', 'is_active' => true]);

        $response = $this->actingAs($admin)->post(route('admin.users.toggle-active', $user));

        $response->assertRedirect();
        $this->assertFalse($user->fresh()->is_active);
    }

    public function test_admin_cannot_deactivate_self(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        $response = $this->actingAs($admin)->post(route('admin.users.toggle-active', $admin));

        $response->assertRedirect();
        $this->assertTrue($admin->fresh()->is_active);
    }

    public function test_admin_can_reset_memberships_to_regular(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer1 = User::factory()->create([
            'role' => 'user',
            'membership_level' => 'gold',
            'tier_points' => 1200,
        ]);
        $customer2 = User::factory()->create([
            'role' => 'user',
            'membership_level' => 'silver',
            'tier_points' => 600,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.users.reset-memberships'));

        $response->assertRedirect();

        $this->assertEquals('regular', $customer1->fresh()->membership_level);
        $this->assertEquals(0, $customer1->fresh()->tier_points);
        $this->assertEquals('regular', $customer2->fresh()->membership_level);
        $this->assertEquals(0, $customer2->fresh()->tier_points);
    }

    public function test_admin_can_simulate_90d_quarter_reset(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = User::factory()->create([
            'role' => 'user',
            'membership_level' => 'gold',
            'tier_points' => 1500,
            'last_tier_reset_at' => now(),
        ]);

        $response = $this->actingAs($admin)->post(route('admin.users.simulate-90d'));

        $response->assertRedirect();

        $customer->refresh();
        $this->assertEquals('regular', $customer->membership_level);
        $this->assertEquals(0, $customer->tier_points);
    }
}
