<?php

// Namespace controller utama (frontend)
namespace App\Http\Controllers;

// Import model Event
use App\Models\Event;

// Import Request untuk mengambil input dari form/search
use Illuminate\Http\Request;

// Deklarasi EventController
class EventController extends Controller
{
    /**
     * Menampilkan daftar event
     */
    public function index()
    {
        // Ambil semua event dan urutkan berdasarkan tanggal mulai terbaru
        $events = Event::orderBy('tanggal_mulai', 'desc')->get();

        // Kirim data event ke view event.index
        return view('event.index', compact('events'));
    }

    /**
     * Menampilkan detail event
     */
    public function show($id)
    {
        // Ambil data event berdasarkan ID
        // Jika tidak ditemukan, otomatis 404
        $event = Event::findOrFail($id);

        // Kirim data event ke view event.detail
        return view('event.detail', compact('event'));
    }

    /**
     * Pencarian event
     */
    public function search(Request $request)
    {
        // Query dasar model Event
        $query = Event::query();

        // ===============================
        // FILTER BERDASARKAN KEYWORD
        // ===============================
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;

            // Cari berdasarkan nama event atau lokasi
            $query->where(function ($q) use ($keyword) {
                $q->where('nama_event', 'like', "%{$keyword}%")
                  ->orWhere('lokasi', 'like', "%{$keyword}%");
            });
        }

        // ===============================
        // FILTER BERDASARKAN TANGGAL MULAI
        // ===============================
        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('tanggal_mulai', $request->tanggal_mulai);
        }

        // Ambil hasil pencarian dan urutkan dari tanggal terdekat
        $events = $query->orderBy('tanggal_mulai', 'desc')->get();

        // Kirim hasil pencarian ke view event.search
        return view('event.search', compact('events'));
    }

    /**
     * Menampilkan halaman checkout event
     */
    public function checkout($id)
    {
        // Ambil data event berdasarkan ID
        $event = Event::findOrFail($id);

        // Kirim data event ke view event.checkout
        return view('event.checkout', compact('event'));
    }
}
