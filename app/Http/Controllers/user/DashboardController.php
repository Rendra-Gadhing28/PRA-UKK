<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Bookings;
use App\Models\Treatments;
use App\Services\Dashboard\DashboardStatsService;
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
    public function __construct(
        private readonly DashboardStatsService $dashboardStats,
    ) {}

    public function index(Request $request): View
    {
        $user = Auth::user();

        // AUDIT: perhitungan stats dipindah ke DashboardStatsService
        // (thin controller, fat service). Cache key & TTL tidak berubah.
        $stats = $this->dashboardStats->forUser($user->id);

        $membership = Membership::progress($user->total_points);

        // AUDIT: top-3-treatment-by-rating IDENTIK untuk semua user (bukan
        // data per-user), tapi sebelumnya query ORDER BY rating dijalankan
        // ulang ke MySQL setiap kali dashboard dibuka siapa pun. Di-cache
        // 10 menit karena rating tidak berubah tiap detik — sesuaikan TTL
        // kalau Anda butuh update lebih real-time.
        $topTreatments = Cache::remember(
            'dashboard:top-treatments',
            now()->addMinutes(10),
            fn () => Treatments::query()
                ->active()
                ->with('category')
                ->orderByDesc('rating')
                ->orderByDesc('rating_count')
                ->take(3)
                ->get()
        );

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
     * Klaim poin check-in harian dan simpan ke database (total_points).
     */
    public function dailyCheckin(Request $request): \Illuminate\Http\JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->last_daily_checkin_at && $user->last_daily_checkin_at->isToday()) {
            return response()->json([
                'success'      => false,
                'already_claimed' => true,
                'message'      => 'Anda sudah mengklaim poin hari ini!',
                'total_points' => $user->total_points,
            ]);
        }

        $pointsAdded = 25;

        // Simpan peningkatan poin ke tabel users di database secara permanen
        $user->increment('total_points', $pointsAdded);
        $user->update(['last_daily_checkin_at' => now()]);
        $user->refresh();

        return response()->json([
            'success'      => true,
            'points_added' => $pointsAdded,
            'total_points' => $user->total_points,
            'message'      => 'Berhasil mengklaim +25 Poin PTS ke akun Anda!',
        ]);
    }
}