@extends('layouts.owner')

@section('title', 'Dashboard Pemilik Lapangan')

@section('content')

{{-- HEADER OWNER --}}
<div class="mb-4">
    <h1 class="fw-bold">Dashboard Pemilik Lapangan</h1>
    <p class="text-muted">
        Lapangan yang Anda Kelola:
        <strong>
            {{ $lapangans->pluck('nama')->join(', ') ?: 'Belum memiliki lapangan' }}
        </strong>
    </p>
</div>

{{-- RINGKASAN --}}
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white shadow">
            <div class="card-body">
                <h6>💰 Pendapatan Bulan Ini</h6>
                <h3>Rp {{ number_format($totalPendapatanBulanIni ?? 0, 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card bg-success text-white shadow">
            <div class="card-body">
                <h6>✅ Booking Sukses</h6>
                <h3>{{ $totalPemesananBulanIni ?? 0 }} Transaksi</h3>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card bg-info text-white shadow">
            <div class="card-body">
                <h6>⭐ Total Lapangan</h6>
                <h3>{{ $lapangans->count() }}</h3>
            </div>
        </div>
    </div>
</div>

{{-- DAFTAR LAPANGAN --}}
<div class="card mb-4 shadow">
    <div class="card-header fw-bold">
        Lapangan Milik Anda
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nama Lapangan</th>
                    <th>Lokasi</th>
                    <th>Harga / Jam</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($lapangans as $lapangan)
                <tr>
                    <td>{{ $lapangan->nama }}</td>
                    <td>{{ $lapangan->lokasi }}</td>
                    <td>Rp {{ number_format($lapangan->harga_per_jam, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- TRANSAKSI TERBARU --}}
<div class="card shadow">
    <div class="card-header fw-bold">
        Transaksi Terbaru
    </div>
    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Lapangan</th>
                    <th>Tanggal</th>
                    <th>Total</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($latestTransactions as $trx)
                <tr>
                    <td>{{ $trx->id }}</td>
                    <td>{{ $trx->lapangan->nama }}</td>
                    <td>{{ $trx->created_at->format('d M Y H:i') }}</td>
                    <td>Rp {{ number_format($trx->amount, 0, ',', '.') }}</td>
                    <td>
                        <span class="badge bg-{{ $trx->status_pembayaran === 'success' ? 'success' : 'warning' }}">
                            {{ strtoupper($trx->status_pembayaran) }}
                        </span>

                        @if ($trx->status_pembayaran === 'pending')
                        <form action="{{ route('owner.transaksi.update-status', $trx->id) }}"
                              method="POST"
                              class="d-inline"
                              onsubmit="return confirm('Tandai transaksi sebagai SUCCESS?')">
                            @csrf
                            @method('PATCH')
                            <button class="btn btn-sm btn-success ms-1">
                                ✔
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted">
                        Belum ada transaksi
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
