<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * Controller registrasi akun pelanggan.
 * Validasi input didelegasikan sepenuhnya ke RegisteredRequest
 * (App\Http\Requests\Auth\RegisteredRequest), sehingga controller ini
 * hanya berfokus pada pembuatan user & sisi otentikasi.
 */
class RegisteredUsedController extends Controller
{
    /**
     * Menampilkan form registrasi.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Memproses pendaftaran akun baru.
     *
     * Data sudah tervalidasi & ternormalisasi (trim, lowercase email)
     * oleh RegisterRequest::prepareForValidation() sebelum sampai di sini.
     * Password di-hash sebelum disimpan (never simpan plaintext).
     */
    public function store(RegisterRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'membership_level' => 'regular',
            'total_points' => 0,
            'tier_points' => 0,
            'total_bookings' => 0,
            'total_spending' => 0,
            'is_active' => true,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('dashboard');
    }
}