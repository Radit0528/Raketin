<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Lapangan;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    /**
     * ================================
     * MENAMPILKAN HALAMAN CHECKOUT LAPANGAN
     * ================================
     */
    public function checkoutLapangan($id, Request $request)
    {
        // Ambil data lapangan berdasarkan ID
        $lapangan = Lapangan::findOrFail($id);

        // Ambil detail booking dari query parameter URL
        $tanggal = $request->query('tanggal');
        $start   = $request->query('start');
        $end     = $request->query('end');
        $durasi  = $request->query('durasi');

        // Kirim data ke view checkout lapangan
        return view('lapangan.checkout', compact(
            'lapangan',
            'tanggal',
            'start',
            'end',
            'durasi'
        ));
    }

    /**
     * ================================
     * MENAMPILKAN HALAMAN CHECKOUT EVENT
     * ================================
     */
    public function checkoutEvent($id)
    {
        // Ambil data event
        $event = Event::findOrFail($id);

        // Tampilkan halaman checkout event
        return view('event.checkout', compact('event'));
    }

    /**
     * ================================
     * PROSES CHECKOUT LAPANGAN
     * - Simpan transaksi
     * - Generate Snap Token Midtrans
     * ================================
     */
    public function processLapangan(Request $request, $id)
    {
        // Validasi input dari form
        $request->validate([
            'nama_customer'   => 'required|string|max:255',
            'email_customer'  => 'required|email',
            'phone_customer'  => 'required|string|max:20',
            'tanggal_booking' => 'required|date',
            'jam_mulai'       => 'required',
            'jam_selesai'     => 'required',
            'durasi_jam'      => 'required|integer|min:1',
        ]);

        // Ambil data lapangan
        $lapangan = Lapangan::findOrFail($id);

        // Hitung total harga
        $totalAmount = $lapangan->harga_per_jam * $request->durasi_jam;

        try {
            // Mulai transaksi database
            DB::beginTransaction();

            // Generate Order ID unik
            $orderId = 'TRX-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));

            // Simpan transaksi ke database
            $transaction = Transaction::create([
                'order_id'     => $orderId,
                'type'         => 'lapangan',
                'lapangan_id'  => $lapangan->id,
                'user_name'    => $request->nama_customer,
                'user_email'   => $request->email_customer,
                'user_phone'   => $request->phone_customer,
                'amount'       => $totalAmount,
                'status'       => 'pending',
                'booking_details' => [
                    'lapangan_nama'   => $lapangan->nama,
                    'lapangan_lokasi'=> $lapangan->lokasi,
                    'tanggal_booking'=> $request->tanggal_booking,
                    'jam_mulai'      => $request->jam_mulai,
                    'jam_selesai'    => $request->jam_selesai,
                    'durasi_jam'     => $request->durasi_jam,
                    'harga_per_jam'  => $lapangan->harga_per_jam,
                ],
            ]);

            // Generate Snap Token Midtrans
            $snapToken = $this->getMidtransSnapToken($transaction, $lapangan);

            // Simpan Snap Token ke database
            $transaction->snap_token = $snapToken;
            $transaction->save();

            // Commit transaksi database
            DB::commit();

            // Response ke frontend
            return response()->json([
                'success'    => true,
                'snap_token'=> $snapToken,
                'order_id'  => $orderId,
            ]);

        } catch (\Exception $e) {
            // Batalkan semua query jika error
            DB::rollBack();

            // Simpan error ke log
            Log::error('Process Lapangan Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * ================================
     * PROSES CHECKOUT EVENT
     * ================================
     */
    public function processEvent(Request $request, $id)
    {
        // Validasi data customer
        $request->validate([
            'nama_customer'  => 'required|string|max:255',
            'email_customer' => 'required|email',
            'phone_customer' => 'required|string|max:20',
        ]);

        // Ambil data event
        $event = Event::findOrFail($id);

        try {
            DB::beginTransaction();

            // Generate Order ID
            $orderId = 'TRX-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));

            // Simpan transaksi event
            $transaction = Transaction::create([
                'order_id'   => $orderId,
                'type'       => 'event',
                'event_id'   => $event->id,
                'user_name'  => $request->nama_customer,
                'user_email' => $request->email_customer,
                'user_phone' => $request->phone_customer,
                'amount'     => $event->biaya_pendaftaran,
                'status'     => 'pending',
                'booking_details' => [
                    'event_nama'     => $event->nama_event,
                    'event_lokasi'  => $event->lokasi,
                    'tanggal_mulai' => $event->tanggal_mulai,
                    'tanggal_selesai'=> $event->tanggal_selesai,
                ],
            ]);

            // Generate Snap Token
            $snapToken = $this->getMidtransSnapToken($transaction, $event);

            // Simpan Snap Token
            $transaction->snap_token = $snapToken;
            $transaction->save();

            DB::commit();

            return response()->json([
                'success'    => true,
                'snap_token'=> $snapToken,
                'order_id'  => $orderId,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Process Event Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * ================================
     * GENERATE SNAP TOKEN MIDTRANS
     * ================================
     */
    private function getMidtransSnapToken($transaction, $item)
    {
        // Konfigurasi Midtrans
        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        \Midtrans\Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        // Tentukan item berdasarkan tipe transaksi
        if ($transaction->type === 'lapangan') {
            $itemName     = 'Booking ' . $item->nama . ' - ' . $transaction->booking_details['durasi_jam'] . ' Jam';
            $itemPrice    = (int) $item->harga_per_jam;
            $itemQuantity = (int) $transaction->booking_details['durasi_jam'];
        } else {
            $itemName     = 'Pendaftaran ' . $item->nama_event;
            $itemPrice    = (int) $item->biaya_pendaftaran;
            $itemQuantity = 1;
        }

        // Parameter ke Midtrans
        $params = [
            'transaction_details' => [
                'order_id'     => $transaction->order_id,
                'gross_amount'=> (int) $transaction->amount,
            ],
            'item_details' => [[
                'id'       => strtoupper($transaction->type) . '-' . $item->id,
                'price'    => $itemPrice,
                'quantity' => $itemQuantity,
                'name'     => $itemName,
            ]],
            'customer_details' => [
                'first_name'=> $transaction->user_name,
                'email'     => $transaction->user_email,
                'phone'     => $transaction->user_phone,
            ],
        ];

        // Generate Snap Token
        return \Midtrans\Snap::getSnapToken($params);
    }
}
