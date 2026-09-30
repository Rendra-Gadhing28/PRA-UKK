<?php

namespace Tests\Feature\Admin;

use App\Models\Beauticians;
use App\Models\BeauticiansSchedules;
use App\Models\Treatments;
use App\Models\User;
use App\Services\Booking\BeauticianAssignmentService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminBeauticianScheduleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_admin_can_create_beautician_with_custom_working_days(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('admin.beauticians.store'), [
            'name' => 'Rina Weekend Specialist',
            'phone' => '08123456789',
            'email' => 'rina@example.com',
            'bio' => 'Spesialis perawatan weekend',
            'is_active' => '1',
            'schedules' => [
                0 => ['is_working' => '1', 'start_time' => '09:00', 'end_time' => '18:00'], // Minggu
                6 => ['is_working' => '1', 'start_time' => '09:00', 'end_time' => '18:00'], // Sabtu
                1 => ['is_working' => '0', 'start_time' => '09:00', 'end_time' => '18:00'], // Senin libur
            ],
        ]);

        $response->assertRedirect(route('admin.beauticians.index'));

        $beautician = Beauticians::where('email', 'rina@example.com')->first();
        $this->assertNotNull($beautician);

        // Check Sunday schedule is active
        $sundaySched = BeauticiansSchedules::where('beautician_id', $beautician->id)
            ->where('day_of_week', 0)
            ->first();
        $this->assertTrue((bool) $sundaySched->is_working);

        // Check Monday schedule is inactive
        $mondaySched = BeauticiansSchedules::where('beautician_id', $beautician->id)
            ->where('day_of_week', 1)
            ->first();
        $this->assertFalse((bool) $mondaySched->is_working);
    }

    public function test_sunday_is_closed_when_no_beauticians_work_on_sunday(): void
    {
        $beautician = Beauticians::create([
            'name' => 'Siti Weekday Only',
            'bio' => 'Weekday therapist',
            'is_active' => true,
        ]);

        // Monday (1) to Saturday (6) active, Sunday (0) inactive
        for ($day = 0; $day <= 6; $day++) {
            BeauticiansSchedules::create([
                'beautician_id' => $beautician->id,
                'day_of_week' => $day,
                'start_time' => '09:00:00',
                'end_time' => '18:00:00',
                'is_working' => $day !== 0,
            ]);
        }

        $service = app(BeauticianAssignmentService::class);
        $nextSunday = Carbon::now()->next(Carbon::SUNDAY);

        $this->expectException(\App\Exceptions\NoBeauticianAvailableException::class);
        $service->findAvailable($nextSunday, '10:00', '11:00');
    }

    public function test_sunday_opens_specifically_for_beautician_working_on_sunday(): void
    {
        $beauticianA = Beauticians::create([
            'name' => 'Siti Weekday',
            'bio' => 'Weekday therapist',
            'is_active' => true,
        ]);
        for ($day = 0; $day <= 6; $day++) {
            BeauticiansSchedules::create([
                'beautician_id' => $beauticianA->id,
                'day_of_week' => $day,
                'start_time' => '09:00:00',
                'end_time' => '18:00:00',
                'is_working' => $day !== 0, // Libur Minggu
            ]);
        }

        $beauticianB = Beauticians::create([
            'name' => 'Rina Weekend',
            'bio' => 'Weekend therapist',
            'is_active' => true,
        ]);
        for ($day = 0; $day <= 6; $day++) {
            BeauticiansSchedules::create([
                'beautician_id' => $beauticianB->id,
                'day_of_week' => $day,
                'start_time' => '09:00:00',
                'end_time' => '18:00:00',
                'is_working' => in_array($day, [0, 6], true), // Kerja Sabtu & Minggu
            ]);
        }

        $service = app(BeauticianAssignmentService::class);
        $nextSunday = Carbon::now()->next(Carbon::SUNDAY);

        // Should successfully assign Beautician B (Rina) on Sunday
        $assigned = $service->findAvailable($nextSunday, '10:00', '11:00');
        $this->assertEquals($beauticianB->id, $assigned->id);

        // Available beauticians list on Sunday should only contain Beautician B
        $availableList = $service->getAvailableBeauticians($nextSunday, '10:00', '11:00');
        $this->assertCount(1, $availableList);
        $this->assertEquals($beauticianB->id, $availableList->first()->id);
    }
}
