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
     * Redirect ke Google consent screen
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->with(['prompt' => 'select_account'])
            ->redirect();
    }

    /**
     * Handle callback dari Google
     */
    public function handleGoogleCallback()
    {
        try {
            // 1. Dapatkan user dari Google
            $googleUser = Socialite::driver('google')->user();
            
            // 2. Cari atau buat user
            $user = $this->findOrCreateUser($googleUser);
            
            // 3. Update Google tokens
            $this->updateGoogleTokens($user, $googleUser);
            
            // 4. Login user
            Auth::login($user, true);
            
            // 5. Regenerate session
            request()->session()->regenerate();
            
            // 6. Redirect dengan toast
            return redirect()->route($user->isAdmin() ? 'admin.dashboard' : 'dashboard')
                ->with('toast', [
                    'type' => 'success',
                    'message' => 'Login Berhasil! ',
                    'description' => "Selamat datang, {$user->name}!",
                ]);
            
        } catch (\Exception $e) {
            \Log::error('Google Login Error: ' . $e->getMessage());
            
            return redirect()->route('login')
                ->with('toast', [
                    'type' => 'error',
                    'message' => 'Login Gagal',
                    'description' => 'Terjadi kesalahan. Silakan coba lagi.',
                ]);
        }
    }

    /**
     * Cari existing user atau buat baru
     */
    private function findOrCreateUser($googleUser): User
    {
        // 1. Cari berdasarkan google_id
        $user = User::where('google_id', $googleUser->getId())->first();
        if ($user) {
            return $user;
        }

        // 2. Cari berdasarkan email
        $user = User::where('email', $googleUser->getEmail())->first();
        if ($user) {
            // Link Google ke akun existing
            $user->update([
                'google_id' => $googleUser->getId(),
                'avatar_url' => $googleUser->getAvatar(),
            ]);
            return $user;
        }

        // 3. Buat user baru
        return User::create([
            'name' => $googleUser->getName(),
            'email' => $googleUser->getEmail(),
            'google_id' => $googleUser->getId(),
            'avatar_url' => $googleUser->getAvatar(),
            'phone' => null, // Bisa diisi nanti
            'password' => null, // No password for Google users
            'email_verified_at' => now(), // Google already verified
            'role' => 'user',
            'membership_level' => 'regular',
        ]);
    }

    /**
     * Update Google tokens
     */
    private function updateGoogleTokens(User $user, $googleUser): void
    {
        $user->update([
            'google_token' => $googleUser->token,
            'google_refresh_token' => $googleUser->refreshToken,
            'avatar_url' => $googleUser->getAvatar(),
        ]);
    }
}