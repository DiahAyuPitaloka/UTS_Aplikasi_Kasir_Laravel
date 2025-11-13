<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Pastikan Anda mengimpor Model yang dibutuhkan
        
        $today = now()->toDateString();
        
        // 1. Mengambil data Penjualan Hari Ini
        $sales_today = Sale::whereDate('created_at', $today)->get();
        $total_revenue_today = $sales_today->sum('total_amount');
        $total_transactions_today = $sales_today->count();
        
        // 2. Mengambil Produk Stok Rendah (Variabel yang hilang)
        $low_stock_products = Product::where('stock', '<', 5)->orderBy('stock')->take(5)->get();
        
        // 3. Mengambil Data User Kasir
        $total_kasir = User::where('role', 'kasir')->count();

        // 4. Mengambil 5 Transaksi Terbaru
        $latest_sales = Sale::with('user')->latest()->take(5)->get();
        
        // Mengirim SEMUA data ke view
        return view('dashboard', compact(
            'total_revenue_today', 
            'total_transactions_today', 
            'low_stock_products', // <-- VARIABEL INI HARUS DI-PASS
            'total_kasir',
            'latest_sales'
        ));
    }
}