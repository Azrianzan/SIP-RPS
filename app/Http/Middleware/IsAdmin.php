<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah user sudah login DAN apakah rolenya 'Admin'
        if (Auth::check() && Auth::user()->role->nama_role === 'Admin') {
            return $next($request); // Boleh lewat
        }

        // Jika bukan Admin, tolak akses (403 Forbidden)
        abort(403, 'Akses Ditolak. Halaman ini khusus Admin.');
    }
}
