<?php

use App\Http\Controllers\Admin\AdminBeauticianController;
use App\Http\Controllers\Admin\AdminBookingController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminFinanceController;
use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Admin\AdminTreatmentController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminVoucherController;
use App\Http\Controllers\Api\ReminderController;
use App\Http\Controllers\Api\SlotController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\BookingController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\ReviewController;
use App\Http\Controllers\User\TreatmentController;
use App\Http\Controllers\User\UserVoucherController;
use App\Http\Controllers\Webhooks\MidtransWebhookController;
use App\Http\Middleware\AdminMiddleware;
use App\Mail\BookingReminderMail;
use App\Models\Bookings;
use App\Models\Reviews;
use App\Models\Treatments;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Yalia Beauty
|--------------------------------------------------------------------------
|
| Grup route berdasarkan hak akses:
|   - Guest: halaman publik dan auth (login, register, OAuth)
|   - Auth (user): dashboard dan fitur pelanggan
|   - Auth + Admin: manajemen salon
|
*/

// =============================================================
// HALAMAN PUBLIK
// =============================================================

Route::post('/api/trigger-reminders', [ReminderController::class, 'trigger']);

Route::get('/test-email', function () {
    try {
        // Ambil booking pertama dari database atau buat objek dummy jika kosong
        $booking = Bookings::with(['user'])->first() ?? new Bookings([
            'booking_code' => 'BK-YALIA-'.rand(1000, 9999),
            'booking_date' => date('Y-m-d'),
            'time_start' => '10:00:00',
            'booking_type' => 'home_service',
            'payment_status' => 'pending',
            'total_amount' => 150000,
        ]);

        if (! $booking->user) {
            $booking->setRelation('user', new User([
                'name' => 'Kak Gadhing',
                'email' => env('MAIL_USERNAME'),
            ]));
        }

        Mail::to(env('MAIL_USERNAME'))->send(new BookingReminderMail($booking, 'H-1 Hari'));

        return '<h2 style="color:#0d9488; font-family:sans-serif;">✨ Sukses! Email Template Yalia Beauty berhasil dikirim ke '.env('MAIL_USERNAME').'</h2><p style="font-family:sans-serif;">Silakan buka Inbox / Spam Gmail Anda untuk melihat tampilan barunya.</p>';
    } catch (Exception $e) {
        return '<h2 style="color:red; font-family:sans-serif;">❌ Gagal mengirim email:</h2><p style="font-family:sans-serif;">'.$e->getMessage().'</p>';
    }
});

Route::get('/', function () {
    $galleryTreatments = Treatments::query()
        ->active()
        ->with('category')
        ->orderByDesc('rating')
        ->take(8)
        ->get();

    $approvedReviews = Reviews::query()
        ->with(['Users', 'Beauticians', 'Bookings.treatments'])
        ->where('is_approved', true)
        ->orderByDesc('created_at')
        ->take(6)
        ->get();

    return view('welcome', compact('galleryTreatments', 'approvedReviews'));
})->name('home');

Route::get('/skeleton-demo', function () {
    return view('skeleton-demo');
})->name('skeleton.demo');

Route::get('/daily-reward-demo', function () {
    return view('demo.daily-reward');
})->name('daily-reward.demo');

// =============================================================
// AUTENTIKASI — Hanya untuk tamu (belum login)
// =============================================================

