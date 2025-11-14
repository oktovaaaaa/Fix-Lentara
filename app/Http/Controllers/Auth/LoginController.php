<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Tampilkan form login admin.
     */
    public function showLoginForm()
    {
        // Kalau sudah login, langsung lempar ke halaman admin (stats)
        if (Auth::check()) {
            return redirect()->route('admin.stats.index');
        }

        return view('auth.login');
    }

    /**
     * Proses login (email + password).
     */
    public function login(Request $request)
    {
        // Validasi sederhana: wajib email & password, dan harus ada @ di email
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Coba login dengan guard "web" (default)
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Setelah login berhasil → ke halaman CRUD Statistik
            return redirect()->route('admin.stats.index');
        }

        // Kalau gagal
        return back()
            ->withInput($request->only('email'))
            ->withErrors([
                'email' => 'Email atau password salah.',
            ]);
    }

    /**
     * Logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Setelah logout, balik ke landing/home
        return redirect()->route('home');
    }
}
