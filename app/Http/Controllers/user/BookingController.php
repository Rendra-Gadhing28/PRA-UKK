<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Bookings;
use App\Models\Treatment;
use App\Models\Treatments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * BookingController (area user)
 *
 * list() dipanggil via fetch/Alpine.js saat user klik tab
 * Upcoming/Past/Cancelled di dashboard. Mengembalikan partial HTML
 * (bukan JSON) supaya markup kartu booking tetap satu sumber (Blade),
 * tidak diduplikasi di JS.
 *
 * Query selalu di-scope ke user login (ownedBy) — mencegah IDOR,
 * user tidak bisa lihat booking user lain hanya dengan ganti parameter.
 */
class BookingController extends Controller
{
    private const ALLOWED_TABS = ['upcoming', 'past', 'cancelled'];

    public function list(Request $request): View
    {
        $tab = $request->string('tab', 'upcoming')->lower()->value();

        if (! in_array($tab, self::ALLOWED_TABS, true)) {
            $tab = 'upcoming';
        }

        $bookings = Bookings::query()
            ->ownedBy(Auth::id())
            ->{$tab}() // scopeUpcoming / scopePast / scopeCancelled
            ->with([
                'beautician:id,name',
                'treatments:id,name,duration_minutes,image',
                'review:id,booking_id,rating',
            ])
            ->paginate(6);

        return view('user.partials.booking-list', [
            'bookings' => $bookings,
            'tab'      => $tab,
        ]);
    }

    public function create(Request $request)
    {
        $treatmentId = $request->query('treatment');
        $selectedTreatment = null;
        
        if ($treatmentId) {
            $selectedTreatment = Treatment::find($treatmentId);
        }

        return view('user.bookings.create', compact('selectedTreatment'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'treatment_id' => 'required|exists:treatments,id',
            'booking_type' => 'required|in:salon,home',
            'booking_date' => 'required|date|after_or_equal:today',
            'time_start' => 'required',
        ]);

        try {
            DB::beginTransaction();

            $treatment = Treatments::findOrFail($request->treatment_id);

            // Generate simple booking code
            $bookingCode = 'BKG-' . date('Ymd') . '-' . strtoupper(str_random(4));

            $booking = Bookings::create([
                'booking_code' => $bookingCode,
                'user_id' => Auth::id(),
                'booking_type' => $request->booking_type,
                'status' => 'pending',
                'booking_date' => $request->booking_date,
                'time_start' => $request->time_start,
                'time_end' => date('H:i', strtotime($request->time_start) + ($treatment->duration_minutes * 60)),
                'subtotal' => $treatment->price,
                'total_amount' => $treatment->price,
                'payment_method' => 'qris', // Default QRIS for this flow
                'payment_status' => 'unpaid',
            ]);

            // Add treatment
            DB::table('booking_treatments')->insert([
                'booking_id' => $booking->id,
                'treatment_id' => $treatment->id,
                'quantity' => 1,
                'price_per_unit' => $treatment->price,
                'subtotal' => $treatment->price,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            // Store QRIS details in session for sandbox payment mock
            session()->flash('qris_payment', [
                'booking_id' => $booking->id,
                'amount' => $booking->total_amount,
                'code' => 'QRIS-SANDBOX-' . $booking->booking_code
            ]);

            return redirect()->route('user.dashboard')->with('success', 'Booking created successfully! Please complete your payment.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create booking. Please try again.');
        }
    }
}
