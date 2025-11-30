<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Raketin - @yield('title')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    @yield('styles')
</head>
<body class="bg-gray-50">

    @include('layouts.navbar')

    <main> {{-- Hapus class="py-4" dan wrapper container --}}
        
        {{-- Pesan Sukses/Error (Dipindahkan ke dalam container) --}}
        @if (session('success'))
            <div class="container mx-auto px-4 pt-4"> {{-- Tambahkan pt-4 agar ada padding atas --}}
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        @endif

        {{-- Lokasi Konten Utama Akan Dimasukkan --}}
        @yield('content')
            
    </main>

    @include('layouts.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    @yield('scripts')
</body>
</html>