<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event; // Pastikan Model Event sudah diimpor
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth; // Tetap diperlukan untuk user saat ini, tapi tidak digunakan sebagai FK

class EventController extends Controller
{
    // [R]EAD: Menampilkan daftar event
    public function index()
    {
        // Ambil semua data event dari database
        $events = Event::orderBy('tanggal_mulai', 'desc')->get();
        
        // Memuat view list event dengan data
        return view('admin.event', compact('events'));
    }

    // [C]REATE: Menampilkan form tambah
    public function create()
    {
        // Karena tidak ada FK, kita hanya perlu menampilkan form
        return view('admin.event.create');
    }

    // [C]REATE: Memproses dan menyimpan data baru
    public function store(Request $request)
    {
        $request->validate([
            'nama_event' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after:tanggal_mulai',
            'biaya_pendaftaran' => 'required|integer|min:0',
            'status' => ['required', Rule::in(['upcoming', 'finished', 'cancelled'])],
            'lokasi' => 'required|string|max:255',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', 
        ]);

        $data = $request->except('gambar');

        if ($request->hasFile('gambar')) {
            // Simpan gambar di storage/app/public/event_images
            $path = $request->file('gambar')->store('public/event_images');
            // Dapatkan URL publik untuk disimpan di database
            $data['gambar'] = Storage::url($path); 
        } else {
            $data['gambar'] = null;
        }

        // Hapus field FK yang tidak diperlukan jika ada di request (court_id, organizer_id)
        unset($data['court_id']);
        unset($data['organizer_id']);

        Event::create($data);

        return redirect()->route('event.index')->with('success', 'Event berhasil ditambahkan!');
    }

    // [U]PDATE: Menampilkan form edit
    // Menggunakan Route Model Binding untuk mengambil data berdasarkan ID
    public function edit(Event $event)
    {
        return view('admin.event.edit', compact('event'));
    }

    // [U]PDATE: Memproses dan menyimpan perubahan
    public function update(Request $request, Event $event)
    {
        $request->validate([
            'nama_event' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after:tanggal_mulai',
            'biaya_pendaftaran' => 'required|integer|min:0',
            'status' => ['required', Rule::in(['upcoming', 'finished', 'cancelled'])],
            'lokasi' => 'required|string|max:255',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', 
        ]);
        
        $data = $request->except('gambar');

        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada
            if ($event->gambar) {
                $oldPath = str_replace('/storage/', 'public/', $event->gambar);
                Storage::delete($oldPath);
            }

            // Simpan gambar baru
            $path = $request->file('gambar')->store('public/event_images');
            $data['gambar'] = Storage::url($path);
        }

        // Hapus field FK yang tidak diperlukan dari array data sebelum update
        unset($data['court_id']);
        unset($data['organizer_id']);
        
        $event->update($data);

        return redirect()->route('event.index')->with('success', 'Event berhasil diperbarui!');
    }

    // [D]ELETE: Menghapus event
    public function destroy(Event $event)
    {
        // Hapus gambar dari storage
        if ($event->gambar) {
            $oldPath = str_replace('/storage/', 'public/', $event->gambar);
            Storage::delete($oldPath);
        }

        $event->delete();

        return redirect()->route('event.index')->with('success', 'Event berhasil dihapus!');
    }

    // Karena Route Model Binding (Event $event) digunakan, method show tidak perlu diubah
    public function show(Event $event)
    {
        return view('admin.event.show', compact('event')); // Opsional: jika Anda punya view show
    }
}
