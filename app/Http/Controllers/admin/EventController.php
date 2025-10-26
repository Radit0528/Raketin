<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    // [R]EAD: Menampilkan daftar event
    public function index()
    {
        $events = Event::orderBy('tanggal_mulai', 'desc')->get();
        return view('admin.event', compact('events'));
    }

    // [C]REATE: Menampilkan form tambah
    public function create()
    {
        return view('admin.event.create');
    }

    // [C]REATE: Memproses dan menyimpan data baru
    public function store(Request $request)
    {
        $request->validate([
            'nama_event' => 'required|string|max:255',
            'lokasi' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after:tanggal_mulai',
            'biaya_pendaftaran' => 'required|integer|min:0',
            'status' => ['required', Rule::in(['upcoming', 'finished', 'cancelled'])],
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->except('gambar');

        if ($request->hasFile('gambar')) {
            // Simpan gambar di storage/app/public/event_images
            $path = $request->file('gambar')->store('event_images', 'public');
            $data['gambar'] = '/storage/' . $path; // path publik untuk Blade
        } else {
            $data['gambar'] = null;
        }

        // Hapus FK yang tidak digunakan (jika ada)
        unset($data['court_id'], $data['organizer_id']);

        Event::create($data);

        return redirect()->route('event.index')->with('success', 'Event berhasil ditambahkan!');
    }

    // [U]PDATE: Menampilkan form edit
    public function edit(Event $event)
    {
        return view('admin.event.edit', compact('event'));
    }

    // [U]PDATE: Memproses dan menyimpan perubahan
    public function update(Request $request, Event $event)
    {
        $request->validate([
            'nama_event' => 'required|string|max:255',
            'lokasi' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after:tanggal_mulai',
            'biaya_pendaftaran' => 'required|integer|min:0',
            'status' => ['required', Rule::in(['upcoming', 'finished', 'cancelled'])],
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->except('gambar');

        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada
            if ($event->gambar) {
                $oldPath = str_replace('/storage/', 'public/', $event->gambar);
                Storage::delete($oldPath);
            }

            // Simpan gambar baru di storage/app/public/event_images
            $path = $request->file('gambar')->store('event_images', 'public');
            $data['gambar'] = '/storage/' . $path;
        }

        // Hapus FK yang tidak digunakan (jika ada)
        unset($data['court_id'], $data['organizer_id']);

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

    // [R]EAD: Menampilkan detail event (opsional)
    public function show(Event $event)
    {
        return view('admin.event.show', compact('event'));
    }
}
