<?php

// app/Models/Category.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; // <-- Tambahkan ini

class Category extends Model
{
    use HasFactory;
    
    // Kolom yang boleh diisi (mass assignment)
    protected $fillable = ['name', 'description'];

    // Relasi 1-N: Satu Kategori memiliki banyak Produk
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}