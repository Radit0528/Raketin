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
                
                <form method="POST" action="{{ route('lapangan.store') }}" enctype="multipart/form-data">
                    @csrf
                    
                    {{-- Nama Lapangan --}}
                    <div class="mb-3">
                        <label class="form-label">Nama Lapangan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama" value="{{ old('nama') }}" required>
                    </div>

                    {{-- Lokasi --}}
                    <div class="mb-3">
                        <label class="form-label">Lokasi Lapangan <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="lokasi" rows="2" required>{{ old('lokasi') }}</textarea>
                    </div>

                    {{-- Harga --}}
                    <div class="mb-3">
                        <label class="form-label">Harga per Jam (Rp) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="harga_per_jam" value="{{ old('harga_per_jam') }}" required min="0">
                    </div>

                    {{-- Deskripsi --}}
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea class="form-control" name="deskripsi" rows="3">{{ old('deskripsi') }}</textarea>
                    </div>

                    {{-- Fasilitas Lapangan --}}
                    <div class="mb-4">
                        <label class="form-label d-block">Fasilitas Lapangan</label>

                        <div class="row g-2">
                            @php
                                $fasilitasList = [
                                    'Toilet', 'Loker', 'Area Tunggu', 'Parkir',
                                    'Mushola', 'Kantin', 'Wifi', 'Kursi Penonton'
                                ];
                            @endphp

                            @foreach ($fasilitasList as $fa)
                                <div class="col-md-6">
                                    <label class="border rounded p-2 d-flex align-items-center gap-2 facility-item"
                                           style="cursor:pointer;">
                                        <input type="checkbox" name="fasilitas[]" value="{{ $fa }}">
                                        <span>{{ $fa }}</span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Gambar --}}
                    <div class="mb-4">
                        <label class="form-label">Gambar Lapangan</label>
                        <input type="file" class="form-control" name="gambar" accept="image/*">
                        <small class="text-muted">Maksimal 2MB (jpeg, png, jpg).</small>
                    </div>

                    <button type="submit" class="btn btn-primary">Simpan Lapangan</button>
                    <a href="{{ route('lapangan.index') }}" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Script Interaktif Checkbox --}}
<script>
document.querySelectorAll(".facility-item").forEach(item => {
    item.addEventListener("click", () => {
        const checkbox = item.querySelector("input");
        checkbox.checked = !checkbox.checked;

        if (checkbox.checked) {
            item.classList.add("border-primary", "bg-light");
        } else {
            item.classList.remove("border-primary", "bg-light");
        }
    });
});
</script>

@endsection
