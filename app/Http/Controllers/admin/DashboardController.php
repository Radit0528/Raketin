<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Lapangan;
use App\Models\User;
use App\Models\Transaction;

// use App\Models\Lapangan; // Opsional: Hapus jika belum digunakan
// use App\Models\Event; // Opsional: Hapus jika belum digunakan

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard utama admin.
     * Route: GET /admin
     * Nama Route: admin.dashboard
     */
    public function index()
    {
        // Jika Anda ingin menampilkan statistik, Anda bisa menambahkannya di sini:
        // $totalLapangans = Lapangan::count();
        // return view('admin.dashboard.index', compact('totalLapangans'));

        // Cukup memuat view yang berisi link Tambah Lapangan/Event
        return view('admin.dashboard');
    }
    public function dashboard()
{
    return view('admin.dashboard', [
        'totalLapangan' => Lapangan::count(),
        'totalEvent' => Event::count(),
        'totalUser' => User::count(),
        'bookingToday' => Transaction::whereDate('created_at', today())->count(),

        'activities' => [
            "Admin menambah event baru",
            "User Radit melakukan booking lapangan",
            "Event Futsal Cup akan dimulai besok",
        ],

        'jadwalHariIni' => [
            ['lapangan' => 'Lapangan A', 'waktu' => '15:00 - 17:00'],
            ['lapangan' => 'Lapangan B', 'waktu' => '18:00 - 20:00'],
        ]
    ]);
}

}
