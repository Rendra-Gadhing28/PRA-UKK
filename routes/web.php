<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\User\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\BookingController;
use App\Http\Controllers\User\TreatmentController;
use App\Http\Controllers\Webhooks\MidtransWebhookController;

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

    Route::get('/', function(){
        return view('welcome');
    })->name('home');
    
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
    Route::get('/bookings/list', [BookingController::class, 'list'])
    ->middleware('throttle:60,1')
    ->name('bookings.list');
    Route::get('/treatments', [TreatmentController::class, 'index'])
    ->name('treatments.index');

    // Endpoint for AJAX checking booked slots
    Route::get('/slots/check', [\App\Http\Controllers\Api\SlotController::class, 'check'])->name('slots.check');

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


    // Pembayaran QRIS
    Route::get('/booking/{booking}/pembayaran', [BookingController::class, 'payment'])->name('bookings.payment');
    Route::get('/booking/{booking}/pembayaran/status', [BookingController::class, 'paymentStatus'])
        ->middleware('throttle:120,1') // dipanggil tiap 5 detik oleh polling
        ->name('bookings.payment.status');

    Route::get('/booking/{booking}', [BookingController::class, 'show'])->name('bookings.show');
    Route::patch('/booking/{booking}/batalkan', [BookingController::class, 'cancel'])->name('bookings.cancel');
   
});


// =============================================================
// WEBHOOK — Notifikasi server-to-server dari Midtrans (tanpa auth/CSRF)
// =============================================================

Route::post('/webhooks/midtrans', [MidtransWebhookController::class, 'handle'])
    ->name('webhooks.midtrans');



// =============================================================
// AREA ADMIN — Harus login + is_admin = true
// =============================================================

Route::middleware(['auth', \App\Http\Middleware\AdminMiddleware::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Dashboard admin
        Route::get('/dashboard', [\App\Http\Controllers\Admin\AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/export/pdf', [\App\Http\Controllers\Admin\AdminDashboardController::class, 'exportPdf'])->name('export.pdf');
        Route::get('/export/excel', [\App\Http\Controllers\Admin\AdminDashboardController::class, 'exportExcel'])->name('export.excel');

        // Admin Booking Management
        Route::prefix('bookings')->name('bookings.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\AdminBookingController::class, 'index'])->name('index');
            Route::get('/export/pdf', [\App\Http\Controllers\Admin\AdminBookingController::class, 'exportPdf'])->name('export.pdf');
            Route::get('/export/excel', [\App\Http\Controllers\Admin\AdminBookingController::class, 'exportExcel'])->name('export.excel');
            Route::get('/{booking}', [\App\Http\Controllers\Admin\AdminBookingController::class, 'show'])->name('show');
            Route::get('/{booking}/receipt', [\App\Http\Controllers\Admin\AdminBookingController::class, 'receipt'])->name('receipt');
            Route::patch('/{booking}/status', [\App\Http\Controllers\Admin\AdminBookingController::class, 'updateStatus'])->name('update-status');
            Route::patch('/{booking}/verify-payment', [\App\Http\Controllers\Admin\AdminBookingController::class, 'verifyPayment'])->name('verify-payment');
        });

        // Admin Treatments Management
        Route::prefix('treatments')->name('treatments.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\AdminTreatmentController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\AdminTreatmentController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\AdminTreatmentController::class, 'store'])->name('store');
            Route::get('/{treatment}/edit', [\App\Http\Controllers\Admin\AdminTreatmentController::class, 'edit'])->name('edit');
            Route::put('/{treatment}', [\App\Http\Controllers\Admin\AdminTreatmentController::class, 'update'])->name('update');
            Route::delete('/{treatment}', [\App\Http\Controllers\Admin\AdminTreatmentController::class, 'destroy'])->name('destroy');
            Route::patch('/{treatment}/toggle-active', [\App\Http\Controllers\Admin\AdminTreatmentController::class, 'toggleActive'])->name('toggle-active');
        });

        // Admin Beauticians Management
        Route::prefix('beauticians')->name('beauticians.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\AdminBeauticianController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\AdminBeauticianController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\AdminBeauticianController::class, 'store'])->name('store');
            Route::get('/{beautician}', [\App\Http\Controllers\Admin\AdminBeauticianController::class, 'show'])->name('show');
            Route::get('/{beautician}/edit', [\App\Http\Controllers\Admin\AdminBeauticianController::class, 'edit'])->name('edit');
            Route::put('/{beautician}', [\App\Http\Controllers\Admin\AdminBeauticianController::class, 'update'])->name('update');
            Route::delete('/{beautician}', [\App\Http\Controllers\Admin\AdminBeauticianController::class, 'destroy'])->name('destroy');
            Route::patch('/{beautician}/toggle-active', [\App\Http\Controllers\Admin\AdminBeauticianController::class, 'toggleActive'])->name('toggle-active');
        });

        // Admin Vouchers Management
        Route::prefix('vouchers')->name('vouchers.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\AdminVoucherController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\AdminVoucherController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\AdminVoucherController::class, 'store'])->name('store');
            Route::get('/{voucher}/edit', [\App\Http\Controllers\Admin\AdminVoucherController::class, 'edit'])->name('edit');
            Route::put('/{voucher}', [\App\Http\Controllers\Admin\AdminVoucherController::class, 'update'])->name('update');
            Route::delete('/{voucher}', [\App\Http\Controllers\Admin\AdminVoucherController::class, 'destroy'])->name('destroy');
            Route::patch('/{voucher}/toggle-active', [\App\Http\Controllers\Admin\AdminVoucherController::class, 'toggleActive'])->name('toggle-active');
        });

        // Admin Finances Management (Track Pengeluaran & Scan Struk)
        Route::prefix('finances')->name('finances.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\AdminFinanceController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\AdminFinanceController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\AdminFinanceController::class, 'store'])->name('store');
            Route::delete('/{finance}', [\App\Http\Controllers\Admin\AdminFinanceController::class, 'destroy'])->name('destroy');
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