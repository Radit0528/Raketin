<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Beranda - Raketin</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#E9F0FB]">

  <!-- Navbar -->
  <nav class="bg-[#D8E2F3] shadow-sm">
    <div class="container mx-auto flex justify-between items-center px-6 py-3">
      <!-- Logo -->
      <div class="flex items-center space-x-2">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-8">
        <span class="text-lg font-bold text-gray-700">RAKETIN</span>
      </div>

      <!-- Menu -->
      <ul class="flex space-x-8 text-gray-700 font-medium">
        <li><a href="#" class="text-blue-600">Beranda</a></li>
        <li><a href="#" class="hover:text-blue-600">Lapangan</a></li>
        <li><a href="#" class="hover:text-blue-600">Event</a></li>
        <li><a href="#" class="hover:text-blue-600">Profil</a></li>
      </ul>

      <!-- Buttons -->
      <div class="flex space-x-3">
        <a href="{{ route('login') }}" class="bg-blue-200 text-blue-700 px-4 py-1 rounded-lg hover:bg-blue-300">Masuk</a>
        <a href="{{ route('register') }}" class="bg-blue-600 text-white px-4 py-1 rounded-lg hover:bg-blue-700">Daftar</a>
      </div>
    </div>
  </nav>

  <!-- Hero Section -->
  <section class="bg-gradient-to-b from-blue-900 to-blue-600 text-white py-16 text-center">
    <div class="container mx-auto px-6">
      <h1 class="text-4xl font-bold mb-4">Temukan Lapangan Sempurna Anda</h1>
      <p class="text-lg mb-8">Temukan dan pesan lapangan badminton terdekat di sekitar Anda. Ikuti event dan terhubung dengan sesama pemain.</p>

      <!-- Search Bar -->
      <div class="flex justify-center">
        <input type="text" placeholder="Cari lapangan atau event" class="w-1/2 px-4 py-2 rounded-l-md border-none focus:ring-2 focus:ring-blue-300 text-gray-700" />
        <button class="bg-blue-500 px-6 rounded-r-md text-white hover:bg-blue-600">Cari</button>
      </div>
    </div>
  </section>

  <!-- Lapangan Terdekat -->
  <section class="py-12">
    <div class="container mx-auto px-6">
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Lapangan Terdekat</h2>
        <a href="#" class="text-blue-600 hover:underline">Lihat Semua</a>
      </div>

      
    </div>
  </section>

  <!-- Event Mendatang -->
  <section class="py-12 bg-white">
    <div class="container mx-auto px-6">
      <h2 class="text-2xl font-bold text-gray-800 mb-6">Event Mendatang</h2>

      <div class="space-y-6">
        
        
      </div>
    </div>
  </section>

  <!-- Pencarian Cepat -->
  <section class="py-12">
    <div class="container mx-auto px-6 text-center">
      <h2 class="text-2xl font-bold text-gray-800 mb-6">Pencarian Cepat</h2>

      <div class="flex flex-col md:flex-row justify-center gap-4">
        <button class="bg-white border border-blue-400 text-blue-600 px-6 py-3 rounded-md hover:bg-blue-50">Lapangan di Dekat Saya</button>
        <button class="bg-white border border-blue-400 text-blue-600 px-6 py-3 rounded-md hover:bg-blue-50">Event Mendatang</button>
        <button class="bg-white border border-blue-400 text-blue-600 px-6 py-3 rounded-md hover:bg-blue-50">Cari Pemain</button>
      </div>
    </div>
  </section>

</body>
</html>
