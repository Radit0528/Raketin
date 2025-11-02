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
}
