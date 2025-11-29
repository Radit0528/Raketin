@extends('layouts.admin') 
{{-- Ganti sesuai layout-mu --}}

@section('title', 'Dashboard')

@section('content')
<div class="p-6">

    {{-- Judul --}}
    <h1 class="text-3xl font-bold mb-2">Dashboard</h1>
    <p class="text-gray-500 mb-6">Overview</p>

    {{-- Statistik Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-5 mb-8">
        <div class="bg-white rounded-xl p-5 shadow">
            <p class="text-sm text-gray-500">Total Lapangan</p>
            <h2 class="text-3xl font-bold">{{ $totalLapangan ?? 0 }}</h2>
        </div>

        <div class="bg-white rounded-xl p-5 shadow">
            <p class="text-sm text-gray-500">Total Event</p>
            <h2 class="text-3xl font-bold">{{ $totalEvent ?? 0 }}</h2>
        </div>

        <div class="bg-white rounded-xl p-5 shadow">
            <p class="text-sm text-gray-500">User Terdaftar</p>
            <h2 class="text-3xl font-bold">{{ $totalUser ?? 0 }}</h2>
        </div>

        <div class="bg-white rounded-xl p-5 shadow">
            <p class="text-sm text-gray-500">Booking Hari Ini</p>
            <h2 class="text-3xl font-bold">{{ $bookingToday ?? 0 }}</h2>
        </div>
    </div>

    {{-- Grafik Placeholder --}}
    <div class="bg-white rounded-xl shadow p-6 mb-8">
        <h3 class="text-xl font-semibold mb-4">Grafik Booking Mingguan</h3>
        <div class="w-full h-56 bg-gray-100 rounded flex items-center justify-center text-gray-400">
            Grafik akan muncul di sini
        </div>
    </div>

    {{-- Aktivitas Terbaru & Jadwal Hari Ini --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- Aktivitas --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-xl font-semibold mb-4">Aktivitas Terbaru</h3>

            <ul class="space-y-3">
                @forelse ($activities ?? [] as $activity)
                    <li class="text-gray-700">
                        • {{ $activity }}
                    </li>
                @empty
                    <li class="text-gray-400">Belum ada aktivitas terbaru</li>
                @endforelse
            </ul>
        </div>

        {{-- Jadwal Hari Ini --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-xl font-semibold mb-4">Jadwal Hari Ini</h3>

            @forelse ($jadwalHariIni ?? [] as $j)
                <div class="border-l-4 border-blue-500 pl-3 mb-3">
                    <p class="font-semibold">{{ $j['lapangan'] }}</p>
                    <p class="text-sm text-gray-500">{{ $j['waktu'] }}</p>
                </div>
            @empty
                <p class="text-gray-400">Tidak ada jadwal hari ini</p>
            @endforelse
        </div>

    </div>

</div>
@endsection
