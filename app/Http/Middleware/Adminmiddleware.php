<?php

namespace App\Http\Middleware;

use App\Helpers\ToastHelper;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware untuk memproteksi route admin.
 *
 * Memastikan hanya pengguna yang terautentikasi
 * dan memiliki flag is_admin = true yang dapat mengakses.
 */
class AdminMiddleware
{
    /**
     * Proses request masuk.
     *
     * Redirect ke dashboard user jika bukan admin,
     * atau ke halaman login jika belum terautentikasi.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (! $user->isAdmin()) {
            ToastHelper::error('Anda tidak memiliki akses ke halaman admin.');

            return redirect()->route('user.dashboard');
        }


        return $next($request);
    }
}