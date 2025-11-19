<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Checkout Event - {{ $event->nama_event }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('midtrans.client_key') }}"></script>
</head>

<body>
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4>Checkout Event</h4>
                    </div>
                    <div class="card-body">
                        <!-- Detail Event -->
                        <div class="mb-4">
                            <h5>Detail Event</h5>
                            @if($event->gambar)
                            <img src="{{ asset('storage/' . $event->gambar) }}" class="img-fluid mb-3" alt="{{ $event->nama_event }}">
                            @endif
                            <table class="table">
                                <tr>
                                    <th width="200">Nama Event</th>
                                    <td>{{ $event->nama_event }}</td>
                                </tr>
                                <tr>
                                    <th>Lokasi</th>
                                    <td>{{ $event->lokasi }}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal Mulai</th>
                                    <td>{{ $event->tanggal_mulai->format('d M Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal Selesai</th>
                                    <td>{{ $event->tanggal_selesai->format('d M Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Biaya Pendaftaran</th>
                                    <td><strong class="text-success">Rp {{ number_format($event->biaya_pendaftaran, 0, ',', '.') }}</strong></td>
                                </tr>
                            </table>
                        </div>

                        <!-- Form Data Customer -->
                        <form id="checkoutForm">
                            @csrf
                            <h5 class="mb-3">Data Peserta</h5>

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

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg" id="payButton">
                                    Bayar Sekarang - Rp {{ number_format($event->biaya_pendaftaran, 0, ',', '.') }}
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
        // Simpan teks tombol ke variabel JS supaya tidak error di VSCode
        const biayaText = "Bayar Sekarang - Rp {{ number_format($event->biaya_pendaftaran, 0, ',', '.') }}";

        document.getElementById('checkoutForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const button = document.getElementById('payButton');
            button.disabled = true;
            button.textContent = 'Memproses...';

            try {
                const formData = new FormData(this);
                const response = await fetch("{{ route('payment.event.checkout', $event->id) }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
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
                            button.textContent = biayaText;
                        },
                        onClose: function() {
                            button.disabled = false;
                            button.textContent = biayaText;
                        }
                    });
                } else {
                    alert('Error: ' + result.message);
                    button.disabled = false;
                    button.textContent = biayaText;
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan!');
                button.disabled = false;
                button.textContent = biayaText;
            }
        });
    </script>

</body>

</html>