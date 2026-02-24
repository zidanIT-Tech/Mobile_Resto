<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Menampilkan halaman login atau redirect berdasarkan role
    public function index()
    {
        // Cek apakah user sudah login
        if ($user = Auth::user()) {
            if ($user->role == 'admin') return redirect()->route('kasir.index');
            if ($user->role == 'kasir') return redirect()->route('kasir.index');
            if ($user->role == 'chef') return redirect()->route('dapur.index');
        }
        return view('auth.login');
    }

    // Proses login
    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Coba Login (Cek Email, Password, DAN Status Aktif)
        $kredensial = [
            'email' => $request->email,
            'password' => $request->password,
            'status' => 'aktif' // hanya status yang aktif yang bisa login
        ];

        // Jika berhasil login
        if (Auth::attempt($kredensial)) {
            $request->session()->regenerate();
            
            $role = Auth::user()->role;

            // Redirect berdasarkan role
            if ($role === 'chef') {
                return redirect()->intended('/dapur');
            } elseif ($role === 'kasir') {
                return redirect()->intended('/kasir');
            } elseif ($role === 'admin') {
                return redirect()->intended('/admin');
            }
        }

        // Jika gagal login
        return back()->withErrors([
            'email' => 'Email atau password salah, atau akun nonaktif.',
        ]);
    }

    // Proses logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}