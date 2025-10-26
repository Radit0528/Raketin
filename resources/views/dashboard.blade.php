<!DOCTYPE html>
<html lang="id">
<head>
 <meta charset="UTF-8" />
 <meta name="viewport" content="width=device-width, initial-scale=1.0" />
 <title>Beranda - Raketin</title>
 <script src="https://cdn.tailwindcss.com"></script>
  {{-- Tambahkan script Font Awesome untuk ikon di bawah (jika belum ada) --}}
 <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body class="bg-[#E9F0FB]">

@include('layouts.navbar')

 <!-- Hero Section -->
 <section class="bg-gradient-to-b from-blue-900 to-blue-600 text-white py-16 text-center">
  <div class="container mx-auto px-6">
   <h1 class="text-4xl font-bold mb-4">Temukan Lapangan Sempurna Anda</h1>
     <p class="text-lg mb-8">Temukan dan pesan lapangan badminton terdekat di sekitar Anda. Ikuti event dan terhubung dengan sesama pemain.</p>

     <!-- Search Bar -->
   <div class="flex justify-center">
        {{-- Form Pencarian harus mengarah ke route('search') --}}
        <form action="" method="GET" class="flex w-1/2 max-w-xl">
            <input 
                type="text" 
                name="query" 
                placeholder="Cari lapangan atau event" 
                class="w-full px-4 py-3 rounded-l-lg border-none focus:ring-4 focus:ring-blue-300 text-gray-700 shadow-lg" 
                value="{{ request('query') }}"
            />
            <button type="submit" class="bg-blue-500 px-6 rounded-r-lg text-white font-semibold hover:bg-blue-600 transition duration-150">Cari</button>
        </form>
   </div>
  </div>
 </section>

 <!-- Lapangan Terdekat (Dinamis dari Database) -->
 <section class="py-12">
  <div class="container mx-auto px-6">
   <div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Lapangan Terdekat</h2>
    <a href="" class="text-blue-600 hover:underline">Lihat Semua</a>
   </div>

 <!-- Area Lapangan Dinamis -->
<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
    @forelse ($lapangans as $lapangan)
    <a href="{{ route('lapangan.show', $lapangan->id) }}" class="block bg-white rounded-xl shadow-lg overflow-hidden transition transform hover:scale-[1.03] hover:shadow-2xl duration-300">
      {{-- Gambar Lapangan --}}
      <div class="h-40 w-full overflow-hidden bg-gray-200">
          <img src="{{ asset('storage/lapangan_images/' . basename($lapangan->gambar)) }}" alt="Gambar {{ $lapangan->nama }}" class="h-full w-full object-cover" onerror="this.onerror=null;this.src='https://placehold.co/600x400/CCCCCC/333333?text=Gambar+Lapangan';">
      </div>
      <div class="p-4">
        <h3 class="text-lg font-bold text-gray-900 line-clamp-1">{{ $lapangan->nama }}</h3>
        <p class="text-sm text-gray-600 mt-1">{{ $lapangan->tipe_lapangan }}</p>
        <p class="text-md text-blue-700 font-extrabold mt-2">Rp {{ number_format($lapangan->harga_per_jam) }} / Jam</p>
     </div>
    </a>
    @empty
    <p class="col-span-4 text-center text-gray-500 p-8 border rounded-lg bg-white">Belum ada data lapangan yang tersedia saat ini. Silakan tambahkan dari panel admin.</p>
    @endforelse
   </div>
  </div>
 </section>

 <!-- Event Mendatang (Dinamis dari Database) -->
 <section class="py-12 bg-white">
    <div class="container mx-auto px-6">
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Event Mendatang</h2>
        <a href="{{ route('event.index') }}" class="text-blue-600 hover:underline">Lihat Semua</a>
      </div>

      <div class="space-y-6">
        @forelse ($events as $event)
        <div class="flex flex-col md:flex-row bg-[#F7F9FC] rounded-xl shadow-lg overflow-hidden transition transform hover:shadow-2xl duration-300 border border-gray-200">
            {{-- Gambar Event --}}
            <div class="w-full md:w-48 h-48 overflow-hidden bg-gray-300">
                <img src="{{ asset('storage/event_images/' . basename($event->gambar)) }}" alt="Poster {{ $event->nama_event }}" class="h-full w-full object-cover" onerror="this.onerror=null;this.src='https://placehold.co/600x400/333333/FFFFFF?text=Poster+Event';">
            </div>
            
            <div class="p-5 flex flex-col justify-between w-full">
                <div>
                    <span class="text-xs font-medium text-blue-600 bg-blue-100 px-2 py-0.5 rounded-full">
                        {{ $event->lokasi ?? 'Lokasi Tidak Diketahui' }}
                    </span>
                    <h3 class="text-xl font-bold text-gray-800 mt-2 line-clamp-1">{{ $event->nama_event }}</h3>
                    <p class="text-gray-600 mt-1 line-clamp-2">{{ Str::limit($event->deskripsi, 150) }}</p>
                </div>
                
                <div class="mt-3 flex justify-between items-center">
                    <div>
                        <span class="text-sm text-gray-700 font-semibold block">
                            Biaya: Rp {{ number_format($event->biaya_pendaftaran ?? 0) }}
                        </span>
                        <span class="text-xs text-gray-500">
                            Mulai: {{ \Carbon\Carbon::parse($event->tanggal_mulai)->translatedFormat('d M Y, H:i') }}
                        </span>
                    </div>
                    <a href="#" class="inline-block bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700 transition duration-150">Daftar Event</a>
                </div>
            </div>
        </div>
        @empty
        <p class="text-center text-gray-500 p-8 border rounded-lg bg-[#F7F9FC]">Saat ini tidak ada event yang akan datang. Cek kembali nanti!</p>
        @endforelse
      </div>
    </div>
 </section>

 <!-- Pencarian Cepat -->
 <section class="py-12">
  <div class="container mx-auto px-6 text-center">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Pencarian Cepat</h2>

      <div class="flex flex-col md:flex-row justify-center gap-4">
        <button class="bg-blue-100 text-blue-600 px-6 py-3 rounded-xl hover:bg-blue-200 flex items-center justify-center gap-2 font-semibold transition duration-150"><i class="fas fa-map-marker-alt"></i> Lapangan di Dekat Saya</button>
        <button class="bg-blue-100 text-blue-600 px-6 py-3 rounded-xl hover:bg-blue-200 flex items-center justify-center gap-2 font-semibold transition duration-150"><i class="fas fa-calendar-alt"></i> Event Mendatang</button>
        <button class="bg-blue-100 text-blue-600 px-6 py-3 rounded-xl hover:bg-blue-200 flex items-center justify-center gap-2 font-semibold transition duration-150"><i class="fas fa-user-friends"></i> Cari Pemain</button>
      </div>
    </div>
 </section>

</body>
</html>