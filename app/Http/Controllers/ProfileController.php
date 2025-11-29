<?php

namespace App\Http\Controllers;

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

    public function profile()
    {
        $user  = Auth::user();
        $today = Carbon::today()->toDateString();

        // Transaksi Lapangan
        $upcomingLapangan = $this->fetchLapangan($user->id)->whereDate('tanggal', '>=', $today)->orderBy('tanggal')->get();
        $historyLapangan  = $this->fetchLapangan($user->id)->whereDate('tanggal', '<',  $today)->orderByDesc('tanggal')->get();

        // Transaksi Event
        $upcomingEvent = $this->fetchEvent($user->id)
            ->where(fn ($q) => 
                $q->whereDate('tanggal', '>=', $today)
                  ->orWhereNull('tanggal')
            )
            ->orderBy('tanggal')
            ->get();

        $historyEvent = $this->fetchEvent($user->id)
            ->whereDate('tanggal', '<', $today)
            ->orderByDesc('tanggal')
            ->get();

        return view('profile.index', compact(
            'user',
            'upcomingLapangan',
            'historyLapangan',
            'upcomingEvent',
            'historyEvent'
        ));
    }

    /**
     * Base query: transaksi lapangan
     */
    private function fetchLapangan($userId)
    {
        return Transaction::with('lapangan')
            ->where('user_id', $userId)
            ->whereNotNull('lapangan_id');
    }

    /**
     * Base query: transaksi event
     */
    private function fetchEvent($userId)
    {
        return Transaction::with('event')
            ->where('user_id', $userId)
            ->whereNotNull('event_id');
    }

    public function edit()
    {
        return view('profile.edit', [
            'user' => Auth::user()
        ]);
    }

    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Validasi input
        $validated = $request->validate([
            'name'  => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Upload foto jika ada
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $filename = time() . '_' . $photo->getClientOriginalName();

            $photo->storeAs('public/profile', $filename);
            $user->profile_photo = 'storage/profile/' . $filename;
        }

        // Update data dasar
        $user->update([
            'name'  => $validated['name'],
            'email' => $validated['email'],
        ]);

        return redirect()->route('profile')->with('success', 'Profil berhasil diperbarui.');
    }
}
