@extends('layouts.admin') {{-- Menggunakan Master Layout SB Admin --}}

@section('title', 'Tambah Event Baru')
@section('title_page', 'Tambah Event Baru')
@section('breadcrumb', 'Event / Tambah')

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Formulir Pendaftaran Event</h6>
        </div>
        <div class="card-body">
            
            {{-- Form akan POST ke EventController@store --}}
            <form action="{{ route('event.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                {{-- 1. Nama Event --}}
                <div class="mb-3">
                    <label for="nama_event" class="form-label">Nama Event</label>
                    <input type="text" name="nama_event" id="nama_event" class="form-control @error('nama_event') is-invalid @enderror" value="{{ old('nama_event') }}" required>
                    @error('nama_event')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- 2. Pilih Lapangan --}}
                <div class="mb-4">
                    <label for="lapangan_id" class="block text-sm font-medium text-gray-700">Pilih Lapangan</label>
                    <select name="lapangan_id" id="lapangan_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm @error('lapangan_id') is-invalid @enderror" required>
                        <option value="">-- Pilih Lapangan --</option>
                        @foreach ($lapangans as $lap)
                            <option value="{{ $lap->id }}" {{ old('lapangan_id') == $lap->id ? 'selected' : '' }}>
                                {{ $lap->nama }} - {{ $lap->lokasi }}
                            </option>
                        @endforeach
                    </select>
                    @error('lapangan_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>


                {{-- 3. Biaya Pendaftaran --}}
                <div class="mb-3">
                    <label for="biaya_pendaftaran" class="form-label">Biaya Pendaftaran (Rp)</label>
                    <input type="number" name="biaya_pendaftaran" id="biaya_pendaftaran" class="form-control @error('biaya_pendaftaran') is-invalid @enderror" value="{{ old('biaya_pendaftaran') }}" required min="0">
                    @error('biaya_pendaftaran')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- 4. Tanggal Mulai dan Selesai --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="tanggal_mulai" class="form-label">Tanggal dan Waktu Mulai</label>
                        <input type="datetime-local" name="tanggal_mulai" id="tanggal_mulai" class="form-control @error('tanggal_mulai') is-invalid @enderror" value="{{ old('tanggal_mulai') }}" required>
                        @error('tanggal_mulai')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="tanggal_selesai" class="form-label">Tanggal dan Waktu Selesai (Opsional)</label>
                        <input type="datetime-local" name="tanggal_selesai" id="tanggal_selesai" class="form-control @error('tanggal_selesai') is-invalid @enderror" value="{{ old('tanggal_selesai') }}">
                        @error('tanggal_selesai')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- 5. Status --}}
                <div class="mb-3">
                    <label for="status" class="form-label">Status Event</label>
                    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                        <option value="">Pilih Status</option>
                        <option value="upcoming" {{ old('status') == 'upcoming' ? 'selected' : '' }}>Akan Datang (Upcoming)</option>
                        <option value="finished" {{ old('status') == 'finished' ? 'selected' : '' }}>Selesai (Finished)</option>
                        <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan (Cancelled)</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- 6. Deskripsi --}}
                <div class="mb-3">
                    <label for="deskripsi" class="form-label">Deskripsi Event</label>
                    <textarea name="deskripsi" id="deskripsi" rows="4" class="form-control @error('deskripsi') is-invalid @enderror" required>{{ old('deskripsi') }}</textarea>
                    @error('deskripsi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                {{-- 7. Gambar/Poster Event --}}
                <div class="mb-3">
                    <label for="gambar" class="form-label">Gambar/Poster Event</label>
                    <input type="file" name="gambar" id="gambar" class="form-control @error('gambar') is-invalid @enderror">
                    <small class="form-text text-muted">Maksimal 2MB (jpeg, png, jpg)</small>
                    @error('gambar')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('event.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Simpan Event
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
