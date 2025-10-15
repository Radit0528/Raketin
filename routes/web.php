<?php

// routes/web.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController; // Tambahkan import ini
use App\Http\Controllers\Admin\LapanganController;   // Tambahkan import ini
use App\Http\Controllers\Admin\EventController;     // Tambahkan import ini
use App\Http\Controllers\HomeController;     // Tambahkan import ini


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
    Route::resource('lapangan', LapanganController::class);
    Route::resource('event', EventController::class);
});