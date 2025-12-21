<?php

// Namespace controller
namespace App\Http\Controllers;

// Import model Lapangan
use App\Models\Lapangan;

// Import Request untuk menangani input user
use Illuminate\Http\Request;

// Deklarasi LapanganController
class LapanganController extends Controller
{
    /**
     * Menampilkan daftar semua lapangan
     */
    public function index()
    {
        // Ambil semua data lapangan
        $lapangans = Lapangan::all();

        // Tampilkan halaman daftar lapangan
        return view('lapangan.index', compact('lapangans'));
    }

    /**
     * Menampilkan detail lapangan berdasarkan ID
     */
    public function show($id)
    {
        // Ambil data lapangan atau gagal jika tidak ditemukan
        $lapangan = Lapangan::findOrFail($id);

        // Tampilkan halaman detail lapangan
        return view('lapangan.detail', compact('lapangan'));
    }

    /**
     * Menampilkan halaman pemilihan waktu booking lapangan
     */
    public function pilihWaktu($id, Request $request)
    {
        // Ambil data lapangan
        $lapangan = Lapangan::findOrFail($id);

        // Ambil tanggal dari query string atau default hari ini
        $tanggalDipilih = $request->query(
            'tanggal',
            now()->format('Y-m-d')
        );

        // Tampilkan halaman pilih waktu
        return view(
            'lapangan.pilih-waktu',
            compact('lapangan', 'tanggalDipilih')
        );
    }

    /**
     * Pencarian lapangan berdasarkan keyword dan lokasi
     */
    public function search(Request $request)
    {
        // Query dasar lapangan
        $query = Lapangan::query();

        // ===============================
        // Filter berdasarkan keyword
        // ===============================
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;

            $query->where(function ($q) use ($keyword) {
                $q->where('nama', 'like', "%{$keyword}%")
                  ->orWhere('lokasi', 'like', "%{$keyword}%");
            });
        }

        // ===============================
        // Filter berdasarkan lokasi
        // ===============================
        if ($request->filled('lokasi')) {
            $query->where(
                'lokasi',
                'like',
                '%' . $request->lokasi . '%'
            );
        }

        // Ambil hasil pencarian
        $lapangans = $query->get();

        // Tampilkan halaman hasil pencarian
        return view('lapangan.search', compact('lapangans'));
    }

    /**
     * Menampilkan halaman checkout booking lapangan
     */
    public function checkout($id)
    {
        // Ambil data lapangan
        $lapangan = Lapangan::findOrFail($id);

        // Tampilkan halaman checkout
        return view('lapangan.checkout', compact('lapangan'));
    }

    /**
     * Mengecek ketersediaan lapangan (AJAX)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkAvailability(Request $request, $id)
    {
        // Validasi input dari AJAX
        $request->validate([
            'tanggal_booking' => 'required|date', // Tanggal booking
            'jam_mulai'       => 'required',       // Jam mulai
            'jam_selesai'     => 'required',       // Jam selesai
        ]);

        // Ambil data lapangan
        $lapangan = Lapangan::findOrFail($id);

        // Cek ketersediaan lapangan menggunakan method di model
        $isAvailable = $lapangan->isAvailable(
            $request->tanggal_booking,
            $request->jam_mulai,
            $request->jam_selesai
        );

        // Kembalikan response JSON ke frontend
        return response()->json([
            'available' => $isAvailable,
            'message' => $isAvailable
                ? 'Lapangan tersedia pada waktu yang dipilih'
                : 'Lapangan sudah dibooking pada waktu tersebut. Silakan pilih waktu lain.',
        ]);
    }
}
