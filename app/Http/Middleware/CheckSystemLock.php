<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class CheckSystemLock
{
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah sistem sedang dikunci
        $isLocked = Storage::exists('system_locked');

        if ($isLocked) {

            // 1. BERI AKSES KHUSUS UNTUK JALUR EMERGENCY
            if ($request->is('emergency/*')) {
                return $next($request);
            }

            // 2. Izinkan proses Logout
            if ($request->routeIs('logout')) {
                return $next($request);
            }

            // 3. Cek User Login
            if (!$request->user()) {
                // PERBAIKAN DI SINI:
                // Gunakan tanda bintang (*) agar 'login' (GET) dan 'login.post' (POST) diizinkan
                if ($request->routeIs('login*')) {
                    return $next($request);
                }

                // Jika user tamu mencoba akses halaman lain -> Tampilkan Maintenance
                return response()->view('errors.maintenance', [], 503);
            }

            // 4. Jika User sudah Login tapi BUKAN Super Admin -> Blokir
            if ($request->user()->role !== 'super_admin') {
                return response()->view('errors.maintenance', [], 503);
            }
        }

        return $next($request);
    }
}
