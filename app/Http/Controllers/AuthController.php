<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // --- PERBAIKAN LOGIKA REDIRECT DI SINI ---
            
            // Ambil role user yang sedang login
            // Pastikan model User punya relasi 'role' dan tabel roles punya kolom 'nama_role'
            $role = Auth::user()->role->nama_role; 

            if ($role === 'Admin') {
                return redirect()->route('pengguna.index');
                } 
            elseif ($role === 'Pimpinan') {
                return redirect()->route('dashboard');
            } 
            elseif ($role === 'PJL') {
                return redirect()->route('proyek.index');
            }
            // Fallback jika role tidak terbaca
            return redirect()->route('dashboard');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
