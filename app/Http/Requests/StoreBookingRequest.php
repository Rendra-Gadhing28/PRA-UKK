<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'booking_type' => ['required', 'string', Rule::in(['home', 'salon'])],

            'treatments' => ['required', 'array', 'min:1'],
            'treatments.*.treatment_id' => ['required', 'integer', 'exists:treatments,id'],
            'treatments.*.quantity' => ['required', 'integer', 'min:1', 'max:10'],

            'home_address' => ['nullable', 'required_if:booking_type,home', 'string', 'max:500'],
            'home_latitude' => ['nullable', 'required_if:booking_type,home', 'numeric', 'between:-90,90'],
            'home_longitude' => ['nullable', 'required_if:booking_type,home', 'numeric', 'between:-180,180'],

            'booking_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'time_start' => ['required', 'date_format:H:i'],

            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'booking_type.required' => 'Pilih jenis layanan (Home Service atau At Salon).',
            'booking_type.in' => 'Pilihan jenis layanan tidak valid.',
            'treatments.required' => 'Pilih minimal satu perawatan (treatment).',
            'treatments.min' => 'Pilih minimal satu perawatan (treatment).',
            'home_address.required_if' => 'Alamat lokasi wajib diisi untuk layanan Home Service.',
            'home_latitude.required_if' => 'Lokasi pada peta wajib dipilih untuk Home Service.',
            'home_longitude.required_if' => 'Lokasi pada peta wajib dipilih untuk Home Service.',
            'booking_date.required' => 'Pilih tanggal reservasi.',
            'booking_date.after_or_equal' => 'Tanggal reservasi tidak boleh tanggal yang sudah lewat.',
            'time_start.required' => 'Pilih jam reservasi.',
            'time_start.date_format' => 'Format jam reservasi tidak valid (HH:MM).',
        ];
    }
}
