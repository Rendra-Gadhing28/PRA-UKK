<?php

namespace Tests\Feature\User;

use App\Models\Beauticians;
use App\Models\Bookings;
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

    public function test_user_can_toggle_favorite_treatment(): void
    {
        $user = User::factory()->create();

        $category = Categories::create([
            'name' => 'Facial Care',
            'slug' => 'facial-care',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $treatment = Treatments::create([
            'category_id' => $category->id,
            'name' => 'Facial Glowing Gold',
            'slug' => 'facial-glowing-gold',
            'price' => 150000,
            'duration_minutes' => 60,
            'badge' => 'none',
            'is_active' => true,
        ]);

        // Toggle ON
        $resOn = $this->actingAs($user)->postJson(route('user.treatments.favorite', $treatment));
        $resOn->assertOk()->assertJson(['success' => true, 'favorited' => true]);
        $this->assertDatabaseHas('user_favorite_treatments', [
            'user_id' => $user->id,
            'treatment_id' => $treatment->id,
        ]);

        // Toggle OFF
        $resOff = $this->actingAs($user)->postJson(route('user.treatments.favorite', $treatment));
        $resOff->assertOk()->assertJson(['success' => true, 'favorited' => false]);
        $this->assertDatabaseMissing('user_favorite_treatments', [
            'user_id' => $user->id,
            'treatment_id' => $treatment->id,
        ]);
    }

    public function test_user_can_submit_dual_review_and_earn_loyalty_points(): void
    {
        $user = User::factory()->create([
            'total_points' => 10,
            'tier_points' => 10,
        ]);

        $beautician = Beauticians::create([
            'name' => 'Siti Terapis',
            'phone' => '08123456789',
            'bio' => 'Senior Beautician',
            'is_active' => true,
        ]);

        $category = Categories::create([
            'name' => 'Nail Art',
            'slug' => 'nail-art',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $treatment = Treatments::create([
            'category_id' => $category->id,
            'name' => 'Gel Nail Art Luxury',
            'slug' => 'gel-nail-art-luxury',
            'price' => 120000,
            'duration_minutes' => 45,
            'badge' => 'none',
            'is_active' => true,
        ]);

        $booking = Bookings::create([
            'booking_code' => 'YB-TEST-REVIEW',
            'user_id' => $user->id,
            'beautician_id' => $beautician->id,
            'booking_type' => 'salon',
            'booking_date' => now()->toDateString(),
            'time_start' => '10:00',
            'time_end' => '10:45',
            'status' => 'completed',
            'payment_status' => 'paid',
            'payment_method' => 'qris',
            'subtotal' => 120000,
            'total_amount' => 120000,
        ]);

        $booking->treatments()->attach($treatment->id, [
            'quantity' => 1,
            'price_per_unit' => 120000,
            'subtotal' => 120000,
        ]);

        $response = $this->actingAs($user)->post(route('user.treatments.review.store', [
            'booking' => $booking->id,
            'treatment' => $treatment->id,
        ]), [
            'rating' => 5,
            'beautician_rating' => 5,
            'beautician_tags' => ['Ramah & Sopan', 'Sangat Teliti & Rapi'],
            'comment' => 'Pelayanan sangat memuaskan dan tempat bersih!',
        ]);

        $response->assertRedirect(route('user.bookings.show', $booking));

        $this->assertDatabaseHas('reviews', [
            'booking_id' => $booking->id,
            'user_id' => $user->id,
            'beautician_id' => $beautician->id,
            'rating' => 5,
            'beautician_rating' => 5,
        ]);

        $user->refresh();
        $this->assertEquals(25, $user->total_points); // 10 + 15 reward
    }
}
