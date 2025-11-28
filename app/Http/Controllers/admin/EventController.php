<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Lapangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class EventController extends Controller
{
    // [R]EAD: Menampilkan daftar event
    public function index()
    {
        $events = Event::with('lapangan')->orderBy('tanggal_mulai', 'desc')->get();

        return view('admin.event', compact('events'));
    }

    // [C]REATE: Menampilkan form tambah
    public function create()
    {
        $lapangans = Lapangan::all();

        return view('admin.event.create', compact('lapangans'));
    }

    // [C]REATE: Memproses dan menyimpan data baru
    public function store(Request $request)
    {
        $request->validate([
            'nama_event' => 'required|string|max:255',
            'lapangan_id' => 'nullable|exists:lapangans,id',
            'deskripsi' => 'required|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after:tanggal_mulai',
            'biaya_pendaftaran' => 'required|integer|min:0',
            'status' => ['required', Rule::in(['upcoming', 'finished', 'cancelled'])],
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->except('gambar');

        // Ambil lokasi otomatis dari lapangan yang dipilih
        if ($request->filled('lapangan_id')) {
            $lapangan = Lapangan::find($request->lapangan_id);
            if ($lapangan) {
                $data['lokasi'] = $lapangan->lokasi;
            }
        }

        if ($request->hasFile('gambar')) {
            $path = $request->file('gambar')->store('event_images', 'public');
            $data['gambar'] = '/storage/'.$path;
        }

        Event::create($data);

        return redirect()->route('event.index')->with('success', 'Event berhasil ditambahkan!');
    }

    // [U]PDATE: Menampilkan form edit
    public function edit(Event $event)
    {
        $lapangans = Lapangan::all();

        return view('admin.event.edit', compact('event', 'lapangans'));
    }

    // [U]PDATE: Memproses dan menyimpan perubahan
    public function update(Request $request, Event $event)
    {
        $request->validate([
            'nama_event' => 'required|string|max:255',
            'lapangan_id' => 'nullable|exists:lapangans,id',
            'deskripsi' => 'required|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after:tanggal_mulai',
            'biaya_pendaftaran' => 'required|integer|min:0',
            'status' => ['required', Rule::in(['upcoming', 'finished', 'cancelled'])],
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->except('gambar');

        // Ambil lokasi otomatis dari lapangan yang dipilih
        if ($request->filled('lapangan_id')) {
            $lapangan = Lapangan::find($request->lapangan_id);
            if ($lapangan) {
                $data['lokasi'] = $lapangan->lokasi;
            }
        }

        if ($request->hasFile('gambar')) {
            if ($event->gambar) {
                $oldPath = str_replace('/storage/', 'public/', $event->gambar);
                Storage::delete($oldPath);
            }

            $path = $request->file('gambar')->store('event_images', 'public');
            $data['gambar'] = '/storage/'.$path;
        }

        $event->update($data);

        return redirect()->route('event.index')->with('success', 'Event berhasil diperbarui!');
    }

    // [D]ELETE: Menghapus event
    public function destroy(Event $event)
    {
        if ($event->gambar) {
            $oldPath = str_replace('/storage/', 'public/', $event->gambar);
            Storage::delete($oldPath);
        }

        $event->delete();

        return redirect()->route('event.index')->with('success', 'Event berhasil dihapus!');
    }

    // [R]EAD: Menampilkan detail event
    public function show(Event $event)
    {
        $event->load('lapangan'); // Ambil data lapangan juga

        return view('admin.event.show', compact('event'));
    }
}
