<?php

// routes/web.php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth; // <-- BARU: Diperlukan untuk cek Auth::user() di luar middleware
use App\Http\Controllers\ProfileController; // <-- BARU: Pastikan ini ada (dari Breeze)
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\SaleController; // <-- BARU: Controller untuk Transaksi
use App\Http\Controllers\ExportController;
use App\Http\Controllers\DashboardController;


// 1. LANDING PAGE (Fitur Wajib: Halaman Utama Publik)
Route::view('/', 'welcome')->name('welcome');


// 2. RUTE TERPROTEKSI (Middleware: auth & verified)
Route::middleware(['auth', 'verified'])->group(function () {
    
    // RUTE DASHBOARD DENGAN LOGIKA OTORISASI PERAN
    Route::get('dashboard', function () {
    if (Auth::user()->role === 'admin') {
        // Jika Admin, tampilkan halaman dashboard (ringkasan)
        return view('dashboard');
    }
    // Jika Kasir, langsung redirect ke Form Transaksi
    return redirect()->route('sales.create'); 
})->name('dashboard');

    // Profil Pengguna (Fitur Wajib: edit profil, ubah password)
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // RUTE TRANSAKSI (Dapat diakses oleh Admin dan Kasir)
    Route::get('sales/create', [SaleController::class, 'create'])->name('sales.create'); // Form Kasir
    Route::post('sales/store', [SaleController::class, 'store'])->name('sales.store'); // Proses Transaksi
    
    // 3. RUTE ADMIN (Middleware: admin)
    
    Route::middleware(['admin'])->group(function () {

        // CRUD PRODUK (Manajemen Data Utama - CRUD Wajib)
        Route::resource('products', ProductController::class);

        // CRUD KATEGORI
        Route::resource('categories', CategoryController::class)->except(['show']); // Exclude show

        // CRUD USER (Manajemen User - CRUD Wajib)
        Route::resource('users', UserManagementController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
        
        // RIWAYAT PENJUALAN (Hanya Admin yang bisa lihat laporan)
        Route::get('sales', [SaleController::class, 'index'])->middleware('admin')->name('sales.index');

        // Rute Export Penjualan (Nilai Tambah)
        Route::get('sales/export', [ExportController::class, 'salesExport'])->name('sales.export');

        // Rute utama Dashboard harus mengarah ke Controller yang baru dibuat
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    });
});

// Autentikasi default Laravel Breeze
require __DIR__.'/auth.php';