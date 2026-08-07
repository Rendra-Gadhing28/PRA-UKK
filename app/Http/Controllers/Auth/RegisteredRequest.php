<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

/**
 * Form Request untuk validasi data registrasi pengguna baru.
 *
 * Memastikan data yang masuk sudah bersih, valid, dan aman
 * sebelum diproses oleh controller.
 */
class RegisterRequest extends FormRequest
{
    /**
     * Semua pengguna diizinkan mengakses endpoint registrasi.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan validasi untuk data registrasi.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'min:2', 'max:100'],
            'email'    => ['required', 'string', 'email:rfc,dns', 'max:255', 'unique:users,email'],
            'phone'    => ['required', 'string', 'regex:/^(\+62|62|0)8[1-9][0-9]{6,10}$/', 'unique:users,phone'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()],
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
            'email.required'     => 'Alamat email wajib diisi.',
            'email.email'        => 'Format email tidak valid.',
            'email.unique'       => 'Email ini sudah terdaftar. Silakan gunakan email lain atau login.',
            'phone.required'     => 'Nomor HP wajib diisi.',
            'phone.regex'        => 'Format nomor HP tidak valid. Gunakan format: 08xx, +628xx, atau 628xx.',
            'phone.unique'       => 'Nomor HP ini sudah terdaftar.',
            'password.required'  => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min'       => 'Password minimal 8 karakter.',
        ];
    }

    /**
     * Bersihkan dan normalisasi data sebelum validasi.
     *
     * @return array<string, mixed>
     */
    protected function prepareForValidation(): void
    {
        // Trim whitespace dari semua input string
        $this->merge([
            'name'  => trim($this->name ?? ''),
            'email' => strtolower(trim($this->email ?? '')),
            'phone' => trim($this->phone ?? ''),
        ]);
    }
}