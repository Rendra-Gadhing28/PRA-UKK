<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Bookings;
use App\Models\Treatments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function create(Bookings $booking, Treatments $treatment)
    {
        // Pastikan booking milik user yang sedang login
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Pastikan booking statusnya sudah selesai (completed)
        if ($booking->status !== 'completed' && $booking->status->value !== 'completed') {
            abort(403, 'Hanya booking yang sudah selesai yang bisa diulas.');
        }

        // Cek apakah review sudah ada
        $existingReview = \DB::table('reviews')->where('booking_id', $booking->id)->first();
        if ($existingReview) {
            return redirect()->route('user.bookings.index')->with('success', 'Anda sudah memberikan ulasan untuk booking ini.');
        }

        return view('user.reviews.create', compact('booking', 'treatment'));
    }

    public function store(Request $request, Bookings $booking, Treatments $treatment)
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        $existingReview = \DB::table('reviews')->where('booking_id', $booking->id)->first();
        if ($existingReview) {
            return redirect()->route('user.bookings.index')->with('success', 'Anda sudah memberikan ulasan untuk booking ini.');
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('reviews', 'public');
        }

        \DB::table('reviews')->insert([
            'booking_id' => $booking->id,
            'user_id' => Auth::id(),
            'beautician_id' => $booking->beautician_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'photo' => $photoPath,
            'is_approved' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('user.bookings.show', $booking)->with('success', 'Terima kasih atas ulasan Anda!');
    }
}
