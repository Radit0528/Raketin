<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
}