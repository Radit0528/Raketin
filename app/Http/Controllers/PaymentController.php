<?php

// Namespace controller
namespace App\Http\Controllers;

// Import model yang digunakan
use App\Models\Event;
use App\Models\Lapangan;
use App\Models\Transaction;

// Import Request
use Illuminate\Http\Request;

// Import Auth untuk user login
use Illuminate\Support\Facades\Auth;

// Import konfigurasi Midtrans
use Midtrans\Config;
use Midtrans\Snap;

// Deklarasi PaymentController
class PaymentController extends Controller
{
    /**
     * Konfigurasi Midtrans
     */
    public function __construct()
    {
        // Set server key Midtrans
        Config::$serverKey = config('midtrans.server_key');

        // Set client key Midtrans
        Config::$clientKey = config('midtrans.client_key');

        // Mode sandbox (false = production)
        Config::$isProduction = false;

        // Aktifkan sanitasi data
        Config::$isSanitized = true;

        // Aktifkan 3DS untuk kartu kredit
        Config::$is3ds = true;
    }

    /* ==========================================================
     * CHECKOUT EVENT
     * ========================================================== */
    public function eventCheckout(Request $request, $id)
    {
        // Ambil data event
        $event = Event::findOrFail($id);

        // Simpan transaksi event ke database
        $transaction = Transaction::create([
            'user_id' => Auth::id(),                    // User yang login
            'event_id' => $event->id,                  // Relasi event
            'nama' => $request->nama,                  // Nama peserta
            'email' => $request->email,                // Email peserta
            'phone' => $request->phone,                // Nomor HP
            'amount' => $event->biaya_pendaftaran,     // Total bayar
            'status_pembayaran' => 'pending',          // Status awal
        ]);

        // Parameter transaksi untuk Midtrans
        $params = [
            'transaction_details' => [
                'order_id' => 'EV-' . $transaction->id . '-' . time(), // ID unik
                'gross_amount' => $event->biaya_pendaftaran,           // Total bayar
            ],
            'customer_details' => [
                'first_name' => $transaction->nama,
                'email' => $transaction->email,
                'phone' => $transaction->phone,
            ],
            'item_details' => [
                [
                    'id' => $event->id,
                    'price' => $event->biaya_pendaftaran,
                    'quantity' => 1,
                    'name' => 'Pendaftaran Event: ' . $event->nama,
                ],
            ],
        ];

        // Generate Snap Token Midtrans
        $snapToken = Snap::getSnapToken($params);

        // Kirim token ke frontend
        return response()->json([
            'success' => true,
            'snap_token' => $snapToken,
        ]);
    }

    /* ==========================================================
     * CHECKOUT LAPANGAN
     * ========================================================== */
    public function lapanganCheckout(Request $request, $id)
    {
        // Ambil data lapangan
        $lapangan = Lapangan::findOrFail($id);

        // Jam mulai & selesai
        $mulai = $request->jam_mulai;
        $selesai = $request->jam_selesai;

        // Hitung durasi sewa (jam)
        $durasi = (strtotime($selesai) - strtotime($mulai)) / 3600;

        // Hitung total harga
        $totalHarga = $lapangan->harga_per_jam * $durasi;

        // Simpan transaksi lapangan
        $transaction = Transaction::create([
            'user_id' => Auth::id(),                 // User login
            'lapangan_id' => $lapangan->id,          // Relasi lapangan
            'nama' => $request->nama_customer,
            'email' => $request->email_customer,
            'phone' => $request->phone_customer,
            'tanggal' => $request->tanggal_booking, // Tanggal booking
            'jam_mulai' => $mulai,
            'jam_selesai' => $selesai,
            'durasi' => $durasi,
            'amount' => $totalHarga,
            'status_pembayaran' => 'pending',
        ]);

        // Parameter Midtrans
        $params = [
            'transaction_details' => [
                'order_id' => 'LP-' . $transaction->id . '-' . time(),
                'gross_amount' => $totalHarga,
            ],
            'customer_details' => [
                'first_name' => $transaction->nama,
                'email' => $transaction->email,
                'phone' => $transaction->phone,
            ],
            'item_details' => [
                [
                    'id' => $lapangan->id,
                    'price' => $lapangan->harga_per_jam,
                    'quantity' => $durasi,
                    'name' => 'Sewa Lapangan: ' . $lapangan->nama,
                ],
            ],
        ];

        // Generate Snap Token
        $snapToken = Snap::getSnapToken($params);

        // Response ke frontend
        return response()->json([
            'success' => true,
            'snap_token' => $snapToken,
            'total_harga' => $totalHarga,
        ]);
    }

    /**
     * Halaman finish pembayaran
     */
    public function finish(Request $request)
    {
        // Ambil order_id dari Midtrans
        $orderId = $request->order_id;

        // Ambil ID transaksi dari order_id
        $id = explode('-', $orderId)[1];

        // Ambil transaksi
        $transaction = Transaction::find($id);

        // Tampilkan halaman finish
        return view('payment.finish', compact('transaction'));
    }

    /**
     * Callback Midtrans (webhook)
     */
    public function callback(Request $request)
    {
        // Ambil semua data notifikasi
        $notif = $request->all();

        // Data penting dari Midtrans
        $orderId = $notif['order_id'];
        $status = $notif['transaction_status'];
        $paymentType = $notif['payment_type'];
        $fraud = $notif['fraud_status'] ?? null;

        // Ambil ID transaksi dari order_id
        $id = explode('-', $orderId)[1];
        $transaction = Transaction::find($id);

        // Jika transaksi tidak ditemukan
        if (!$transaction) {
            return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
        }

        // Update status pembayaran
        if ($status == 'capture') {
            if ($paymentType == 'credit_card') {
                $transaction->status_pembayaran =
                    ($fraud == 'challenge') ? 'challenge' : 'success';
            }
        } elseif ($status == 'settlement') {
            $transaction->status_pembayaran = 'success';
        } elseif ($status == 'pending') {
            $transaction->status_pembayaran = 'pending';
        } elseif (in_array($status, ['deny', 'cancel', 'expire'])) {
            $transaction->status_pembayaran = 'failed';
        }

        // Simpan perubahan
        $transaction->save();

        // Response ke Midtrans
        return response()->json(['message' => 'Callback processed']);
    }
}
