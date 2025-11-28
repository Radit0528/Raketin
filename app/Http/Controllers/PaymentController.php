<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Lapangan;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;   // <── Tambahkan ini
use Midtrans\Config;
use Midtrans\Snap;

class PaymentController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$clientKey = config('midtrans.client_key');
        Config::$isProduction = false;
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
            'user_id' => Auth::id(),    // <── Tambah user_id
            'event_id' => $event->id,
            'nama' => $request->nama,
            'email' => $request->email,
            'phone' => $request->phone,
            'amount' => $event->biaya_pendaftaran,
            'status_pembayaran' => 'pending',
        ]);

        $params = [
            'transaction_details' => [
                'order_id' => 'EV-'.$transaction->id.'-'.time(),
                'gross_amount' => $event->biaya_pendaftaran,
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
                    'name' => 'Pendaftaran Event: '.$event->nama,
                ],
            ],
        ];

        $snapToken = Snap::getSnapToken($params);

        return response()->json([
            'success' => true,
            'snap_token' => $snapToken,
        ]);
    }

    /* ===================================================================
     * 📌 CHECKOUT LAPANGAN
     * =================================================================== */
    public function lapanganCheckout(Request $request, $id)
    {
        $lapangan = Lapangan::findOrFail($id);

        $mulai = $request->jam_mulai;
        $selesai = $request->jam_selesai;

        $durasi = (strtotime($selesai) - strtotime($mulai)) / 3600;
        $totalHarga = $lapangan->harga_per_jam * $durasi;

        $transaction = Transaction::create([
            'user_id' => Auth::id(),     // <── Tambah user_id
            'lapangan_id' => $lapangan->id,
            'nama' => $request->nama_customer,
            'email' => $request->email_customer,
            'phone' => $request->phone_customer,
            'tanggal' => $request->tanggal_booking,
            'jam_mulai' => $mulai,
            'jam_selesai' => $selesai,
            'durasi' => $durasi,
            'amount' => $totalHarga,
            'status_pembayaran' => 'pending',
        ]);

        $params = [
            'transaction_details' => [
                'order_id' => 'LP-'.$transaction->id.'-'.time(),
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
                    'name' => 'Sewa Lapangan: '.$lapangan->nama,
                ],
            ],
        ];

        $snapToken = Snap::getSnapToken($params);

        return response()->json([
            'success' => true,
            'snap_token' => $snapToken,
            'total_harga' => $totalHarga,
        ]);
    }

    public function finish(Request $request)
    {
        $orderId = $request->order_id;
        $id = explode('-', $orderId)[1];
        $transaction = Transaction::find($id);

        return view('payment.finish', compact('transaction'));
    }

    public function callback(Request $request)
    {
        $notif = $request->all();
        $orderId = $notif['order_id'];
        $status = $notif['transaction_status'];
        $paymentType = $notif['payment_type'];
        $fraud = $notif['fraud_status'] ?? null;

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
        } elseif (in_array($status, ['deny', 'cancel', 'expire'])) {
            $transaction->status_pembayaran = 'failed';
        }

        $transaction->save();

        return response()->json(['message' => 'Callback processed']);
    }
}
