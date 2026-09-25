<?php

namespace Tests\Unit;

use App\Models\User;
use App\Support\Membership;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MembershipTest extends TestCase
{
    use RefreshDatabase;

    public function test_membership_progress_calculates_correct_tiers(): void
    {
        // 0 poin -> regular
        $reg = Membership::progress(0);
        $this->assertEquals('regular', $reg['current']);
        $this->assertEquals(0, $reg['current_meta']['discount_val']);
        $this->assertEquals('silver', $reg['next']);
        $this->assertEquals(500, $reg['points_needed']);

        // 500 poin -> silver
        $silver = Membership::progress(500);
        $this->assertEquals('silver', $silver['current']);
        $this->assertEquals(5, $silver['current_meta']['discount_val']);
        $this->assertEquals('gold', $silver['next']);

        // 1000 poin -> gold
        $gold = Membership::progress(1000);
        $this->assertEquals('gold', $gold['current']);
        $this->assertEquals(10, $gold['current_meta']['discount_val']);
        $this->assertEquals('purple', $gold['next']);

        // 2500 poin -> purple (highest tier)
        $purple = Membership::progress(2500);
        $this->assertEquals('purple', $purple['current']);
        $this->assertEquals(15, $purple['current_meta']['discount_val']);
        $this->assertNull($purple['next']);
        $this->assertEquals(0, $purple['points_needed']);
        $this->assertEquals(100, $purple['percent']);
    }

    public function test_user_add_and_subtract_points_syncs_membership_level(): void
    {
        $user = User::factory()->create([
            'total_points' => 0,
            'tier_points' => 0,
            'membership_level' => 'regular',
        ]);

        // Add 600 points -> Should reach Silver
        $user->addPoints(600);
        $user->refresh();

        $this->assertEquals(600, $user->total_points);
        $this->assertEquals(600, $user->tier_points);
        $this->assertEquals('silver', $user->membership_level);

        // Add 500 more points -> Total 1100 -> Should reach Gold
        $user->addPoints(500);
        $user->refresh();

        $this->assertEquals(1100, $user->total_points);
        $this->assertEquals(1100, $user->tier_points);
        $this->assertEquals('gold', $user->membership_level);

        // Subtract 700 points -> 400 tier points -> Downgrades to Regular
        $user->subtractPoints(700);
        $user->refresh();

        $this->assertEquals(400, $user->total_points);
        $this->assertEquals(400, $user->tier_points);
        $this->assertEquals('regular', $user->membership_level);
    }

    public function test_user_sync_tier_reset_resets_points_and_level_after_quarter(): void
    {
        $user = User::factory()->create([
            'total_points' => 1500,
            'tier_points' => 1500,
            'membership_level' => 'gold',
            'last_tier_reset_at' => now()->subDays(95),
        ]);

        $user->syncTierReset();

        $this->assertEquals(0, $user->tier_points);
        $this->assertEquals('regular', $user->membership_level);
        // Lifetime total_points should NOT be lost, only tier_points resets
        $this->assertEquals(1500, $user->total_points);
    }
}
