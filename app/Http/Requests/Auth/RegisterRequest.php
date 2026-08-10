<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

/**
 * RegisterRequest
 *
 * Validasi & sanitasi data registrasi sebelum masuk ke controller.
 * Semua aturan keamanan (unique check, format nomor HP, kekuatan
 * password) ditegakkan di sini — controller cukup percaya data valid.
 */
class RegisterRequest extends FormRequest
{
    /**
     * Endpoint registrasi terbuka untuk semua tamu.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:100'],

            'email' => ['required', 'string', 'email:rfc', 'max:255', 'unique:users,email'],

            // Format Indonesia: 08xx, +628xx, atau 628xx
            'phone' => ['required', 'string', 'regex:/^(\+62|62|0)8[1-9][0-9]{6,10}$/', 'unique:users,phone'],

            // Min 8 karakter, wajib huruf besar+kecil+angka, harus diisi konfirmasi (password_confirmation)
            'password' => [
                'required',
                'confirmed',
                Password::min(8)->letters()->mixedCase()->numbers(),
            ],
        ];
    }

    /**
     * Pesan error dalam Bahasa Indonesia.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required'      => 'Nama lengkap wajib diisi.',
            'name.min'           => 'Nama minimal 2 karakter.',
            'name.max'           => 'Nama maksimal 100 karakter.',

            'email.required'     => 'Alamat email wajib diisi.',
            'email.email'        => 'Format email tidak valid.',
            'email.unique'       => 'Email ini sudah terdaftar. Silakan gunakan email lain atau masuk.',

            'phone.required'     => 'Nomor HP wajib diisi.',
            'phone.regex'        => 'Format nomor HP tidak valid. Gunakan 08xx, +628xx, atau 628xx.',
            'phone.unique'       => 'Nomor HP ini sudah terdaftar.',

            'password.required'  => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ];
    }

    /**
     * Nama field yang ramah dipakai kalau pesan default Laravel terpakai
     * (mis. aturan Password::letters()/mixedCase()/numbers() tidak
     * dioverride khusus di messages()).
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name'     => 'nama',
            'email'    => 'email',
            'phone'    => 'nomor HP',
            'password' => 'password',
        ];
    }

    /**
     * Bersihkan & normalisasi input sebelum divalidasi:
     * trim semua field teks, lowercase email (konsisten dengan unique check).
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'name'  => trim((string) $this->name),
            'email' => strtolower(trim((string) $this->email)),
            'phone' => trim((string) $this->phone),
        ]);
    }
}