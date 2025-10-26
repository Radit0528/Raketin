@extends('layouts.admin') {{-- Menggunakan Master Layout SB Admin --}}

@section('title', 'Daftar Event')
@section('title_page', 'Pengelolaan Event')
@section('breadcrumb', 'Event')

@section('content')

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Event yang Terdaftar</h6>
            <a href="{{ route('event.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tambah Event Baru
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Event</th>
                            <th>Tanggal Mulai</th>
                            <th>Organizer</th>
                            <th>Lokasi Lapangan</th>
                            <th>Biaya Daftar</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Pastikan variabel $events dikirimkan dari EventController --}}
                        @forelse ($events as $event)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $event->nama_event }}</td>
                            <td>{{ $event->tanggal_mulai->format('d M Y H:i') }}</td>
                            <td>{{ $event->organizer->username ?? 'N/A' }}</td>
                            <!-- <td>{{ $event->court->nama ?? 'N/A' }}</td> -->
                            <td>{{ $event->lokasi }}</td>
                            <td>Rp {{ number_format($event->biaya_pendaftaran) }}</td>
                            <td>
                                @if ($event->status === 'upcoming')
                                    <span class="badge bg-info text-white">Akan Datang</span>
                                @elseif ($event->status === 'finished')
                                    <span class="badge bg-secondary">Selesai</span>
                                @else
                                    <span class="badge bg-primary">{{ ucfirst($event->status) }}</span>
                                @endif
                            </td>
                            <td>
                                {{-- Tombol Edit --}}
                                <a href="{{ route('event.edit', $event->id) }}" class="btn btn-warning btn-sm mr-2">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                
                                {{-- Tombol Hapus --}}
                                <form action="{{ route('event.destroy', $event->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus Event ini?')">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">Belum ada data event yang terdaftar.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection
