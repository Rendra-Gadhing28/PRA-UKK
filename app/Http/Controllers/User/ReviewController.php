<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Bookings;
use App\Models\Reviews;
use App\Models\Treatments;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReviewController extends Controller
{
    /**
     * Tampilkan halaman formulir review ulasan untuk booking yang telah selesai.
     */
    public function create(Bookings $booking, Treatments $treatment): View|RedirectResponse
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $st = is_object($booking->status) ? $booking->status->value : (string) $booking->status;
        if ($st !== 'completed') {
            abort(403, 'Hanya booking yang sudah selesai yang bisa diulas.');
        }

        $existingReview = Reviews::where('booking_id', $booking->id)->first();
        if ($existingReview) {
            return redirect()->route('user.bookings.show', $booking)->with('success', 'Anda sudah memberikan ulasan untuk booking ini.');
        }

        $booking->load(['beautician', 'treatments', 'user']);

        return view('user.reviews.create', compact('booking', 'treatment'));
    }

    /**
     * Simpan rating dan ulasan pelanggan (treatment & beautician) ke database + reward +15 PTS.
     */
    public function store(Request $request, Bookings $booking, Treatments $treatment): RedirectResponse
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        $existingReview = Reviews::where('booking_id', $booking->id)->first();
        if ($existingReview) {
            return redirect()->route('user.bookings.show', $booking)->with('success', 'Anda sudah memberikan ulasan untuk booking ini.');
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'beautician_rating' => 'nullable|integer|min:1|max:5',
            'beautician_tags' => 'nullable|array',
            'beautician_tags.*' => 'string|max:50',
            'comment' => 'nullable|string|max:1000',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('reviews', 'public');
        }

        $beauticianRating = $request->input('beautician_rating') ? (int) $request->input('beautician_rating') : (int) $request->rating;
        $beauticianTags = $request->input('beautician_tags') ? json_encode($request->input('beautician_tags')) : null;

        $review = Reviews::create([
            'booking_id' => $booking->id,
            'user_id' => Auth::id(),
            'beautician_id' => $booking->beautician_id,
            'rating' => $request->rating,
            'beautician_rating' => $beauticianRating,
            'beautician_tags' => $beauticianTags,
            'comment' => $request->comment,
            'photo' => $photoPath,
            'is_approved' => true,
        ]);

        ActivityLogger::log('create', "Memberikan ulasan bintang {$request->rating} untuk reservasi #{$booking->booking_code}.", $review, [
            'rating' => $request->rating,
            'beautician_rating' => $beauticianRating,
        ]);

        // Berikan Reward +15 PTS ke User
        /** @var User $user */
        $user = Auth::user();
        if ($user) {
            $user->addPoints(15);
        }

        // Update rating count & average pada treatment
        $treatmentAvg = (float) DB::table('reviews')
            ->join('booking_treatments', 'reviews.booking_id', '=', 'booking_treatments.booking_id')
            ->where('booking_treatments.treatment_id', $treatment->id)
            ->where('reviews.is_approved', true)
            ->avg('reviews.rating');

        if ($treatmentAvg > 0) {
            $treatment->update([
                'rating' => round($treatmentAvg, 1),
                'rating_count' => $treatment->rating_count + 1,
            ]);
        }

        return redirect()->route('user.bookings.show', $booking)->with('success', 'Terima kasih atas ulasan Anda! Selamat, Anda mendapatkan +15 PTS Poin Loyalty ✨');
    }
}
