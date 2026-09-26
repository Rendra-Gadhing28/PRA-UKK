<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActiveStatusEnforcementTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_user_can_access_dashboard_and_account_status(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->get(route('account.status'));
        $response->assertOk();
        $response->assertJson(['active' => true]);

        $responseDashboard = $this->actingAs($user)->get(route('user.dashboard'));
        $responseDashboard->assertOk();
    }

    public function test_deactivated_user_is_logged_out_and_redirected_to_login(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
            'is_active' => false,
        ]);

        $response = $this->actingAs($user)->get(route('user.dashboard'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_deactivated_user_ajax_request_returns_403_and_deactivated_flag(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
            'is_active' => false,
        ]);

        $response = $this->actingAs($user)->getJson(route('account.status'));

        $response->assertStatus(403);
        $response->assertJson([
            'active' => false,
            'deactivated' => true,
        ]);
        $this->assertGuest();
    }

    public function test_deactivated_user_cannot_login_with_password(): void
    {
        $user = User::factory()->create([
            'email' => 'deactivated@yaliabeauty.test',
            'password' => bcrypt('password123'),
            'is_active' => false,
        ]);

        $response = $this->post(route('login'), [
            'email' => 'deactivated@yaliabeauty.test',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_admin_deactivating_user_logs_them_out_on_next_request(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user', 'is_active' => true]);

        // 1. Admin deactivates user
        $this->actingAs($admin)->post(route('admin.users.toggle-active', $user));
        $this->assertFalse($user->fresh()->is_active);

        // 2. User attempts to make any request while still holding their session
        $response = $this->actingAs($user->fresh())->get(route('user.dashboard'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
