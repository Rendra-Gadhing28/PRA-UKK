<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Bookings;
use App\Support\Membership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

/**
 * DashboardController
 *
 * Menyajikan ringkasan akun user: stats, progress membership,
 * dan tab "Upcoming" (di-render server-side saat load pertama;
 * tab lain diambil via AJAX oleh BookingController::list).
 *
 * Statistik ringan (jumlah booking, dsb) di-cache per-user 60 detik
 * untuk menekan beban query saat dashboard sering dibuka.
 */
class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();

        $stats = Cache::remember(
            "user:{$user->id}:dashboard-stats",
            now()->addSeconds(60),
            fn () => $this->buildStats($user->id)
        );

        $membership = Membership::progress($user->total_points);

        // Render awal hanya tab "Upcoming" — hemat query di initial load.
        $upcomingBookings = Bookings
        ::query()
            ->ownedBy($user->id)
            ->upcoming()
            ->with([
                'beautician:id,name',
                'treatments:id,name,duration_minutes,image',
            ])
            ->limit(10)
            ->get();

        return view('user.dashboard', [
            'user'             => $user,
            'stats'            => $stats,
            'membership'       => $membership,
            'upcomingBookings' => $upcomingBookings,
        ]);
    }

    /**
     * Hitung statistik ringan pakai satu query agregat (hindari 3x count() terpisah).
     *
     * @return array{total_bookings:int, upcoming_count:int}
     */
    private function buildStats(int $userId): array
    {
        $row = Bookings::query()
            ->ownedBy($userId)
            ->selectRaw("
                COUNT(*) as total_bookings,
                SUM(CASE WHEN status IN ('pending','confirmed','in_progress') THEN 1 ELSE 0 END) as upcoming_count
            ")
            ->first();

        return [
            'total_bookings' => (int) $row->total_bookings,
            'upcoming_count' => (int) $row->upcoming_count,
        ];
    }
}