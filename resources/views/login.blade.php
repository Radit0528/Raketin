@extends('layouts.app')

@section('styles')
{{-- Menimpa body class default --}}
<style>
    body {
        background-color: #E9F0FB;
    }
</style>
@endsection

@section('content')
  <div class="flex justify-center items-center min-h-[80vh]">
    <div class="bg-white p-8 rounded-xl shadow-md w-[350px]">
      <h2 class="text-2xl font-bold text-center text-gray-800">Masuk ke Akun Anda</h2>
      <p class="text-sm text-gray-500 text-center mb-6">Sewa lapangan badminton dengan mudah</p>

      <form method="POST" action="{{ route('login') }}">
        @csrf
        <input type="text" name="username" placeholder="Username" class="w-full px-4 py-2 mb-4 border rounded-md" />
        <input type="password" name="password" placeholder="Password" class="w-full px-4 py-2 mb-2 border rounded-md" />
        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700">Masuk</button>
      </form>

      @if ($errors->any())
      <div class="text-red-600 text-sm mt-3 text-center">
        {{ $errors->first() }}
      </div>
      @endif

      <p class="text-sm text-gray-600 text-center mt-4">
        Belum punya akun? <a href="register" class="text-blue-500 hover:underline">Daftar di sini</a>
      </p>
    </div>
  </div>
@endsection