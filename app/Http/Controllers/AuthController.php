<?php

// Namespace controller utama
namespace App\Http\Controllers;

// Import model User
use App\Models\User;

// Import Request untuk menangani input form
use Illuminate\Http\Request;

// Import Auth untuk autentikasi
use Illuminate\Support\Facades\Auth;

// Import Hash untuk enkripsi password
use Illuminate\Support\Facades\Hash;

// Import ValidationException (jika diperlukan untuk handling error khusus)
use Illuminate\Validation\ValidationException;

// Deklarasi AuthController
class AuthController extends Controller
{
    /**
     * Menampilkan halaman login
     */
    public function showLogin()
    {
        // Return view login
        return view('login');
    }

    /**
     * Proses login user
     */
    public function login(Request $request)
    {
        // ===============================
        // 1. VALIDASI INPUT LOGIN
        // ===============================

        // Validasi username dan password
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Bersihkan spasi di username
        $credentials['username'] = trim($credentials['username']);

        // ===============================
        // 2. PROSES AUTENTIKASI
        // ===============================

        // Coba login menggunakan Auth::attempt
        if (Auth::attempt($credentials)) {

            // Regenerasi session untuk keamanan
            $request->session()->regenerate();

            // ===============================
            // 3. REDIRECT KHUSUS (JIKA ADA)
            // ===============================

            // Ambil parameter redirect dari URL (?redirect=...)
            $redirectUrl = $request->query('redirect');

            // Jika ada redirect URL, arahkan ke halaman tersebut
            if ($redirectUrl) {
                return redirect($redirectUrl)
                    ->with('success', 'Berhasil login!');
            }

            // ===============================
            // 4. REDIRECT BERDASARKAN ROLE
            // ===============================

            // Ambil role user yang login
            $userRole = Auth::user()->role;

            // Redirect untuk admin
            if ($userRole === 'admin') {
                return redirect()
                    ->intended(route('admin.dashboard'))
                    ->with('success', 'Berhasil login sebagai Admin!');
            }

            // Redirect untuk owner (pemilik lapangan)
            if ($userRole === 'owner') {
                return redirect()
                    ->intended(route('owner.dashboard'))
                    ->with('success', 'Berhasil login sebagai Pemilik Lapangan!');
            }

            // Redirect default untuk user biasa
            return redirect()
                ->intended(route('dashboard'))
                ->with('success', 'Berhasil login!');
        }

        // ===============================
        // 5. LOGIN GAGAL
        // ===============================

        // Kembalikan ke halaman login dengan pesan error
        return back()
            ->withErrors([
                'username' => 'Username atau password salah.',
            ])
            ->onlyInput('username');
    }

    /**
     * Menampilkan halaman register
     */
    public function showRegister()
    {
        // Return view register
        return view('register');
    }

    /**
     * Proses registrasi user baru
     */
    public function register(Request $request)
    {
        // Validasi input register
        $request->validate([
            'name' => 'required|string|max:255',      // Nama lengkap
            'username' => 'required|string|max:255',  // Username
            'email' => 'required|email|unique:users', // Email unik
            'password' => 'required|min:6|confirmed', // Password + konfirmasi
        ]);

        // Simpan user baru ke database
        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Enkripsi password
            'role' => 'user', // Default role adalah user biasa
        ]);

        // Redirect ke halaman login
        return redirect()
            ->route('login')
            ->with('success', 'Akun berhasil dibuat! Silakan login.');
    }

    /**
     * Proses logout user
     */
    public function logout(Request $request)
    {
        // Logout user
        Auth::logout();

        // Invalidasi session lama
        $request->session()->invalidate();

        // Regenerasi CSRF token
        $request->session()->regenerateToken();

        // Redirect ke dashboard
        return redirect()
            ->route('dashboard')
            ->with('success', 'Berhasil logout.');
    }

    /**
     * Dashboard user (default)
     */
    public function dashboard()
    {
        // Tampilkan dashboard user
        return view('dashboard');
    }
}
