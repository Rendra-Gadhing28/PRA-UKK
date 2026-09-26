<?php

declare(strict_types=1);

namespace App\Services\Booking;

use App\Exceptions\NoBeauticianAvailableException;
use App\Models\Beauticians;
use App\Models\BeauticiansSchedules;
use Carbon\Carbon;
use Illuminate\Support\Collection;
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
     * Helper privat untuk mengambil ID beautician aktif yang bertugas di jam tersebut.
     *
     * @return Collection<int, int>
     */
    private function getCandidateBeauticianIds(Carbon $bookingDate, string $timeStart, string $timeEnd): Collection
    {
        $dayOfWeek = $bookingDate->dayOfWeek; // 0 = Minggu ... 6 = Sabtu

        $activeBeauticianIds = Beauticians::query()
            ->where('is_active', true)
            ->pluck('id');

        if ($activeBeauticianIds->isEmpty()) {
            return collect();
        }

        // Beautician yang secara spesifik punya jadwal libur / diluar jam kerja di hari tersebut
        $offBeauticianIds = BeauticiansSchedules::query()
            ->whereIn('beautician_id', $activeBeauticianIds)
            ->where('day_of_week', $dayOfWeek)
            ->where(function ($q) use ($timeStart, $timeEnd) {
                $q->where('is_working', false)
                    ->orWhere('start_time', '>', $timeStart)
                    ->orWhere('end_time', '<', $timeEnd);
            })
            ->pluck('beautician_id');

        return $activeBeauticianIds->diff($offBeauticianIds);
    }

    /**
     * Cari & kembalikan satu beautician yang available, atau lempar
     * exception kalau tidak ada sama sekali.
     * Mendukung pesimistic locking (FOR UPDATE) untuk pencegahan race condition.
     *
     * @throws NoBeauticianAvailableException
     */
    public function findAvailable(
        Carbon $bookingDate,
        string $timeStart,
        string $timeEnd,
        ?int $excludeBookingId = null,
        bool $lock = false
    ): Beauticians {
        $candidateBeauticianIds = $this->getCandidateBeauticianIds($bookingDate, $timeStart, $timeEnd);

        if ($candidateBeauticianIds->isEmpty()) {
            throw new NoBeauticianAvailableException(
                'Tidak ada beautician aktif yang bertugas di jam tersebut. Silakan pilih jam lain.'
            );
        }

        if ($lock) {
            // Urutkan ID secara konsisten untuk mencegah DB deadlock saat multi-row locking
            Beauticians::query()
                ->whereIn('id', $candidateBeauticianIds)
                ->orderBy('id')
                ->lockForUpdate()
                ->get();
        }

        $busyBeauticianIds = DB::table('bookings')
            ->whereIn('beautician_id', $candidateBeauticianIds)
            ->whereDate('booking_date', $bookingDate->toDateString())
            ->whereNotIn('status', ['canceled', 'cancelled'])
            ->where(function ($query) {
                $query->where('status', '!=', 'pending')
                    ->orWhereNull('payment_expires_at')
                    ->orWhere('payment_expires_at', '>', now());
            })
            ->when($excludeBookingId, fn ($q) => $q->where('id', '!=', $excludeBookingId))
            ->where('time_start', '<', $timeEnd)
            ->where('time_end', '>', $timeStart)
            ->pluck('beautician_id');

        $availableBeauticianIds = $candidateBeauticianIds->diff($busyBeauticianIds);

        if ($availableBeauticianIds->isEmpty()) {
            throw new NoBeauticianAvailableException(
                "Maaf, slot jam {$timeStart} WIB baru saja diambil oleh pelanggan lain beberapa detik yang lalu. Silakan pilih jam lainnya."
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
     * Cari beautician spesifik dan validasi ketersediaannya dengan opsi locking.
     *
     * @throws NoBeauticianAvailableException
     */
    public function findSpecificAvailable(
        int $beauticianId,
        Carbon $bookingDate,
        string $timeStart,
        string $timeEnd,
        ?int $excludeBookingId = null,
        bool $lock = false
    ): Beauticians {
        $candidateBeauticianIds = $this->getCandidateBeauticianIds($bookingDate, $timeStart, $timeEnd);

        if (! $candidateBeauticianIds->contains($beauticianId)) {
            throw new NoBeauticianAvailableException(
                'Beautician yang dipilih tidak bertugas pada jadwal tersebut. Silakan pilih beautician atau jam lain.'
            );
        }

        if ($lock) {
            Beauticians::query()
                ->where('id', $beauticianId)
                ->lockForUpdate()
                ->first();
        }

        $isBusy = DB::table('bookings')
            ->where('beautician_id', $beauticianId)
            ->whereDate('booking_date', $bookingDate->toDateString())
            ->whereNotIn('status', ['canceled', 'cancelled'])
            ->where(function ($query) {
                $query->where('status', '!=', 'pending')
                    ->orWhereNull('payment_expires_at')
                    ->orWhere('payment_expires_at', '>', now());
            })
            ->when($excludeBookingId, fn ($q) => $q->where('id', '!=', $excludeBookingId))
            ->where('time_start', '<', $timeEnd)
            ->where('time_end', '>', $timeStart)
            ->exists();

        if ($isBusy) {
            throw new NoBeauticianAvailableException(
                "Maaf, beautician yang dipilih pada jam {$timeStart} WIB baru saja diambil oleh pelanggan lain. Silakan pilih jam atau terapis lainnya."
            );
        }

        $beautician = Beauticians::query()
            ->where('id', $beauticianId)
            ->where('is_active', true)
            ->first();

        if (! $beautician) {
            throw new NoBeauticianAvailableException(
                'Beautician yang dipilih sedang tidak aktif. Silakan pilih beautician lain.'
            );
        }

        return $beautician;
    }

    /**
     * Mengambil daftar seluruh slot jam (09:00 - 17:30 per 30 menit) pada tanggal tertentu
     * lengkap dengan status ketersediaannya untuk durasi treatment yang ditentukan.
     * Otomatis mengunci slot jam yang sudah lewat jika memilih hari ini,
     * serta mengunci slot jika total durasi treatment melebihi jam operasional salon (18:00 WIB).
     *
     * @return array<int, array{time: string, formatted_time: string, available: bool, reason: string}>
     */
    public function getDailySlotsAvailability(Carbon $bookingDate, int $durationMinutes, int $intervalMinutes = 30): array
    {
        $slots = [];
        $start = Carbon::createFromFormat('Y-m-d H:i', $bookingDate->format('Y-m-d').' 09:00');
        $end = Carbon::createFromFormat('Y-m-d H:i', $bookingDate->format('Y-m-d').' 17:30');

        $isToday = $bookingDate->isToday();
        $now = Carbon::now();

        $current = $start->copy();
        while ($current->lte($end)) {
            $timeStartStr = $current->format('H:i');
            $timeEndCalc = $current->copy()->addMinutes($durationMinutes)->format('H:i');

            $isAvailable = false;
            $reason = '';

            if ($isToday && $current->lte($now)) {
                $isAvailable = false;
                $reason = 'Waktu slot sudah terlewat.';
            } elseif ($timeEndCalc > '18:00' || $timeEndCalc < $timeStartStr) {
                $isAvailable = false;
                $reason = 'Durasi perawatan melewati jam tutup salon (18:00 WIB).';
            } else {
                try {
                    $this->findAvailable($bookingDate, $timeStartStr, $timeEndCalc);
                    $isAvailable = true;
                } catch (NoBeauticianAvailableException $e) {
                    $isAvailable = false;
                    $reason = $e->getMessage();
                }
            }

            $slots[] = [
                'time' => $timeStartStr,
                'formatted_time' => $current->format('H:i').' WIB',
                'available' => $isAvailable,
                'reason' => $reason,
            ];

            $current->addMinutes($intervalMinutes);
        }

        return $slots;
    }

    /**
     * Mengembalikan koleksi Beauticians yang tersedia (aktif, bertugas, dan belum terisi booking)
     * pada tanggal dan rentang jam tertentu.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, Beauticians>
     */
    public function getAvailableBeauticians(Carbon $bookingDate, string $timeStart, string $timeEnd, ?int $excludeBookingId = null): \Illuminate\Database\Eloquent\Collection
    {
        $candidateBeauticianIds = $this->getCandidateBeauticianIds($bookingDate, $timeStart, $timeEnd);

        if ($candidateBeauticianIds->isEmpty()) {
            return Beauticians::query()->whereRaw('1 = 0')->get();
        }

        $busyBeauticianIds = DB::table('bookings')
            ->whereIn('beautician_id', $candidateBeauticianIds)
            ->whereDate('booking_date', $bookingDate->toDateString())
            ->whereNotIn('status', ['canceled', 'cancelled'])
            ->where(function ($query) {
                $query->where('status', '!=', 'pending')
                    ->orWhereNull('payment_expires_at')
                    ->orWhere('payment_expires_at', '>', now());
            })
            ->when($excludeBookingId, fn ($q) => $q->where('id', '!=', $excludeBookingId))
            ->where('time_start', '<', $timeEnd)
            ->where('time_end', '>', $timeStart)
            ->pluck('beautician_id');

        $availableBeauticianIds = $candidateBeauticianIds->diff($busyBeauticianIds);

        if ($availableBeauticianIds->isEmpty()) {
            return Beauticians::query()->whereRaw('1 = 0')->get();
        }

        return Beauticians::query()
            ->whereIn('id', $availableBeauticianIds)
            ->where('is_active', true)
            ->withCount('bookings')
            ->orderBy('name')
            ->get();
    }
}
