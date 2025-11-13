<?php

namespace App\Exports;

use App\Models\Sale;
use Maatwebsite\Excel\Concerns\FromQuery; // GANTI: Import FromQuery
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SalesExport implements FromQuery, WithHeadings, WithMapping // GANTI: Implementasi FromQuery
{
    /**
     * @return \Illuminate\Database\Eloquent\Builder
     * Menggantikan collection() dengan query()
     */
    public function query()
    {
        // Mengembalikan query builder yang akan mengambil data penjualan beserta relasi user (kasir)
        return Sale::query()->with('user');
    }
    
    /**
     * Menambahkan Heading (Judul Kolom)
     */
    public function headings(): array
    {
        return [
            'ID Transaksi',
            'Kode Invoice',
            'Nama Kasir',
            'Total Jumlah (Rp)',
            'Uang Dibayar (Rp)',
            'Kembalian (Rp)',
            'Tanggal Transaksi',
        ];
    }
    
    /**
     * MAPPING: Memetakan setiap baris data ke kolom Excel
     */
    public function map($sale): array
    {
        return [
            $sale->id,
            $sale->invoice_code,
            $sale->user->name ?? 'N/A', // Nama Kasir dari relasi user
            $sale->total_amount,
            $sale->paid_amount,
            $sale->change_amount,
            $sale->created_at->format('Y-m-d H:i:s'),
        ];
    }
}