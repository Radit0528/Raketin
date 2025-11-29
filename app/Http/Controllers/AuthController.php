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
        // 1. Validasi credentials
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials['username'] = trim($credentials['username']);

        // 2. Coba Auth::attempt
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // 3. ✅ Cek jika ada redirect URL dari query parameter
            $redirectUrl = $request->query('redirect');
            
            if ($redirectUrl) {
                // Dekode URL dan redirect ke halaman yang dimaksud
                return redirect($redirectUrl)->with('success', 'Berhasil login!');
            }

            // 4. Cek Role dan Pengarahan default
            if (Auth::user()->role === 'admin') {
                return redirect()->intended(route('admin.dashboard'))
                    ->with('success', 'Berhasil login sebagai Admin!');
            }

            // Default redirect untuk user biasa
            return redirect()->intended(route('dashboard'))
                ->with('success', 'Berhasil login!');
        }

        // 5. Gagal login
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
            'role' => 'user',
        ]);

        return redirect()->route('login')->with('success', 'Akun berhasil dibuat! Silakan login.');
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('dashboard')->with('success', 'Berhasil logout.');
    }

    // Dashboard user
    public function dashboard()
    {
        return view('dashboard');
    }
}