<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Lapangan; // Pastikan Anda mengimpor Model Event
use Illuminate\Http\Request;

// Deklarasi HomeController yang mewarisi Controller Laravel
class HomeController extends Controller
{
    /**
     * Menampilkan halaman beranda (dashboard)
     * berisi lapangan dan event
     */
    public function index()
    {
        // Mengambil 4 data lapangan terbaru berdasarkan waktu dibuat
        $lapangans = Lapangan::orderBy('created_at', 'desc') // Urutkan dari terbaru
            ->take(4)                                       // Ambil 4 data saja
            ->get();                                        // Eksekusi query

        // Mengambil 3 data event berdasarkan tanggal mulai terbaru
        $events = Event::orderBy('tanggal_mulai', 'desc')   // Urutkan berdasarkan tanggal mulai
            ->take(3)                                       // Ambil 3 data
            ->get();                                        // Eksekusi query

        // Mengirim data lapangan dan event ke view dashboard
        return view('dashboard', compact('lapangans', 'events'));
    }

    /**
     * Fitur pencarian lapangan dan event
     */
    public function search(Request $request)
    {
        // Mengambil keyword pencarian dari parameter URL (?query=...)
        $query = $request->query('query');

        // Mencari data lapangan berdasarkan nama atau lokasi
        $lapangans = Lapangan::where('nama', 'like', "%{$query}%") // Cari berdasarkan nama
            ->orWhere('lokasi', 'like', "%{$query}%")              // Atau berdasarkan lokasi
            ->get();                                               // Ambil hasil pencarian

        // Mencari data event berdasarkan nama event atau lokasi
        $events = Event::where('nama_event', 'like', "%{$query}%") // Cari berdasarkan nama event
            ->orWhere('lokasi', 'like', "%{$query}%")              // Atau berdasarkan lokasi
            ->get();                                               // Ambil hasil pencarian

        // Mengirim hasil pencarian ke view dashboard
        return view('dashboard', compact('lapangans', 'events', 'query'));
    }
}
