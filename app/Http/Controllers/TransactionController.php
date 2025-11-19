<?php

namespace App\Http\Controllers;

use App\Models\Lapangan;
use App\Models\Event;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    /**
     * Show checkout page for Lapangan
     */
    public function checkoutLapangan($id, Request $request)
    {
        $lapangan = Lapangan::findOrFail($id);
        
        // Get booking details from query params
        $tanggal = $request->query('tanggal');
        $start = $request->query('start');
        $end = $request->query('end');
        $durasi = $request->query('durasi');
        
        return view('lapangan.checkout', compact('lapangan', 'tanggal', 'start', 'end', 'durasi'));
    }

    /**
     * Show checkout page for Event
     */
    public function checkoutEvent($id)
    {
        $event = Event::findOrFail($id);
        return view('event.checkout', compact('event'));
    }

    /**
     * Process checkout lapangan - Create transaction and get Snap Token
     */
    public function processLapangan(Request $request, $id)
    {
        $request->validate([
            'nama_customer' => 'required|string|max:255',
            'email_customer' => 'required|email',
            'phone_customer' => 'required|string|max:20',
            'tanggal_booking' => 'required|date',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'durasi_jam' => 'required|integer|min:1',
        ]);

        $lapangan = Lapangan::findOrFail($id);
        $totalAmount = $lapangan->harga_per_jam * $request->durasi_jam;

        try {
            DB::beginTransaction();

            // Generate Order ID
            $orderId = 'TRX-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));

            // Create transaction
            $transaction = Transaction::create([
                'order_id' => $orderId,
                'type' => 'lapangan',
                'lapangan_id' => $lapangan->id,
                'user_name' => $request->nama_customer,
                'user_email' => $request->email_customer,
                'user_phone' => $request->phone_customer,
                'amount' => $totalAmount,
                'status' => 'pending',
                'booking_details' => [
                    'lapangan_nama' => $lapangan->nama,
                    'lapangan_lokasi' => $lapangan->lokasi,
                    'tanggal_booking' => $request->tanggal_booking,
                    'jam_mulai' => $request->jam_mulai,
                    'jam_selesai' => $request->jam_selesai,
                    'durasi_jam' => $request->durasi_jam,
                    'harga_per_jam' => $lapangan->harga_per_jam,
                ],
            ]);

            // Get Snap Token
            $snapToken = $this->getMidtransSnapToken($transaction, $lapangan);

            // Save snap token
            $transaction->snap_token = $snapToken;
            $transaction->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'snap_token' => $snapToken,
                'order_id' => $orderId,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Process Lapangan Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Process checkout event - Create transaction and get Snap Token
     */
    public function processEvent(Request $request, $id)
    {
        $request->validate([
            'nama_customer' => 'required|string|max:255',
            'email_customer' => 'required|email',
            'phone_customer' => 'required|string|max:20',
        ]);

        $event = Event::findOrFail($id);

        try {
            DB::beginTransaction();

            // Generate Order ID
            $orderId = 'TRX-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));

            // Create transaction
            $transaction = Transaction::create([
                'order_id' => $orderId,
                'type' => 'event',
                'event_id' => $event->id,
                'user_name' => $request->nama_customer,
                'user_email' => $request->email_customer,
                'user_phone' => $request->phone_customer,
                'amount' => $event->biaya_pendaftaran,
                'status' => 'pending',
                'booking_details' => [
                    'event_nama' => $event->nama_event,
                    'event_lokasi' => $event->lokasi,
                    'tanggal_mulai' => $event->tanggal_mulai,
                    'tanggal_selesai' => $event->tanggal_selesai,
                ],
            ]);

            // Get Snap Token
            $snapToken = $this->getMidtransSnapToken($transaction, $event);

            // Save snap token
            $transaction->snap_token = $snapToken;
            $transaction->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'snap_token' => $snapToken,
                'order_id' => $orderId,
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
     * Get Snap Token from Midtrans
     */
    private function getMidtransSnapToken($transaction, $item)
    {
        // Set Midtrans Configuration
        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        \Midtrans\Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        // Prepare parameters
        if ($transaction->type === 'lapangan') {
            $itemName = 'Booking ' . $item->nama . ' - ' . $transaction->booking_details['durasi_jam'] . ' Jam';
            $itemPrice = (int) $item->harga_per_jam;
            $itemQuantity = (int) $transaction->booking_details['durasi_jam'];
        } else {
            $itemName = 'Pendaftaran ' . $item->nama_event;
            $itemPrice = (int) $item->biaya_pendaftaran;
            $itemQuantity = 1;
        }

        $params = [
            'transaction_details' => [
                'order_id' => $transaction->order_id,
                'gross_amount' => (int) $transaction->amount,
            ],
            'item_details' => [
                [
                    'id' => strtoupper($transaction->type) . '-' . $item->id,
                    'price' => $itemPrice,
                    'quantity' => $itemQuantity,
                    'name' => $itemName,
                ]
            ],
            'customer_details' => [
                'first_name' => $transaction->user_name,
                'email' => $transaction->user_email,
                'phone' => $transaction->user_phone,
            ],
        ];

        // Get Snap Token
        $snapToken = \Midtrans\Snap::getSnapToken($params);
        
        return $snapToken;
    }

    /**
     * Webhook from Midtrans
     */
    public function webhook(Request $request)
    {
        try {
            // Set config
            \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
            \Midtrans\Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);

            $notification = new \Midtrans\Notification();

            $orderId = $notification->order_id;
            $transactionStatus = $notification->transaction_status;
            $transactionId = $notification->transaction_id;
            $paymentType = $notification->payment_type;

            Log::info('Midtrans Webhook', [
                'order_id' => $orderId,
                'status' => $transactionStatus,
            ]);

            // Find transaction
            $transaction = Transaction::where('order_id', $orderId)->first();

            if (!$transaction) {
                return response()->json(['message' => 'Transaction not found'], 404);
            }

            // Update transaction
            $transaction->transaction_id = $transactionId;
            $transaction->payment_type = $paymentType;

            // Update status
            if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
                $transaction->status = 'success';
            } elseif ($transactionStatus == 'pending') {
                $transaction->status = 'pending';
            } elseif ($transactionStatus == 'deny' || $transactionStatus == 'cancel' || $transactionStatus == 'expire') {
                $transaction->status = 'failed';
            }

            $transaction->save();

            return response()->json(['message' => 'OK']);

        } catch (\Exception $e) {
            Log::error('Webhook Error: ' . $e->getMessage());
            return response()->json(['message' => 'Error'], 500);
        }
    }

    /**
     * Payment finish page
     */
    public function finish(Request $request)
    {
        $orderId = $request->order_id;

        if (!$orderId) {
            return redirect('/')->with('error', 'Order ID tidak ditemukan');
        }

        $transaction = Transaction::where('order_id', $orderId)->first();

        if (!$transaction) {
            return redirect('/')->with('error', 'Transaksi tidak ditemukan');
        }

        return view('payment.finish', compact('transaction'));
    }

    /**
     * Cancel transaction
     */
    public function cancel($id)
    {
        $transaction = Transaction::findOrFail($id);

        if (!in_array($transaction->status, ['pending'])) {
            return redirect()->back()->with('error', 'Transaksi tidak dapat dibatalkan');
        }

        $transaction->status = 'canceled';
        $transaction->save();

        return redirect('/')->with('success', 'Transaksi berhasil dibatalkan');
    }

    /**
     * Show transaction detail
     */
    public function show($id)
    {
        $transaction = Transaction::findOrFail($id);
        return view('transactions.show', compact('transaction'));
    }

    /**
     * List all transactions
     */
    public function index(Request $request)
    {
        $query = Transaction::query()->orderBy('created_at', 'desc');

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        if ($request->has('type') && $request->type != '') {
            $query->where('type', $request->type);
        }

        $transactions = $query->paginate(20);

        return view('transactions.index', compact('transactions'));
    }
}