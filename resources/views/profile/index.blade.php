@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-10">

    <!-- TITLE -->
    <h1 class="text-3xl font-bold mb-3">Profil Saya</h1>
    <p class="text-gray-600 mb-8">
        Kelola informasi pribadi Anda dan lihat riwayat aktivitas Anda.
    </p>

    <!-- PROFILE CARD -->
    <div class="bg-white rounded-xl p-6 shadow-sm flex items-center justify-between">
        <div class="flex items-center gap-4">
            <img src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name) }}"
                 class="w-20 h-20 rounded-full object-cover">

            <div>
                <h2 class="text-xl font-semibold">{{ $user->name }}</h2>
                <p class="text-gray-600">{{ $user->email }}</p>
            </div>
        </div>

        <a href="{{ route('profile.edit') }}"
           class="px-4 py-2 bg-blue-500 text-white rounded-lg text-sm hover:bg-blue-600">
            Edit Profil
        </a>
    </div>

    <!-- TAB MENU -->
    <div class="mt-10 border-b">
        <ul class="flex gap-6 text-sm">
            <li>
                <a href="#lapangan"
                   class="tab-link font-medium py-3 block border-b-2 border-blue-600 text-blue-600">
                    Riwayat Pemesanan Lapangan
                </a>
            </li>
            <li>
                <a href="#event"
                   class="tab-link font-medium py-3 block text-gray-600 hover:text-blue-600">
                    Riwayat Pendaftaran Event
                </a>
            </li>
        </ul>
    </div>

    <!-- CONTENT LAPANGAN -->
    <div id="lapangan" class="tab-content mt-8">

        <!-- UPCOMING -->
        <h3 class="text-lg font-semibold mb-4">Pemesanan Mendatang</h3>

        @forelse ($upcomingLapangan as $item)
            <div class="bg-white rounded-xl shadow-sm p-5 mb-4 flex justify-between items-center">
                <div>
                    <p class="font-semibold">Pemesanan #{{ $item->id }}</p>
                    <p class="text-gray-600 text-sm">
                        {{ $item->lapangan->nama }}  
                        {{ \Carbon\Carbon::parse($item->tanggal)->format('d F Y') }},
                        {{ $item->jam_mulai }} - {{ $item->jam_selesai }}
                    </p>
                </div>

                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm">
                    Dikonfirmasi
                </span>
            </div>
        @empty
            <p class="text-gray-500">Belum ada pemesanan mendatang.</p>
        @endforelse

        <!-- HISTORY -->
        <h3 class="text-lg font-semibold mt-8 mb-4">Riwayat Pemesanan</h3>

        @forelse ($historyLapangan as $item)
            <div class="bg-white rounded-xl shadow-sm p-5 mb-4 flex justify-between items-center">
                <div>
                    <p class="font-semibold">Pemesanan #{{ $item->id }}</p>
                    <p class="text-gray-600 text-sm">
                        {{ $item->lapangan->nama }}  
                        {{ \Carbon\Carbon::parse($item->tanggal)->format('d F Y') }},
                        {{ $item->jam_mulai }} - {{ $item->jam_selesai }}
                    </p>
                </div>

                <span class="bg-gray-200 text-gray-700 px-3 py-1 rounded-full text-sm">
                    Selesai
                </span>
            </div>
        @empty
            <p class="text-gray-500">Belum ada riwayat pemesanan.</p>
        @endforelse
    </div>

    <!-- CONTENT EVENT -->
    <div id="event" class="tab-content hidden mt-8">

        <!-- UPCOMING -->
        <h3 class="text-lg font-semibold mb-4">Event Mendatang</h3>

        @forelse ($upcomingEvent as $item)
            <div class="bg-white rounded-xl shadow-sm p-5 mb-4 flex justify-between items-center">
                <div>
                    <p class="font-semibold">Event #{{ $item->id }}</p>
                    <p class="text-gray-600 text-sm">
                        {{ $item->event->nama }}
                    </p>
                </div>

                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm">
                    Dikonfirmasi
                </span>
            </div>
        @empty
            <p class="text-gray-500">Belum ada event mendatang.</p>
        @endforelse

        <!-- HISTORY -->
        <h3 class="text-lg font-semibold mt-8 mb-4">Riwayat Event</h3>

        @forelse ($historyEvent as $item)
            <div class="bg-white rounded-xl shadow-sm p-5 mb-4 flex justify-between items-center">
                <div>
                    <p class="font-semibold">Event #{{ $item->id }}</p>
                    <p class="text-gray-600 text-sm">
                        {{ $item->event->nama }}
                    </p>
                </div>

                <span class="bg-gray-200 text-gray-700 px-3 py-1 rounded-full text-sm">
                    Selesai
                </span>
            </div>
        @empty
            <p class="text-gray-500">Belum ada riwayat event.</p>
        @endforelse
    </div>

</div>

<!-- SIMPLE TAB SCRIPT -->
<script>
    const tabLinks = document.querySelectorAll('.tab-link');
    const contents = document.querySelectorAll('.tab-content');

    tabLinks.forEach(link => {
        link.addEventListener('click', () => {
            tabLinks.forEach(l => l.classList.remove('border-blue-600', 'text-blue-600'));
            contents.forEach(c => c.classList.add('hidden'));

            link.classList.add('border-blue-600', 'text-blue-600');
            document.querySelector(link.getAttribute('href')).classList.remove('hidden');
        });
    });
</script>

@endsection
