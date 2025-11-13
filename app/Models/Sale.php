<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// app/Models/Sale.php

// ... (tambahkan use HasMany dan use BelongsTo di bagian atas)
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sale extends Model
{
    // ...
    protected $fillable = ['invoice_code', 'total_amount', 'paid_amount', 'change_amount', 'user_id'];

    // Relasi 1-N: Satu Transaksi memiliki banyak Detail
    public function saleDetails(): HasMany
    {
        return $this->hasMany(SaleDetail::class);
    }

    // Relasi N-1: Transaksi dilakukan oleh satu User/Kasir
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}