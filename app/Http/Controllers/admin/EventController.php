<?php

// Namespace controller khusus admin
namespace App\Http\Controllers\Admin;

// Import controller utama
use App\Http\Controllers\Controller;

// Import model yang digunakan
use App\Models\Event;
use App\Models\Lapangan;

// Import Request untuk mengambil input form
use Illuminate\Http\Request;

// Import Storage untuk upload & hapus file
use Illuminate\Support\Facades\Storage;

// Import Rule untuk validasi enum/status
use Illuminate\Validation\Rule;

// Deklarasi EventController
class EventController extends Controller
{
    /**
     * [R]EAD
     * Menampilkan daftar event
     */
    public function index()
    {
        // Ambil semua event beserta relasi lapangan
        // Urutkan berdasarkan tanggal mulai terbaru
        $events = Event::with('lapangan')
            ->orderBy('tanggal_mulai', 'desc')
            ->get();

        // Kirim data ke view admin.event
        return view('admin.event', compact('events'));
    }

    /**
     * [C]REATE
     * Menampilkan form tambah event
     */
    public function create()
    {
        // Ambil semua data lapangan untuk dropdown
        $lapangans = Lapangan::all();

        // Tampilkan form create event
        return view('admin.event.create', compact('lapangans'));
    }

    /**
     * [C]REATE
     * Menyimpan data event baru
     */
    public function store(Request $request)
    {
        // Validasi input dari form
        $request->validate([
            'nama_event' => 'required|string|max:255', // Nama event wajib
            'lapangan_id' => 'nullable|exists:lapangans,id', // Relasi lapangan (opsional)
            'deskripsi' => 'required|string', // Deskripsi event
            'tanggal_mulai' => 'required|date', // Tanggal mulai
            'tanggal_selesai' => 'nullable|date|after:tanggal_mulai', // Tanggal selesai
            'biaya_pendaftaran' => 'required|integer|min:0', // Biaya minimal 0
            'status' => ['required', Rule::in(['upcoming', 'finished', 'cancelled'])], // Status event
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Upload gambar
        ]);

        // Ambil semua data request kecuali gambar
        $data = $request->except('gambar');

        // ===============================
        // Ambil lokasi otomatis dari lapangan
        // ===============================
        if ($request->filled('lapangan_id')) {
            $lapangan = Lapangan::find($request->lapangan_id);
            if ($lapangan) {
                $data['lokasi'] = $lapangan->lokasi;
            }
        }

        // ===============================
        // Upload gambar jika ada
        // ===============================
        if ($request->hasFile('gambar')) {
            // Simpan gambar ke storage/public/event_images
            $path = $request->file('gambar')->store('event_images', 'public');

            // Simpan path gambar ke database
            $data['gambar'] = '/storage/' . $path;
        }

        // Simpan data event ke database
        Event::create($data);

        // Redirect ke halaman index event
        return redirect()
            ->route('event.index')
            ->with('success', 'Event berhasil ditambahkan!');
    }

    /**
     * [U]PDATE
     * Menampilkan form edit event
     */
    public function edit(Event $event)
    {
        // Ambil semua lapangan untuk dropdown edit
        $lapangans = Lapangan::all();

        // Tampilkan form edit
        return view('admin.event.edit', compact('event', 'lapangans'));
    }

    /**
     * [U]PDATE
     * Memproses update data event
     */
    public function update(Request $request, Event $event)
    {
        // Validasi input
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

        // Ambil data selain gambar
        $data = $request->except('gambar');

        // ===============================
        // Update lokasi dari lapangan
        // ===============================
        if ($request->filled('lapangan_id')) {
            $lapangan = Lapangan::find($request->lapangan_id);
            if ($lapangan) {
                $data['lokasi'] = $lapangan->lokasi;
            }
        }

        // ===============================
        // Update gambar jika diupload
        // ===============================
        if ($request->hasFile('gambar')) {

            // Hapus gambar lama jika ada
            if ($event->gambar) {
                $oldPath = str_replace('/storage/', 'public/', $event->gambar);
                Storage::delete($oldPath);
            }

            // Simpan gambar baru
            $path = $request->file('gambar')->store('event_images', 'public');
            $data['gambar'] = '/storage/' . $path;
        }

        // Update data event
        $event->update($data);

        // Redirect ke halaman index
        return redirect()
            ->route('event.index')
            ->with('success', 'Event berhasil diperbarui!');
    }

    /**
     * [D]ELETE
     * Menghapus data event
     */
    public function destroy(Event $event)
    {
        // Hapus gambar dari storage jika ada
        if ($event->gambar) {
            $oldPath = str_replace('/storage/', 'public/', $event->gambar);
            Storage::delete($oldPath);
        }

        // Hapus data event dari database
        $event->delete();

        // Redirect kembali ke index
        return redirect()
            ->route('event.index')
            ->with('success', 'Event berhasil dihapus!');
    }

    /**
     * [R]EAD
     * Menampilkan detail event
     */
    public function show(Event $event)
    {
        // Load relasi lapangan
        $event->load('lapangan');

        // Tampilkan halaman detail event
        return view('admin.event.show', compact('event'));
    }
}
