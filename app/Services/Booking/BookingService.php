<?php

declare(strict_types=1);

namespace App\Services\Booking;

use App\Exceptions\OutOfServiceAreaException;
use App\Models\Bookings;
use App\Models\BookingTreatments;
use App\Models\Treatments;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Orkestrasi pembuatan booking: snapshot harga treatment, hitung ongkir
 * (untuk Home Service), auto-assign beautician, dan simpan booking beserta
 * baris booking_treatments dalam satu transaksi DB.
 */
class BookingService
{
    public function __construct(
        private readonly DistanceCalculatorService $distanceCalculator,
        private readonly GoogleMapsService $googleMaps,
        private readonly BeauticianAssignmentService $beauticianAssignment,
    ) {}

    /**
     * @param  array<int, array{treatment_id: int, quantity: int}>  $treatmentItems
     * @param  array{latitude: float, longitude: float, address: ?string}|null  $homeLocation
     */
    public function createBooking(
        User $user,
        array $treatmentItems,
        string $bookingType,
        ?array $homeLocation,
        Carbon $bookingDate,
        string $timeStart,
        ?string $timeEnd = null,
        ?string $notes = null,
        ?int $userVoucherId = null,
        ?string $paymentType = 'cashless',
    ): Bookings {
        return DB::transaction(function () use (
            $user, $treatmentItems, $bookingType, $homeLocation,
            $bookingDate, $timeStart, $timeEnd, $notes, $userVoucherId, $paymentType,
        ) {
            $treatments = Treatments::query()
                ->whereIn('id', array_column($treatmentItems, 'treatment_id'))
                ->active()
                ->get()
                ->keyBy('id');

            if ($treatments->count() !== count($treatmentItems)) {
                throw ValidationException::withMessages([
                    'treatments' => 'Salah satu treatment yang dipilih tidak tersedia lagi.',
                ]);
            }

            $subtotal = 0.0;
            $treatmentLines = [];

            foreach ($treatmentItems as $item) {
                $treatment = $treatments->get($item['treatment_id']);
                $quantity = max(1, (int) $item['quantity']);
                $linePrice = (float) $treatment->price;
                $lineSubtotal = $linePrice * $quantity;

                $subtotal += $lineSubtotal;
                $treatmentLines[] = [
                    'treatment_id' => $treatment->id,
                    'quantity' => $quantity,
                    'price_per_unit' => $linePrice,
                    'subtotal' => $lineSubtotal,
                ];
            }

            $totalDurationMinutes = collect($treatmentLines)->sum(
                fn (array $line) => $treatments->get($line['treatment_id'])->duration_minutes * $line['quantity']
            );

            if (! $timeEnd) {
                $timeEnd = Carbon::createFromFormat('Y-m-d H:i', $bookingDate->format('Y-m-d').' '.$timeStart)
                    ->addMinutes((int) $totalDurationMinutes)
                    ->format('H:i');
            }

            $transportFee = 0.0;
            $distanceKm = null;
            $homeAddress = null;
            $homeLat = null;
            $homeLng = null;

            if ($bookingType === 'home') {
                if (! $homeLocation) {
                    throw ValidationException::withMessages([
                        'home_location' => 'Lokasi Home Service wajib diisi.',
                    ]);
                }

                $homeLat = $homeLocation['latitude'];
                $homeLng = $homeLocation['longitude'];

                try {
                    $distanceKm = $this->distanceCalculator->distanceFromSalonKm($homeLat, $homeLng);
                } catch (OutOfServiceAreaException $e) {
                    throw ValidationException::withMessages([
                        'home_location' => $e->getMessage(),
                    ]);
                }

                $transportFee = (float) $this->distanceCalculator->calculateTransportFee($distanceKm);

                $homeAddress = $homeLocation['address']
                    ?? $this->googleMaps->reverseGeocode($homeLat, $homeLng)
                    ?? $user->home_address
                    ?? $user->address
                    ?? null;

                if (blank($homeAddress)) {
                    throw ValidationException::withMessages([
                        'home_location' => 'Alamat tidak dapat ditentukan otomatis, mohon isi alamat secara manual.',
                    ]);
                }
            }

            $beautician = $this->beauticianAssignment->findAvailable($bookingDate, $timeStart, $timeEnd);

            // Calculate Voucher Discount
            $discountAmount = 0.0;
            $userVoucherRecord = null;

            if ($userVoucherId) {
                $userVoucherRecord = \App\Models\UserVouchers::with('voucher')
                    ->where('id', $userVoucherId)
                    ->where('user_id', $user->id)
                    ->where('is_used', false)
                    ->first();

                if ($userVoucherRecord && $userVoucherRecord->voucher && $userVoucherRecord->voucher->is_active) {
                    $v = $userVoucherRecord->voucher;

                    // Validate min_purchase against subtotal
                    if (! $v->min_purchase || $subtotal >= (float) $v->min_purchase) {
                        if ($v->type === 'free_shipping' || str_contains(strtolower($v->code), 'freeship') || str_contains(strtolower($v->name), 'ongkir')) {
                            // Discount applies to transport fee
                            $rawDiscount = $transportFee * ((float) $v->value / 100);
                            $discountAmount = (float) $v->max_discount ? min($rawDiscount, (float) $v->max_discount) : $rawDiscount;
                        } elseif ($v->type === 'percentage') {
                            $rawDiscount = $subtotal * ((float) $v->value / 100);
                            $discountAmount = (float) $v->max_discount ? min($rawDiscount, (float) $v->max_discount) : $rawDiscount;
                        } else { // fixed
                            $discountAmount = min((float) $v->value, $subtotal);
                        }
                    }
                }
            }

            $totalAmount = max(0, ($subtotal + $transportFee) - $discountAmount);

            $effectivePaymentType = ($bookingType === 'salon' && $paymentType === 'cash') ? 'cash' : 'cashless';
            $dpAmount = 0.0;
            $remainingAmount = 0.0;

            if ($effectivePaymentType === 'cash') {
                $dpAmount = (float) round($totalAmount * 0.35, 2);
                $remainingAmount = max(0.0, $totalAmount - $dpAmount);
            }

            $booking = Bookings::create([
                'booking_code' => $this->generateBookingCode(),
                'user_id' => $user->id,
                'beautician_id' => $beautician->id,
                'booking_type' => $bookingType,
                'status' => 'pending',
                'booking_date' => $bookingDate->toDateString(),
                'time_start' => $timeStart,
                'time_end' => $timeEnd,
                'home_address' => $homeAddress,
                'home_latitude' => $homeLat,
                'home_longitude' => $homeLng,
                'distance_km' => $distanceKm,
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'transport_fee' => $transportFee,
                'total_amount' => $totalAmount,
                'payment_method' => 'qris',
                'payment_type' => $effectivePaymentType,
                'dp_amount' => $dpAmount,
                'remaining_amount' => $remainingAmount,
                'payment_status' => 'unpaid',
                'notes' => $notes,
                'payment_expires_at' => now()->addMinutes((int) config('booking.payment_expiry_minutes')),
                'version' => 1,
            ]);

            foreach ($treatmentLines as $line) {
                BookingTreatments::create([
                    'booking_id' => $booking->id,
                    'treatment_id' => $line['treatment_id'],
                    'quantity' => $line['quantity'],
                    'price_per_unit' => $line['price_per_unit'],
                    'subtotal' => $line['subtotal'],
                ]);
            }

            // Mark UserVoucher as used
            if ($userVoucherRecord) {
                $userVoucherRecord->update([
                    'is_used' => true,
                    'used_at' => now(),
                    'booking_id' => $booking->id,
                ]);
                $userVoucherRecord->voucher->increment('used_count');
            }

            return $booking->fresh(['treatments', 'beautician']);
        });
    }

    /**
     * Batalkan booking yang sudah lewat batas waktu bayar dan belum lunas.
     * Dipanggil secara real-time (bukan scheduler) saat halaman
     * pembayaran/status dibuka.
     */
    public function cancelIfExpired(Bookings $booking): bool
    {
        if ($booking->payment_status === 'paid') {
            return false;
        }

        if (! $booking->payment_expires_at || $booking->payment_expires_at->isFuture()) {
            return false;
        }

        if ($booking->status === 'canceled') {
            return false;
        }

        $booking->update([
            'status' => 'canceled',
            'payment_status' => 'unpaid',
            'cancel_reason' => 'Pembayaran tidak diselesaikan dalam 15 menit.',
            'canceled_at' => now(),
            'version' => $booking->version + 1,
        ]);

        return true;
    }

    private function generateBookingCode(): string
    {
        do {
            $code = 'YB'.now()->format('ymd').strtoupper(Str::random(5));
        } while (Bookings::where('booking_code', $code)->exists());

        return $code;
    }
}
