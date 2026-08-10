<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\ToastHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Auth\RegisteredRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

/**
 * Controller untuk menangani proses autentikasi dasar.
 *
 * Meliputi: tampil form login/register, proses login,
 * proses registrasi, dan logout.
 */
class AuthenticatedSessionController extends Controller
{
    /**
     * Tampilkan halaman form login.
     */
    public function showLogin(): View
    {
        return view('auth.login');
    }

    /**
     * Tampilkan halaman form registrasi.
     */
    public function showRegister(): View
    {
        return view('auth.register');
    }

    /**
     * Proses autentikasi login pengguna.
     *
     * Mendukung login dengan email atau nomor HP.
     * Menerapkan rate limiting via LoginRequest.
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        // Regenerasi session untuk mencegah session fixation attack
        $request->session()->regenerate();

        /** @var User $user */
        $user = Auth::user();

        // Periksa status akun aktif
        if (! $user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            ToastHelper::error('Akun Anda telah dinonaktifkan. Hubungi admin untuk informasi lebih lanjut.');

            return redirect()->route('login');
        }

        $namaDepan = explode(' ', $user->name)[0];
        ToastHelper::success("Selamat datang kembali, {$namaDepan}! 👋");

        // Arahkan admin ke dashboard admin, user biasa ke dashboard user
        return $user->role === 'admin'
            ? redirect()->intended(route('admin.dashboard'))
            : redirect()->intended(route('user.dashboard'));
    }

    /**
     * Proses registrasi pengguna baru.
     *
     * Membuat akun baru dengan membership level 'regular'
     * dan langsung login setelah berhasil.
     */
    public function register(RegisterRequest $request): RedirectResponse
    {
        $user = User::create([
            'name'             => $request->name,
            'email'            => $request->email,
            'phone'            => $request->phone,
            'password'         => Hash::make($request->password),
            'membership_level' => 'regular',
            'is_active'        => true,
            'role'         => 'user',
        ]);

        // Login otomatis setelah registrasi berhasil
        Auth::login($user);

        $request->session()->regenerate();

        $namaDepan = explode(' ', $user->name)[0];
        ToastHelper::success("Selamat datang di Yalia Beauty, {$namaDepan}! 🌸  Akun Anda berhasil dibuat.");

        return redirect()->route('user.dashboard');
    }

    /**
     * Proses logout pengguna.
     *
     * Menghapus session dan token CSRF untuk keamanan.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        // Invalidasi session dan regenerasi token untuk mencegah CSRF
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        ToastHelper::info('Anda telah berhasil keluar.');

        return redirect()->route('login');
    }
}