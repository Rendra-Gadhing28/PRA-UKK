<?php

declare(strict_types=1);

namespace App\Http\Requests\User;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class RescheduleBookingRequest extends FormRequest
{
    /**
     * Otorisasi hanya untuk pengguna yang telah login.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Aturan validasi tanggal dan jam reschedule booking.
     */
    public function rules(): array
    {
        return [
            'booking_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'time_start' => ['required', 'date_format:H:i'],
            'reason' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Hook validator tambahan untuk validasi jam operasional dan waktu lampau.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->filled('booking_date') && $this->filled('time_start')) {
                try {
                    $slotTime = Carbon::createFromFormat('Y-m-d H:i', $this->input('booking_date').' '.$this->input('time_start'));
                    $timeStr = $this->input('time_start');
                    if ($timeStr < '09:00' || $timeStr > '17:30') {
                        $validator->errors()->add('time_start', 'Jam kedatangan harus berada di rentang 09:00 - 17:30 WIB.');
                    } elseif ($slotTime->isPast()) {
                        $validator->errors()->add('time_start', 'Waktu reservasi yang dipilih sudah terlewat. Silakan pilih jam lain.');
                    }
                } catch (\Throwable) {
                    // Handled by date_format rules
                }
            }
        });
    }

    /**
     * Kustomisasi pesan error validasi dalam bahasa Indonesia.
     */
    public function messages(): array
    {
        return [
            'booking_date.required' => 'Tanggal reservasi baru wajib diisi.',
            'booking_date.date_format' => 'Format tanggal tidak valid (YYYY-MM-DD).',
            'booking_date.after_or_equal' => 'Tanggal reservasi baru tidak boleh tanggal yang sudah lewat.',
            'time_start.required' => 'Jam reservasi baru wajib dipilih.',
            'time_start.date_format' => 'Format jam tidak valid (HH:MM).',
        ];
    }
}
