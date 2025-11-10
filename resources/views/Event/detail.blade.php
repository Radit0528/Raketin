@extends('layouts.app')

@section('content')
<div class="container my-5">
    {{-- Banner / Gambar Event --}}
    @if($event->gambar)
    <div class="flex justify-center mb-4">
    <div class="rounded overflow-hidden shadow-sm">
        <img src="{{ asset($event->gambar) }}" 
             alt="{{ $event->nama_event }}" 
             class="w-[150%] max-w-md object-contain rounded-lg shadow-md">
    </div>
</div>

    @endif

    {{-- Judul Event --}}
    <h2 class="fw-bold mb-3">{{ $event->nama_event }}</h2>

    {{-- Informasi Umum --}}
    <div class="row mb-4">
        <div class="col-md-8">
            <h5>Deskripsi Event</h5>
            <p>{{ $event->deskripsi }}</p>

            <h5 class="mt-4">Lokasi</h5>
            <p>{{ $event->lokasi }}</p>

            {{-- Contoh embed map, bisa disimpan di DB juga kalau mau --}}
            <div class="ratio ratio-16x9 rounded overflow-hidden shadow-sm">
                <iframe
                    src="https://www.google.com/maps?q={{ urlencode($event->lokasi) }}&output=embed"
                    width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy">
                </iframe>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Informasi Event</h5>
                    <p><strong>Tanggal Mulai:</strong> {{ \Carbon\Carbon::parse($event->tanggal_mulai)->translatedFormat('d F Y, H:i') }}</p>
                    <p><strong>Tanggal Selesai:</strong> 
                        {{ $event->tanggal_selesai ? \Carbon\Carbon::parse($event->tanggal_selesai)->translatedFormat('d F Y, H:i') : '-' }}
                    </p>
                    <p><strong>Biaya Pendaftaran:</strong> Rp{{ number_format($event->biaya_pendaftaran, 0, ',', '.') }}</p>
                    <p><strong>Status:</strong> 
                        <span class="badge bg-{{ $event->status == 'upcoming' ? 'primary' : ($event->status == 'finished' ? 'success' : 'secondary') }}">
                            {{ ucfirst($event->status) }}
                        </span>
                    </p>
                    <a href="#" class="btn btn-primary w-100 mt-3">Daftar Event</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
