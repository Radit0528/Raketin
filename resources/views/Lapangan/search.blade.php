@extends('layouts.app')


@section('title', 'Sewa Lapangan')

@section('styles')
{{-- Menimpa body class default --}}
<style>
    body {
        background-color: #F7FAFC;
    }
</style>
{{-- Memanggil font awesome yang dibutuhkan --}}
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
@endsection

@section('content')
    <div class="container mx-auto px-6 py-12">
        <section class="bg-gradient-to-b from-blue-900 to-blue-600 text-white py-16 text-center">
            <div class="container mx-auto px-6">
                <h1 class="text-3xl font-bold mb-2">Cari Lapangan</h1>
                <p class="text-lg mb-8">Cari lapangan berdasarkan lokasi, tanggal, dan waktu.</p>
            </div>
        </section>

        <div class="bg-white rounded-2xl shadow-sm p-6 mb-10 border border-gray-100">
            <form action="{{ route('lapangan.search') }}" method="GET" class="space-y-4">
                <div class="flex flex-col md:flex-row gap-4 items-center">
                    <div class="flex-1 relative">
                    <i class="fas fa-search absolute left-3 top-3.5 text-gray-400"></i>
                    <input type="text"
                        name="keyword"
                        value="{{ request('keyword') }}"
                        placeholder="Cari berdasarkan nama lapangan atau lokasi"
                        class="w-full border border-gray-300 rounded-lg py-3 px-10 focus:ring-2 focus:ring-blue-500 outline-none">
                </div>

                    <input type="date" name="tanggal" class="border border-gray-300 rounded-lg py-3 px-4 focus:ring-2 focus:ring-blue-500">
                    <input type="time" name="waktu" class="border border-gray-300 rounded-lg py-3 px-4 focus:ring-2 focus:ring-blue-500">

                    <button type="submit"
                        class="bg-blue-600 text-white font-semibold px-6 py-3 rounded-lg hover:bg-blue-700 transition">
                        Search
                    </button>
                </div>
            </form>
        </div>

        <h2 class="text-lg font-bold mb-4">Search Results</h2>

        <div class="space-y-4">
            @foreach($lapangans as $lapangan)
                <div class="flex items-center bg-white rounded-xl shadow-sm p-4 justify-between hover:shadow-md transition border border-gray-100">
                    <div class="flex items-center space-x-4">
                        <img src="{{ asset('storage/lapangan_images/' . basename($lapangan->gambar)) }}"
                             class="w-20 h-20 rounded-lg object-cover"
                             onerror="this.onerror=null;this.src='https://placehold.co/80x80?text=No+Image';">
                        <div>
                            <h3 class="font-semibold text-gray-800">{{ $lapangan->nama }}</h3>
                            <p class="text-sm text-gray-500">
                                <!-- {{ $lapangan->tipe_lapangan ?? 'Indoor court' }}, -->
                                tersedia jam {{ $lapangan->jam_tersedia ?? '10:00 AM' }}
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('lapangan.detail', $lapangan->id) }}"
                       class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 font-semibold">
                       Book
                    </a>
                </div>
            @endforeach

            @if($lapangans->isEmpty())
                <p class="text-center text-gray-500 py-6">Tidak ada lapangan yang ditemukan.</p>
            @endif
        </div>
    </div>
@endsection