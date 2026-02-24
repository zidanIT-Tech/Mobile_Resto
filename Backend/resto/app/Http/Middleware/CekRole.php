<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CekRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Cek apakah user sudah login?
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // 2. Ambil user yang sedang login
        $user = Auth::user();

        // 3. Cek apakah role user ada di daftar yang diizinkan?
        // Contoh: jika akses butuh 'admin' atau 'kasir', dan user adalah 'chef', maka ditolak.
        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        // 4. Jika role tidak cocok, tendang atau kasih error
        abort(403, 'Akses Ditolak! Anda tidak memiliki izin.');
    }
}