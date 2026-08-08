<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\User\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\BookingController;
use App\Http\Controllers\User\TreatmentController;

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


    Route::get('/reset-password', [AuthenticatedSessionController::class, 'showResetPasswordForm'])->name('password.request');
    Route::post('/reset-password', [AuthenticatedSessionController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthenticatedSessionController::class, 'showResetPasswordFormWithToken'])->name('password.reset');
    Route::post('/reset-password/{token}', [AuthenticatedSessionController::class, 'resetPassword'])->name('password.update');
});

// Logout (membutuhkan autentikasi)
Route::post('/logout', [AuthenticatedSessionController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');


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
 
    Route::get('/treatments/search', [TreatmentController::class, 'search'])
    ->middleware('throttle:30,1')
    ->name('treatments.search');
//     // Treatment (browse & detail)
//     Route::get('/treatment', [\App\Http\Controllers\User\TreatmentController::class, 'index'])->name('treatments.index');
//     Route::get('/treatment/{slug}', [\App\Http\Controllers\User\TreatmentController::class, 'show'])->name('treatments.show');

//     // Booking
//     Route::get('/booking', [\App\Http\Controllers\User\BookingController::class, 'index'])->name('bookings.index');
//     Route::get('/booking/buat', [\App\Http\Controllers\User\BookingController::class, 'create'])->name('bookings.create');
//     Route::post('/booking', [\App\Http\Controllers\User\BookingController::class, 'store'])->name('bookings.store');
//     Route::get('/booking/{booking}', [\App\Http\Controllers\User\BookingController::class, 'show'])->name('bookings.show');
//     Route::patch('/booking/{booking}/batalkan', [\App\Http\Controllers\User\BookingController::class, 'cancel'])->name('bookings.cancel');
   
});


// =============================================================
// AREA ADMIN — Harus login + is_admin = true
// =============================================================

// Route::middleware(['auth', \App\Http\Middleware\AdminMiddleware::class])
//     ->prefix('admin')
//     ->name('admin.')
//     ->group(function () {

//         // Dashboard admin
//         Route::get('/', [\App\Http\Controllers\Admin\AdminDashboardController::class, 'index'])->name('dashboard');

//         // Manajemen booking
//         Route::get('/booking', [\App\Http\Controllers\Admin\AdminBookingController::class, 'index'])->name('bookings.index');
//         Route::get('/booking/{booking}', [\App\Http\Controllers\Admin\AdminBookingController::class, 'show'])->name('bookings.show');
//         Route::patch('/booking/{booking}/konfirmasi', [\App\Http\Controllers\Admin\AdminBookingController::class, 'confirm'])->name('bookings.confirm');
//         Route::patch('/booking/{booking}/selesai', [\App\Http\Controllers\Admin\AdminBookingController::class, 'complete'])->name('bookings.complete');
//         Route::patch('/booking/{booking}/batalkan', [\App\Http\Controllers\Admin\AdminBookingController::class, 'cancel'])->name('bookings.cancel');
//         Route::patch('/booking/{booking}/verifikasi-pembayaran', [\App\Http\Controllers\Admin\AdminBookingController::class, 'verifyPayment'])->name('bookings.verify-payment');

//     });

    Route::prefix('profile')->name('profile.')->group(function () {
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