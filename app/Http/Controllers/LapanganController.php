<?php

namespace App\Http\Controllers;

use App\Models\Lapangan;
use Illuminate\Http\Request;

class LapanganController extends Controller
{
    public function index()
    {
        $lapangans = Lapangan::all();

        return view('lapangan.index', compact('lapangans'));
    }

    public function show($id)
    {
        $lapangan = Lapangan::findOrFail($id);

        return view('lapangan.detail', compact('lapangan'));
    }

    public function pilihWaktu($id, Request $request)
    {
        $lapangan = Lapangan::findOrFail($id);
        $tanggalDipilih = $request->query('tanggal', now()->format('Y-m-d'));

        return view('lapangan.pilih-waktu', compact('lapangan', 'tanggalDipilih'));
    }

    public function search(Request $request)
    {
        $query = Lapangan::query();

        // Filter lokasi
        if ($request->filled('lokasi')) {
            $query->where('lokasi', 'like', '%'.$request->lokasi.'%');
        }

        // Filter jenis olahraga
        if ($request->filled('sport')) {
            $query->where('jenis_olahraga', $request->sport);
        }

        // Filter fasilitas (Indoor/Outdoor)
        if ($request->filled('fasilitas')) {
            $query->where('tipe_lapangan', $request->fasilitas);
        }

        // Ambil hasil
        $lapangans = $query->get();

        return view('lapangan.search', compact('lapangans'));
    }

    /**
     * Menampilkan halaman checkout untuk booking lapangan
     */
    public function checkout($id)
    {
        $lapangan = Lapangan::findOrFail($id);

        return view('lapangan.checkout', compact('lapangan'));
    }

    /**
     * Check availability of lapangan via AJAX
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkAvailability(Request $request, $id)
    {
        $request->validate([
            'tanggal_booking' => 'required|date',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
        ]);

        $lapangan = Lapangan::findOrFail($id);

        $isAvailable = $lapangan->isAvailable(
            $request->tanggal_booking,
            $request->jam_mulai,
            $request->jam_selesai
        );

        return response()->json([
            'available' => $isAvailable,
            'message' => $isAvailable
                ? 'Lapangan tersedia pada waktu yang dipilih'
                : 'Lapangan sudah dibooking pada waktu tersebut. Silakan pilih waktu lain.',
        ]);
    }
}
