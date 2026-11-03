<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pegawai;

class AuthController extends Controller
{
    public function showLogin(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            return $user->isAdmin() ? redirect()->route('admin.dashboard') : redirect()->route('pk.dashboard');
        }

        $activeRole = $request->query('role', 'admin');
        return view('auth.login', compact('activeRole'));
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'nip_usn' => 'required|string',
            'password' => 'required|string',
            'role_target' => 'nullable|string',
        ], [
            'nip_usn.required' => 'NIP (Username) wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt(['nip_usn' => $credentials['nip_usn'], 'password' => $credentials['password']], $remember)) {
            $request->session()->regenerate();
            $user = Auth::user();

            // Check if user matches targeted role tab if specified
            if (!empty($credentials['role_target'])) {
                if ($credentials['role_target'] === 'admin' && !$user->isAdmin()) {
                    Auth::logout();
                    return back()->withErrors([
                        'nip_usn' => 'Akun NIP ini bukan merupakan akun Admin / Bagian Umum. Silakan pilih tab Pembimbing Kemasyarakatan.',
                    ])->withInput($request->only('nip_usn'));
                }
                if ($credentials['role_target'] === 'pk' && !$user->isPK()) {
                    Auth::logout();
                    return back()->withErrors([
                        'nip_usn' => 'Akun NIP ini bukan merupakan akun Pembimbing Kemasyarakatan. Silakan pilih tab Admin / Bagian Umum.',
                    ])->withInput($request->only('nip_usn'));
                }
            }

            if ($user->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'))->with('success', 'Selamat datang kembali, ' . $user->nama_pegawai . '!');
            } else {
                return redirect()->intended(route('pk.dashboard'))->with('success', 'Selamat datang kembali, ' . $user->nama_pegawai . '!');
            }
        }

        return back()->withErrors([
            'nip_usn' => 'NIP atau kata sandi yang Anda masukkan tidak sesuai.',
        ])->withInput($request->only('nip_usn'));
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar dari sistem.');
    }
}
