<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // --- LOGIC REDIRECT KONDISIONAL BERDASARKAN ROLE ---
        
        // Cek apakah pengguna adalah Admin (asumsi Anda memiliki helper isAdmin() di User Model)
        if (Auth::user()->isAdmin()) {
            // Jika Admin, kembalikan ke /dashboard (default)
            return redirect()->intended(route('dashboard', absolute: false));
        }
        
        // Jika Kasir (Non-Admin), arahkan langsung ke Form Transaksi
        return redirect()->route('sales.create'); 
        
        // --- AKHIR LOGIC REDIRECT KONDISIONAL ---
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
