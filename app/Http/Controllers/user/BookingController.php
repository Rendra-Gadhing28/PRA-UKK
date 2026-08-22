<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Enums\BookingStatus;
use App\Exceptions\NoBeauticianAvailableException;
use App\Http\Controllers\Controller;
use App\Http\Requests\CheckAvailabilityRequest;
use App\Http\Requests\DailySlotsRequest;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UploadPhotoAssignRequest;
use App\Models\Bookings;
use App\Models\Treatments;
use App\Models\UserVouchers;
use App\Services\Booking\BeauticianAssignmentService;
use App\Services\Booking\BookingService;
use App\Services\Booking\PhotoAssignService;
use App\Services\Payment\MidtransQrisService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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
        private readonly PhotoAssignService $photoAssignService,
    ) {}

    /**
     * Cek ketersediaan jadwal secara real-time (dipanggil AJAX dari custom
     * date/time picker setiap kali tanggal/jam/menit berubah). Karena
     * beautician di-assign otomatis, ini mengecek "apakah ADA beautician
     * yang free", bukan slot 1 beautician tertentu.
     */
    public function checkAvailability(CheckAvailabilityRequest $request): JsonResponse
    {
        $date = Carbon::createFromFormat('Y-m-d', $request->validated('booking_date'));
        $timeStart = $request->validated('time_start');
        $timeEnd = Carbon::createFromFormat(
            'Y-m-d H:i',
            $request->validated('booking_date').' '.$timeStart
        )->addMinutes((int) $request->validated('duration_minutes'))->format('H:i');

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
    public function dailySlots(DailySlotsRequest $request): JsonResponse
    {
        $date = Carbon::createFromFormat('Y-m-d', $request->validated('booking_date'));
        $duration = (int) $request->validated('duration_minutes');

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
            ->with([
                'treatments:id,name,duration_minutes,images,price',
                'beautician:id,name',
            ])
            ->when($activeTab === 'upcoming', fn ($q) => $q->upcoming())
            ->when($activeTab === 'past', fn ($q) => $q->past())
            ->when(in_array($activeTab, ['canceled', 'cancelled'], true), fn ($q) => $q->cancelled())
            ->orderByDesc('booking_date')
            ->orderByDesc('time_start')
            ->paginate(10);

        return view('user.bookings.index', [
            'bookings' => $bookings,
            'activeTab' => $activeTab,
            'tab' => $activeTab,
        ]);
    }

    public function list(Request $request)
    {
        $activeTab = $request->string('tab', 'upcoming')->toString();
        $sort = $request->string('sort', 'desc')->toString();

        $bookings = Bookings::query()
            ->ownedBy(auth()->id())
            ->with([
                'treatments:id,name,duration_minutes,images,price',
                'beautician:id,name',
            ])
            ->when($activeTab === 'upcoming', fn ($q) => $q->upcoming())
            ->when($activeTab === 'past', fn ($q) => $q->past())
            ->when(in_array($activeTab, ['canceled', 'cancelled'], true), fn ($q) => $q->cancelled())
            ->orderBy('booking_date', $sort)
            ->orderBy('time_start', $sort)
            // AUDIT: sebelumnya get() tanpa limit sama sekali — bisa menarik
            // ribuan baris ke memori untuk user dengan riwayat sangat panjang.
            // Diberi batas pengaman; ganti ke paginate() kalau frontend tab ini
            // memang butuh true pagination, bukan cuma daftar pendek.
            ->limit(100)
            ->get();

        // Jika dipanggil via AJAX (misal switch tab di dashboard/booking list), kembalikan partial HTML
        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return view('user.bookings.BookingList', [
                'bookings' => $bookings,
                'tab' => $activeTab,
                'paginated' => false,
            ]);
        }

        // Jika dibuka langsung via browser address bar (non-AJAX), alihkan ke halaman utama booking berpeta CSS & Layout lengkap
        return redirect()->route('user.bookings.index', ['tab' => $activeTab]);
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

        // AUDIT: daftar treatment aktif dipakai bersama oleh SEMUA user dan
        // jarang berubah, jadi di-cache singkat supaya wizard page tidak
        // memukul MySQL setiap kali dibuka. PENTING: cache ini perlu di-clear
        // (Cache::forget('treatments:active-with-category') atau lewat model
        // event) setiap kali treatment dibuat/diubah/dihapus/dinonaktifkan,
        // kalau tidak, treatment baru bisa tidak muncul selama 5 menit.
        $treatmentsData = Cache::remember(
            'treatments:active-with-category',
            now()->addMinutes(5),
            fn () => Treatments::query()
                ->active()
                ->with('category')
                ->orderBy('name')
                ->get()
                ->map(fn ($t) => [
                    'id'               => $t->id,
                    'name'             => $t->name,
                    'price'            => (float) $t->price,
                    'duration_minutes' => $t->duration_minutes,
                    'image_url'        => $t->image_url,
                    'badge'            => $t->badge,
                    'category'         => $t->category?->name,
                ])
                ->values()
                ->all()
        );

        $treatmentsData = collect($treatmentsData);

        $preselectedData = $preselectedTreatment ? [
            'id' => $preselectedTreatment->id,
            'name' => $preselectedTreatment->name,
            'price' => (float) $preselectedTreatment->price,
            'duration_minutes' => $preselectedTreatment->duration_minutes,
            'image_url' => $preselectedTreatment->image_url,
            'badge' => $preselectedTreatment->badge,
            'category' => $preselectedTreatment->category?->name,
        ] : null;

        // Ambil voucher milik user yang aktif, belum kadaluarsa, dan belum terpakai
        $userVouchers = UserVouchers::query()
            ->with('voucher:id,code,name,description,type,value,min_purchase,max_discount')
            ->where('user_id', auth()->id())
            ->where('is_used', false)
            ->whereHas('voucher', function ($q) {
                $q->where('is_active', true)
                    ->where('valid_until', '>=', now()->toDateString());
            })
            ->get();

        $userVouchersData = $userVouchers->map(fn ($uv) => [
            'id' => $uv->id,
            'voucher_id' => $uv->voucher_id,
            'code' => $uv->voucher?->code,
            'name' => $uv->voucher?->name,
            'description' => $uv->voucher?->description,
            'type' => $uv->voucher?->type, // percentage, fixed, free_shipping
            'value' => (float) ($uv->voucher?->value ?? 0),
            'min_purchase' => (float) ($uv->voucher?->min_purchase ?? 0),
            'max_discount' => $uv->voucher?->max_discount ? (float) $uv->voucher->max_discount : null,
            'is_free_shipping' => ($uv->voucher?->type === 'free_shipping' || str_contains(strtolower($uv->voucher?->code ?? ''), 'freeship') || str_contains(strtolower($uv->voucher?->name ?? ''), 'ongkir')),
        ])->values();

        return view('user.bookings.create', [
            'treatmentsData' => $treatmentsData,
            'selectedTreatment' => $preselectedTreatment,
            'preselectedTreatment' => $preselectedTreatment,
            'preselectedData' => $preselectedData,
            'userVouchersData' => $userVouchersData,
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

        // AUDIT: hitung total durasi hanya mengambil kolom yang dipakai
        // (id, duration_minutes), bukan seluruh kolom Treatments.
        // CATATAN ARSITEKTUR: logika hitung time_end dari durasi ini duplikat
        // secara konsep dengan yang ada di BeauticianAssignmentService
        // (dipakai checkAvailability/dailySlots). Idealnya dikonsolidasi jadi
        // satu sumber kebenaran di BookingService — saya belum ubah karena
        // belum melihat isi BookingService, supaya tidak menebak API-nya.
        $totalDurationMinutes = Treatments::query()
            ->whereIn('id', array_column($treatmentItems, 'treatment_id'))
            ->get(['id', 'duration_minutes'])
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
                userVoucherId: $request->filled('user_voucher_id') ? (int) $request->input('user_voucher_id') : null,
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

        // AUDIT: pembuatan charge Midtrans SENGAJA tetap sinkron (bukan queue
        // job) karena user menunggu QR code untuk langsung tampil di halaman
        // ini. Memindahkan ke queue justru menambah latency yang dirasakan
        // user (halaman harus polling lagi menunggu job selesai).
        if (blank($booking->qris_image_url)) {
            $booking = $this->midtransQris->createCharge($booking);
        }

        return view('user.bookings.payment', [
            'booking' => $booking->load([
                'treatments:id,name,duration_minutes,images,price',
                'beautician:id,name',
            ]),
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
                    $this->markBookingPaid($booking);
                } elseif (in_array($trxStatus, ['cancel', 'deny', 'expire'], true)) {
                    $this->cancelBookingRecord($booking, "Pembayaran {$trxStatus} via Midtrans.");
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

        $currentStatus = $booking->status instanceof BookingStatus ? $booking->status->value : $booking->status;
        if ($currentStatus === 'pending') {
            $booking->update(['status' => 'confirmed']);
            $booking->refresh();
        }

        return view('user.bookings.receipt', [
            // AUDIT: kolom 'user' SENGAJA tidak dibatasi — saya belum melihat
            // blade user.bookings.receipt sehingga tidak tahu field apa saja
            // yang benar-benar dipakai (nama/email/telp/dll). Kirim blade-nya
            // kalau mau dibatasi juga seperti treatments & beautician di bawah.
            'booking' => $booking->load([
                'treatments:id,name,duration_minutes,images,price',
                'beautician:id,name',
                'user',
            ]),
        ]);
    }

    public function cancel(Request $request, Bookings $booking): RedirectResponse
    {
        $this->authorizeOwnership($booking);

        if (in_array($booking->status, ['completed', 'canceled'], true)) {
            return back()->with('error', 'Booking ini tidak bisa dibatalkan.');
        }

        $this->cancelBookingRecord(
            $booking,
            $request->string('reason', 'Dibatalkan oleh customer.')->toString()
        );

        return redirect()
            ->route('user.bookings.index')
            ->with('success', 'Booking berhasil dibatalkan.');
    }

    /**
     * Upload & convert photo_assign (hasil treatment) ke WebP.
     * Hanya owner booking yang dapat mengupload.
     *
     * AUDIT: logika GD/resize/convert dipindah ke PhotoAssignService
     * (thin controller, fat service) + ditambah resize maks 1200px yang
     * sebelumnya tidak ada.
     */
    public function uploadPhotoAssign(UploadPhotoAssignRequest $request, Bookings $booking): RedirectResponse
    {
        $this->authorizeOwnership($booking);

        $this->photoAssignService->process($booking, $request->file('photo_assign'));

        return back()->with('success', 'Foto hasil treatment berhasil diunggah.');
    }

    private function authorizeOwnership(Bookings $booking): void
    {
        abort_unless($booking->user_id === auth()->id(), 403);
    }

    /**
     * AUDIT: sebelumnya blok update ini ditulis inline di paymentStatus().
     * Diekstrak agar tidak duplikat dengan pola optimistic-locking
     * (kolom 'version') yang sama, dan jadi satu titik perubahan kalau
     * aturan "apa artinya booking lunas" berubah nanti.
     * Kandidat kuat untuk dipindah ke BookingService begitu file itu
     * ikut direview.
     */
    private function markBookingPaid(Bookings $booking): void
    {
        $booking->update([
            'payment_status' => 'paid',
            'status' => $booking->status === 'pending' ? 'confirmed' : $booking->status,
            'payment_verified_at' => now(),
            'version' => $booking->version + 1,
        ]);
        $booking->refresh();
    }

    /**
     * Dipakai baik oleh cancel() (dibatalkan user) maupun paymentStatus()
     * (dibatalkan otomatis karena Midtrans deny/expire) — sebelumnya
     * dua blok update terpisah dengan bentuk yang persis sama.
     */
    private function cancelBookingRecord(Bookings $booking, string $reason): void
    {
        $booking->update([
            'status' => 'canceled',
            'cancel_reason' => $reason,
            'canceled_at' => now(),
            'version' => $booking->version + 1,
        ]);
        $booking->refresh();
    }
}