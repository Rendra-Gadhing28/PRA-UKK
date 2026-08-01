<?php

namespace App\Services;

use App\Models\Beautician;
use App\Models\BeauticiansSchedules;
use App\Models\Bookings;
use App\Models\Treatments;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingService
{
    protected int $lockTtlSeconds = 10;

    /**
     * Hitung waktu selesai booking = waktu mulai + total durasi semua treatment.
     *
     * @param  Carbon  $startDateTime  Tanggal + jam mulai booking
     * @param  array<int>  $treatmentIds
     * @return array{end: Carbon, total_duration: int, treatments: \Illuminate\Support\Collection}
     */
    public function calculateEndTime(Carbon $startDateTime, array $treatmentIds): array
    {
        $treatments = Treatments::whereIn('id', $treatmentIds)->get();

        if ($treatments->count() !== count($treatmentIds)) {
            throw ValidationException::withMessages([
                'treatments' => 'Salah satu treatment yang dipilih tidak ditemukan atau sudah tidak aktif.',
            ]);
        }

        $totalDuration = $treatments->sum('duration_minutes'); // dalam menit

        $end = $startDateTime->copy()->addMinutes($totalDuration);

        return [
            'end' => $end,
            'total_duration' => $totalDuration,
            'treatments' => $treatments,
        ];
    }

    /**
     * Cek apakah beautician tersedia pada rentang waktu tertentu.
     * Mengecek 2 hal: jadwal kerja beautician & bentrok dengan booking lain.
     */
    public function isSlotAvailable(int $beauticianId, string $date, string $startTime, string $endTime, ?int $excludeBookingId = null): bool
    {
        return $this->isWithinWorkingHours($beauticianId, $date, $startTime, $endTime)
            && ! $this->hasOverlappingBooking($beauticianId, $date, $startTime, $endTime, $excludeBookingId);
    }

    /**
     * Cek jadwal kerja beautician (hari & jam) mencakup rentang booking yang diminta.
     */
    protected function isWithinWorkingHours(int $beauticianId, string $date, string $startTime, string $endTime): bool
    {
        $dayOfWeek = Carbon::parse($date)->dayOfWeek; // 0 (Minggu) - 6 (Sabtu)

        $schedule = BeauticiansSchedules::where('beautician_id', $beauticianId)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_working', true)
            ->first();

        if (! $schedule) {
            return false; // beautician libur di hari itu
        }

        return $startTime >= $schedule->start_time && $endTime <= $schedule->end_time;
    }

    /**
     * Cek apakah ada booking lain milik beautician yang sama, di tanggal yang sama,
     * dan rentang waktunya bentrok (overlap) dengan rentang yang diminta.
     *
     * Formula overlap standar: existing.start < new.end AND existing.end > new.start
     * Status 'canceled' diabaikan karena slotnya sudah kembali kosong.
     */
    protected function hasOverlappingBooking(int $beauticianId, string $date, string $startTime, string $endTime, ?int $excludeBookingId = null): bool
    {
        return Bookings::where('beautician_id', $beauticianId)
            ->where('booking_date', $date)
            ->where('status', '!=', 'canceled')
            ->when($excludeBookingId, fn ($q) => $q->where('id', '!=', $excludeBookingId))
            ->where('time_start', '<', $endTime)
            ->where('time_end', '>', $startTime)
            ->exists();
    }

    /**
     * Buat booking baru dengan proteksi race condition 3 lapis:
     *   1. Cache lock (mencegah 2 request bersamaan proses booking beautician+tanggal yang sama)
     *   2. DB transaction + lockForUpdate (row lock di level database)
     *   3. Re-check overlap di dalam transaction sebelum insert (defense terakhir)
     *
     * @param  array<int, array{treatment_id:int, quantity:int}>  $items
     */
    public function createBooking(
        int $userId,
        int $beauticianId,
        string $bookingType,
        Carbon $startDateTime,
        array $items,
        array $extra = []
    ): Bookings {
        $treatmentIds = collect($items)->pluck('treatment_id')->all();
        $calc = $this->calculateEndTime($startDateTime, $treatmentIds);

        $date = $startDateTime->toDateString();
        $startTime = $startDateTime->toTimeString();
        $endTime = $calc['end']->toTimeString();

        // Lock key unik per beautician + tanggal, supaya request lain untuk
        // beautician/tanggal yang sama harus antre, tapi beautician lain tidak terpengaruh.
        $lockKey = "booking-lock:beautician:{$beauticianId}:{$date}";

        // === LAYER 1: Cache Lock ===
        $lock = Cache::lock($lockKey, $this->lockTtlSeconds);

        if (! $lock->get()) {
            throw ValidationException::withMessages([
                'slot' => 'Sedang ada proses booking lain untuk beautician ini di tanggal yang sama. Silakan coba lagi.',
            ]);
        }

        try {
            return DB::transaction(function () use (
                $userId, $beauticianId, $bookingType, $date, $startTime, $endTime, $calc, $items, $extra
            ) {
                // === LAYER 2: DB row lock ===
                // Kunci baris booking existing milik beautician ini di tanggal ini,
                // supaya transaction lain yang paralel harus menunggu sampai transaction ini selesai.
                Bookings::where('beautician_id', $beauticianId)
                    ->where('booking_date', $date)
                    ->lockForUpdate()
                    ->get();

                // === LAYER 3: Re-check overlap di dalam transaction (defense terakhir) ===
                if ($this->hasOverlappingBooking($beauticianId, $date, $startTime, $endTime)) {
                    throw ValidationException::withMessages([
                        'slot' => 'Maaf, slot ini baru saja dibooking orang lain. Silakan pilih jadwal lain.',
                    ]);
                }

                if (! $this->isWithinWorkingHours($beauticianId, $date, $startTime, $endTime)) {
                    throw ValidationException::withMessages([
                        'slot' => 'Beautician tidak bekerja pada jam tersebut.',
                    ]);
                }

                $subtotal = $calc['treatments']->sum(function ($treatment) use ($items) {
                    $qty = collect($items)->firstWhere('treatment_id', $treatment->id)['quantity'] ?? 1;
                    return $treatment->price * $qty;
                });

                $transportFee = $extra['transport_fee'] ?? 0;
                $discountAmount = $extra['discount_amount'] ?? 0;

                $booking = Bookings::create([
                    'booking_code' => $this->generateBookingCode(),
                    'user_id' => $userId,
                    'beautician_id' => $beauticianId,
                    'booking_type' => $bookingType,
                    'status' => 'pending',
                    'booking_date' => $date,
                    'time_start' => $startTime,
                    'time_end' => $endTime,
                    'subtotal' => $subtotal,
                    'discount_amount' => $discountAmount,
                    'transport_fee' => $transportFee,
                    'total_amount' => $subtotal + $transportFee - $discountAmount,
                    'payment_status' => 'unpaid',
                    'notes' => $extra['notes'] ?? null,
                    'version' => 1,
                ]);

                foreach ($calc['treatments'] as $treatment) {
                    $qty = collect($items)->firstWhere('treatment_id', $treatment->id)['quantity'] ?? 1;

                    $booking->bookingTreatments()->create([
                        'treatment_id' => $treatment->id,
                        'quantity' => $qty,
                        'price_per_unit' => $treatment->price,
                        'subtotal' => $treatment->price * $qty,
                    ]);
                }

                return $booking->fresh('bookingTreatments.treatment');
            });
        } finally {
            $lock->release();
        }
    }

    protected function generateBookingCode(): string
    {
        return 'YB-'.now()->format('Ymd').'-'.strtoupper(substr(uniqid(), -5));
    }
}