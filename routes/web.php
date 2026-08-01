<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleAuthController;
Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::prefix('auth/google')->name('google.')->group(function () {
    // Redirect ke Google
    Route::get('/redirect', [GoogleAuthController::class, 'redirectToGoogle'])
        ->name('redirect');
    
    // Callback dari Google
    Route::get('/callback', [GoogleAuthController::class, 'handleGoogleCallback'])
        ->name('callback');
});
require __DIR__.'/auth.php';
