<nav class="bg-[#D8E2F3] shadow-sm">
    <div class="container mx-auto flex justify-between items-center px-6 py-3">
        
        {{-- Logo --}}
        <div class="flex items-center space-x-2">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-8">
            <span class="text-lg font-bold text-gray-700">RAKETIN</span>
        </div>

        {{-- Navigasi --}}
        <ul class="flex space-x-8 text-gray-700 font-medium">
    <li>
        <a href="{{ url('/') }}"
           class="pb-1 border-b-2 transition duration-150 ease-in-out
           {{ request()->is('/') 
               ? 'text-blue-600 font-semibold border-blue-600' 
               : 'border-transparent hover:text-blue-600 hover:border-blue-600' }}">
           Beranda
        </a>
    </li>

    <li>
        <a href="{{ route('lapangan.search') }}"
           class="pb-1 border-b-2 transition duration-150 ease-in-out
           {{ request()->is('lapangan') || request()->is('lapangan/*') || request()->is('cari-lapangan') 
               ? 'text-blue-600 font-semibold border-blue-600' 
               : 'border-transparent hover:text-blue-600 hover:border-blue-600' }}">
           Lapangan
        </a>
    </li>

    <li>
        <a href="{{ route('event.index') }}"
           class="pb-1 border-b-2 transition duration-150 ease-in-out
           {{ request()->routeIs('event.*') 
               ? 'text-blue-600 font-semibold border-blue-600' 
               : 'border-transparent hover:text-blue-600 hover:border-blue-600' }}">
           Event
        </a>
    </li>

    <li>
        <a href="{{ url('/profil') }}"
           class="pb-1 border-b-2 transition duration-150 ease-in-out
           {{ request()->is('profil') || request()->is('profil/*') 
               ? 'text-blue-600 font-semibold border-blue-600' 
               : 'border-transparent hover:text-blue-600 hover:border-blue-600' }}">
           Profil
        </a>
    </li>
</ul>



        {{-- Tombol Login / Logout --}}
        <div class="flex space-x-3">
            @guest
                <a href="{{ route('login') }}" class="bg-blue-200 text-blue-700 px-4 py-1 rounded-lg hover:bg-blue-300 transition duration-150 ease-in-out">Masuk</a>
                <a href="{{ route('register') }}" class="bg-blue-600 text-white px-4 py-1 rounded-lg hover:bg-blue-700 transition duration-150 ease-in-out">Daftar</a>
            @endguest

            @auth
                @php
                    $dashboardRoute = Auth::user()->role === 'admin' ? route('admin.dashboard') : route('dashboard');
                    $dashboardLabel = Auth::user()->role === 'admin' ? 'Dashboard Admin' : 'Dashboard';
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
