<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// app/Http/Middleware/AdminMiddleware.php

// ... (Use statements lainnya di bagian atas)

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah user sudah login dan memiliki role 'admin'
        if (auth()->check() && auth()->user()->role === 'admin') {
            return $next($request);
        }
        
        // Jika bukan admin, redirect ke dashboard atau berikan error
        // Kita redirect ke dashboard dengan pesan status (flash message)
        return redirect('/dashboard')->with('status', 'Akses ditolak. Anda tidak memiliki izin Administrator.');
    }
}
