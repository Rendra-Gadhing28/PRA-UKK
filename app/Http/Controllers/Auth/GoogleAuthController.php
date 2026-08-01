<?php
// app/Http/Controllers/Auth/GoogleAuthController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Helpers\ToastHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Redirect user ke Google consent screen.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle callback dari Google.
     */
    public function handleGoogleCallback()
    {
        try {
            // 1. Dapatkan data user dari Google
            $googleUser = Socialite::driver('google')->user();
            
            // 2. Cari user existing atau buat baru
            $user = $this->findOrCreateUser($googleUser);
            
            // 3. Update Google token (untuk keperluan refresh token)
            $this->updateGoogleToken($user, $googleUser);
            
            // 4. Login user
            Auth::login($user, true); // true = remember me
            
            // 5. Redirect dengan toast
            ToastHelper::success(
                'Login Berhasil! 🎉',
                "Selamat datang kembali, {$user->name}!"
            );
            
            return $this->redirectAfterLogin($user);
            
        } catch (\Exception $e) {
            // Log error untuk debugging
            \Log::error('Google Login Error: ' . $e->getMessage());
            
            ToastHelper::error(
                'Login Gagal 😥',
                'Terjadi kesalahan saat login dengan Google. Silakan coba lagi.'
            );
            
            return redirect()->route('login');
        }
    }

    /**
     * Cari user existing atau buat baru.
     */
    private function findOrCreateUser($googleUser): User
    {
        // Cari berdasarkan google_id
        $user = User::where('google_id', $googleUser->id)->first();
        
        if ($user) {
            // User sudah pernah login dengan Google sebelumnya
            return $user;
        }
        
        // Cari berdasarkan email
        $user = User::where('email', $googleUser->email)->first();
        
        if ($user) {
            // User sudah terdaftar dengan email yang sama
            // Link akun Google ke akun existing
            $user->update([
                'google_id' => $googleUser->id,
                'google_token' => $googleUser->token,
                'google_refresh_token' => $googleUser->refreshToken,
                'avatar_url' => $googleUser->avatar,
            ]);
            
            return $user;
        }
        
        // Buat user baru
        return User::create([
            'name' => $googleUser->name,
            'email' => $googleUser->email,
            'google_id' => $googleUser->id,
            'google_token' => $googleUser->token,
            'google_refresh_token' => $googleUser->refreshToken,
            'avatar_url' => $googleUser->avatar,
            'phone' => null, // User bisa isi nanti
            'password' => bcrypt(Str::random(32)), // Random password (tidak digunakan)
            'email_verified_at' => now(), // Email dari Google sudah verified
            'role' => 'user',
            'membership_level' => 'regular',
        ]);
    }

    /**
     * Update Google token.
     */
    private function updateGoogleToken(User $user, $googleUser): void
    {
        $user->update([
            'google_token' => $googleUser->token,
            'google_refresh_token' => $googleUser->refreshToken,
            'avatar_url' => $googleUser->avatar,
        ]);
    }

    /**
     * Redirect setelah login berdasarkan role.
     */
    private function redirectAfterLogin(User $user)
    {
        // Redirect berdasarkan role
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        
        // Redirect ke halaman sebelumnya atau dashboard user
        return redirect()->intended(route('user.dashboard'));
    }
}