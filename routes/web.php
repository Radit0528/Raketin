<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\LapanganController as AdminLapanganController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LapanganController;
use App\Http\Controllers\EventController as PublicEventController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;

use App\Http\Middleware\EnsureUserIsAdmin;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES (Tidak Perlu Login)
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('dashboard');

// Dashboard user TIDAK WAJIB LOGIN
Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('user.dashboard');

// AUTH
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// LAPANGAN PUBLIC
Route::get('/cari-lapangan', [LapanganController::class, 'search'])->name('lapangan.search');
Route::get('/lapangan/{id}', [LapanganController::class, 'show'])->name('lapangan.detail');
Route::get('/lapangan/{id}/pilih-waktu', [LapanganController::class, 'pilihWaktu'])->name('lapangan.pilihWaktu');

// EVENT PUBLIC
Route::get('/event', [PublicEventController::class, 'index'])->name('event.list');
Route::get('/cari-event', [PublicEventController::class, 'search'])->name('event.search');
Route::get('/event/{id}', [PublicEventController::class, 'show'])->name('event.detail');


/*
|--------------------------------------------------------------------------
| ROUTES YANG WAJIB LOGIN
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // CHECKOUT (WAJIB LOGIN)
    Route::get('/lapangan/{id}/checkout', [LapanganController::class, 'checkout'])
        ->name('lapangan.checkout');

    Route::get('/event/{id}/checkout', [PublicEventController::class, 'checkout'])
        ->name('event.checkout');

    // PAYMENT (WAJIB LOGIN)
    Route::post('/payment/event/{id}/checkout', [PaymentController::class, 'eventCheckout'])
        ->name('payment.event.checkout');

    Route::post('/payment/lapangan/{id}/checkout', [PaymentController::class, 'lapanganCheckout'])
        ->name('payment.lapangan.checkout');
    
    // Profile
    Route::get('/profil', [ProfileController::class, 'profile'])->name('profile');
    Route::get('/profil/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profil/update', [ProfileController::class, 'update'])->name('profile.update');
});


/*
|--------------------------------------------------------------------------
| PAYMENT CALLBACK (Tidak pakai login — untuk Midtrans)
|--------------------------------------------------------------------------
*/

Route::post('/payment/callback', [PaymentController::class, 'callback'])
    ->name('payment.callback');

// Payment Finish
Route::get('/payment/finish', [PaymentController::class, 'finish'])
    ->name('payment.finish');


/*
|--------------------------------------------------------------------------
| ADMIN ROUTES (WAJIB LOGIN + WAJIB ADMIN)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', EnsureUserIsAdmin::class])
    ->prefix('admin')
    ->group(function () {

        Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');

        Route::resource('lapangan', AdminLapanganController::class);
        Route::resource('event', AdminEventController::class);
    });
?>