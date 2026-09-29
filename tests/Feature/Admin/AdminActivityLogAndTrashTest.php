<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\ActivityLog;
use App\Models\Beauticians;
use App\Models\Bookings;
use App\Models\Categories;
use App\Models\Treatments;
use App\Models\User;
use App\Models\Vouchers;
use App\Services\ActivityLogger;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class AdminActivityLogAndTrashTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $customer;

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
    }

    public function test_activity_logger_creates_record(): void
    {
        $log = ActivityLogger::log('create', 'Admin membuat data uji.', null, ['key' => 'value'], $this->admin);

        $this->assertNotNull($log);
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'create',
            'description' => 'Admin membuat data uji.',
            'user_id' => $this->admin->id,
            'is_archived' => false,
        ]);
    }

    public function test_admin_can_view_activity_logs(): void
    {
        ActivityLogger::log('login', 'User login.', null, [], $this->customer);

        $response = $this->actingAs($this->admin)->get(route('admin.activity-logs.index'));
        $response->assertStatus(200);
        $response->assertSee('Audit Log Aktivitas');
        $response->assertSee('User login.');
    }

    public function test_admin_can_archive_and_unarchive_activity_log(): void
    {
        $log = ActivityLogger::log('test', 'Log to archive.', null, [], $this->admin);

        $response = $this->actingAs($this->admin)->post(route('admin.activity-logs.archive', $log));
        $response->assertRedirect();
        $this->assertTrue($log->fresh()->is_archived);

        $response2 = $this->actingAs($this->admin)->post(route('admin.activity-logs.unarchive', $log));
        $response2->assertRedirect();
        $this->assertFalse($log->fresh()->is_archived);
    }

    public function test_bulk_archive_and_archive_expired_command(): void
    {
        $log1 = ActivityLogger::log('test', 'Log 1', null, [], $this->admin);
        $log2 = ActivityLogger::log('test', 'Log 2', null, [], $this->admin);

        $response = $this->actingAs($this->admin)->post(route('admin.activity-logs.bulk-archive'), [
            'ids' => [$log1->id, $log2->id],
        ]);
        $response->assertRedirect();
        $this->assertTrue($log1->fresh()->is_archived);
        $this->assertTrue($log2->fresh()->is_archived);

        // Command logs:archive-expired
        $exitCode = Artisan::call('logs:archive-expired');
        $this->assertSame(0, $exitCode);
    }

    public function test_soft_delete_and_trash_index(): void
    {
        $category = Categories::create(['name' => 'Facial Care', 'slug' => 'facial-care', 'sort_order' => 1, 'is_active' => true]);

        $treatment = Treatments::create([
            'category_id' => $category->id,
            'name' => 'Facial Glowing',
            'slug' => 'facial-glowing',
            'badge' => 'best_seller',
            'price' => 150000,
            'duration_minutes' => 60,
            'is_active' => true,
        ]);

        $treatment->delete();
        $this->assertSoftDeleted($treatment);

        $response = $this->actingAs($this->admin)->get(route('admin.trash.index', ['type' => 'treatments']));
        $response->assertStatus(200);
        $response->assertSee('Facial Glowing');
    }

    public function test_trash_restore_and_force_delete(): void
    {
        $beautician = Beauticians::create([
            'name' => 'Siti Beautician',
            'phone' => '081234567890',
            'bio' => 'Senior Stylist & Beautician',
            'is_active' => true,
        ]);

        $beautician->delete();
        $this->assertSoftDeleted($beautician);

        // Restore
        $response = $this->actingAs($this->admin)->post(route('admin.trash.restore', ['type' => 'beauticians', 'id' => $beautician->id]));
        $response->assertRedirect();
        $this->assertNull($beautician->fresh()->deleted_at);

        // Force delete
        $beautician->delete();
        $response2 = $this->actingAs($this->admin)->delete(route('admin.trash.force-delete', ['type' => 'beauticians', 'id' => $beautician->id]));
        $response2->assertRedirect();
        $this->assertDatabaseMissing('beauticians', ['id' => $beautician->id]);
    }

    public function test_relation_integrity_with_soft_deleted_models(): void
    {
        $category = Categories::create(['name' => 'Hair Care', 'slug' => 'hair-care', 'sort_order' => 1, 'is_active' => true]);

        $treatment = Treatments::create([
            'category_id' => $category->id,
            'name' => 'Hair Spa Deluxe',
            'slug' => 'hair-spa-deluxe',
            'badge' => 'new',
            'price' => 200000,
            'duration_minutes' => 60,
            'is_active' => true,
        ]);

        $beautician = Beauticians::create([
            'name' => 'Maya Beautician',
            'phone' => '081298765432',
            'bio' => 'Hair Stylist Expert',
            'is_active' => true,
        ]);

        $booking = Bookings::create([
            'booking_code' => 'BK-TEST-1234',
            'user_id' => $this->customer->id,
            'beautician_id' => $beautician->id,
            'booking_type' => 'salon',
            'status' => 'confirmed',
            'booking_date' => now()->toDateString(),
            'time_start' => '10:00',
            'time_end' => '11:00',
            'subtotal' => 200000,
            'total_amount' => 200000,
            'payment_method' => 'qris',
            'payment_status' => 'paid',
        ]);

        $booking->treatments()->attach($treatment->id, [
            'quantity' => 1,
            'price_per_unit' => 200000,
            'subtotal' => 200000,
        ]);

        // Soft delete the treatment, beautician, and customer
        $treatment->delete();
        $beautician->delete();
        $this->customer->delete();

        $freshBooking = Bookings::with(['user', 'beautician', 'treatments'])->find($booking->id);

        $this->assertNotNull($freshBooking->user);
        $this->assertNotNull($freshBooking->beautician);
        $this->assertCount(1, $freshBooking->treatments);
        $this->assertSame('Hair Spa Deluxe', $freshBooking->treatments->first()->name);
    }

    public function test_simulate_skip_30_days_makes_logs_active_and_expired(): void
    {
        $log = ActivityLogger::log('test_action', 'Test log for simulate skip.', null, [], $this->admin);
        $log->archive();
        $this->assertTrue($log->fresh()->is_archived);

        $response = $this->actingAs($this->admin)->post(route('admin.activity-logs.simulate-skip-30d'));
        $response->assertRedirect();

        $freshLog = $log->fresh();
        $this->assertFalse($freshLog->is_archived);
        $this->assertNull($freshLog->archived_at);
        $this->assertTrue($freshLog->created_at->lt(now()->subDays(30)));
    }
}
