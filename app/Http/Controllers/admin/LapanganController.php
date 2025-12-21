<?php

// Namespace controller admin
namespace App\Http\Controllers\Admin;

// Import controller utama Laravel
use App\Http\Controllers\Controller;

// Import model Lapangan
use App\Models\Lapangan;

// Import model User
use App\Models\User;

// Import Request untuk menangani input form
use Illuminate\Http\Request;

// Import Storage untuk hapus file
use Illuminate\Support\Facades\Storage;

// Deklarasi LapanganController
class LapanganController extends Controller
{
    // =====================
    // READ: Menampilkan daftar lapangan
    // =====================
    public function index()
    {
        // Ambil semua lapangan beserta data owner (user)
        // Urutkan dari ID terbaru
        $lapangans = Lapangan::with('user')
            ->orderBy('id', 'desc')
            ->get();

        // Kirim data ke view admin.lapangan
        return view('admin.lapangan', compact('lapangans'));
    }

    // =====================
    // CREATE: Menampilkan form tambah lapangan
    // =====================
    public function create()
    {
        // Ambil semua user dengan role owner
        $owners = User::where('role', 'owner')->get();

        // Tampilkan form create lapangan
        return view('admin.lapangan.create', compact('owners'));
    }

    // =====================
    // CREATE: Menyimpan data lapangan baru
    // =====================
    public function store(Request $request)
    {
        // Validasi input form
        $request->validate([
            'nama'           => 'required|string|max:255',     // Nama lapangan
            'lokasi'         => 'nullable|string',              // Lokasi lapangan
            'deskripsi'      => 'nullable|string',              // Deskripsi
            'harga_per_jam'  => 'required|integer|min:0',       // Harga sewa
            'fasilitas'      => 'nullable|array',               // Fasilitas (array)
            'fasilitas.*'    => 'string',                        // Isi fasilitas
            'gambar'         => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Gambar
            'owner_id'       => 'required|exists:users,id',     // Owner
        ]);

        // Pastikan user yang dipilih benar-benar owner
        $owner = User::where('id', $request->owner_id)
            ->where('role', 'owner')
            ->firstOrFail();

        // Ambil data utama lapangan
        $data = $request->only([
            'nama',
            'lokasi',
            'deskripsi',
            'harga_per_jam',
        ]);

        // Gabungkan fasilitas menjadi string
        $data['fasilitas'] = $request->filled('fasilitas')
            ? implode(', ', $request->fasilitas)
            : null;

        // Simpan owner sebagai user_id
        $data['user_id'] = $owner->id;

        // Upload gambar lapangan jika ada
        if ($request->hasFile('gambar')) {
            $path = $request->file('gambar')->store('lapangan_images', 'public');
            $data['gambar'] = '/storage/' . $path;
        }

        // Simpan data lapangan ke database
        Lapangan::create($data);

        // Redirect ke halaman index
        return redirect()
            ->route('lapangan.index')
            ->with('success', 'Lapangan berhasil ditambahkan.');
    }

    // =====================
    // UPDATE: Menampilkan form edit lapangan
    // =====================
    public function edit(Lapangan $lapangan)
    {
        // Ambil semua owner
        $owners = User::where('role', 'owner')->get();

        // Tampilkan form edit
        return view('admin.lapangan.edit', compact('lapangan', 'owners'));
    }

    // =====================
    // UPDATE: Menyimpan perubahan lapangan
    // =====================
    public function update(Request $request, Lapangan $lapangan)
    {
        $request->validate([
            'nama'          => 'required|string|max:255',
            'lokasi'        => 'nullable|string',
            'deskripsi'     => 'nullable|string',
            'harga_per_jam' => 'required|integer|min:0',
            'gambar'        => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->only([
            'nama',
            'lokasi',
            'deskripsi',
            'harga_per_jam',
        ]);

        if ($request->hasFile('gambar')) {

            if ($lapangan->gambar) {
                $old = $_SERVER['DOCUMENT_ROOT'] . '/' . $lapangan->gambar;
                if (file_exists($old)) unlink($old);
            }

            $destination = $_SERVER['DOCUMENT_ROOT'] . '/uploads/lapangan_images';
            if (!file_exists($destination)) mkdir($destination, 0777, true);

            $file = $request->file('gambar');
            $filename = time() . '-' . preg_replace('/\s+/', '-', $file->getClientOriginalName());
            $file->move($destination, $filename);

            $data['gambar'] = 'uploads/lapangan_images/' . $filename;
        }
        
        $lapangan->update($data);

        return redirect()
            ->route('lapangan.index')
            ->with('success', 'Lapangan berhasil diperbarui.');
    }


    // =====================
    // DELETE: Menghapus lapangan
    // =====================
    public function destroy(Lapangan $lapangan)
    {
        // Hapus gambar dari storage jika ada
        if ($lapangan->gambar) {
            Storage::delete(
                str_replace('/storage/', 'public/', $lapangan->gambar)
            );
        }

        // Hapus data lapangan dari database
        $lapangan->delete();

        // Redirect kembali ke index
        return redirect()
            ->route('lapangan.index')
            ->with('success', 'Lapangan berhasil dihapus.');
    }
}
