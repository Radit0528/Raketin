<nav class="bg-[#D8E2F3] shadow-sm">
    <div class="container mx-auto flex justify-between items-center px-6 py-3">
        
        <div class="flex items-center space-x-2">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-8">
            <span class="text-lg font-bold text-gray-700">RAKETIN</span>
        </div>

        <ul class="flex space-x-8 text-gray-700 font-medium">
            <li><a href="/" class="text-blue-600">Beranda</a></li>
            <li><a href="{{ route('lapangan.search') }}" class="hover:text-blue-600">Lapangan</a></li>
            <li><a href="/" class="hover:text-blue-600">Event</a></li>
            <li><a href="/" class="hover:text-blue-600">Profil</a></li>
        </ul>

        <div class="flex space-x-3">
            
            {{-- TAMPIL JIKA BELUM LOGIN (GUEST) --}}
            @guest
                <a href="{{ route('login') }}" class="bg-blue-200 text-blue-700 px-4 py-1 rounded-lg hover:bg-blue-300 transition duration-150 ease-in-out">Masuk</a>
                <a href="{{ route('register') }}" class="bg-blue-600 text-white px-4 py-1 rounded-lg hover:bg-blue-700 transition duration-150 ease-in-out">Daftar</a>
            @endguest

            {{-- TAMPIL JIKA SUDAH LOGIN (AUTH) --}}
            @auth
                @php
                    // Asumsi: Cek role di Auth::user()->role
                    $dashboardRoute = (Auth::user()->role === 'admin') ? route('admin.dashboard') : route('dashboard');
                    $dashboardLabel = (Auth::user()->role === 'admin') ? 'Dashboard Admin' : 'Dashboard';
                @endphp

                <a href="{{ $dashboardRoute }}" class="bg-green-500 text-white px-4 py-1 rounded-lg hover:bg-green-600 transition duration-150 ease-in-out">
                    {{ $dashboardLabel }}
                </a>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-red-500 text-white px-4 py-1 rounded-lg hover:bg-red-600 transition duration-150 ease-in-out">
                        Logout
                    </button>
                </form>
            @endauth
        </div>
    </div>
</nav>