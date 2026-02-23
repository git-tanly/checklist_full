<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckGlobalChecklistAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Pastikan User Sedang Login
        if (!Auth::check()) {
            return $next($request); // Biarkan middleware Auth lain yang menangani redirect login
        }

        $user = Auth::user();

        // 2. CEK GLOBAL FLAG DARI PORTAL
        // Karena $user konek ke DB Portal, kita bisa langsung baca kolom ini.
        if ($user->access_checklist !== true) {

            // Opsional: Logout paksa session lokal agar bersih
            // Auth::logout();

            // Tampilkan error 403
            abort(403, 'AKSES DITOLAK: Akun Portal Anda tidak diizinkan mengakses Uniform System. Hubungi IT Dept.');
        }

        // 3. CEK STATUS AKTIF GLOBAL (Opsional, jika belum ada middleware is_active)
        if ($user->is_active !== true) {
            abort(403, 'AKUN DINONAKTIFKAN: Hubungi Administrator.');
        }

        return $next($request);
    }
}
