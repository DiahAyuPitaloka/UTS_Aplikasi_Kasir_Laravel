<?php

// app/Models/Product.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // <-- Tambahkan ini

class Product extends Model
{
    use HasFactory;

    // Kolom yang boleh diisi (mass assignment)
    protected $fillable = [
        'name', 
        'sku', 
        'description', 
        'stock', 
        'price', 
        'category_id'
    ];

    // Relasi N-1: Satu Produk dimiliki oleh satu Kategori
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}