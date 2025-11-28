<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;
use Carbon\Carbon;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function profile(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::today()->toDateString();

        // Upcoming Lapangan
        $upcomingLapangan = Transaction::with('lapangan')
            ->where('user_id', $user->id)
            ->whereNotNull('lapangan_id')
            ->whereDate('tanggal', '>=', $today)
            ->orderBy('tanggal', 'asc')
            ->get();

        // History Lapangan
        $historyLapangan = Transaction::with('lapangan')
            ->where('user_id', $user->id)
            ->whereNotNull('lapangan_id')
            ->whereDate('tanggal', '<', $today)
            ->orderBy('tanggal', 'desc')
            ->get();

        // Upcoming Event
        $upcomingEvent = Transaction::with('event')
            ->where('user_id', $user->id)
            ->whereNotNull('event_id')
            ->where(function ($query) use ($today) {
                $query->whereDate('tanggal', '>=', $today)
                      ->orWhereNull('tanggal');
            })
            ->orderBy('tanggal', 'asc')
            ->get();

        // History Event
        $historyEvent = Transaction::with('event')
            ->where('user_id', $user->id)
            ->whereNotNull('event_id')
            ->whereDate('tanggal', '<', $today)
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('profile.index', compact(
            'user',
            'upcomingLapangan',
            'historyLapangan',
            'upcomingEvent',
            'historyEvent'
        ));
    }

    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $request->validate([
            'name'  => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Jika ada file foto baru
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $photoName = time() . '_' . $photo->getClientOriginalName();

            // Simpan foto ke storage
            $photo->storeAs('public/profile', $photoName);

            // Simpan path foto
            $user->profile_photo = 'storage/profile/' . $photoName;
        }

        // Update data dasar
        $user->name  = $request->name;
        $user->email = $request->email;

        // Simpan ke database
        $user->save();

        return redirect()->route('profile')->with('success', 'Profil berhasil diperbarui.');
    }
}
