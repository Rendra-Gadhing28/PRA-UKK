<?php

namespace App\Http\Middleware;

use App\Helpers\ToastHelper;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAccountIsActive
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            if (! $user->is_active) {
                Auth::guard('web')->logout();

                $request->session()->invalidate();
                $request->session()->regenerateToken();

                if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                    return response()->json([
                        'message' => 'Akun Anda telah dinonaktifkan oleh Administrator.',
                        'active' => false,
                        'deactivated' => true,
                        'redirect' => route('login'),
                    ], 403);
                }

                ToastHelper::error('Akun Anda telah dinonaktifkan oleh Administrator.');

                return redirect()->route('login')->withErrors([
                    'email' => 'Akun Anda telah dinonaktifkan oleh Administrator.',
                ]);
            }
        }

        return $next($request);
    }
}
