<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Exceptions\NoBeauticianAvailableException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingRequest;
use App\Models\Bookings;
use App\Models\Treatments;
use App\Services\Booking\BeauticianAssignmentService;
use App\Services\Booking\BookingService;
use App\Services\Payment\MidtransQrisService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * Booking Wizard: pilih treatment -> tipe layanan (+ lokasi bila home) ->
 * jadwal custom -> submit -> pembayaran QRIS -> struk.
 */
class BookingController extends Controller
{
    public function __construct(
        private readonly BookingService $bookingService,
        private readonly MidtransQrisService $midtransQris,
        private readonly BeauticianAssignmentService $beauticianAssignment,
    ) {}

    /**
     * Cek ketersediaan jadwal secara real-time (dipanggil AJAX dari custom
     * date/time picker setiap kali tanggal/jam/menit berubah). Karena
     * beautician di-assign otomatis, ini mengecek "apakah ADA beautician
     * yang free", bukan slot 1 beautician tertentu.
     */
    public function checkAvailability(Request $request): JsonResponse
    {
        $request->validate([
            'booking_date' => ['required', 'date_format:Y-m-d'],
            'time_start' => ['required', 'date_format:H:i'],
            'duration_minutes' => ['required', 'integer', 'min:1'],
        ]);

        $date = Carbon::createFromFormat('Y-m-d', $request->string('booking_date')->toString());
        $timeStart = $request->string('time_start')->toString();
        $timeEnd = Carbon::createFromFormat(
            'Y-m-d H:i',
            $request->string('booking_date').' '.$timeStart
        )->addMinutes($request->integer('duration_minutes'))->format('H:i');

        try {
            $this->beauticianAssignment->findAvailable($date, $timeStart, $timeEnd);

            return response()->json(['available' => true]);
        } catch (NoBeauticianAvailableException $e) {
            return response()->json(['available' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Endpoint untuk mengambil seluruh slot jam sekaligus dalam 1 tanggal (08:00 - 20:00).
     */
    public function dailySlots(Request $request): JsonResponse
    {
        $request->validate([
            'booking_date' => ['required', 'date_format:Y-m-d'],
            'duration_minutes' => ['required', 'integer', 'min:1'],
        ]);

        $date = Carbon::createFromFormat('Y-m-d', $request->string('booking_date')->toString());
        $duration = $request->integer('duration_minutes');

        $slots = $this->beauticianAssignment->getDailySlotsAvailability($date, $duration);

        return response()->json([
            'date' => $date->format('Y-m-d'),
            'duration_minutes' => $duration,
            'slots' => $slots,
        ]);
    }


    /**
     * Riwayat booking milik user (dipakai oleh route bookings.index / bookings.list).
     */
    public function index(Request $request): View
    {
        $activeTab = $request->string('tab', 'upcoming')->toString();

        $bookings = Bookings::query()
            ->ownedBy(auth()->id())
            ->with(['treatments', 'beautician'])
            ->when($activeTab === 'upcoming', fn ($q) => $q->upcoming())
            ->when($activeTab === 'past', fn ($q) => $q->past())
            ->when(in_array($activeTab, ['canceled', 'cancelled'], true), fn ($q) => $q->cancelled())
            ->orderByDesc('booking_date')
            ->orderByDesc('time_start')
            ->paginate(10);

        return view('user.bookings.index', [
            'bookings'  => $bookings,
            'activeTab' => $activeTab,
            'tab'       => $activeTab,
        ]);
    }

    public function list(Request $request): View
    {
        $activeTab = $request->string('tab', 'upcoming')->toString();
        $sort = $request->string('sort', 'desc')->toString();

        $bookings = Bookings::query()
            ->ownedBy(auth()->id())
            ->with(['treatments', 'beautician'])
            ->when($activeTab === 'upcoming', fn ($q) => $q->upcoming())
            ->when($activeTab === 'past', fn ($q) => $q->past())
            ->when(in_array($activeTab, ['canceled', 'cancelled'], true), fn ($q) => $q->cancelled())
            ->orderBy('booking_date', $sort)
            ->orderBy('time_start', $sort)
            ->get();

        return view('user.bookings.BookingList', [
            'bookings'  => $bookings,
            'tab'       => $activeTab,
            'paginated' => false,
        ]);
    }

 

    /**
     * Tampilkan Booking Wizard. Kalau datang dari halaman Treatments dengan
     * ?treatment={id}, treatment tersebut otomatis terisi sebagai item pertama.
     */
    public function create(Request $request): View
    {
        $treatmentId = $request->input('treatment') ?? $request->input('treatment_id');
        $preselectedTreatment = null;

        if ($treatmentId) {
            $preselectedTreatment = Treatments::query()
                ->active()
                ->with('category')
                ->find((int) $treatmentId);
        }

        $treatments = Treatments::query()
            ->active()
            ->with('category')
            ->orderBy('name')
            ->get();

        $treatmentsData = $treatments->map(fn ($t) => [
            'id' => $t->id,
            'name' => $t->name,
            'price' => (float) $t->price,
            'duration_minutes' => $t->duration_minutes,
        ])->values();

        $preselectedData = $preselectedTreatment ? [
            'id' => $preselectedTreatment->id,
            'name' => $preselectedTreatment->name,
            'price' => (float) $preselectedTreatment->price,
            'duration_minutes' => $preselectedTreatment->duration_minutes,
        ] : null;

        return view('user.bookings.create', [
            'treatments' => $treatments,
            'treatmentsData' => $treatmentsData,
            'selectedTreatment' => $preselectedTreatment,
            'preselectedTreatment' => $preselectedTreatment,
            'preselectedData' => $preselectedData,
            'googleMapsKey' => config('booking.google_maps_key'),
            'serviceRadiusKm' => config('booking.service_radius_km'),
        ]);
    }

    public function store(StoreBookingRequest $request): RedirectResponse
    {
        $treatmentItems = collect($request->validated('treatments'))
            ->groupBy('treatment_id')
            ->map(fn ($items, $treatmentId) => [
                'treatment_id' => (int) $treatmentId,
                'quantity' => (int) $items->sum('quantity'),
            ])->values()->all();

        $bookingType = $request->validated('booking_type');
        $bookingDate = Carbon::createFromFormat('Y-m-d', $request->validated('booking_date'));
        $timeStart = $request->validated('time_start');

        // Hitung total durasi dari treatment yang dipilih untuk dapat time_end.
        $totalDurationMinutes = Treatments::query()
            ->whereIn('id', array_column($treatmentItems, 'treatment_id'))
            ->get()
            ->reduce(function (int $carry, Treatments $treatment) use ($treatmentItems) {
                $qty = collect($treatmentItems)->firstWhere('treatment_id', $treatment->id)['quantity'] ?? 1;

                return $carry + ($treatment->duration_minutes * $qty);
            }, 0);

        $timeEnd = Carbon::createFromFormat('Y-m-d H:i', $request->validated('booking_date').' '.$timeStart)
            ->addMinutes($totalDurationMinutes)
            ->format('H:i');

        $homeLocation = null;
        if ($bookingType === 'home') {
            $homeLocation = [
                'latitude' => (float) $request->validated('home_latitude'),
                'longitude' => (float) $request->validated('home_longitude'),
                'address' => $request->validated('home_address'),
            ];
        }

        try {
            $booking = $this->bookingService->createBooking(
                user: $request->user(),
                treatmentItems: $treatmentItems,
                bookingType: $bookingType,
                homeLocation: $homeLocation,
                bookingDate: $bookingDate,
                timeStart: $timeStart,
                timeEnd: $timeEnd,
                notes: $request->validated('notes'),
            );
        } catch (NoBeauticianAvailableException $e) {
            throw ValidationException::withMessages([
                'time_start' => $e->getMessage(),
            ]);
        }

        return redirect()
            ->route('user.bookings.payment', $booking)
            ->with('success', 'Booking berhasil dibuat, silakan selesaikan pembayaran.');
    }

    /**
     * Halaman pembayaran QRIS. Meng-generate charge Midtrans kalau belum ada,
     * dan otomatis membatalkan booking bila sudah lewat 15 menit.
     */
    public function payment(Bookings $booking): View|RedirectResponse
    {
        $this->authorizeOwnership($booking);

        if ($this->bookingService->cancelIfExpired($booking)) {
            return redirect()
                ->route('user.bookings.create')
                ->with('error', 'Waktu pembayaran habis, booking dibatalkan. Silakan buat booking baru.');
        }

        if ($booking->payment_status === 'paid') {
            return redirect()->route('user.bookings.show', $booking);
        }

        if (blank($booking->qris_image_url)) {
            $booking = $this->midtransQris->createCharge($booking);
        }

        return view('user.bookings.payment', [
            'booking' => $booking->load(['treatments', 'beautician']),
        ]);
    }

    /**
     * Endpoint polling (dipanggil tiap beberapa detik dari halaman pembayaran).
     * Membaca status di database & secara aktif memeriksa status langsung ke API Midtrans
     * sebagai fallback (terutama berguna saat testing sandbox di localhost).
     */
    public function paymentStatus(Bookings $booking): JsonResponse
    {
        $this->authorizeOwnership($booking);

        $expired = $this->bookingService->cancelIfExpired($booking);
        $booking->refresh();

        // Fallback Direct API Check ke Midtrans jika di DB belum 'paid'
        if ($booking->payment_status !== 'paid' && ! $expired) {
            $orderId = $booking->midtrans_booking_code ?: $booking->booking_code;
            if ($orderId) {
                $statusRes = $this->midtransQris->checkStatus($orderId);
                $trxStatus = $statusRes['transaction_status'] ?? null;

                if (in_array($trxStatus, ['settlement', 'capture'], true)) {
                    $booking->update([
                        'payment_status' => 'paid',
                        'status' => $booking->status === 'pending' ? 'confirmed' : $booking->status,
                        'payment_verified_at' => now(),
                        'version' => $booking->version + 1,
                    ]);
                    $booking->refresh();
                } elseif (in_array($trxStatus, ['cancel', 'deny', 'expire'], true)) {
                    $booking->update([
                        'status' => 'canceled',
                        'cancel_reason' => "Pembayaran {$trxStatus} via Midtrans.",
                        'canceled_at' => now(),
                        'version' => $booking->version + 1,
                    ]);
                    $booking->refresh();
                }
            }
        }

        return response()->json([
            'payment_status' => $booking->payment_status,
            'status' => $booking->status,
            'expired' => $expired,
            'seconds_remaining' => $booking->payment_expires_at
                ? max(0, now()->diffInSeconds($booking->payment_expires_at, false))
                : 0,
            'redirect_url' => $booking->payment_status === 'paid'
                ? route('user.bookings.show', $booking)
                : null,
        ]);
    }


    /**
     * Halaman struk (hanya untuk booking yang sudah lunas) atau detail booking.
     */
    public function show(Bookings $booking): View|RedirectResponse
    {
        $this->authorizeOwnership($booking);

        if ($booking->payment_status !== 'paid') {
            return redirect()->route('user.bookings.payment', $booking);
        }

        return view('user.bookings.receipt', [
            'booking' => $booking->load(['treatments', 'beautician', 'user']),
        ]);
    }

    public function cancel(Request $request, Bookings $booking): RedirectResponse
    {
        $this->authorizeOwnership($booking);

        if (in_array($booking->status, ['completed', 'canceled'], true)) {
            return back()->with('error', 'Booking ini tidak bisa dibatalkan.');
        }

        $booking->update([
            'status' => 'canceled',
            'cancel_reason' => $request->string('reason', 'Dibatalkan oleh customer.')->toString(),
            'canceled_at' => now(),
            'version' => $booking->version + 1,
        ]);

        return redirect()
            ->route('user.bookings.index')
            ->with('success', 'Booking berhasil dibatalkan.');
    }

    private function authorizeOwnership(Bookings $booking): void
    {
        abort_unless($booking->user_id === auth()->id(), 403);
    }
}
