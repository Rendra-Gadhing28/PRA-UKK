<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SlotController extends Controller
{
    /**
     * Mengecek slot waktu yang sudah dibooking pada tanggal tertentu.
     */
    public function check(Request $request): JsonResponse
    {
        $request->validate([
            'date' => ['required', 'date_format:Y-m-d'],
            'beautician_id' => ['nullable', 'exists:beauticians,id'],
        ]);

        $query = Booking::query()
            ->where('booking_date', $request->date)
            ->whereNotIn('status', ['canceled']);

        if ($request->filled('beautician_id')) {
            $query->where('beautician_id', $request->beautician_id);
        }

        $bookedSlots = $query->select('time_start', 'time_end')->get();

        return response()->json([
            'date' => $request->date,
            'booked_slots' => $bookedSlots,
        ]);
    }
}
