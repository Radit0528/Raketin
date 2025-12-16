<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="Dashboard Admin Raketin" />
        <meta name="author" content="Raketin" />
        
        {{-- SLOT 1: TITLE DINAMIS --}}
        <title>Raketin Admin - @yield('title', 'Dashboard')</title>

        {{-- ASSET CSS (PERBAIKI PATH DENGAN asset('sb-admin-assets/...') --}}
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
        <link href="{{ asset('assets/admin/css/styles.css') }}" rel="stylesheet" />
        <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
        @yield('styles')
    </head>
    <body class="sb-nav-fixed">
        
        {{-- TOP NAVIGATION BAR (NAVBAR) --}}
        <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
            <a class="navbar-brand ps-3" href="{{ route('admin.dashboard') }}">RAKETIN Admin</a>
            <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
            
            <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0"></form>
            
            {{-- USER DROPDOWN (LOGOUT) --}}
            <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><div class="dropdown-item small">Logged in as: <strong>{{ Auth::user()->username ?? 'Admin' }}</strong></div></li>
                        <li><hr class="dropdown-divider" /></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item">Logout</button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
        
        <div id="layoutSidenav">
            
            {{-- SIDEBAR (DISESUAIKAN DENGAN KEBUTUHAN RAKETIN) --}}
            <div id="layoutSidenav_nav">
                <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                    <div class="sb-sidenav-menu">
                        <div class="nav">
                            
                            <div class="sb-sidenav-menu-heading">Utama</div>
                            <a class="nav-link" href="{{ route('admin.dashboard') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                Dashboard
                            </a>
                            
                            <div class="sb-sidenav-menu-heading">Pengelolaan Data</div>
                            
                            {{-- TAUTAN LAPANGAN --}}
                            <a class="nav-link" href="{{ route('lapangan.index') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-basketball-ball"></i></div>
                                Daftar Lapangan
                            </a>
                            <a class="nav-link" href="{{ route('lapangan.create') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-plus"></i></div>
                                Tambah Lapangan
                            </a>
                            
                            {{-- TAUTAN EVENT --}}
                            <a class="nav-link" href="{{ route('event.index') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-calendar-alt"></i></div>
                                Daftar Event
                            </a>
                            <a class="nav-link" href="{{ route('event.create') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-plus"></i></div>
                                Tambah Event
                            </a>
                            
                        </div>
                    </div>
                    <div class="sb-sidenav-footer">
                        <div class="small">Logged in as:</div>
                        {{ Auth::user()->username ?? 'Admin' }}
                    </div>
                </nav>
            </div>
            
            {{-- KONTEN UTAMA (AREA DINAMIS) --}}
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        {{-- Judul dan Breadcrumb Dinamis --}}
                        <h1 class="mt-4">@yield('title_page', 'Dashboard')</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">@yield('breadcrumb', 'Overview')</li>
                        </ol>
                        
                        {{-- SLOT 2: KONTEN UTAMA HALAMAN --}}
                        @yield('content')
                        
                    </div>
                </main>
                
                {{-- FOOTER --}}
                <footer class="py-4 bg-light mt-auto">
                    <div class="container-fluid px-4">
                        <div class="d-flex align-items-center justify-content-between small">
                            <div class="text-muted">Copyright &copy; Raketin {{ date('Y') }}</div>
                            <div>
                                <a href="#">Privacy Policy</a>
                                &middot;
                                <a href="#">Terms &amp; Conditions</a>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
        
        {{-- ASSET JAVASCRIPT (PERBAIKI PATH) --}}
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="{{ asset('assets/admin/js/scripts.js') }}"></script>
        
        {{-- Hapus semua Chart.js dan DataTables bawaan SB Admin agar lebih minimalis --}}
        
        @yield('scripts')
        @stack('scripts')
    </body>
</html>
