<?php


// Namespace controller
namespace App\Http\Controllers;

// Import Request
use Illuminate\Http\Request;

// Import Auth untuk user login
use Illuminate\Support\Facades\Auth;

// Import model Transaction
use App\Models\Transaction;

// Import Carbon untuk manipulasi tanggal
use Carbon\Carbon;

// Deklarasi ProfileController
class ProfileController extends Controller
{
    /**
     * Constructor
     * Middleware auth agar hanya user login yang bisa akses
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Halaman profil user
     * Menampilkan data user dan riwayat transaksi
     */
    public function profile()
    {
        // Ambil user yang sedang login
        $user  = Auth::user();
        // Ambil tanggal hari ini
        $today = Carbon::today()->toDateString();

        // Transaksi Lapangan
        // Booking lapangan yang akan datang
        $upcomingLapangan = $this->fetchLapangan($user->id)->whereDate('tanggal', '>=', $today)->orderBy('tanggal')->get();
        // Riwayat booking lapangan yang sudah lewat
        $historyLapangan  = $this->fetchLapangan($user->id)->whereDate('tanggal', '<',  $today)->orderByDesc('tanggal')->get();

        // Transaksi Event
        // Event yang akan datang (atau tanpa tanggal)
        $upcomingEvent = $this->fetchEvent($user->id)
            ->where(fn ($q) => 
                $q->whereDate('tanggal', '>=', $today)
                  ->orWhereNull('tanggal')
            )
            ->orderBy('tanggal')
            ->get();

        // Riwayat event yang sudah lewat
        $historyEvent = $this->fetchEvent($user->id)
            ->whereDate('tanggal', '<', $today)
            ->orderByDesc('tanggal')
            ->get();

        // Kirim semua data ke view profile.index
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
        return Transaction::with('lapangan')  // relasi lapangan
            ->where('user_id', $userId)       // milik user login
            ->whereNotNull('lapangan_id');    // yang ada lapangan_id
    }

    /**
     * Base query: transaksi event
     */
    private function fetchEvent($userId)
    {
        return Transaction::with('event')   // relasi event
            ->where('user_id', $userId)     // milik user login
            ->whereNotNull('event_id');     // yang ada event_id
    }

    /**
     * Menampilkan form edit profil
     */
    public function edit()
    {
        return view('profile.edit', [
            'user' => Auth::user()
        ]);
    }

    /**
     * Memperbarui data profil user
     */
    public function update(Request $request)
    {
        // Ambil user login
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Validasi input
        $validated = $request->validate([
            'name'  => 'required|string|max:100',  //nama
            'email' => 'required|email|unique:users,email,' . $user->id,    //email
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',    //foto profil
        ]);

        // Upload foto jika ada
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');

            // Nama file unik
            $filename = time() . '_' . $photo->getClientOriginalName();
            // Simpan ke storage/app/public/profile
            $photo->storeAs('public/profile', $filename);
            // Simpan path foto ke database
            $user->profile_photo = 'storage/profile/' . $filename;
        }

        // Update data dasar
        $user->update([
            'name'  => $validated['name'],
            'email' => $validated['email'],
        ]);

        // Redirect kembali ke halaman profil
        return redirect()->route('profile')->with('success', 'Profil berhasil diperbarui.');
    }
}
