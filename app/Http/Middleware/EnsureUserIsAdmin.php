<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Pastikan pengguna sudah login
        // 2. Cek apakah kolom 'role' pada pengguna yang login BUKAN 'admin'
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            
            // Jika tidak memenuhi syarat admin, hentikan akses dan kembalikan response 403
            abort(403, 'Akses Ditolak. Halaman ini hanya untuk Admin.');
        }

        return $next($request);
    }
}
