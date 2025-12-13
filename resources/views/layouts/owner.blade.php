<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Dashboard Owner')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- CSS (SAMAKAN DENGAN ADMIN) --}}
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
        <link href="{{ asset('assets/admin/css/styles.css') }}" rel="stylesheet" />
        <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>

<body class="sb-nav-fixed">

<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
    <a class="navbar-brand ps-3" href="#">
        Dashboard Pemilik Lapangan
    </a>

    <ul class="navbar-nav ms-auto me-3">
        <li class="nav-item text-white mt-2 me-3">
            {{ auth()->user()->name }}
        </li>
        <li class="nav-item">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="btn btn-sm btn-danger">Logout</button>
            </form>
        </li>
    </ul>
</nav>

<div id="layoutSidenav_content">
    <main class="container-fluid px-4 py-4" style="margin-top: 60px;">
        @yield('content')
    </main>
</div>

<script src="{{ asset('js/scripts.js') }}"></script>
</body>
</html>
