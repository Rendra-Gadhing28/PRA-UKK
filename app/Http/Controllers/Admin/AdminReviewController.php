<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Beauticians;
use App\Models\Reviews;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminReviewController extends Controller
{
    /**
     * Tampilkan halaman daftar ulasan & feedback pelanggan di portal admin.
     */
    public function index(Request $request): View
    {
        $tab = $request->string('tab', 'all')->toString();
        $search = $request->string('search', '')->trim()->toString();
        $beauticianFilter = $request->input('beautician_id');
        $sort = $request->string('sort', 'latest')->toString();

        // 1. Hitung Ringkasan Statistik
        $avgRating = round((float) (Reviews::avg('rating') ?: 5.0), 1);
        $avgBeauticianRating = round((float) (Reviews::avg('beautician_rating') ?: 5.0), 1);
        $totalReviews = Reviews::count();
        $unrepliedCount = Reviews::whereNull('admin_reply')->orWhere('admin_reply', '')->count();
        $unapprovedCount = Reviews::where('is_approved', false)->count();

        // Top Rated Beautician
        $topBeautician = Beauticians::query()
            ->whereHas('reviews')
            ->withAvg('reviews', 'beautician_rating')
            ->withCount('reviews')
            ->orderByDesc('reviews_avg_beautician_rating')
            ->first();

        // 2. Query Reviews dengan Filter
        $query = Reviews::query()
            ->with([
                'user:id,name,email,avatar,avatar_url,membership_level',
                'beautician:id,name,photo',
                'booking' => fn ($q) => $q->select('id', 'booking_code', 'booking_date', 'status', 'payment_status', 'booking_type')
                    ->with('treatments:id,name,images,price'),
            ]);

        // Filter tab
        match ($tab) {
            'unreplied' => $query->where(function ($q) {
                $q->whereNull('admin_reply')->orWhere('admin_reply', '');
            }),
            'unapproved' => $query->where('is_approved', false),
            'five_star' => $query->where('rating', 5),
            'needs_attention' => $query->where('rating', '<=', 3),
            'with_photo' => $query->whereNotNull('photo')->where('photo', '!=', ''),
            default => null,
        };

        // Filter pencarian
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('comment', 'like', "%{$search}%")
                    ->orWhere('admin_reply', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"))
                    ->orWhereHas('beautician', fn ($bq) => $bq->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('booking', fn ($bkq) => $bkq->where('booking_code', 'like', "%{$search}%")
                        ->orWhereHas('treatments', fn ($tq) => $tq->where('name', 'like', "%{$search}%")));
            });
        }

        // Filter beautician
        if ($beauticianFilter) {
            $query->where('beautician_id', (int) $beauticianFilter);
        }

        // Sorting
        match ($sort) {
            'oldest' => $query->orderBy('created_at', 'asc'),
            'rating_desc' => $query->orderByDesc('rating')->orderByDesc('created_at'),
            'rating_asc' => $query->orderBy('rating', 'asc')->orderByDesc('created_at'),
            default => $query->orderByDesc('created_at'),
        };

        $reviews = $query->paginate(12)->withQueryString();
        $beauticiansList = Beauticians::query()->orderBy('name')->get(['id', 'name']);

        return view('admin.reviews.index', [
            'reviews' => $reviews,
            'activeTab' => $tab,
            'currentSearch' => $search,
            'selectedBeautician' => $beauticianFilter,
            'currentSort' => $sort,
            'avgRating' => $avgRating,
            'avgBeauticianRating' => $avgBeauticianRating,
            'totalReviews' => $totalReviews,
            'unrepliedCount' => $unrepliedCount,
            'unapprovedCount' => $unapprovedCount,
            'topBeautician' => $topBeautician,
            'beauticiansList' => $beauticiansList,
        ]);
    }

    /**
     * Balas ulasan pelanggan secara langsung dari portal admin.
     */
    public function reply(Request $request, Reviews $review): RedirectResponse
    {
        $request->validate([
            'admin_reply' => 'required|string|max:1000',
        ]);

        $review->update([
            'admin_reply' => $request->input('admin_reply'),
        ]);

        return back()->with('success', 'Balasan ulasan berhasil disimpan & dikirim ke pelanggan.');
    }

    /**
     * Toggle status publikasi/persetujuan ulasan.
     */
    public function toggleApprove(Reviews $review): RedirectResponse
    {
        $newStatus = ! $review->is_approved;
        $review->update(['is_approved' => $newStatus]);

        $msg = $newStatus
            ? 'Ulasan berhasil disetujui untuk ditampilkan di publik.'
            : 'Ulasan disembunyikan dari publik.';

        return back()->with('success', $msg);
    }

    /**
     * Hapus ulasan dari database.
     */
    public function destroy(Reviews $review): RedirectResponse
    {
        $review->delete();

        return back()->with('success', 'Ulasan berhasil dihapus.');
    }
}
