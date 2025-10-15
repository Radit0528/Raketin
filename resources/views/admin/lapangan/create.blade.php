<?php
// Catatan: File ini akan dimuat oleh LapanganController@create
// dan akan memposting data ke LapanganController@store
?>
@extends('layouts.admin')

@section('title', 'Tambah Lapangan')
@section('title_page', 'Tambah Lapangan Baru')
@section('breadcrumb', 'Tambah Lapangan')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Formulir Tambah Lapangan</h6>
            </div>
            <div class="card-body">
                
                {{-- Tampilkan Error Validasi --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                {{-- Form mengarah ke LapanganController@store --}}
                <form method="POST" action="{{ route('lapangan.store') }}" enctype="multipart/form-data">
                    @csrf
                    
                    {{-- Nama Lapangan --}}
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama Lapangan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nama" name="nama" value="{{ old('nama') }}" required>
                    </div>

                    {{-- Tipe Lapangan --}}
                    <div class="mb-3">
                        <label for="tipe_lapangan" class="form-label">Tipe Lapangan <span class="text-danger">*</span></label>
                        <select class="form-control" id="tipe_lapangan" name="tipe_lapangan" required>
                            <option value="">Pilih Tipe</option>
                            {{-- Anda dapat menambahkan tipe lapangan sesuai kebutuhan Anda --}}
                            <option value="Badminton" {{ old('tipe_lapangan') == 'Badminton' ? 'selected' : '' }}>Badminton</option>
                            <option value="Futsal" {{ old('tipe_lapangan') == 'Futsal' ? 'selected' : '' }}>Futsal</option>
                            <option value="Basket" {{ old('tipe_lapangan') == 'Basket' ? 'selected' : '' }}>Basket</option>
                        </select>
                    </div>

                    {{-- Harga per Jam --}}
                    <div class="mb-3">
                        <label for="harga_per_jam" class="form-label">Harga per Jam (Rp) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="harga_per_jam" name="harga_per_jam" value="{{ old('harga_per_jam') }}" required min="0">
                    </div>

                    {{-- Deskripsi --}}
                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3">{{ old('deskripsi') }}</textarea>
                    </div>

                    {{-- Gambar --}}
                    <div class="mb-4">
                        <label for="gambar" class="form-label">Gambar Lapangan</label>
                        <input type="file" class="form-control" id="gambar" name="gambar" accept="image/*">
                        <small class="form-text text-muted">Maksimal 2MB (jpeg, png, jpg).</small>
                    </div>

                    <button type="submit" class="btn btn-primary">Simpan Lapangan</button>
                    <a href="{{ route('lapangan.index') }}" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
