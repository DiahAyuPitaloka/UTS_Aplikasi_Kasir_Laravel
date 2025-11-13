<?php

// app/Models/SaleDetail.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // <-- Tambahkan ini

class SaleDetail extends Model
{
    use HasFactory;
    
    // Kolom yang boleh diisi
    protected $fillable = ['quantity', 'price_per_unit', 'subtotal', 'sale_id', 'product_id'];

    // Relasi N-1: SaleDetail milik satu Sale
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }
    
    // Relasi N-1: SaleDetail milik satu Product
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}