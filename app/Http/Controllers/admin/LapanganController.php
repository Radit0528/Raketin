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
        $lapangans = Lapangan::orderBy('id', 'asc')->get();
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
            'lokasi' => 'nullable|string',
            'deskripsi' => 'nullable|string',
            'harga_per_jam' => 'required|integer|min:0',
            'fasilitas' => 'nullable|array',
            'fasilitas.*' => 'string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);
    
        $data = $request->except('gambar');
    
        // Convert array fasilitas menjadi string
        $data['fasilitas'] = $request->has('fasilitas')
            ? implode(', ', $request->fasilitas)
            : null;
    
        // Upload gambar jika ada
        if ($request->hasFile('gambar')) {
            $path = $request->file('gambar')->store('lapangan_images', 'public');
            $data['gambar'] = '/storage/' . $path;
        }
    
        Lapangan::create($data);
    
        return redirect()->route('lapangan.index')
            ->with('success', 'Lapangan berhasil ditambahkan!');
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
            'lokasi' => 'nullable|string',
            'deskripsi' => 'nullable|string',
            'harga_per_jam' => 'required|integer|min:0',
            'fasilitas' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);
    
        $data = $request->except('gambar');
    
        if ($request->hasFile('gambar')) {
            if ($lapangan->gambar) {
                $oldPath = str_replace('/storage/', 'public/', $lapangan->gambar);
                Storage::delete($oldPath);
            }
    
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
