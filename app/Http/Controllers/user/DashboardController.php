<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Bookings;
use App\Models\Treatment;
use App\Models\Treatments;
use App\Models\UserVouchers;
use App\Models\Vouchers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use Illuminate\Database\Eloquent\Builder;

/**
 * Controller untuk halaman dashboard pengguna.
 *
 * Menampilkan ringkasan aktivitas, booking terbaru,
 * statistik membership, dan treatment rekomendasi.
 *
 * Optimalisasi performa menggunakan Cache untuk data statis
 * dan eager loading (with) untuk menghindari N+1 query problem.
 */
class DashboardController extends Controller
{
    /**
     * Durasi cache untuk data treatment populer (dalam detik).
     */
    private const CACHE_TREATMENT_TTL = 3600; // 1 jam

    /**
     * Tampilkan halaman dashboard pengguna.
     *
     * Mengambil data:
     * - Statistik booking user
     * - Booking aktif (pending/confirmed/in_progress)
     * - Riwayat booking terbaru
     * - Treatment populer (dari cache)
     * - Voucher aktif milik user
     */
    public function index(): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Ambil semua data yang dibutuhkan secara efisien
        $statistikBooking   = $this->getStatistikBooking($user->id);
        $bookingAktif       = $this->getBookingAktif($user->id);
        $bookingTerbaru     = $this->getBookingTerbaru($user->id);
        $treatmentPopuler   = $this->getTreatmentPopuler();
        $voucherAktif       = $this->getVoucherAktif($user->id);

        return view('user.dashboard', compact(
            'user',
            'statistikBooking',
            'bookingAktif',
            'bookingTerbaru',
            'treatmentPopuler',
            'voucherAktif',
        ));
    }

    /**
     * Hitung statistik booking pengguna.
     *
     * Menggunakan satu query dengan selectRaw untuk efisiensi
     * dibanding memanggil count() berkali-kali.
     *
     * @param  int  $userId
     * @return array{total: int, selesai: int, dibatalkan: int, menunggu: int}
     */
    private function getStatistikBooking(int $userId): array
    {
        $statistik = Bookings::where('user_id', $userId)
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as selesai,
                SUM(CASE WHEN status = "canceled" THEN 1 ELSE 0 END) as dibatalkan,
                SUM(CASE WHEN status IN ("pending", "confirmed", "in_progress") THEN 1 ELSE 0 END) as menunggu
            ')
            ->first();

        return [
            'total'      => (int) ($statistik->total ?? 0),
            'selesai'    => (int) ($statistik->selesai ?? 0),
            'dibatalkan' => (int) ($statistik->dibatalkan ?? 0),
            'menunggu'   => (int) ($statistik->menunggu ?? 0),
        ];
    }

    /**
     * Ambil booking aktif pengguna (pending/confirmed/in_progress).
     *
     * Menggunakan eager loading untuk mencegah N+1 query problem.
     *
     * @param  int  $userId
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Booking>
     */
    private function getBookingAktif(int $userId)
    {
        return Bookings::with([
                'treatments:id,name,duration_minutes,image',
                'beautician:id,name,photo',
            ])
            ->where('user_id', $userId)
            ->whereIn('status', ['pending', 'confirmed', 'in_progress'])
            ->orderBy('booking_date', 'asc')
            ->orderBy('time_start', 'asc')
            ->get();
    }

    /**
     * Ambil 5 riwayat booking terbaru pengguna.
     *
     * Menggunakan eager loading untuk mencegah N+1 query problem.
     * Hanya ambil kolom yang dibutuhkan untuk performa.
     *
     * @param  int  $userId
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Booking>
     */
    private function getBookingTerbaru(int $userId)
    {
        return Bookings::with([
                'treatments:id,name,image',
                'review:id,booking_id,rating',
            ])
            ->where('user_id', $userId)
            ->whereIn('status', ['completed', 'canceled'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * Ambil treatment populer dari cache.
     *
     * Data di-cache selama 1 jam karena jarang berubah.
     * Menggunakan eager loading untuk relasi kategori.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Treatment>
     */
    private function getTreatmentPopuler()
    {
        return Cache::remember('treatment_populer', self::CACHE_TREATMENT_TTL, function () {
            return Treatments::with('category:id,name,icon')
                ->where('is_active', true)
                ->where('badge', 'best_seller')
                ->orderBy('rating_count', 'desc')
                ->limit(4)
                ->get(['id', 'category_id', 'name', 'slug', 'price', 'duration_minutes', 'images', 'badge', 'rating', 'rating_count']);
        });
    }

    /**
     * Ambil voucher aktif yang dimiliki pengguna.
     *
     * Hanya menampilkan voucher yang belum digunakan dan masih berlaku.
     * Eager loading relasi voucher untuk menghindari N+1 problem.
     *
     * @param  int  $userId
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserVoucher>
     */
    private function getVoucherAktif(int $userId)
    {
        return UserVouchers::with([
        'vouchers:id,code,name,type,value,valid_until,min_purchase',
    ])
    ->where('user_id', $userId)
    ->where('is_used', false)
    ->whereHas('vouchers', function ($query) {
        $query->where('is_active', true)
              ->where('valid_until', '>=', today());
    })
    // Solusi: Menggunakan Subquery untuk mengurutkan berdasarkan valid_until di tabel vouchers
    ->orderBy(
        Vouchers::select('valid_until')
            ->whereColumn('vouchers.id', 'user_vouchers.voucher_id')
            ->limit(1),
        'asc'
    )
    ->limit(3)
    ->get();
    }
}