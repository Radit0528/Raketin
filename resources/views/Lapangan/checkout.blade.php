<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Checkout Lapangan - {{ $lapangan->nama }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('midtrans.client_key') }}"></script>
</head>

<body>

    @php
    $tanggal = request('tanggal');
    $start = request('start');
    $end = request('end');
    $durasi = request('durasi');
    @endphp

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4>Checkout Lapangan</h4>
                    </div>
                    <div class="card-body">
                        <!-- Detail Lapangan -->
                        <div class="mb-4">
                            <h5>Detail Lapangan</h5>
                            <!-- @if($lapangan->gambar)
                                <img src="{{ asset('storage/' . $lapangan->gambar) }}" class="img-fluid mb-3" alt="{{ $lapangan->nama }}">
                            @endif -->
                            <table class="table">
                                <tr>
                                    <th width="200">Nama Lapangan</th>
                                    <td>{{ $lapangan->nama }}</td>
                                </tr>
                                <tr>
                                    <th>Lokasi</th>
                                    <td>{{ $lapangan->lokasi }}</td>
                                </tr>
                                <tr>
                                    <th>Harga per Jam</th>
                                    <td><strong class="text-success">Rp {{ number_format($lapangan->harga_per_jam, 0, ',', '.') }}</strong></td>
                                </tr>
                            </table>
                        </div>

                        <!-- Form Booking -->
                        <form id="checkoutForm">
                            @csrf
                            <h5 class="mb-3">Data Pemesan</h5>

                            <div class="mb-3">
                                <label for="nama_customer" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama_customer" name="nama_customer" required>
                            </div>

                            <div class="mb-3">
                                <label for="email_customer" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email_customer" name="email_customer" required>
                            </div>

                            <div class="mb-3">
                                <label for="phone_customer" class="form-label">No. Telepon <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="phone_customer" name="phone_customer" required>
                            </div>

                            <hr class="my-4">

                            <h5 class="mb-3">Detail Booking</h5>

                            <div class="mb-3">
                                <label class="form-label">Tanggal Booking</label>
                                <input type="date" class="form-control" value="{{ $tanggal }}" readonly>
                                <input type="hidden" name="tanggal_booking" value="{{ $tanggal }}">
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Jam Mulai</label>
                                    <input type="time" class="form-control" value="{{ $start }}" readonly>
                                    <input type="hidden" name="jam_mulai" value="{{ $start }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Jam Selesai</label>
                                    <input type="time" class="form-control" value="{{ $end }}" readonly>
                                    <input type="hidden" name="jam_selesai" value="{{ $end }}">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Durasi (Jam)</label>
                                <input type="number" class="form-control" value="{{ $durasi }}" readonly>
                                <input type="hidden" name="durasi_jam" value="{{ $durasi }}">
                            </div>


                            <div class="alert alert-info">
                                <strong>Total Harga:</strong>
                                <span id="totalHarga">Rp {{ number_format($lapangan->harga_per_jam * $durasi, 0, ',', '.') }}</span>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg" id="payButton">
                                    Bayar Sekarang
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.getElementById('checkoutForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const button = document.getElementById('payButton');

            button.disabled = true;
            button.textContent = 'Memproses...';

            try {
                const formData = new FormData(this);
                const response = await fetch("{{ route('payment.lapangan.checkout', $lapangan->id) }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    // 🔵 Trigger Snap popup
                    snap.pay(result.snap_token, {
                        onSuccess: function(result) {
                            window.location.href = "{{ route('payment.finish') }}?order_id=" + result.order_id;
                        },
                        onPending: function(result) {
                            window.location.href = "{{ route('payment.finish') }}?order_id=" + result.order_id;
                        },
                        onError: function(result) {
                            alert('Pembayaran gagal!');
                            button.disabled = false;
                            button.textContent = 'Bayar Sekarang';
                        },
                        onClose: function() {
                            button.disabled = false;
                            button.textContent = 'Bayar Sekarang';
                        }
                    });
                } else {
                    alert('Error: ' + result.message);
                    button.disabled = false;
                    button.textContent = 'Bayar Sekarang';
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan!');
                button.disabled = false;
                button.textContent = 'Bayar Sekarang';
            }
        });
    </script>

</body>

</html>