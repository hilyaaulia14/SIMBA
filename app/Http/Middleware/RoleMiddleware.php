<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan masuk terlebih dahulu untuk mengakses sistem.');
        }

        $user = Auth::user();

        if ($role === 'admin' && !$user->isAdmin()) {
            return redirect()->route('pk.dashboard')->with('error', 'Anda tidak memiliki hak akses ke halaman Admin / Bagian Umum.');
        }

        if ($role === 'pk' && !$user->isPK()) {
            return redirect()->route('admin.dashboard')->with('error', 'Anda tidak memiliki hak akses ke halaman Pembimbing Kemasyarakatan.');
        }

        return $next($request);
    }
}
