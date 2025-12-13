<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsOwner
{
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah pengguna sudah login DAN memiliki role 'owner'
        if (! Auth::check() || Auth::user()->role !== 'owner') {

            // Anda bisa mengarahkan ke halaman 403 atau dashboard umum
            abort(403, 'Akses Ditolak. Halaman ini hanya untuk Pemilik Lapangan.');
        }

        return $next($request);
    }
}