<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf; // Import Facade PDF
use Illuminate\Http\Request;

class ExportController extends Controller
{
    public function salesExport(Request $request)
    {
        // Ambil data penjualan (dengan filter jika ada)
        $sales = Sale::with('user')->latest()->get(); // Mengambil semua data penjualan

        // Load view Blade (yang akan menjadi template PDF)
        $pdf = Pdf::loadView('exports.sales_pdf', compact('sales'));
        
        // Kembalikan sebagai download
        return $pdf->download('riwayat_penjualan_pdf_' . now()->format('Ymd_His') . '.pdf');
    }
}