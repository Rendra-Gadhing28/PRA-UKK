<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Helpers\ToastHelper;
use App\Http\Controllers\Controller;
use App\Models\Beauticians;
use App\Models\Reviews;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminReviewController extends Controller
{
    /**
     * Tampilkan halaman pusat moderasi & balasan ulasan pelanggan.
     */
    public function index(Request $request): View
    {
        $tab = $request->string('tab', 'all')->toString();
        $search = $request->string('search', '')->toString();
        $beauticianId = $request->input('beautician_id');

        // Executive Stats Bar
        $totalReviews = Reviews::count();
        $avgRating = round((float) (Reviews::avg('rating') ?: 5.0), 1);
        $pendingReplies = Reviews::whereNull('admin_reply')->count();
        $topBeautician = Beauticians::query()
            ->withCount('reviews')
            ->whereHas('reviews')
            ->get()
            ->sortByDesc('average_rating')
            ->first();

        $query = Reviews::query()
            ->with([
                'Users',
                'Beauticians',
                'Bookings.treatments',
            ]);

        // Filter: Tab status
        if ($tab === 'unreplied') {
            $query->whereNull('admin_reply');
        } elseif ($tab === 'five_star') {
            $query->where('rating', 5);
        } elseif ($tab === 'attention') {
            $query->where('rating', '<=', 3);
        } elseif ($tab === 'with_photo') {
            $query->whereNotNull('photo');
        }

        // Filter: Beautician
        if ($beauticianId && $beauticianId !== 'all') {
            $query->where('beautician_id', (int) $beauticianId);
        }

        // Filter: Search (User name, comment, booking code)
        if ($search) {
            $query->where(function ($q) use ($search): void {
                $q->where('comment', 'like', "%{$search}%")
                    ->orWhere('admin_reply', 'like', "%{$search}%")
                    ->orWhereHas('Users', fn ($uq) => $uq->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('Bookings', fn ($bq) => $bq->where('booking_code', 'like', "%{$search}%"));
            });
        }

        $reviews = $query->orderByDesc('created_at')->paginate(10)->withQueryString();
        $allBeauticians = Beauticians::query()->active()->orderBy('name')->get(['id', 'name']);

        return view('admin.reviews.index', [
            'reviews' => $reviews,
            'tab' => $tab,
            'search' => $search,
            'selectedBeauticianId' => $beauticianId,
            'allBeauticians' => $allBeauticians,
            'totalReviews' => $totalReviews,
            'avgRating' => $avgRating,
            'pendingReplies' => $pendingReplies,
            'topBeautician' => $topBeautician,
        ]);
    }

    /**
     * Simpan balasan admin terhadap ulasan pelanggan.
     */
    public function reply(Request $request, Reviews $review): RedirectResponse
    {
        $validated = $request->validate([
            'admin_reply' => 'required|string|max:1000',
        ]);

        $review->update([
            'admin_reply' => $validated['admin_reply'],
            'is_approved' => true,
        ]);

        ToastHelper::success('Balasan admin berhasil disimpan dan dipublikasikan!');

        return back();
    }

    /**
     * Toggle status approval publikasi review.
     */
    public function toggleApprove(Reviews $review): RedirectResponse
    {
        $review->update([
            'is_approved' => ! $review->is_approved,
        ]);

        $statusText = $review->is_approved ? 'dipublikasikan' : 'disembunyikan';
        ToastHelper::success("Ulasan berhasil {$statusText}.");

        return back();
    }

    /**
     * Hapus ulasan pelanggan (spam/moderasi).
     */
    public function destroy(Reviews $review): RedirectResponse
    {
        $review->delete();

        ToastHelper::success('Ulasan berhasil dihapus dari sistem.');

        return back();
    }
}
