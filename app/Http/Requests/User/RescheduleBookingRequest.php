<?php

declare(strict_types=1);

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class RescheduleBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'booking_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'time_start' => ['required', 'date_format:H:i'],
            'reason' => ['nullable', 'string', 'max:500'],
        ];
    }

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