Route::middleware('guest')->group(function () {

    // Login
    Route::get('/login', [AuthenticatedSessionController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'login'])->name('login.post');

    // Register
    Route::get('/register', [AuthenticatedSessionController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthenticatedSessionController::class, 'register'])->name('register.post');

    Route::get('/auth/google', [SocialiteController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [SocialiteController::class, 'handleGoogleCallback'])->name('auth.google.callback');

});

// =============================================================
// AREA PENGGUNA — Harus login dan aktif
// =============================================================

Route::middleware(['auth'])->prefix('dashboard')->name('user.')->group(function () {

    // Dashboard utama
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/daily-checkin', [DashboardController::class, 'dailyCheckin'])->name('daily-checkin');
    Route::get('/bookings/list', [BookingController::class, 'list'])
        ->middleware('throttle:60,1')
        ->name('bookings.list');
    Route::get('/treatments', [TreatmentController::class, 'index'])
        ->name('treatments.index');
    Route::get('/treatments/search', [TreatmentController::class, 'search'])->name('user.treatments.search');
    // Vouchers & Tukar Point
    Route::get('/vouchers', [UserVoucherController::class, 'index'])->name('vouchers.index');
    Route::post('/vouchers/{voucher}/claim', [UserVoucherController::class, 'claim'])->name('vouchers.claim');

    // Endpoint for AJAX checking booked slots
    Route::get('/slots/check', [SlotController::class, 'check'])->name('slots.check');

    // Booking
    Route::get('/booking', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/booking/buat', [BookingController::class, 'create'])->name('bookings.create');
    Route::post('/booking', [BookingController::class, 'store'])->name('bookings.store');

    // Cek ketersediaan jadwal (auto-assign aware) - dipanggil AJAX dari custom time picker
    Route::get('/booking/cek-ketersediaan', [BookingController::class, 'checkAvailability'])
        ->middleware('throttle:60,1')
        ->name('bookings.check-availability');

    // Cek seluruh slot harian sekaligus
    Route::get('/booking/slot-harian', [BookingController::class, 'dailySlots'])
        ->middleware('throttle:60,1')
        ->name('bookings.daily-slots');

    // Ambil daftar beautician tersedia untuk tanggal & jam tertentu
    Route::get('/booking/beautician-tersedia', [BookingController::class, 'availableBeauticians'])
        ->middleware('throttle:60,1')
        ->name('bookings.available-beauticians');

    // Pembayaran QRIS
    Route::get('/booking/{booking}/pembayaran', [BookingController::class, 'payment'])->name('bookings.payment');
    Route::get('/booking/{booking}/pembayaran/status', [BookingController::class, 'paymentStatus'])
        ->middleware('throttle:120,1') // dipanggil tiap 5 detik oleh polling
        ->name('bookings.payment.status');

    Route::get('/booking/{booking}', [BookingController::class, 'show'])->name('bookings.show');
    Route::patch('/booking/{booking}/batalkan', [BookingController::class, 'cancel'])->name('bookings.cancel');
    Route::patch('/booking/{booking}/ganti-jadwal', [BookingController::class, 'reschedule'])->name('bookings.reschedule');
    Route::post('/booking/{booking}/photo-assign', [BookingController::class, 'uploadPhotoAssign'])->name('bookings.photo-assign');

    // Reviews
    Route::get('/booking/{booking}/treatments/{treatment}/review', [ReviewController::class, 'create'])->name('treatments.review');
    Route::post('/booking/{booking}/treatments/{treatment}/review', [ReviewController::class, 'store'])->name('treatments.review.store');
});

// =============================================================
// TRACKER PENGELUARAN STRUK (LLM AI) — Harus Login
// =============================================================

Route::middleware(['auth'])->group(function () {
    Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
    Route::post('/expenses/scan', [ExpenseController::class, 'scan'])->name('expenses.scan');
    Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
});

// =============================================================
// WEBHOOK — Notifikasi server-to-server dari Midtrans (tanpa auth/CSRF)
// =============================================================

Route::post('/webhooks/midtrans', [MidtransWebhookController::class, 'handle'])
    ->name('webhooks.midtrans');

// =============================================================
// AREA ADMIN — Harus login + is_admin = true
// =============================================================

Route::middleware(['auth', AdminMiddleware::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Dashboard admin
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/export/pdf', [AdminDashboardController::class, 'exportPdf'])->name('export.pdf');
        Route::get('/export/excel', [AdminDashboardController::class, 'exportExcel'])->name('export.excel');

        // Admin Booking Management
        Route::prefix('bookings')->name('bookings.')->group(function () {
            Route::get('/', [AdminBookingController::class, 'index'])->name('index');
            Route::get('/export/pdf', [AdminBookingController::class, 'exportPdf'])->name('export.pdf');
            Route::get('/export/excel', [AdminBookingController::class, 'exportExcel'])->name('export.excel');
            Route::get('/{booking}', [AdminBookingController::class, 'show'])->name('show');
            Route::get('/{booking}/receipt', [AdminBookingController::class, 'receipt'])->name('receipt');
            Route::patch('/{booking}/status', [AdminBookingController::class, 'updateStatus'])->name('update-status');
            Route::patch('/{booking}/verify-payment', [AdminBookingController::class, 'verifyPayment'])->name('verify-payment');
            Route::post('/{booking}/reply-review', [AdminBookingController::class, 'replyReview'])->name('reply-review');
        });

        // Admin Treatments Management
        Route::prefix('treatments')->name('treatments.')->group(function () {
            Route::get('/', [AdminTreatmentController::class, 'index'])->name('index');
            Route::get('/create', [AdminTreatmentController::class, 'create'])->name('create');
            Route::post('/', [AdminTreatmentController::class, 'store'])->name('store');
            Route::get('/{treatment}/edit', [AdminTreatmentController::class, 'edit'])->name('edit');
            Route::put('/{treatment}', [AdminTreatmentController::class, 'update'])->name('update');
            Route::delete('/{treatment}', [AdminTreatmentController::class, 'destroy'])->name('destroy');
            Route::match(['POST', 'PATCH'], '/{treatment}/toggle-active', [AdminTreatmentController::class, 'toggleActive'])->name('toggle-active');
        });

        // Admin Beauticians Management
        Route::prefix('beauticians')->name('beauticians.')->group(function () {
            Route::get('/', [AdminBeauticianController::class, 'index'])->name('index');
            Route::get('/create', [AdminBeauticianController::class, 'create'])->name('create');
            Route::post('/', [AdminBeauticianController::class, 'store'])->name('store');
            Route::get('/{beautician}', [AdminBeauticianController::class, 'show'])->name('show');
            Route::get('/{beautician}/edit', [AdminBeauticianController::class, 'edit'])->name('edit');
            Route::put('/{beautician}', [AdminBeauticianController::class, 'update'])->name('update');
            Route::delete('/{beautician}', [AdminBeauticianController::class, 'destroy'])->name('destroy');
            Route::match(['POST', 'PATCH'], '/{beautician}/toggle-active', [AdminBeauticianController::class, 'toggleActive'])->name('toggle-active');
        });

        // Admin Vouchers Management
        Route::prefix('vouchers')->name('vouchers.')->group(function () {
            Route::get('/', [AdminVoucherController::class, 'index'])->name('index');
            Route::get('/create', [AdminVoucherController::class, 'create'])->name('create');
            Route::post('/', [AdminVoucherController::class, 'store'])->name('store');
            Route::get('/{voucher}/edit', [AdminVoucherController::class, 'edit'])->name('edit');
            Route::put('/{voucher}', [AdminVoucherController::class, 'update'])->name('update');
            Route::delete('/{voucher}', [AdminVoucherController::class, 'destroy'])->name('destroy');
            Route::match(['POST', 'PATCH'], '/{voucher}/toggle-active', [AdminVoucherController::class, 'toggleActive'])->name('toggle-active');
        });

        // Admin Finances Management (Track Pengeluaran & Scan Struk)
        Route::prefix('finances')->name('finances.')->group(function () {
            Route::get('/', [AdminFinanceController::class, 'index'])->name('index');
            Route::get('/create', [AdminFinanceController::class, 'create'])->name('create');
            Route::post('/', [AdminFinanceController::class, 'store'])->name('store');
            Route::delete('/{finance}', [AdminFinanceController::class, 'destroy'])->name('destroy');
        });

        // Admin Users Management
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [AdminUserController::class, 'index'])->name('index');
            Route::delete('/{user}', [AdminUserController::class, 'destroy'])->name('destroy');
            Route::match(['POST', 'PATCH'], '/{user}/toggle-active', [AdminUserController::class, 'toggleActive'])->name('toggle-active');
        });

        // Admin Profile Management
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [AdminProfileController::class, 'edit'])->name('edit');
            Route::patch('/', [AdminProfileController::class, 'update'])->name('update');
            Route::put('/password', [AdminProfileController::class, 'updatePassword'])->name('password.update');
        });
    });

Route::middleware(['auth'])->prefix('profile')->name('profile.')->group(function () {
    Route::get('/', [ProfileController::class, 'edit'])->name('edit');
    Route::patch('/', [ProfileController::class, 'update'])->name('update');
    Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
});

// =============================================================
// API ENDPOINT — Untuk request AJAX dari Alpine.js
// =============================================================

// Route::middleware('auth')->prefix('api')->name('api.')->group(function () {

//     // Cek ketersediaan slot
//     Route::get('/slot', [\App\Http\Controllers\Api\SlotController::class, 'check'])->name('slots.check');

//     // Validasi voucher
//     Route::post('/voucher/validasi', [\App\Http\Controllers\Api\VoucherController::class, 'validate'])->name('vouchers.validate');

// });

require __DIR__.'/auth.php';
