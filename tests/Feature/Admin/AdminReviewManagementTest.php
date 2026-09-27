<?php

namespace Tests\Feature\Admin;

use App\Models\Beauticians;
use App\Models\Bookings;
use App\Models\Categories;
use App\Models\Reviews;
use App\Models\Treatments;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminReviewManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $customer;

    private Beauticians $beautician;

    private Treatments $treatment;

    private Bookings $booking;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->customer = User::factory()->create([
            'role' => 'user',
            'is_active' => true,
        ]);

        $this->beautician = Beauticians::create([
            'name' => 'Maya Terapis',
            'phone' => '081234567890',
            'bio' => 'Senior Aesthetician',
            'is_active' => true,
        ]);

        $category = Categories::create([
            'name' => 'Facial Care',
            'slug' => 'facial-care',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $this->treatment = Treatments::create([
            'category_id' => $category->id,
            'name' => 'Acne Glow Facial',
            'slug' => 'acne-glow-facial',
            'price' => 180000,
            'duration_minutes' => 60,
            'badge' => 'best_seller',
            'is_active' => true,
        ]);

        $this->booking = Bookings::create([
            'booking_code' => 'YB-REV-TEST',
            'user_id' => $this->customer->id,
            'beautician_id' => $this->beautician->id,
            'booking_type' => 'salon',
            'booking_date' => now()->toDateString(),
            'time_start' => '11:00',
            'time_end' => '12:00',
            'status' => 'completed',
            'payment_status' => 'paid',
            'payment_method' => 'qris',
            'subtotal' => 180000,
            'total_amount' => 180000,
        ]);

        $this->booking->treatments()->attach($this->treatment->id, [
            'quantity' => 1,
            'price_per_unit' => 180000,
            'subtotal' => 180000,
        ]);
    }

    public function test_non_admin_cannot_access_admin_reviews(): void
    {
        $response = $this->actingAs($this->customer)->get(route('admin.reviews.index'));
        $response->assertRedirect(route('user.dashboard'));
    }

    public function test_admin_can_view_reviews_index_page(): void
    {
        Reviews::create([
            'booking_id' => $this->booking->id,
            'user_id' => $this->customer->id,
            'beautician_id' => $this->beautician->id,
            'rating' => 5,
            'beautician_rating' => 5,
            'comment' => 'Pelayanan sangat bagus dan ramah!',
            'is_approved' => true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.reviews.index'));

        $response->assertOk();
        $response->assertSee('Pelayanan sangat bagus dan ramah!');
        $response->assertSee('Maya Terapis');
    }

    public function test_admin_can_reply_to_review(): void
    {
        $review = Reviews::create([
            'booking_id' => $this->booking->id,
            'user_id' => $this->customer->id,
            'beautician_id' => $this->beautician->id,
            'rating' => 5,
            'comment' => 'Suka banget sama hasilnya!',
            'is_approved' => true,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.reviews.reply', $review), [
            'admin_reply' => 'Terima kasih kak sudah mempercayakan perawatan di Yalia Beauty!',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'admin_reply' => 'Terima kasih kak sudah mempercayakan perawatan di Yalia Beauty!',
        ]);
    }

    public function test_admin_can_toggle_review_approval(): void
    {
        $review = Reviews::create([
            'booking_id' => $this->booking->id,
            'user_id' => $this->customer->id,
            'beautician_id' => $this->beautician->id,
            'rating' => 4,
            'is_approved' => true,
        ]);

        $response = $this->actingAs($this->admin)->patch(route('admin.reviews.toggle-approve', $review));
        $response->assertRedirect();

        $review->refresh();
        $this->assertFalse($review->is_approved);
    }

    public function test_admin_can_delete_review(): void
    {
        $review = Reviews::create([
            'booking_id' => $this->booking->id,
            'user_id' => $this->customer->id,
            'beautician_id' => $this->beautician->id,
            'rating' => 1,
            'comment' => 'Spam review',
            'is_approved' => false,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.reviews.destroy', $review));
        $response->assertRedirect();

        $this->assertDatabaseMissing('reviews', [
            'id' => $review->id,
        ]);
    }
}
