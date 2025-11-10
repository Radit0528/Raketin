<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    // Menampilkan daftar event (opsional)
    public function index()
    {
        $events = Event::orderBy('tanggal_mulai', 'desc')->get();
        return view('event.index', compact('events'));
    }

    // Menampilkan detail event
    public function show($id)
    {
        $event = Event::findOrFail($id);
        return view('event.detail', compact('event'));
    }

    public function search(Request $request)
{
    $query = Event::query();

    // Filter berdasarkan nama event
    if ($request->filled('nama_event')) {
        $query->where('nama_event', 'like', '%' . $request->nama_event . '%');
    }

    // Filter berdasarkan lokasi (jika kolom lokasi ada di tabel event)
    if ($request->filled('lokasi')) {
        $query->where('lokasi', 'like', '%' . $request->lokasi . '%');
    }

    // Filter berdasarkan tanggal mulai
    if ($request->filled('tanggal_mulai')) {
        $query->whereDate('tanggal_mulai', $request->tanggal_mulai);
    }

    // Filter berdasarkan kategori (jika tabel event punya kolom 'kategori')
    if ($request->filled('kategori')) {
        $query->where('kategori', $request->kategori);
    }

    // Ambil hasil pencarian
    $events = $query->orderBy('tanggal_mulai', 'desc')->get();

    return view('event.search', compact('events'));
}

}
