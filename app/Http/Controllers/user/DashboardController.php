<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Bookings;
use App\Models\Treatments;
use App\Support\Membership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

/**
 * DashboardController
 *
 * Menyajikan ringkasan akun user: stats, progress membership,
 * top 3 rated treatments, dan tab "Upcoming" booking.
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

        // Ambil 3 treatment dengan rating tertinggi untuk ditampilkan di Dashboard
        $topTreatments = Treatments::query()
            ->active()
            ->with('category')
            ->orderByDesc('rating')
            ->orderByDesc('rating_count')
            ->take(3)
            ->get();

        // Render awal hanya tab "Upcoming" — hemat query di initial load.
        $upcomingBookings = Bookings::query()
            ->ownedBy($user->id)
            ->upcoming()
            ->with([
                'beautician:id,name',
                'treatments:id,name,duration_minutes,images,price',
            ])
            ->limit(10)
            ->get();

        return view('user.dashboard', [
            'user'             => $user,
            'stats'            => $stats,
            'membership'       => $membership,
            'topTreatments'    => $topTreatments,
            'upcomingBookings' => $upcomingBookings,
        ]);
    }

    /**
     * Hitung statistik ringan pakai satu query agregat.
     *
     * @return array{total_bookings:int, upcoming_count:int, total_spending:float}
     */
    private function buildStats(int $userId): array
    {
        $row = Bookings::query()
            ->ownedBy($userId)
            ->selectRaw("
                COUNT(*) as total_bookings,
                SUM(CASE WHEN status IN ('pending','confirmed','in_progress') THEN 1 ELSE 0 END) as upcoming_count,
                SUM(CASE WHEN payment_status = 'paid' OR status = 'completed' THEN total_amount ELSE 0 END) as total_spending
            ")
            ->first();

        return [
            'total_bookings' => (int) ($row->total_bookings ?? 0),
            'upcoming_count' => (int) ($row->upcoming_count ?? 0),
            'total_spending' => (float) ($row->total_spending ?? 0),
        ];
    }
}