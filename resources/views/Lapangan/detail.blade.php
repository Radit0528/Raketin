<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Detail Lapangan - Raketin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>

<body class="bg-[#E9F0FB]">
    @include('layouts.navbar')

    <!-- Breadcrumb -->
    <div class="container mx-auto px-6 py-6 text-sm text-gray-500">
        <a href="" class="hover:text-blue-600">Home</a> /
        <a href="{{ route('lapangan.index') }}" class="hover:text-blue-600">Lapangan</a> /
        <span class="text-gray-800 font-medium">{{ $lapangan->nama }}</span>
    </div>

    <!-- Detail Lapangan -->
    <section class="container mx-auto px-6 pb-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- Gambar & Deskripsi -->
            <div class="lg:col-span-2 bg-white rounded-xl shadow-md p-6">
                <!-- Galeri Gambar -->
                <div class="grid grid-cols-3 gap-2 mb-6">
                    <div class="col-span-3">
                        <img src="{{ asset('storage/lapangan_images/' . basename($lapangan->gambar)) }}"
                            alt="Lapangan {{ $lapangan->nama }}"
                            class="w-full h-64 rounded-xl object-cover bg-gray-200"
                            onerror="this.onerror=null;this.src='https://placehold.co/600x400/CCCCCC/333333?text=Gambar+Lapangan';">
                    </div>
                    <img src="https://placehold.co/300x200/AAAAAA/FFFFFF?text=Foto+1" class="rounded-lg object-cover w-full h-32">
                    <img src="https://placehold.co/300x200/AAAAAA/FFFFFF?text=Foto+2" class="rounded-lg object-cover w-full h-32">
                    <img src="https://placehold.co/300x200/AAAAAA/FFFFFF?text=Foto+3" class="rounded-lg object-cover w-full h-32">
                </div>

                <!-- Nama & Deskripsi -->
                <h1 class="text-2xl font-bold text-gray-800 mb-3">{{ $lapangan->nama }}</h1>
                <p class="text-gray-600 leading-relaxed mb-6">
                    {{ $lapangan->deskripsi ?? 'Lapangan ini menawarkan permukaan permainan profesional dengan pencahayaan dan ventilasi yang sangat baik. Cocok untuk pertandingan maupun latihan santai.' }}
                </p>

                <!-- Fasilitas -->
                <h3 class="text-lg font-semibold text-gray-800 mb-3">Fasilitas</h3>
                <div class="flex flex-wrap gap-4 mb-6">
                    <div class="flex items-center gap-2 bg-blue-50 px-4 py-2 rounded-lg text-blue-700 font-medium">
                        <i class="fas fa-restroom"></i> Toilet
                    </div>
                    <div class="flex items-center gap-2 bg-blue-50 px-4 py-2 rounded-lg text-blue-700 font-medium">
                        <i class="fas fa-lock"></i> Loker
                    </div>
                    <div class="flex items-center gap-2 bg-blue-50 px-4 py-2 rounded-lg text-blue-700 font-medium">
                        <i class="fas fa-couch"></i> Area Tunggu
                    </div>
                </div>

                <!-- Ulasan -->
                <h3 class="text-lg font-semibold text-gray-800 mb-3">Ulasan Pengguna</h3>
                <div class="border-t pt-4">
                    <div class="flex items-center gap-3 mb-3">
                        <span class="text-4xl font-bold text-gray-800">{{ number_format($lapangan->rating ?? 4.5, 1) }}</span>
                        <div>
                            <div class="text-yellow-400">★★★★☆</div>
                            <p class="text-sm text-gray-500">Berdasarkan 20 ulasan</p>
                        </div>
                    </div>

                    <!-- Progress Bar Rating -->
                    <div class="space-y-1 mb-6">
                        @foreach ([5,4,3,2,1] as $star)
                            <div class="flex items-center gap-2 text-sm text-gray-600">
                                <span class="w-3">{{ $star }}</span>
                                <div class="flex-1 bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-500 h-2 rounded-full" style="width:8 ;"></div>
                                </div>
                                <span>{{ [40,30,15,10,5][$star-1] }}%</span>
                            </div>
                        @endforeach
                    </div>

                    <!-- List Ulasan -->
                    <div class="space-y-6">
                    </div>
                </div>
            </div>

            <!-- Sidebar Harga & Ketersediaan -->
            <div class="bg-white shadow-md rounded-xl p-6 h-fit">
                <h3 class="text-sm text-gray-500 mb-1">Harga</h3>
                <p class="text-2xl font-bold text-blue-600 mb-4">
                    Rp {{ number_format($lapangan->harga_per_jam, 0, ',', '.') }}
                    <span class="text-gray-500 text-base font-normal">/ jam</span>
                </p>

                <h3 class="text-sm text-gray-500 mb-2">Ketersediaan</h3>
                <div class="border rounded-lg p-3 mb-4 bg-gray-50">
                    <p class="text-center text-gray-600 text-sm mb-2 font-medium">
                        {{ now()->translatedFormat('F Y') }}
                    </p>
                    <div class="grid grid-cols-7 gap-1 text-center text-sm text-gray-500">
                        @foreach (['S','M','T','W','T','F','S'] as $day)
                            <span class="font-medium">{{ $day }}</span>
                        @endforeach
                        @for ($i = 1; $i <= 31; $i++)
                            <button
                                class="p-1 rounded-lg {{ in_array($i, [5,6,7,12,13,14,19,20,21,26,27,28]) ? 'bg-blue-100 text-blue-600 font-semibold' : 'hover:bg-gray-100' }}">
                                {{ $i }}
                            </button>
                        @endfor
                    </div>
                </div>

                <button class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded-lg transition">
                    Pesan Sekarang
                </button>
            </div>
        </div>
    </section>

</body>
</html>
