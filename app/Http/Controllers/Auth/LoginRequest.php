<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Form Request untuk validasi dan autentikasi login.
 *
 * Menerapkan rate limiting untuk mencegah brute force attack
 * dan mendukung login dengan email atau nomor HP.
 */
class LoginRequest extends FormRequest
{
    /**
     * Semua pengguna diizinkan mengakses endpoint login.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan validasi untuk data login.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'email'    => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
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
            'email.required'    => 'Email atau nomor HP wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ];
    }

    /**
     * Proses autentikasi pengguna.
     *
     * Mendukung login dengan email atau nomor HP.
     * Menerapkan rate limiting 5 percobaan per menit per IP.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // Tentukan field login: email atau nomor HP
        $credentials = $this->resolveLoginCredentials();

        if (! Auth::attempt($credentials, $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('Email/nomor HP atau password yang Anda masukkan salah.'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Tentukan kredensial berdasarkan input (email atau nomor HP).
     *
     * @return array<string, string>
     */
    private function resolveLoginCredentials(): array
    {
        $input = $this->input('email');

        // Cek apakah input berupa nomor HP
        $isPhone = preg_match('/^(\+62|62|0)8[1-9][0-9]{6,10}$/', $input);

        return [
            $isPhone ? 'phone' : 'email' => $input,
            'password'                    => $this->input('password'),
        ];
    }

    /**
     * Pastikan request tidak sedang dalam kondisi rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    private function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Key unik untuk rate limiting berdasarkan email + IP.
     */
    private function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')) . '|' . $this->ip());
    }
}