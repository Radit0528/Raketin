<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Daftar - Raketin</title>
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
        <li><a href="#" class="hover:text-blue-600">Beranda</a></li>
        <li><a href="#" class="hover:text-blue-600">Lapangan</a></li>
        <li><a href="#" class="hover:text-blue-600">Tentang Kami</a></li>
        <li><a href="#" class="hover:text-blue-600">Kontak</a></li>
      </ul>
      <!-- Buttons -->
      <div class="flex space-x-3">
        <a href="login" class="bg-blue-200 text-blue-700 px-4 py-1 rounded-lg hover:bg-blue-300">Masuk</a>
        <a href="register" class="bg-blue-600 text-white px-4 py-1 rounded-lg hover:bg-blue-700">Daftar</a>
      </div>
    </div>
  </nav>

  <!-- Register Form -->
  <div class="flex justify-center items-center min-h-[80vh]">
    <div class="bg-white p-8 rounded-xl shadow-md w-[380px]">
      <h2 class="text-2xl font-bold text-center text-gray-800">Buat Akun Baru</h2>
      <p class="text-sm text-gray-500 text-center mb-6">Daftar untuk mulai sewa lapangan badminton</p>

      <form>
        <!-- Nama Lengkap -->
        <input type="text" placeholder="Nama Lengkap" class="w-full px-4 py-2 mb-4 border rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none" />

        <!-- Username -->
        <input type="text" placeholder="Username" class="w-full px-4 py-2 mb-4 border rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none" />

        <!-- Email -->
        <input type="email" placeholder="Email" class="w-full px-4 py-2 mb-4 border rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none" />

        <!-- Password -->
        <input type="password" placeholder="Password" class="w-full px-4 py-2 mb-4 border rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none" />

        <!-- Konfirmasi Password -->
        <input type="password" placeholder="Konfirmasi Password" class="w-full px-4 py-2 mb-6 border rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none" />

        <!-- Button -->
        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 transition">Daftar</button>
      </form>

      <p class="text-sm text-gray-600 text-center mt-4">
        Sudah punya akun? <a href="login" class="text-blue-500 hover:underline">Masuk di sini</a>
      </p>
    </div>
  </div>

</body>
</html>
