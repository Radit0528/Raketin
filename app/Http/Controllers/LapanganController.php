<?php

namespace App\Http\Controllers;

use App\Models\Lapangan;
use Illuminate\Http\Request;

class LapanganController extends Controller
{
    public function index()
    {
        $lapangans = Lapangan::all();
        return view('lapangan.index', compact('lapangans'));
    }

    public function show($id)
    {
        $lapangan = Lapangan::findOrFail($id);
        return view('lapangan.detail', compact('lapangan'));
    }

   public function pilihWaktu($id, Request $request)
{
    $lapangan = Lapangan::findOrFail($id);
    $tanggalDipilih = $request->query('tanggal', now()->format('Y-m-d'));

    return view('lapangan.pilih-waktu', compact('lapangan', 'tanggalDipilih'));
}

}
