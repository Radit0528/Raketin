@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-semibold mb-1">Edit Profil</h1>
    <p class="text-gray-600 mb-6">Perbarui informasi pribadi Anda.</p>

    <div class="bg-white p-6 rounded-xl shadow-sm border">
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- FOTO -->
            <div class="flex items-center gap-4 mb-6">
                <img src="{{ $user->profile_photo ? asset($user->profile_photo) : asset('default-user.png') }}"
                     class="w-20 h-20 rounded-full object-cover border">

                <div>
                    <label class="text-sm text-gray-700">Foto Profil</label>
                    <input type="file" name="photo"
                           class="block mt-1 text-sm text-gray-600">
                    <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG (max 2MB)</p>
                </div>
            </div>

            <!-- Nama -->
            <div class="mb-4">
                <label class="block text-gray-700 mb-1 text-sm">Nama Lengkap</label>
                <input type="text" name="name"
                       value="{{ old('name', $user->name) }}"
                       class="w-full px-3 py-2 border rounded-lg focus:ring focus:ring-blue-200">
            </div>

            <!-- Email -->
            <div class="mb-4">
                <label class="block text-gray-700 mb-1 text-sm">Email</label>
                <input type="email" name="email"
                       value="{{ old('email', $user->email) }}"
                       class="w-full px-3 py-2 border rounded-lg focus:ring focus:ring-blue-200">
            </div>

            <!-- Tombol -->
            <div class="flex justify-end gap-3 mt-6">
                <a href="{{ route('profile') }}"
                   class="px-4 py-2 border rounded-lg text-gray-700">
                    Batal
                </a>

                <button type="submit"
                        class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Simpan Perubahan
                </button>
            </div>

        </form>
    </div>
</div>
@endsection
