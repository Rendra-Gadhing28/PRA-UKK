<?php

namespace Tests\Feature\User;

use App\Models\Beauticians;
use App\Models\Bookings;
use App\Models\Categories;
use App\Models\Treatments;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentPageTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;

    private Beauticians $beautician;

    private Treatments $treatment;

    private Bookings $booking;

    protected function setUp(): void
    {
        parent::setUp();

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
            'booking_code' => 'YB-PAY-TEST',
            'user_id' => $this->customer->id,
            'beautician_id' => $this->beautician->id,
            'booking_type' => 'salon',
            'booking_date' => now()->toDateString(),
            'time_start' => '11:00',
            'time_end' => '12:00',
            'status' => 'pending',
            'payment_status' => 'pending',
            'payment_method' => 'qris',
            'payment_expires_at' => now()->addMinutes(15),
            'subtotal' => 180000,
            'total_amount' => 180000,
        ]);

        $this->booking->treatments()->attach($this->treatment->id, [
            'quantity' => 1,
            'price_per_unit' => 180000,
            'subtotal' => 180000,
        ]);
    }

    public function test_customer_can_view_payment_terminal_page(): void
    {
        $response = $this->actingAs($this->customer)->get(route('user.bookings.payment', $this->booking));

        $response->assertOk();
        $response->assertSee('Selesaikan Pembayaran QRIS');
        $response->assertSee('#YB-PAY-TEST');
        $response->assertSee('Acne Glow Facial');
        $response->assertSee('Maya Terapis');
        $response->assertSee('BCA');
        $response->assertSee('GoPay');
    }

    public function test_other_customer_cannot_view_payment_page(): void
    {
        $otherUser = User::factory()->create(['role' => 'user', 'is_active' => true]);

        $response = $this->actingAs($otherUser)->get(route('user.bookings.payment', $this->booking));
        $response->assertForbidden();
    }
}
