@extends('layouts.app')

@section('content')
<div class="container my-5">

    <div class="text-center mb-5">
        <h2 class="fw-bold">Status Pembayaran</h2>
        <p class="text-muted">Terima kasih! Berikut detail pembayaran Anda.</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card shadow-lg border-0">
                <div class="card-body">

                    <h4 class="fw-bold text-center mb-4">
                        @if($transaction->status_pembayaran == 'success')
                            <span class="text-success">Pembayaran Berhasil</span>
                        @elseif($transaction->status_pembayaran == 'pending')
                            <span class="text-warning">Menunggu Pembayaran</span>
                        @else
                            <span class="text-danger">Pembayaran Gagal</span>
                        @endif
                    </h4>

                    <hr>

                    <h5>Detail Pembayaran</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th>Status</th>
                            <td>{{ ucfirst($transaction->status_pembayaran) }}</td>
                        </tr>
                        <tr>
                            <th>Nama Customer</th>
                            <td>{{ $transaction->nama }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>{{ $transaction->email }}</td>
                        </tr>
                        <tr>
                            <th>Nomor Telepon</th>
                            <td>{{ $transaction->phone }}</td>
                        </tr>
                        <tr>
                            <th>Total Pembayaran</th>
                            <td><strong>Rp {{ number_format($transaction->amount, 0, ',', '.') }}</strong></td>
                        </tr>
                    </table>

                    @if($transaction->isEvent())
                    <h5 class="mt-4">Detail Event</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th>Nama Event</th>
                            <td>{{ $transaction->event->nama_event }}</td>
                        </tr>
                        <tr>
                            <th>Lokasi</th>
                            <td>{{ $transaction->event->lokasi }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal</th>
                            <td>{{ $transaction->event->tanggal_mulai->format('d M Y H:i') }}</td>
                        </tr>
                    </table>
                    @endif

                    @if($transaction->isLapangan())
                    <h5 class="mt-4">Detail Booking Lapangan</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th>Lapangan</th>
                            <td>{{ $transaction->lapangan->nama }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal</th>
                            <td>{{ $transaction->tanggal }}</td>
                        </tr>
                        <tr>
                            <th>Jam</th>
                            <td>{{ $transaction->jam_mulai }} - {{ $transaction->jam_selesai }}</td>
                        </tr>
                        <tr>
                            <th>Durasi</th>
                            <td>{{ $transaction->durasi }} Jam</td>
                        </tr>
                    </table>
                    @endif

                    <div class="text-center mt-4">
                        <a href="{{ url('/') }}" class="btn btn-primary">Kembali ke Beranda</a>
                    </div>

                </div>
            </div>

        </div>
    </div>

</div>
@endsection
