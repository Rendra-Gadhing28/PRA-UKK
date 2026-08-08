<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Bookings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
}