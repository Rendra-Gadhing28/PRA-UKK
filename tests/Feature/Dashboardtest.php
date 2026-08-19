<?php

namespace Tests\Feature;

use App\Models\Bookings;
use App\Models\Treatments;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_cannot_be_accessed_without_login(): void
    {
        $response = $this->get(route('user.dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_dashboard_can_be_accessed_by_authenticated_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('user.dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('user.dashboard');
        $response->assertViewHas(['user', 'stats', 'membership', 'topTreatments', 'upcomingBookings']);
    }
}