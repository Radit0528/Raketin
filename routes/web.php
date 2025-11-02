<?php

// routes/web.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController; // Tambahkan import ini
use App\Http\Controllers\Admin\LapanganController as AdminLapanganController;   // Tambahkan import ini
use App\Http\Controllers\Admin\EventController;     // Tambahkan import ini
use App\Http\Controllers\HomeController;     // Tambahkan import ini
use App\Http\Controllers\LapanganController;
use App\Http\Controllers\EventController as PublicEventController;




// routes/web.php

// ... imports Controller lainnya
use App\Http\Middleware\EnsureUserIsAdmin; // TAMBAHKAN INI


Route::get('/', [HomeController::class, 'index'])->name('dashboard');

// Rute Otentikasi (Publik)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);


// Rute Setelah Login (Dilindungi oleh 'auth')
Route::middleware('auth')->group(function () {
    // Logout harus menggunakan POST untuk keamanan
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout'); 
    
    // Rute Dashboard User Biasa
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard'); 
    
});


// Rute Admin (Dilindungi oleh 'auth' dan 'admin')
Route::middleware(['auth', EnsureUserIsAdmin::class])->prefix('admin')->group(function () {

    // Dashboard Admin (Akses: /admin)
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Pengelolaan Resource
    Route::resource('lapangan', AdminLapanganController::class);
    Route::resource('event', EventController::class);
});

Route::get('/cari-lapangan', [LapanganController::class, 'search'])->name('lapangan.search');

Route::get('/lapangan', [LapanganController::class, 'index'])->name('lapangan.index');
Route::get('/lapangan/{id}', [LapanganController::class, 'show'])->name('lapangan.detail');

// Route untuk halaman pilih waktu
Route::get('/lapangan/{id}/pilih-waktu', [App\Http\Controllers\LapanganController::class, 'pilihWaktu'])
    ->name('lapangan.pilihWaktu');

Route::get('/event', [PublicEventController::class, 'index'])->name('event.list');
Route::get('/event/{id}', [PublicEventController::class, 'show'])->name('event.detail');

