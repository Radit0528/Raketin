<?php

// Namespace controller khusus owner
namespace App\Http\Controllers\Owner;

// Import controller utama
use App\Http\Controllers\Controller;

// Import Request (jika nanti dibutuhkan)
use Illuminate\Http\Request;

// Import Auth untuk mengambil user yang sedang login
use Illuminate\Support\Facades\Auth;

// Import model Transaction
use App\Models\Transaction;

// Import model Lapangan
use App\Models\Lapangan;

// Deklarasi DashboardController untuk owner
class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard owner
     */
    public function index()
    {
        // Ambil data owner yang sedang login
        $owner = Auth::user();

        // ===============================
        // DATA LAPANGAN MILIK OWNER
        // ===============================

        // Ambil semua lapangan yang dimiliki oleh owner
        $lapangans = $owner->lapangans;

        // Ambil ID lapangan untuk query transaksi
        $lapanganIds = $lapangans->pluck('id');

        // ===============================
        // QUERY DASAR TRANSAKSI
        // ===============================

        // Ambil transaksi yang:
        // - milik lapangan owner
        // - status pembayaran sukses
        // - terjadi di bulan ini
        $baseQuery = Transaction::whereIn('lapangan_id', $lapanganIds) // pakai lapangan_id
            ->where('status_pembayaran', 'success')
            ->whereMonth('created_at', now()->month);

        // ===============================
        // KINERJA KEUANGAN
        // ===============================

        // Total pendapatan bulan ini (pakai kolom amount)
        $totalPendapatanBulanIni = (clone $baseQuery)->sum('amount');

        // ===============================
        // KINERJA PEMESANAN
        // ===============================

        // Total pemesanan bulan ini
        $totalPemesananBulanIni = (clone $baseQuery)->count();

        // ===============================
        // TRANSAKSI TERBARU
        // ===============================

        // Ambil 10 transaksi terbaru untuk lapangan owner
        $latestTransactions = Transaction::whereIn('lapangan_id', $lapanganIds)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        // Kirim data ke view owner.dashboard
        return view('owner.dashboard', compact(
            'lapangans',
            'totalPendapatanBulanIni',
            'totalPemesananBulanIni',
            'latestTransactions'
        ));
    }

    /**
     * Update status pembayaran transaksi oleh owner
     */
    public function updateStatus(Transaction $transaction)
    {
        // ===============================
        // VALIDASI KEPEMILIKAN TRANSAKSI
        // ===============================

        // Pastikan transaksi memiliki lapangan
        // dan lapangan tersebut milik owner yang login
        if (
            !$transaction->lapangan ||
            $transaction->lapangan->user_id !== Auth::id()
        ) {
            abort(403); // Akses ditolak
        }

        // ===============================
        // CEGAH UPDATE GANDA
        // ===============================

        // Jika status sudah success, batalkan update
        if ($transaction->status_pembayaran === 'success') {
            return back()->with('info', 'Transaksi sudah sukses.');
        }

        // ===============================
        // UPDATE STATUS PEMBAYARAN
        // ===============================

        // Ubah status pembayaran menjadi success
        $transaction->update([
            'status_pembayaran' => 'success'
        ]);

        // Kembali ke halaman sebelumnya
        return back()->with(
            'success',
            'Status pembayaran berhasil diperbarui.'
        );
    }
}
