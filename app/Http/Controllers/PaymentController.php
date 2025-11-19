<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Midtrans\Snap;
use Midtrans\Config;
use App\Models\Transaction;
use App\Models\Event;
use App\Models\Lapangan;

class PaymentController extends Controller
{
    public function __construct()
    {
        // Konfigurasi Midtrans global
        Config::$serverKey = config('midtrans.server_key');
        Config::$clientKey = config('midtrans.client_key');
        Config::$isProduction = false; // SANDBOX
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    /* ===================================================================
     * 📌 CHECKOUT EVENT
     * =================================================================== */
    public function eventCheckout(Request $request, $id)
    {
        $event = Event::findOrFail($id);

        // simpan transaksi
        $transaction = Transaction::create([
            'event_id' => $event->id,
            'nama' => $request->nama,
            'email' => $request->email,
            'phone' => $request->phone,
            'amount' => $event->harga,
            'status_pembayaran' => 'pending'
        ]);

        // Data Midtrans
        $params = [
            'transaction_details' => [
                'order_id' => 'EV-' . $transaction->id . '-' . time(),
                'gross_amount' => $event->harga,
            ],
            'customer_details' => [
                'first_name' => $transaction->nama,
                'email' => $transaction->email,
                'phone' => $transaction->phone,
            ],
            'item_details' => [
                [
                    'id' => $event->id,
                    'price' => $event->harga,
                    'quantity' => 1,
                    'name' => "Pendaftaran Event: " . $event->nama,
                ]
            ]
        ];

        $snapToken = Snap::getSnapToken($params);

        return response()->json([
            'success' => true,
            'snap_token' => $snapToken
        ]);
    }

    /* ===================================================================
     * 📌 CHECKOUT LAPANGAN
     * =================================================================== */
    public function lapanganCheckout(Request $request, $id)
    {
        $lapangan = Lapangan::findOrFail($id);

        $mulai   = $request->jam_mulai;
        $selesai = $request->jam_selesai;

        // hitung durasi
        $durasi = (strtotime($selesai) - strtotime($mulai)) / 3600;
        $totalHarga = $lapangan->harga_per_jam * $durasi;

        // Simpan transaksi
        $transaction = Transaction::create([
            'lapangan_id' => $lapangan->id,
            'nama' => $request->nama_customer,
            'email' => $request->email_customer,
            'phone' => $request->phone_customer,
            'tanggal' => $request->tanggal_booking,
            'jam_mulai' => $mulai,
            'jam_selesai' => $selesai,
            'durasi' => $durasi,
            'amount' => $totalHarga,
            'status_pembayaran' => 'pending'
        ]);

        // Data Midtrans
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
                    'name' => "Sewa Lapangan: " . $lapangan->nama,
                ]
            ]
        ];

        $snapToken = Snap::getSnapToken($params);

        return response()->json([
            'success' => true,
            'snap_token' => $snapToken,
            'total_harga' => $totalHarga
        ]);
    }

    /* ===================================================================
     * 📌 FINISH PAGE
     * =================================================================== */
    public function finish(Request $request)
    {
        $orderId = $request->order_id;

        // Ambil ID dari format LP-12-173123123
        $id = explode('-', $orderId)[1];

        $transaction = Transaction::find($id);

        return view('payment.finish', compact('transaction'));
    }

    /* ===================================================================
     * 📌 CALLBACK MIDTRANS (AUTO UPDATE STATUS)
     * =================================================================== */
    public function callback(Request $request)
    {
        $notif = $request->all();

        $orderId = $notif['order_id'];
        $status = $notif['transaction_status'];
        $paymentType = $notif['payment_type'];
        $fraud = $notif['fraud_status'] ?? null;

        // Ambil ID transaksi dari format LP-12-xxxxx
        $id = explode('-', $orderId)[1];
        $transaction = Transaction::find($id);

        if (!$transaction) {
            return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
        }

        if ($status == 'capture') {
            if ($paymentType == 'credit_card') {
                if ($fraud == 'challenge') {
                    $transaction->status_pembayaran = 'challenge';
                } else {
                    $transaction->status_pembayaran = 'success';
                }
            }
        } elseif ($status == 'settlement') {
            $transaction->status_pembayaran = 'success';
        } elseif ($status == 'pending') {
            $transaction->status_pembayaran = 'pending';
        } elseif ($status == 'deny' || $status == 'cancel' || $status == 'expire') {
            $transaction->status_pembayaran = 'failed';
        }

        $transaction->save();

        return response()->json(['message' => 'Callback processed']);
    }
}
