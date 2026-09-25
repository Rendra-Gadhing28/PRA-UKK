<?php

namespace Tests\Feature\User;

use App\Models\Bookings;
use App\Models\Categories;
use App\Models\Treatments;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private function createTreatment(): Treatments
    {
        $category = Categories::create([
            'name' => 'Hair Care',
            'slug' => 'hair-care',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        return Treatments::create([
            'category_id' => $category->id,
            'name' => 'Creambath Spa',
            'slug' => 'creambath-spa',
            'price' => 100000,
            'duration_minutes' => 60,
            'badge' => 'none',
            'is_active' => true,
        ]);
    }

    public function test_user_cannot_access_another_users_booking_payment(): void
    {
        $owner = User::factory()->create();
        $attacker = User::factory()->create();
        $treatment = $this->createTreatment();
        $beautician = \App\Models\Beauticians::create([
            'name' => 'Siti Beautician',
            'bio' => 'Professional Stylist',
            'is_active' => true,
        ]);

        $booking = Bookings::create([
            'booking_code' => 'YB-AUTH-001',
            'user_id' => $owner->id,
            'treatment_id' => $treatment->id,
            'beautician_id' => $beautician->id,
            'booking_type' => 'salon',
            'booking_date' => now()->addDays(2)->format('Y-m-d'),
            'booking_time' => '10:00',
            'time_start' => '10:00',
            'time_end' => '11:00',
            'subtotal' => 100000,
            'total_amount' => 100000,
            'total_price' => 100000,
            'payment_method' => 'qris',
            'payment_type' => 'full',
            'payment_status' => 'pending',
            'status' => 'pending',
        ]);

        // Attacker attempts to view or poll payment status of owner's booking
        $response = $this->actingAs($attacker)->getJson(route('user.bookings.payment.status', $booking));

        // Must return 403 Forbidden, NOT 200 OK
        $response->assertStatus(403);
    }

    public function test_user_cannot_view_another_users_booking_details(): void
    {
        $owner = User::factory()->create();
        $attacker = User::factory()->create();
        $treatment = $this->createTreatment();
        $beautician = \App\Models\Beauticians::create([
            'name' => 'Siti Beautician 2',
            'bio' => 'Professional Stylist',
            'is_active' => true,
        ]);

        $booking = Bookings::create([
            'booking_code' => 'YB-AUTH-002',
            'user_id' => $owner->id,
            'treatment_id' => $treatment->id,
            'beautician_id' => $beautician->id,
            'booking_type' => 'salon',
            'booking_date' => now()->addDays(2)->format('Y-m-d'),
            'booking_time' => '10:00',
            'time_start' => '10:00',
            'time_end' => '11:00',
            'subtotal' => 100000,
            'total_amount' => 100000,
            'total_price' => 100000,
            'payment_method' => 'qris',
            'payment_type' => 'full',
            'payment_status' => 'paid',
            'status' => 'confirmed',
        ]);

        $response = $this->actingAs($attacker)->get(route('user.bookings.show', $booking));

        $response->assertStatus(403);
    }
}
