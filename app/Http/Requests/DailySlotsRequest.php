<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validasi untuk BookingController::dailySlots().
 */
class DailySlotsRequest extends FormRequest
{
    /**
     * Otorisasi pengecekan slot harian.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan validasi tanggal booking dan durasi layanan.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'booking_date' => ['required', 'date_format:Y-m-d'],
            'duration_minutes' => ['required', 'integer', 'min:1'],
        ];
    }
}
