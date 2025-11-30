@extends('layouts.app')

@section('title', 'Event')

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
                <h1 class="text-3xl font-bold mb-2">Cari Event</h1>
                <p class="text-lg mb-8">Temukan event olahraga terbaru di sekitar Anda.</p>
            </div>
        </section>

        <div class="bg-white rounded-2xl shadow-sm p-6 mb-10 border border-gray-100">
            <form action="{{ route('event.search') }}" method="GET" class="space-y-4">
                <div class="flex flex-col md:flex-row gap-4 items-center">
                    <div class="flex-1 relative">
                        <i class="fas fa-search absolute left-3 top-3.5 text-gray-400"></i>
                        <input type="text" name="nama_event" placeholder="Cari berdasarkan nama event atau lokasi"
                            class="w-full border border-gray-300 rounded-lg py-3 px-10 focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>

                    <input type="date" name="tanggal" class="border border-gray-300 rounded-lg py-3 px-4 focus:ring-2 focus:ring-blue-500">

                    <select name="kategori" class="border border-gray-300 rounded-lg py-3 px-4 focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Kategori</option>
                        <option>Turnamen</option>
                        <option>Pelatihan</option>
                        <option>Komunitas</option>
                    </select>

                    <button type="submit"
                        class="bg-blue-600 text-white font-semibold px-6 py-3 rounded-lg hover:bg-blue-700 transition">
                        Search
                    </button>
                </div>
            </form>
        </div>
        <div class="mb-6">
            <a href="https://wa.me/6282136934003?text=Halo%20admin,%20saya%20ingin%20menambah%20atau%20membuat%20event%20di%20platform%20Raketin."
            target="_blank"
            class="inline-flex items-center bg-green-600 text-white px-6 py-3 rounded-lg font-semibold shadow hover:bg-green-700 transition">
                <span class="text-2xl font-bold ">+</span>
                <i class="fab fa-whatsapp text-xl mr-2"></i>
                Buat Event
            </a>
        </div>


        <h2 class="text-lg font-bold mb-4">Daftar Event</h2>

        <div class="space-y-4">
            @foreach($events as $event)
                <div class="flex items-center bg-white rounded-xl shadow-sm p-4 justify-between hover:shadow-md transition border border-gray-100">
                    <div class="flex items-center space-x-4">
                        <img src="{{ asset($event->gambar) }}"
                             class="w-20 h-20 rounded-lg object-cover"
                             onerror="this.onerror=null;this.src='https://placehold.co/80x80?text=No+Image';">
                        <div>
                            <h3 class="font-semibold text-gray-800">{{ $event->nama_event }}</h3>
                            <p class="text-sm text-gray-500">
                                {{ $event->tanggal_event ? \Carbon\Carbon::parse($event->tanggal_event)->format('d M Y') : 'Tanggal belum ditentukan' }},
                                {{ $event->lokasi ?? 'Lokasi belum ditentukan' }}
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('event.detail', $event->id) }}"
                       class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 font-semibold">
                       Lihat Detail
                    </a>
                </div>
            @endforeach

            @if($events->isEmpty())
                <p class="text-center text-gray-500 py-6">Tidak ada event yang ditemukan.</p>
            @endif
        </div>
    </div>
@endsection