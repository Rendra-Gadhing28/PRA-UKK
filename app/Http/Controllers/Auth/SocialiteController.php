<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\ToastHelper;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

/**
 * Controller untuk autentikasi via Google OAuth menggunakan Socialite.
 *
 * Menangani alur: redirect ke Google → callback → login/register otomatis.
 * Mendukung akun yang sudah ada (update token) dan akun baru (buat otomatis).
 */
class SocialiteController extends Controller
{
    /**
     * Redirect pengguna ke halaman autentikasi Google.
     *
     * Meminta scope dasar: profil dan email.
     */
    public function redirectToGoogle(): RedirectResponse
    {
        return Socialite::driver('google')
            ->scopes(['openid', 'profile', 'email'])
            ->redirect();
    }

    /**
     * Proses callback dari Google setelah pengguna mengizinkan akses.
     *
     * Alur:
     * 1. Ambil data pengguna dari Google
     * 2. Cari akun berdasarkan google_id atau email
     * 3. Buat akun baru jika belum ada
     * 4. Update token Google
     * 5. Login pengguna
     */
    public function handleGoogleCallback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (\Exception $e) {
            Log::error('Google OAuth callback error', [
                'error'   => $e->getMessage(),
                'ip'      => request()->ip(),
            ]);

            ToastHelper::error('Gagal login dengan Google. Silakan coba lagi.');

            return redirect()->route('login');
        }

        // Cari user berdasarkan google_id terlebih dahulu, lalu fallback ke email
        $user = $this->findOrCreateUserFromGoogle($googleUser);

        if (! $user->is_active) {
            ToastHelper::error('Akun Anda telah dinonaktifkan. Hubungi admin untuk informasi lebih lanjut.');

            return redirect()->route('login');
        }

        Auth::login($user, remember: true);

        request()->session()->regenerate();

        $namaDepan   = explode(' ', $user->name)[0];
        $isNewUser   = $user->wasRecentlyCreated;

        if ($isNewUser) {
            ToastHelper::success("Selamat datang di Yalia Beauty, {$namaDepan}! 🌸 Akun Google Anda berhasil terhubung.");
        } else {
            ToastHelper::success("Selamat datang kembali, {$namaDepan}! 👋");
        }

        return $user->is_admin
            ? redirect()->route('admin.dashboard')
            : redirect()->route('user.dashboard');
    }

    /**
     * Cari user yang sudah ada atau buat user baru berdasarkan data Google.
     *
     * Prioritas pencarian:
     * 1. Berdasarkan google_id (sudah pernah login Google)
     * 2. Berdasarkan email (sudah punya akun manual, hubungkan ke Google)
     * 3. Buat akun baru (pertama kali)
     *
     * @param  \Laravel\Socialite\Contracts\User  $googleUser
     */
    private function findOrCreateUserFromGoogle(\Laravel\Socialite\Contracts\User $googleUser): User
    {
        // Cari berdasarkan google_id
        $user = User::where('google_id', $googleUser->getId())->first();

        if ($user) {
            // Update token Google yang mungkin sudah expired
            $user->update([
                'google_token'         => $googleUser->token,
                'google_refresh_token' => $googleUser->refreshToken,
                'avatar_url'           => $googleUser->getAvatar(),
            ]);

            return $user;
        }

        // Cari berdasarkan email (hubungkan akun yang sudah ada)
        $user = User::where('email', $googleUser->getEmail())->first();

        if ($user) {
            $user->update([
                'google_id'            => $googleUser->getId(),
                'google_token'         => $googleUser->token,
                'google_refresh_token' => $googleUser->refreshToken,
                'avatar_url'           => $googleUser->getAvatar(),
            ]);

            return $user;
        }

        // Buat akun baru dari data Google
        return User::create([
            'name'                 => $googleUser->getName(),
            'email'                => $googleUser->getEmail(),
            'phone'                => null, // Tidak tersedia dari Google
            'password'             => bcrypt(Str::random(32)), // Password acak karena login via Google
            'google_id'            => $googleUser->getId(),
            'google_token'         => $googleUser->token,
            'google_refresh_token' => $googleUser->refreshToken,
            'avatar_url'           => $googleUser->getAvatar(),
            'email_verified_at'    => now(), // Email dari Google sudah terverifikasi
            'membership_level'     => 'regular',
            'is_active'            => true,
            'role'             => 'user',
        ]);
    }
}