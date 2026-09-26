<?php

namespace Tests\Feature\User;

use App\Models\Beauticians;
use App\Models\BeauticiansSchedules;
use App\Models\Bookings;
use App\Models\Categories;
use App\Models\Treatments;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingConcurrencyTest extends TestCase
{
    use RefreshDatabase;

    private function setupSalonData(): array
    {
        $category = Categories::create([
            'name' => 'Hair Care',
            'slug' => 'hair-care',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $treatment = Treatments::create([
            'category_id' => $category->id,
            'name' => 'Hair Spa Luxury',
            'slug' => 'hair-spa-luxury',
            'price' => 150000,
            'duration_minutes' => 60,
            'badge' => 'none',
            'is_active' => true,
        ]);

        $beautician = Beauticians::create([
            'name' => 'Siti Terapis',
            'bio' => 'Senior Hair Expert',
            'is_active' => true,
            'total_bookings' => 0,
        ]);

        // Jadwal kerja Senin-Minggu (0-6) dari 09:00 sampai 18:00
        for ($i = 0; $i <= 6; $i++) {
            BeauticiansSchedules::create([
                'beautician_id' => $beautician->id,
                'day_of_week' => $i,
                'start_time' => '09:00',
                'end_time' => '18:00',
                'is_working' => true,
            ]);
        }

        return [$treatment, $beautician];
    }

    public function test_first_booking_succeeds_and_second_fails_when_beautician_slot_taken(): void
    {
        [$treatment, $beautician] = $this->setupSalonData();
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $bookingDate = now()->addDays(2);
        $timeStart = '14:00';

        // User A submits booking at 14:00
        $responseA = $this->actingAs($userA)->post(route('user.bookings.store'), [
            'booking_type' => 'salon',
            'treatments' => [
                ['treatment_id' => $treatment->id, 'quantity' => 1],
            ],
            'booking_date' => $bookingDate->format('Y-m-d'),
            'time_start' => $timeStart,
            'payment_type' => 'cashless',
        ]);

        $responseA->assertSessionHasNoErrors();
        $responseA->assertRedirect();

        // User B submits booking at the same slot (14:00) with only 1 beautician available
        $responseB = $this->actingAs($userB)->post(route('user.bookings.store'), [
            'booking_type' => 'salon',
            'treatments' => [
                ['treatment_id' => $treatment->id, 'quantity' => 1],
            ],
            'booking_date' => $bookingDate->format('Y-m-d'),
            'time_start' => $timeStart,
            'payment_type' => 'cashless',
        ]);

        // User B must get validation error on time_start
        $responseB->assertSessionHasErrors(['time_start']);
    }

    public function test_second_booking_allocates_next_beautician_when_multiple_available(): void
    {
        [$treatment, $beautician1] = $this->setupSalonData();

        $beautician2 = Beauticians::create([
            'name' => 'Dewi Terapis',
            'bio' => 'Facial & Hair Expert',
            'is_active' => true,
            'total_bookings' => 0,
        ]);

        for ($i = 0; $i <= 6; $i++) {
            BeauticiansSchedules::create([
                'beautician_id' => $beautician2->id,
                'day_of_week' => $i,
                'start_time' => '09:00',
                'end_time' => '18:00',
                'is_working' => true,
            ]);
        }

        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $bookingDate = now()->addDays(2);
        $timeStart = '14:00';

        // User A books 14:00
        $responseA = $this->actingAs($userA)->post(route('user.bookings.store'), [
            'booking_type' => 'salon',
            'treatments' => [
                ['treatment_id' => $treatment->id, 'quantity' => 1],
            ],
            'booking_date' => $bookingDate->format('Y-m-d'),
            'time_start' => $timeStart,
            'payment_type' => 'cashless',
        ]);
        $responseA->assertSessionHasNoErrors();

        // User B also books 14:00 -> should succeed by assigning Beautician 2
        $responseB = $this->actingAs($userB)->post(route('user.bookings.store'), [
            'booking_type' => 'salon',
            'treatments' => [
                ['treatment_id' => $treatment->id, 'quantity' => 1],
            ],
            'booking_date' => $bookingDate->format('Y-m-d'),
            'time_start' => $timeStart,
            'payment_type' => 'cashless',
        ]);
        $responseB->assertSessionHasNoErrors();

        // Check that 2 bookings exist with different beauticians
        $this->assertDatabaseCount('bookings', 2);
        $assignedBeauticianIds = Bookings::pluck('beautician_id')->all();
        $this->assertContains($beautician1->id, $assignedBeauticianIds);
        $this->assertContains($beautician2->id, $assignedBeauticianIds);
    }

    public function test_expired_pending_booking_does_not_block_new_booking(): void
    {
        [$treatment, $beautician] = $this->setupSalonData();
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $bookingDate = now()->addDays(2);
        $timeStart = '14:00';

        // Create expired pending booking for User A
        Bookings::create([
            'booking_code' => 'YB-EXP-001',
            'user_id' => $userA->id,
            'beautician_id' => $beautician->id,
            'booking_type' => 'salon',
            'booking_date' => $bookingDate->toDateString(),
            'time_start' => '14:00',
            'time_end' => '15:00',
            'subtotal' => 150000,
            'total_amount' => 150000,
            'payment_method' => 'qris',
            'payment_type' => 'cashless',
            'payment_status' => 'unpaid',
            'status' => 'pending',
            'payment_expires_at' => now()->subMinutes(5), // Already expired
            'version' => 1,
        ]);

        // User B should be able to book 14:00 because previous pending booking is expired
        $responseB = $this->actingAs($userB)->post(route('user.bookings.store'), [
            'booking_type' => 'salon',
            'treatments' => [
                ['treatment_id' => $treatment->id, 'quantity' => 1],
            ],
            'booking_date' => $bookingDate->format('Y-m-d'),
            'time_start' => $timeStart,
            'payment_type' => 'cashless',
        ]);

        $responseB->assertSessionHasNoErrors();
    }

    public function test_booking_rejected_when_outside_operational_hours(): void
    {
        [$treatment, $beautician] = $this->setupSalonData();
        $user = User::factory()->create();
        $bookingDate = now()->addDays(2);

        // Before 09:00 (e.g. 08:30)
        $response = $this->actingAs($user)->post(route('user.bookings.store'), [
            'booking_type' => 'salon',
            'treatments' => [
                ['treatment_id' => $treatment->id, 'quantity' => 1],
            ],
            'booking_date' => $bookingDate->format('Y-m-d'),
            'time_start' => '08:30',
            'payment_type' => 'cashless',
        ]);
        $response->assertSessionHasErrors(['time_start']);

        // After 18:00 (e.g. 18:30)
        $responseAfter = $this->actingAs($user)->post(route('user.bookings.store'), [
            'booking_type' => 'salon',
            'treatments' => [
                ['treatment_id' => $treatment->id, 'quantity' => 1],
            ],
            'booking_date' => $bookingDate->format('Y-m-d'),
            'time_start' => '18:30',
            'payment_type' => 'cashless',
        ]);
        $responseAfter->assertSessionHasErrors(['time_start']);
    }

    public function test_past_time_slot_today_is_locked_and_rejected(): void
    {
        [$treatment, $beautician] = $this->setupSalonData();
        $user = User::factory()->create();

        // Fix current time to 14:30 today
        Carbon::setTestNow(Carbon::today()->setTime(14, 30));

        // Daily slots API check: 09:00 - 14:00 should be unavailable (past)
        $responseSlots = $this->actingAs($user)->getJson(route('user.bookings.daily-slots', [
            'booking_date' => Carbon::today()->toDateString(),
            'duration_minutes' => 60,
        ]));

        $responseSlots->assertOk();
        $slots = collect($responseSlots->json('slots'));

        // Slot 09:00 should be unavailable because it is in the past
        $slot09 = $slots->firstWhere('time', '09:00');
        $this->assertNotNull($slot09);
        $this->assertFalse($slot09['available']);
        $this->assertStringContainsString('terlewat', $slot09['reason']);

        // Slot 15:00 should be available
        $slot15 = $slots->firstWhere('time', '15:00');
        $this->assertNotNull($slot15);
        $this->assertTrue($slot15['available']);

        // Attempting to store a past slot (e.g. 10:00 today) must fail validation
        $responseSubmit = $this->actingAs($user)->post(route('user.bookings.store'), [
            'booking_type' => 'salon',
            'treatments' => [
                ['treatment_id' => $treatment->id, 'quantity' => 1],
            ],
            'booking_date' => Carbon::today()->toDateString(),
            'time_start' => '10:00',
            'payment_type' => 'cashless',
        ]);
        $responseSubmit->assertSessionHasErrors(['time_start']);

        // Reset test clock
        Carbon::setTestNow();
    }

    public function test_slot_17_30_availability_depends_on_treatment_duration(): void
    {
        [$treatment60, $beautician] = $this->setupSalonData(); // 60 minutes
        $category = Categories::first();
        $treatment30 = Treatments::create([
            'category_id' => $category->id,
            'name' => 'Express Nail Care',
            'slug' => 'express-nail-care',
            'price' => 50000,
            'duration_minutes' => 30,
            'badge' => 'none',
            'is_active' => true,
        ]);

        $user = User::factory()->create();
        $tomorrow = Carbon::tomorrow()->toDateString();

        // 1. Treatment 30 min: Slot 17:30 must be AVAILABLE (ends at 18:00)
        $res30 = $this->actingAs($user)->getJson(route('user.bookings.daily-slots', [
            'booking_date' => $tomorrow,
            'duration_minutes' => 30,
        ]));
        $res30->assertOk();
        $slots30 = collect($res30->json('slots'));
        $slot1730_30 = $slots30->firstWhere('time', '17:30');
        $this->assertNotNull($slot1730_30);
        $this->assertTrue($slot1730_30['available']);

        // 2. Treatment 60 min: Slot 17:30 must be UNAVAILABLE (ends at 18:30 > 18:00)
        $res60 = $this->actingAs($user)->getJson(route('user.bookings.daily-slots', [
            'booking_date' => $tomorrow,
            'duration_minutes' => 60,
        ]));
        $res60->assertOk();
        $slots60 = collect($res60->json('slots'));
        $slot1730_60 = $slots60->firstWhere('time', '17:30');
        $this->assertNotNull($slot1730_60);
        $this->assertFalse($slot1730_60['available']);
        $this->assertStringContainsString('melewati jam tutup', $slot1730_60['reason']);

        // 3. Ensure no slot starts at 18:00
        $this->assertNull($slots30->firstWhere('time', '18:00'));
    }
}
