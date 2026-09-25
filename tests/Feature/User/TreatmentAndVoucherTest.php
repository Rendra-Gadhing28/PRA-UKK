<?php

namespace Tests\Feature\User;

use App\Models\Categories;
use App\Models\Treatments;
use App\Models\User;
use App\Models\Vouchers;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TreatmentAndVoucherTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_when_accessing_treatments(): void
    {
        $response = $this->get(route('user.treatments.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_treatments_catalogue(): void
    {
        $user = User::factory()->create();

        $category = Categories::create([
            'name' => 'Hair Care',
            'slug' => 'hair-care',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Treatments::create([
            'category_id' => $category->id,
            'name' => 'Hair Smoothing',
            'slug' => 'hair-smoothing',
            'price' => 200000,
            'duration_minutes' => 120,
            'badge' => 'best_seller',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->get(route('user.treatments.index'));

        $response->assertStatus(200);
        $response->assertViewIs('user.treatments.index');
        $response->assertSee('Hair Smoothing');
    }

    public function test_authenticated_user_can_view_and_claim_voucher(): void
    {
        $user = User::factory()->create([
            'total_points' => 500,
            'tier_points' => 500,
        ]);

        $voucher = Vouchers::create([
            'code' => 'DISC20K',
            'name' => 'Diskon 20 Ribu',
            'value' => 20000,
            'points_required' => 100,
            'min_purchase' => 50000,
            'quota' => 10,
            'type' => 'fixed',
            'is_active' => true,
            'valid_from' => now()->subDay(),
            'valid_until' => now()->addDays(30),
        ]);

        $response = $this->actingAs($user)->get(route('user.vouchers.index'));
        $response->assertStatus(200);

        $claimResponse = $this->actingAs($user)->post(route('user.vouchers.claim', $voucher));
        $claimResponse->assertRedirect();

        $this->assertDatabaseHas('user_vouchers', [
            'user_id' => $user->id,
            'voucher_id' => $voucher->id,
        ]);
    }
}
