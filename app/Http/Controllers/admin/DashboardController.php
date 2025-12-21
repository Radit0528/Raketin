<?php

// Namespace controller admin
namespace App\Http\Controllers\Admin;

// Import controller utama Laravel
use App\Http\Controllers\Controller;

// Import model yang digunakan
use App\Models\Event;
use App\Models\Lapangan;
use App\Models\User;
use App\Models\Transaction;

// Import Carbon untuk manipulasi tanggal
use Carbon\Carbon;

// Deklarasi DashboardController
class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard admin
     */
    public function index()
    {
        // ==============================
        // 1. STATISTIK DATA
        // ==============================

        // Menghitung total lapangan
        $totalLapangan = Lapangan::count();

        // Menghitung total event
        $totalEvent = Event::count();

        // Menghitung total user dengan role owner
        $totalOwner = User::where('role', 'owner')->count();

        // Menghitung total user dengan role user
        $totalUser = User::where('role', 'user')->count();

        // ==============================
        // 2. BOOKING HARI INI
        // ==============================

        // Menghitung jumlah booking berdasarkan tanggal booking hari ini
        $bookingToday = Transaction::whereDate(
            'tanggal',
            Carbon::today()
        )->count();

        // ==============================
        // 3. 5 TRANSAKSI TERBARU
        // ==============================

        // Mengambil 5 transaksi terbaru beserta relasi user dan lapangan
        $recentTransactions = Transaction::with(['user', 'lapangan'])
            ->latest()     // Urutkan dari data terbaru
            ->limit(5)     // Batasi 5 data
            ->get();       // Eksekusi query

        // ==============================
        // 4. GRAFIK BOOKING PER BULAN
        // ==============================

        // Mengambil jumlah transaksi per bulan dalam 1 tahun
        $rawData = Transaction::selectRaw(
                'MONTH(created_at) as bulan, COUNT(*) as total'
            )
            ->whereYear('created_at', now()->year) // Filter tahun berjalan
            ->groupBy('bulan')                     // Kelompokkan per bulan
            ->pluck('total', 'bulan');             // Ambil data (bulan => total)

        // Menyiapkan array booking per bulan (Jan - Des)
        $bookingPerMonth = [];

        for ($i = 1; $i <= 12; $i++) {
            // Jika bulan tidak ada data, isi dengan 0
            $bookingPerMonth[] = $rawData[$i] ?? 0;
        }

        // ==============================
        // 5. STATUS TRANSAKSI (MIDTRANS)
        // ==============================

        // Menghitung transaksi dengan status pending
        $taskPending = Transaction::where(
            'status_pembayaran',
            'pending'
        )->count();

        // Menghitung transaksi dengan status success
        $taskSuccess = Transaction::where(
            'status_pembayaran',
            'success'
        )->count();

        // Menghitung transaksi gagal (expire, cancel, deny)
        $taskFailed = Transaction::whereIn(
            'status_pembayaran',
            ['expire', 'cancel', 'deny']
        )->count();

        // Total seluruh transaksi
        $totalTask = $taskPending + $taskSuccess + $taskFailed;

        // Menghitung persentase transaksi sukses
        $taskPercentage = $totalTask > 0
            ? round(($taskSuccess / $totalTask) * 100)
            : 0;

        // ==============================
        // KIRIM DATA KE VIEW
        // ==============================

        return view('admin.dashboard', compact(
            'totalLapangan',
            'totalEvent',
            'totalOwner',
            'totalUser',
            'bookingToday',
            'recentTransactions',
            'bookingPerMonth',
            'taskPending',
            'taskSuccess',
            'taskFailed',
            'taskPercentage'
        ));
    }
}
