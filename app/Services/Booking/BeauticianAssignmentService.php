<?php

declare(strict_types=1);

namespace App\Services\Booking;

use App\Exceptions\NoBeauticianAvailableException;
use App\Models\Beauticians;
use App\Models\BeauticiansSchedules;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Auto-assign beautician untuk sebuah booking: cari beautician aktif yang
 * jadwal kerjanya (BeauticiansSchedules) mencakup jam yang diminta pada
 * hari tersebut, dan belum punya booking lain yang bentrok jamnya. Kalau
 * ada lebih dari satu kandidat, pilih yang total_bookings-nya paling
 * sedikit (load balancing sederhana).
 */
class BeauticianAssignmentService
{
    /**
     * Cari & kembalikan satu beautician yang available, atau lempar
     * exception kalau tidak ada sama sekali.
     *
     * @throws NoBeauticianAvailableException
     */
    public function findAvailable(Carbon $bookingDate, string $timeStart, string $timeEnd, ?int $excludeBookingId = null): Beauticians
    {
        $dayOfWeek = $bookingDate->dayOfWeek; // 0 = Minggu ... 6 = Sabtu

        $scheduledBeauticianIds = BeauticiansSchedules::query()
            ->where('day_of_week', $dayOfWeek)
            ->where('is_working', true)
            ->where('start_time', '<=', $timeStart)
            ->where('end_time', '>=', $timeEnd)
            ->pluck('beautician_id');

        if ($scheduledBeauticianIds->isEmpty()) {
            $hasDaySchedule = BeauticiansSchedules::query()->where('day_of_week', $dayOfWeek)->exists();
            if (! $hasDaySchedule) {
                $scheduledBeauticianIds = Beauticians::query()->where('is_active', true)->pluck('id');
            } else {
                throw new NoBeauticianAvailableException(
                    'Tidak ada beautician yang bertugas di jam tersebut. Silakan pilih jam lain.'
                );
            }
        }

        if ($scheduledBeauticianIds->isEmpty()) {
            throw new NoBeauticianAvailableException(
                'Tidak ada beautician aktif yang bertugas di jam tersebut. Silakan pilih jam lain.'
            );
        }

        // Beautician yang sudah punya booking lain (belum dibatalkan) yang
        // jamnya overlap dengan slot yang diminta, pada tanggal yang sama.
        $busyBeauticianIds = DB::table('bookings')
            ->whereIn('beautician_id', $scheduledBeauticianIds)
            ->whereDate('booking_date', $bookingDate->toDateString())
            ->whereNotIn('status', ['canceled', 'cancelled'])
            ->when($excludeBookingId, fn ($q) => $q->where('id', '!=', $excludeBookingId))
            ->where('time_start', '<', $timeEnd)
            ->where('time_end', '>', $timeStart)
            ->pluck('beautician_id');

        $availableBeauticianIds = $scheduledBeauticianIds->diff($busyBeauticianIds);

        if ($availableBeauticianIds->isEmpty()) {
            throw new NoBeauticianAvailableException(
                'Semua beautician yang bertugas di jam tersebut sedang dalam pengerjaan perawatan lain (terisi).'
            );
        }

        $beautician = Beauticians::query()
            ->whereIn('id', $availableBeauticianIds)
            ->where('is_active', true)
            ->orderBy('total_bookings') // load balancing
            ->first();

        if (! $beautician) {
            throw new NoBeauticianAvailableException(
                'Tidak ada beautician aktif yang tersedia di jam tersebut. Silakan pilih jam lain.'
            );
        }

        return $beautician;
    }

    /**
     * Mengambil daftar seluruh slot jam (08:00 - 20:00 per 30 menit) pada tanggal tertentu
     * lengkap dengan status ketersediaannya untuk durasi treatment yang ditentukan.
     *
     * @return array<int, array{time: string, formatted_time: string, available: bool, reason: string}>
     */
    public function getDailySlotsAvailability(Carbon $bookingDate, int $durationMinutes, int $intervalMinutes = 30): array
    {
        $slots = [];
        $start = Carbon::createFromFormat('Y-m-d H:i', $bookingDate->format('Y-m-d') . ' 08:00');
        $end = Carbon::createFromFormat('Y-m-d H:i', $bookingDate->format('Y-m-d') . ' 20:00');

        $current = $start->copy();
        while ($current->lte($end)) {
            $timeStartStr = $current->format('H:i');
            $timeEndCalc = $current->copy()->addMinutes($durationMinutes)->format('H:i');

            $isAvailable = false;
            $reason = '';

            try {
                $this->findAvailable($bookingDate, $timeStartStr, $timeEndCalc);
                $isAvailable = true;
            } catch (NoBeauticianAvailableException $e) {
                $isAvailable = false;
                $reason = $e->getMessage();
            }

            $slots[] = [
                'time' => $timeStartStr,
                'formatted_time' => $current->format('H:i') . ' WIB',
                'available' => $isAvailable,
                'reason' => $reason,
            ];

            $current->addMinutes($intervalMinutes);
        }

        return $slots;
    }
}
