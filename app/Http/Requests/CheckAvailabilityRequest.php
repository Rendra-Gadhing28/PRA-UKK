<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validasi untuk BookingController::checkAvailability().
 *
 * Endpoint publik-terautentikasi (dipanggil AJAX dari date/time picker),
 * tidak ada pengecekan kepemilikan resource di sini karena belum ada
 * booking yang dibuat pada tahap ini.
 */
class CheckAvailabilityRequest extends FormRequest
{
    /**
     * Otorisasi pengecekan ketersediaan jadwal terapis.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan validasi tanggal, jam mulai, dan durasi reservasi.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'booking_date' => ['required', 'date_format:Y-m-d'],
            'time_start' => ['required', 'date_format:H:i'],
            'duration_minutes' => ['required', 'integer', 'min:1'],
        ];
    }
}
