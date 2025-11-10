<?php

namespace App\Http\Controllers;

use App\Models\Lapangan;
use App\Models\Event; // Pastikan Anda mengimpor Model Event
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Menampilkan halaman beranda dengan Lapangan dan Event Mendatang.
     */
    public function index()
    {
        // Mengambil 4 Lapangan terbaru
        $lapangans = Lapangan::orderBy('created_at', 'desc')->take(4)->get();
        
        // Mengambil Event yang akan datang (tanggal_mulai >= hari ini)
        // dan mengurutkannya berdasarkan tanggal terdekat.
        $events = Event::orderBy('tanggal_mulai', 'desc')
                       ->take(3)
                       ->get();

        return view('dashboard', compact('lapangans', 'events'));
    }
}
