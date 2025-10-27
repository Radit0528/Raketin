<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lapangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class LapanganController extends Controller
{
    // [R]EAD: Menampilkan daftar lapangan
    public function index()
    {
        $lapangans = Lapangan::orderBy('id', 'desc')->get();
        return view('admin.lapangan', compact('lapangans'));
    }

    // [C]REATE: Menampilkan form tambah
    public function create()
    {
        return view('admin.lapangan.create');
    }

    // [C]REATE: Memproses dan menyimpan data baru
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'harga_per_jam' => 'required|integer|min:0',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->except('gambar');

        if ($request->hasFile('gambar')) {
            // Simpan gambar di storage/app/public/lapangan_images
            $path = $request->file('gambar')->store('lapangan_images', 'public');
            // Simpan URL publik ke database (bisa langsung dipanggil dengan asset())
            $data['gambar'] = '/storage/' . $path;
        } else {
            $data['gambar'] = null;
        }

        Lapangan::create($data);

        return redirect()->route('lapangan.index')->with('success', 'Lapangan berhasil ditambahkan!');
    }

    // [U]PDATE: Menampilkan form edit
    public function edit(Lapangan $lapangan)
    {
        return view('admin.lapangan.edit', compact('lapangan'));
    }

    // [U]PDATE: Memproses dan menyimpan perubahan
    public function update(Request $request, Lapangan $lapangan)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'harga_per_jam' => 'required|integer|min:0',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->except('gambar');

        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada
            if ($lapangan->gambar) {
                $oldPath = str_replace('/storage/', 'public/', $lapangan->gambar);
                Storage::delete($oldPath);
            }

            // Simpan gambar baru di storage/app/public/lapangan_images
            $path = $request->file('gambar')->store('lapangan_images', 'public');
            $data['gambar'] = '/storage/' . $path;
        }

        $lapangan->update($data);

        return redirect()->route('lapangan.index')->with('success', 'Lapangan berhasil diperbarui!');
    }

    // [D]ELETE: Menghapus lapangan
    public function destroy(Lapangan $lapangan)
    {
        // Hapus gambar dari storage jika ada
        if ($lapangan->gambar) {
            $oldPath = str_replace('/storage/', 'public/', $lapangan->gambar);
            Storage::delete($oldPath);
        }

        $lapangan->delete();

        return redirect()->route('lapangan.index')->with('success', 'Lapangan berhasil dihapus!');
    }

    
}
