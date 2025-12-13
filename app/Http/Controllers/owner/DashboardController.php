<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction; 
use App\Models\Lapangan;

class DashboardController extends Controller
{
    public function index()
    {
        $owner = Auth::user();
        
        // Dapatkan semua Lapangan yang dimiliki oleh user yang sedang login
        $lapangans = $owner->lapangans;
        $lapanganIds = $lapangans->pluck('id');

        // Mengambil data transaksi hanya untuk lapangan mereka
        $baseQuery = Transaction::whereIn('lapangan_id', $lapanganIds) // MENGGUNAKAN lapangan_id, bukan item_id
                                     ->where('status_pembayaran', 'success') 
                                     ->whereMonth('created_at', now()->month);
        
        // Umpan Balik Kinerja (Keuangan)
        // KOREKSI: Menggunakan kolom 'amount' bukan 'total_harga'
        $totalPendapatanBulanIni = (clone $baseQuery)->sum('amount'); 

        // Umpan Balik Kinerja (Pemesanan)
        $totalPemesananBulanIni = (clone $baseQuery)->count();
        
        // Ambil Transaksi Terbaru (untuk tabel notifikasi/aksi cepat)
        $latestTransactions = Transaction::whereIn('lapangan_id', $lapanganIds)
                                                    ->orderByDesc('created_at')
                                                    ->limit(10)
                                                    ->get();


        return view('owner.dashboard', compact(
            'lapangans', 
            'totalPendapatanBulanIni', 
            'totalPemesananBulanIni',
            'latestTransactions'
        ));
    }
    
    public function showJadwal(Lapangan $lapangan)
    {
        if ($lapangan->user_id !== Auth::id()) {
            abort(403);
        }

        // KOREKSI: Menggunakan successfulTransactions() dan pastikan total_harga diganti amount di view
        $bookings = $lapangan->successfulTransactions()->get(); 
        
        return view('owner.lapangan.jadwal', compact('lapangan', 'bookings'));
    }

    public function updateHarga(Request $request, Lapangan $lapangan)
    {
        if ($lapangan->user_id !== Auth::id()) {
            abort(403);
        }
        
        $request->validate(['harga_baru' => 'required|integer|min:0']);
        
        $lapangan->update(['harga_per_jam' => $request->harga_baru]);

        return back()->with('success', 'Harga dasar berhasil diperbarui.');
    }
    public function updateStatus(Request $request, Transaction $transaction)
    {
        // Pastikan transaksi milik lapangan owner
        if ($transaction->lapangan->user_id !== Auth::id()) {
            abort(403);
        }

        // Validasi hanya boleh ke success
        $request->validate([
            'status_pembayaran' => 'required|in:success'
        ]);

        // Cegah update ulang
        if ($transaction->status_pembayaran === 'success') {
            return back()->with('info', 'Transaksi sudah sukses.');
        }

        $transaction->update([
            'status_pembayaran' => 'success'
        ]);

        return back()->with('success', 'Status pembayaran berhasil diperbarui.');
    }

}