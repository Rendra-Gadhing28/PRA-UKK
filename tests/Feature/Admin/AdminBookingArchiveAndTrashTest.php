<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\Beauticians;
use App\Models\Bookings;
use App\Models\Categories;
use App\Models\Treatments;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminBookingArchiveAndTrashTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $customer;
    private Beauticians $beautician;
    private Categories $category;
    private Treatments $treatment;

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
            'name' => 'Maya Beautician',
            'phone' => '081298765432',
            'bio' => 'Hair Stylist Expert',
            'is_active' => true,
        ]);

        $this->category = Categories::create([
            'name' => 'Hair Care',
            'slug' => 'hair-care',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $this->treatment = Treatments::create([
            'category_id' => $this->category->id,
            'name' => 'Hair Spa Deluxe',
            'slug' => 'hair-spa-deluxe',
            'badge' => 'new',
            'price' => 200000,
            'duration_minutes' => 60,
            'is_active' => true,
        ]);
    }

    private function createBooking(string $status = 'completed', bool $isArchived = false): Bookings
    {
        $booking = Bookings::create([
            'booking_code' => 'BK-' . uniqid(),
            'user_id' => $this->customer->id,
            'beautician_id' => $this->beautician->id,
            'booking_type' => 'salon',
            'status' => $status,
            'booking_date' => now()->toDateString(),
            'time_start' => '10:00',
            'time_end' => '11:00',
            'subtotal' => 200000,
            'total_amount' => 200000,
            'payment_method' => 'cash',
            'payment_status' => 'paid',
            'is_archived' => $isArchived,
            'archived_at' => $isArchived ? now() : null,
        ]);

        $booking->treatments()->attach($this->treatment->id, [
            'quantity' => 1,
            'price_per_unit' => 200000,
            'subtotal' => 200000,
        ]);

        return $booking;
    }

    public function test_admin_can_view_active_and_archived_booking_tabs(): void
    {
        $activeBooking = $this->createBooking('confirmed', false);
        $archivedBooking = $this->createBooking('completed', false); // status 'completed' will auto-archive

        $this->assertTrue($archivedBooking->fresh()->is_archived);
        $this->assertFalse($activeBooking->fresh()->is_archived);

        // Tab Active (Default)
        $response = $this->actingAs($this->admin)->get(route('admin.bookings.index', ['tab' => 'active']));
        $response->assertStatus(200);
        $response->assertSee($activeBooking->booking_code);
        $response->assertDontSee($archivedBooking->booking_code);

        // Tab Archived
        $responseArchived = $this->actingAs($this->admin)->get(route('admin.bookings.index', ['tab' => 'archived']));
        $responseArchived->assertStatus(200);
        $responseArchived->assertSee($archivedBooking->booking_code);
        $responseArchived->assertDontSee($activeBooking->booking_code);
    }

    public function test_booking_is_automatically_archived_when_status_becomes_completed(): void
    {
        $booking = $this->createBooking('confirmed', false);
        $this->assertFalse($booking->is_archived);
        $this->assertNull($booking->archived_at);

        // Update status to completed via admin controller
        $response = $this->actingAs($this->admin)->patch(route('admin.bookings.update-status', $booking), [
            'status' => 'completed',
        ]);
        $response->assertRedirect();

        $fresh = $booking->fresh();
        $this->assertEquals('completed', is_object($fresh->status) ? $fresh->status->value : $fresh->status);
        $this->assertTrue($fresh->is_archived);
        $this->assertNotNull($fresh->archived_at);

        // Flush flash message session from redirect
        $this->flushSession();

        // Verify index active tab doesn't show it and archived tab shows it
        $this->actingAs($this->admin)->get(route('admin.bookings.index', ['tab' => 'active']))
            ->assertDontSee($booking->booking_code);

        $this->actingAs($this->admin)->get(route('admin.bookings.index', ['tab' => 'archived']))
            ->assertSee($booking->booking_code);
    }

    public function test_admin_can_soft_delete_restore_and_force_delete_booking(): void
    {
        $booking = $this->createBooking('completed', false);

        // Soft Delete
        $response = $this->actingAs($this->admin)->delete(route('admin.bookings.destroy', $booking));
        $response->assertRedirect(route('admin.bookings.index'));
        $this->assertSoftDeleted($booking);

        // Appears in Trash
        $responseTrash = $this->actingAs($this->admin)->get(route('admin.trash.index', ['type' => 'bookings']));
        $responseTrash->assertStatus(200);
        $responseTrash->assertSee($booking->booking_code);

        // Restore from Trash
        $responseRestore = $this->actingAs($this->admin)->post(route('admin.trash.restore', [
            'type' => 'bookings',
            'id' => $booking->id,
        ]));
        $responseRestore->assertRedirect();
        $this->assertNull($booking->fresh()->deleted_at);

        // Force Delete
        $booking->delete();
        $responseForce = $this->actingAs($this->admin)->delete(route('admin.trash.force-delete', [
            'type' => 'bookings',
            'id' => $booking->id,
        ]));
        $responseForce->assertRedirect();
        $this->assertDatabaseMissing('bookings', ['id' => $booking->id]);
    }

    public function test_admin_can_soft_delete_treatment_and_restore(): void
    {
        $treatment = Treatments::create([
            'category_id' => $this->category->id,
            'name' => 'Facial Soft Delete Test',
            'slug' => 'facial-soft-delete-test',
            'badge' => 'none',
            'price' => 120000,
            'duration_minutes' => 45,
            'is_active' => true,
        ]);

        // Soft Delete
        $response = $this->actingAs($this->admin)->delete(route('admin.treatments.destroy', $treatment));
        $response->assertRedirect(route('admin.treatments.index'));
        $this->assertSoftDeleted($treatment);

        // Appears in Trash
        $responseTrash = $this->actingAs($this->admin)->get(route('admin.trash.index', ['type' => 'treatments']));
        $responseTrash->assertStatus(200);
        $responseTrash->assertSee('Facial Soft Delete Test');

        // Restore
        $responseRestore = $this->actingAs($this->admin)->post(route('admin.trash.restore', [
            'type' => 'treatments',
            'id' => $treatment->id,
        ]));
        $responseRestore->assertRedirect();
        $this->assertNull($treatment->fresh()->deleted_at);
    }
}
