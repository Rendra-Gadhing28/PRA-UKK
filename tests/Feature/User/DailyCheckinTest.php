<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DailyCheckinTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_claim_daily_checkin(): void
    {
        $response = $this->post(route('user.daily-checkin'));

        $response->assertRedirect(route('login'));
    }

    public function test_user_can_claim_daily_checkin_first_time_today(): void
    {
        $user = User::factory()->create([
            'total_points' => 0,
            'tier_points' => 0,
            'last_daily_checkin_at' => null,
        ]);

        $response = $this->actingAs($user)->postJson(route('user.daily-checkin'));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'points_added' => 25,
                'total_points' => 25,
            ]);

        $user->refresh();
        $this->assertEquals(25, $user->total_points);
        $this->assertEquals(25, $user->tier_points);
        $this->assertNotNull($user->last_daily_checkin_at);
    }

    public function test_user_cannot_claim_daily_checkin_twice_in_same_day(): void
    {
        $user = User::factory()->create([
            'total_points' => 50,
            'last_daily_checkin_at' => now(),
        ]);

        $response = $this->actingAs($user)->postJson(route('user.daily-checkin'));

        $response->assertStatus(200)
            ->assertJson([
                'success' => false,
                'already_claimed' => true,
                'total_points' => 50,
            ]);

        $this->assertEquals(50, $user->fresh()->total_points);
    }
}
