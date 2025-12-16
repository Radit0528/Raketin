<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Lapangan;
use App\Models\User;
use App\Models\Transaction;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Statistik
        $totalLapangan = Lapangan::count();
        $totalEvent    = Event::count();
        $totalOwner    = User::where('role', 'owner')->count();
        $totalUser     = User::where('role', 'user')->count();

        // 2. Booking Hari Ini (PAKAI TANGGAL BOOKING)
        $bookingToday = Transaction::whereDate('tanggal', Carbon::today())->count();

        // 3. 5 Transaksi Terbaru
        $recentTransactions = Transaction::with(['user', 'lapangan'])
            ->latest()
            ->limit(5)
            ->get();

        // 4. Grafik Booking per Bulan 
        $rawData = Transaction::selectRaw('MONTH(created_at) as bulan, COUNT(*) as total')
            ->whereYear('created_at', now()->year)
            ->groupBy('bulan')
            ->pluck('total', 'bulan');

        $bookingPerMonth = [];
        for ($i = 1; $i <= 12; $i++) {
            $bookingPerMonth[] = $rawData[$i] ?? 0;
        }

        // 5. Status Transaksi (MIDTRANS)
        $taskPending = Transaction::where('status_pembayaran', 'pending')->count();
        $taskSuccess = Transaction::where('status_pembayaran', 'success')->count();
        $taskFailed  = Transaction::whereIn('status_pembayaran', ['expire', 'cancel', 'deny'])->count();

        $totalTask = $taskPending + $taskSuccess + $taskFailed;

        $taskPercentage = $totalTask > 0
            ? round(($taskSuccess / $totalTask) * 100) : 0;

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
