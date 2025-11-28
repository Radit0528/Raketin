<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Tampilkan halaman login
    public function showLogin()
    {
        return view('login');
    }

    // Proses login
    public function login(Request $request)
    {
        // 1. WAJIB: Validasi dan Definisi $credentials
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Opsional: Membersihkan spasi untuk pencegahan bug login
        $credentials['username'] = trim($credentials['username']);

        // 2. Coba Auth::attempt
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // 3. Cek Role dan Pengarahan
            if (Auth::user()->role === 'admin') {
                // Admin diarahkan ke rute yang bernama 'admin.dashboard'
                return redirect()->intended(route('dashboard'))->with('success', 'Berhasil login sebagai Admin!');
            }

            // Pengarahan default ke dashboard user
            return redirect()->intended(route('dashboard'))->with('success', 'Berhasil login!');
        }

        // 4. Pengarahan saat Gagal
        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ])->onlyInput('username');
    }

    // Tampilkan halaman register
    public function showRegister()
    {
        return view('register');
    }

    // Proses register
    public function register(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);

        User::create([
            'name' => $request->nama,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user', // Default role 'user' saat register
        ]);

        return redirect()->route('login')->with('success', 'Akun berhasil dibuat! Silakan login.');
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // PERBAIKAN: Arahkan ke halaman login, bukan dashboard.
        return redirect()->route('login')->with('success', 'Berhasil logout.');
    }

    public function dashboard()
    {
        // Tampilkan view dashboard user biasa
        return view('dashboard');
    }
    public function profile()
    {
        return view('profile.index');
    }
}
