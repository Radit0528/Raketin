<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Daftar - Raketin</title>
  <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#E9F0FB]">

  <!-- Navbar -->
  @include('layouts.navbar')
  <!-- Register Form -->
  <div class="flex justify-center items-center min-h-[80vh]">
    <div class="bg-white p-8 rounded-xl shadow-md w-[380px]">
      <h2 class="text-2xl font-bold text-center text-gray-800">Buat Akun Baru</h2>
      <p class="text-sm text-gray-500 text-center mb-6">Daftar untuk mulai sewa lapangan badminton</p>

      <form method="POST" action="{{ route('register') }}">
        @csrf
        <input type="text" name="nama" placeholder="Nama Lengkap" class="w-full px-4 py-2 mb-4 border rounded-md" />
        <input type="text" name="username" placeholder="Username" class="w-full px-4 py-2 mb-4 border rounded-md" />
        <input type="email" name="email" placeholder="Email" class="w-full px-4 py-2 mb-4 border rounded-md" />
        <input type="password" name="password" placeholder="Password" class="w-full px-4 py-2 mb-4 border rounded-md" />
        <input type="password" name="password_confirmation" placeholder="Konfirmasi Password" class="w-full px-4 py-2 mb-6 border rounded-md" />
        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700">Daftar</button>
      </form>

      @if ($errors->any())
      <div class="text-red-600 text-sm mt-3 text-center">
        {{ $errors->first() }}
      </div>
      @endif


      <p class="text-sm text-gray-600 text-center mt-4">
        Sudah punya akun? <a href="login" class="text-blue-500 hover:underline">Masuk di sini</a>
      </p>
    </div>
  </div>

</body>

</html>