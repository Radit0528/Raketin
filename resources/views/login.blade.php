<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login - Raketin</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#E9F0FB]">

  <!-- Navbar -->
  <nav class="bg-[#D8E2F3] shadow-sm">
    <div class="container mx-auto flex justify-between items-center px-6 py-3">
      <!-- Logo -->
      <div class="flex items-center space-x-2">
        <img src="https://via.placeholder.com/40" alt="Logo" class="h-8">
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
        <a href="#" class="bg-blue-600 text-white px-4 py-1 rounded-lg hover:bg-blue-700">Masuk</a>
        <a href="#" class="bg-blue-200 text-blue-700 px-4 py-1 rounded-lg hover:bg-blue-300">Daftar</a>
      </div>
    </div>
  </nav>

  <!-- Login Form -->
  <div class="flex justify-center items-center min-h-[80vh]">
    <div class="bg-white p-8 rounded-xl shadow-md w-[350px]">
      <h2 class="text-2xl font-bold text-center text-gray-800">Masuk ke Akun Anda</h2>
      <p class="text-sm text-gray-500 text-center mb-6">Sewa lapangan badminton dengan mudah</p>

      <form>
        <!-- Username -->
        <input type="text" placeholder="Username" class="w-full px-4 py-2 mb-4 border rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none" />
        
        <!-- Password -->
        <input type="password" placeholder="Password" class="w-full px-4 py-2 mb-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none" />

        <!-- Forgot password -->
        <div class="flex justify-end mb-4">
          <a href="#" class="text-sm text-blue-500 hover:underline">Lupa Password?</a>
        </div>

        <!-- Button -->
        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 transition">Masuk</button>
      </form>

      <p class="text-sm text-gray-600 text-center mt-4">
        Belum punya akun? <a href="#" class="text-blue-500 hover:underline">Daftar di sini</a>
      </p>
    </div>
  </div>

</body>
</html>
