<?php
/**
 * View Form Edit Lapangan Admin
 * Menggunakan Layout Master: layouts.sb_admin
 * Variabel yang dibutuhkan: $lapangan (objek Lapangan yang akan diedit)
 */
?>

@extends('layouts.admin')

@section('title', 'Edit Lapangan')
@section('title_page', 'Edit Lapangan: ' . $lapangan->nama)
@section('breadcrumb', 'Edit')

@section('content')
<div class="container-fluid px-4">
    
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-edit me-1"></i>
            Form Edit Lapangan
        </div>
        <div class="card-body">
            
            {{-- Form akan mengirim data ke LapanganController@update --}}
            <form action="{{ route('lapangan.update', $lapangan->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT') {{-- Wajib menggunakan PUT/PATCH untuk update --}}

                {{-- Input Nama Lapangan --}}
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama Lapangan</label>
                    <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" value="{{ old('nama', $lapangan->nama) }}" required>
                    @error('nama')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Input Harga Per Jam --}}
                <div class="mb-3">
                    <label for="harga_per_jam" class="form-label">Harga Per Jam (Rp)</label>
                    <input type="number" class="form-control @error('harga_per_jam') is-invalid @enderror" id="harga_per_jam" name="harga_per_jam" value="{{ old('harga_per_jam', $lapangan->harga_per_jam) }}" required>
                    @error('harga_per_jam')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Input Deskripsi --}}
                <div class="mb-3">
                    <label for="deskripsi" class="form-label">Deskripsi</label>
                    <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="3">{{ old('deskripsi', $lapangan->deskripsi) }}</textarea>
                    @error('deskripsi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Input Gambar (File Upload) --}}
                <div class="mb-4">
                    <label for="gambar" class="form-label">Ganti Gambar Lapangan (Opsional)</label>
                    
                    {{-- Tampilkan Gambar Lama --}}
                    @if ($lapangan->gambar)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $lapangan->gambar) }}" alt="Gambar Lama" class="img-thumbnail" style="max-height: 150px;">
                            <small class="text-muted d-block">Gambar saat ini.</small>
                        </div>
                    @endif

                    <input type="file" class="form-control @error('gambar') is-invalid @enderror" id="gambar" name="gambar">
                    <small class="form-text text-muted">Abaikan jika tidak ingin mengganti gambar.</small>
                    @error('gambar')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-success">Update Lapangan</button>
                    <a href="{{ route('lapangan.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection
