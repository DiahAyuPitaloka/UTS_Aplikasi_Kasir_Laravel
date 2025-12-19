<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;

// Endpoint untuk menampilkan seluruh data produk [cite: 143]
Route::get('/produk', [ProductController::class, 'index']);

// Endpoint untuk menambahkan data produk baru [cite: 155]
Route::post('/produk', [ProductController::class, 'store']);

// Endpoint untuk menghapus data produk berdasarkan ID [cite: 172]
Route::delete('/produk/{id}', [ProductController::class, 'destroy']);
