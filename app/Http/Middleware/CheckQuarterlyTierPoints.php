<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckQuarterlyTierPoints
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Periksa apakah kuartal sudah berganti. Jika ya, tier_points otomatis 0.
            $user->syncTierReset();
            
            // Simpan perubahan ke database jika ada reset yang terjadi
            if ($user->isDirty('tier_points') || $user->isDirty('last_tier_reset_at') || $user->isDirty('membership_level')) {
                $user->save();
            }
        }

        return $next($request);
    }
}
