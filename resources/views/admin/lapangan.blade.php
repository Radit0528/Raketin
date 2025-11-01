@extends('layouts.admin') {{-- Menggunakan Master Layout SB Admin --}}

@section('title', 'Daftar Lapangan')
@section('title_page', 'Pengelolaan Lapangan')
@section('breadcrumb', 'Lapangan')

@section('content')

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Lapangan</h6>
            <a href="{{ route('lapangan.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tambah Lapangan Baru
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Lapangan</th>
                            <th>Lokasi</th>
                            <th>Harga/Jam</th>
                            <th>Gambar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($lapangans as $lapangan)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $lapangan->nama }}</td>
                            <td>{{ $lapangan->lokasi }}</td>
                            <td>Rp {{ number_format($lapangan->harga_per_jam) }}</td>
                            <td>
                                @if ($lapangan->gambar)
                                    <img src="{{ asset('storage/lapangan_images/' . basename($lapangan->gambar)) }}" alt="{{ $lapangan->nama }}" style="width: 80px; height: auto;">
                                @else
                                    Tidak Ada
                                @endif
                            </td>
                            <td>
                                {{-- Tombol Edit --}}
                                <a href="{{ route('lapangan.edit', $lapangan->id) }}" class="btn btn-warning btn-sm mr-2">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                
                                {{-- Tombol Hapus --}}
                                <form action="{{ route('lapangan.destroy', $lapangan->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus lapangan ini?')">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">Belum ada data lapangan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection
