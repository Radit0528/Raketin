<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lapangan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LapanganController extends Controller
{
    // =====================
    // READ: List Lapangan
    // =====================
    public function index()
    {
        $lapangans = Lapangan::with('user')
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.lapangan', compact('lapangans'));
    }

    // =====================
    // CREATE: Form
    // =====================
    public function create()
    {
        $owners = User::where('role', 'owner')->get();

        return view('admin.lapangan.create', compact('owners'));
    }

    // =====================
    // CREATE: Store
    // =====================
    public function store(Request $request)
    {
        $request->validate([
            'nama'           => 'required|string|max:255',
            'lokasi'         => 'nullable|string',
            'deskripsi'      => 'nullable|string',
            'harga_per_jam'  => 'required|integer|min:0',
            'fasilitas'      => 'nullable|array',
            'fasilitas.*'    => 'string',
            'gambar'         => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'owner_id'       => 'required|exists:users,id',
        ]);

        // pastikan benar owner
        $owner = User::where('id', $request->owner_id)
            ->where('role', 'owner')
            ->firstOrFail();

        $data = $request->only([
            'nama',
            'lokasi',
            'deskripsi',
            'harga_per_jam',
        ]);

        // fasilitas
        $data['fasilitas'] = $request->filled('fasilitas')
            ? implode(', ', $request->fasilitas)
            : null;

        // owner -> user_id
        $data['user_id'] = $owner->id;

        // upload gambar
        if ($request->hasFile('gambar')) {
            $path = $request->file('gambar')->store('lapangan_images', 'public');
            $data['gambar'] = '/storage/' . $path;
        }

        Lapangan::create($data);

        return redirect()
            ->route('lapangan.index')
            ->with('success', 'Lapangan berhasil ditambahkan.');
    }

    // =====================
    // UPDATE: Form
    // =====================
    public function edit(Lapangan $lapangan)
    {
        $owners = User::where('role', 'owner')->get();

        return view('admin.lapangan.edit', compact('lapangan', 'owners'));
    }

    // =====================
    // UPDATE: Save
    // =====================
    public function update(Request $request, Lapangan $lapangan)
    {
        $request->validate([
            'nama'           => 'required|string|max:255',
            'lokasi'         => 'nullable|string',
            'deskripsi'      => 'nullable|string',
            'harga_per_jam'  => 'required|integer|min:0',
            'fasilitas'      => 'nullable|array',
            'fasilitas.*'    => 'string',
            'gambar'         => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'owner_id'       => 'required|exists:users,id',
        ]);

        $owner = User::where('id', $request->owner_id)
            ->where('role', 'owner')
            ->firstOrFail();

        $data = $request->only([
            'nama',
            'lokasi',
            'deskripsi',
            'harga_per_jam',
        ]);

        $data['fasilitas'] = $request->filled('fasilitas')
            ? implode(', ', $request->fasilitas)
            : null;

        $data['user_id'] = $owner->id;

        // replace gambar
        if ($request->hasFile('gambar')) {
            if ($lapangan->gambar) {
                Storage::delete(str_replace('/storage/', 'public/', $lapangan->gambar));
            }

            $path = $request->file('gambar')->store('lapangan_images', 'public');
            $data['gambar'] = '/storage/' . $path;
        }

        $lapangan->update($data);

        return redirect()
            ->route('lapangan.index')
            ->with('success', 'Lapangan berhasil diperbarui.');
    }

    // =====================
    // DELETE
    // =====================
    public function destroy(Lapangan $lapangan)
    {
        if ($lapangan->gambar) {
            Storage::delete(str_replace('/storage/', 'public/', $lapangan->gambar));
        }

        $lapangan->delete();

        return redirect()
            ->route('lapangan.index')
            ->with('success', 'Lapangan berhasil dihapus.');
    }
}
