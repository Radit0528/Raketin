@extends('layouts.admin')

@section('title', 'Edit Event')
@section('title_page', 'Edit Data Event')
@section('breadcrumb', 'Event / Edit')

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Formulir Edit Event: {{ $event->nama_event }}</h6>
        </div>
        <div class="card-body">
            
            {{-- Form akan PUT/PATCH ke EventController@update --}}
            <form action="{{ route('event.update', $event->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT') {{-- Wajib untuk operasi UPDATE --}}
                
                {{-- 1. Nama Event --}}
                <div class="mb-3">
                    <label for="nama_event" class="form-label">Nama Event</label>
                    <input type="text" name="nama_event" id="nama_event" class="form-control @error('nama_event') is-invalid @enderror" 
                           value="{{ old('nama_event', $event->nama_event) }}" required>
                    @error('nama_event')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- 2. Lokasi --}}
                <div class="mb-3">
                    <label for="lokasi" class="form-label">Lokasi Event (Nama Tempat/Alamat)</label>
                    <input type="text" name="lokasi" id="lokasi" class="form-control @error('lokasi') is-invalid @enderror" 
                           value="{{ old('lokasi', $event->lokasi) }}" required>
                    @error('lokasi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- 3. Biaya Pendaftaran --}}
                <div class="mb-3">
                    <label for="biaya_pendaftaran" class="form-label">Biaya Pendaftaran (Rp)</label>
                    <input type="number" name="biaya_pendaftaran" id="biaya_pendaftaran" class="form-control @error('biaya_pendaftaran') is-invalid @enderror" 
                           value="{{ old('biaya_pendaftaran', $event->biaya_pendaftaran) }}" required min="0">
                    @error('biaya_pendaftaran')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- 4. Tanggal Mulai dan Selesai --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="tanggal_mulai" class="form-label">Tanggal dan Waktu Mulai</label>
                        {{-- Format tanggal/waktu agar kompatibel dengan input datetime-local --}}
                        <input type="datetime-local" name="tanggal_mulai" id="tanggal_mulai" class="form-control @error('tanggal_mulai') is-invalid @enderror" 
                               value="{{ old('tanggal_mulai', \Carbon\Carbon::parse($event->tanggal_mulai)->format('Y-m-d\TH:i')) }}" required>
                        @error('tanggal_mulai')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="tanggal_selesai" class="form-label">Tanggal dan Waktu Selesai (Opsional)</label>
                        <input type="datetime-local" name="tanggal_selesai" id="tanggal_selesai" class="form-control @error('tanggal_selesai') is-invalid @enderror" 
                               value="{{ old('tanggal_selesai', $event->tanggal_selesai ? \Carbon\Carbon::parse($event->tanggal_selesai)->format('Y-m-d\TH:i') : '') }}">
                        @error('tanggal_selesai')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- 5. Status --}}
                <div class="mb-3">
                    <label for="status" class="form-label">Status Event</label>
                    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                        {{-- Menggunakan operator ?? untuk default nilai --}}
                        @php $currentStatus = old('status', $event->status); @endphp
                        <option value="upcoming" {{ $currentStatus == 'upcoming' ? 'selected' : '' }}>Akan Datang (Upcoming)</option>
                        <option value="finished" {{ $currentStatus == 'finished' ? 'selected' : '' }}>Selesai (Finished)</option>
                        <option value="cancelled" {{ $currentStatus == 'cancelled' ? 'selected' : '' }}>Dibatalkan (Cancelled)</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- 6. Deskripsi --}}
                <div class="mb-3">
                    <label for="deskripsi" class="form-label">Deskripsi Event</label>
                    <textarea name="deskripsi" id="deskripsi" rows="4" class="form-control @error('deskripsi') is-invalid @enderror" required>{{ old('deskripsi', $event->deskripsi) }}</textarea>
                    @error('deskripsi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                {{-- 7. Gambar/Poster Event Saat Ini --}}
                <div class="mb-3">
                    <label class="form-label">Gambar Saat Ini</label>
                    @if ($event->gambar)
                        <div class="mb-2">
                            {{-- Memanggil gambar dari path database --}}
                            <img src="{{ $event->gambar }}" alt="Poster Event" style="width: 150px; height: auto; border: 1px solid #ddd;">
                        </div>
                    @else
                        <p class="text-muted small">Tidak ada gambar yang diunggah.</p>
                    @endif
                    
                    <label for="gambar" class="form-label mt-2">Unggah Gambar Baru</label>
                    <input type="file" name="gambar" id="gambar" class="form-control @error('gambar') is-invalid @enderror">
                    <small class="form-text text-muted">Abaikan jika tidak ingin mengubah gambar. Maksimal 2MB (jpeg, png, jpg)</small>
                    @error('gambar')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-between mt-4">
                    {{-- TOMBOL KEMBALI --}}
                    <a href="{{ route('event.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Batal / Kembali
                    </a>
                    
                    {{-- TOMBOL UPDATE --}}
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Perbarui Event
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
