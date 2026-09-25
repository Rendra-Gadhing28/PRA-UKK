<?php

namespace Tests\Feature\Webhooks;

use App\Models\Bookings;
use App\Models\Categories;
use App\Models\Treatments;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class MidtransWebhookTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Config::set('booking.midtrans.server_key', 'test-server-key-12345');
    }

    public function test_webhook_returns_422_when_payload_incomplete(): void
    {
        $response = $this->postJson('/webhooks/midtrans', [
            'order_id' => 'ORDER-123',
        ]);

        $response->assertStatus(422)
            ->assertJson(['message' => 'Payload tidak lengkap.']);
    }

    public function test_webhook_returns_403_when_signature_is_invalid(): void
    {
        $response = $this->postJson('/webhooks/midtrans', [
            'order_id' => 'ORDER-123',
            'status_code' => '200',
            'gross_amount' => '100000.00',
            'signature_key' => 'invalid-signature-hash',
            'transaction_status' => 'settlement',
        ]);

        $response->assertStatus(403)
            ->assertJson(['message' => 'Invalid signature.']);
    }

    public function test_webhook_returns_404_when_booking_not_found(): void
    {
        $orderId = 'ORDER-NON-EXISTENT';
        $statusCode = '200';
        $grossAmount = '100000.00';
        $validSignature = hash('sha512', $orderId . $statusCode . $grossAmount . 'test-server-key-12345');

        $response = $this->postJson('/webhooks/midtrans', [
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $validSignature,
            'transaction_status' => 'settlement',
        ]);

        $response->assertStatus(404)
            ->assertJson(['message' => 'Booking tidak ditemukan.']);
    }

    public function test_webhook_successfully_processes_settlement_and_updates_booking(): void
    {
        $user = User::factory()->create();
        $category = Categories::create([
            'name' => 'Hair Care',
            'slug' => 'hair-care-midtrans',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $treatment = Treatments::create([
            'category_id' => $category->id,
            'name' => 'Hair Coloring',
            'slug' => 'hair-coloring',
            'price' => 150000,
            'duration_minutes' => 90,
            'badge' => 'none',
            'is_active' => true,
        ]);

        $beautician = \App\Models\Beauticians::create([
            'name' => 'Beautician Midtrans',
            'bio' => 'Professional Beautician',
            'is_active' => true,
        ]);

        $booking = Bookings::create([
            'booking_code' => 'YB-TEST-001',
            'midtrans_order_id' => 'YB-TEST-001',
            'user_id' => $user->id,
            'treatment_id' => $treatment->id,
            'beautician_id' => $beautician->id,
            'booking_type' => 'salon',
            'booking_date' => now()->addDays(2)->format('Y-m-d'),
            'booking_time' => '10:00',
            'time_start' => '10:00',
            'time_end' => '11:30',
            'subtotal' => 150000,
            'total_amount' => 150000,
            'total_price' => 150000,
            'payment_method' => 'qris',
            'payment_type' => 'full',
            'payment_status' => 'pending',
            'status' => 'pending',
            'version' => 1,
        ]);

        $orderId = 'YB-TEST-001';
        $statusCode = '200';
        $grossAmount = '150000.00';
        $validSignature = hash('sha512', $orderId . $statusCode . $grossAmount . 'test-server-key-12345');

        $response = $this->postJson('/webhooks/midtrans', [
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $validSignature,
            'transaction_status' => 'settlement',
            'transaction_id' => 'trx-midtrans-123',
        ]);

        $response->assertStatus(200);

        $booking->refresh();
        $this->assertEquals('paid', $booking->payment_status);
        $this->assertEquals('confirmed', $booking->status instanceof \App\Enums\BookingStatus ? $booking->status->value : $booking->status);
        $this->assertEquals('trx-midtrans-123', $booking->midtrans_transaction_id);
    }
}
