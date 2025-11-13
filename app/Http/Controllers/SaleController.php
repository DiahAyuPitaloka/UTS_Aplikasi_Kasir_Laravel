<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaleController extends Controller
{
    // READ (Index - Riwayat Penjualan)
    public function index(Request $request)
{
    $query = Sale::with('user')->latest();

    // Logika Filter dan Pencarian (Nilai Tambah)
    if ($request->filled('search')) {
        $query->where('invoice_code', 'like', '%' . $request->search . '%')
              ->orWhereHas('user', function ($q) use ($request) {
                  $q->where('name', 'like', '%' . $request->search . '%');
              });
    }

    if ($request->filled('start_date') && $request->filled('end_date')) {
        $query->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
    }

    $sales = $query->paginate(15)->appends($request->all()); // Menambahkan parameter query ke pagination

    return view('sales.index', compact('sales'));
}

    // CREATE (Form Kasir) - Digunakan oleh role 'kasir'
    public function create()
    {
        // Mengambil semua produk yang statusnya aktif untuk form kasir
        $products = Product::where('stock', '>', 0)->get();
        return view('sales.create', compact('products'));
    }

    // STORE (Proses Transaksi)
    public function store(Request $request)
    {
        // 1. Validasi Dasar
        $request->validate([
            'items' => 'required|array|min:1', // Harus ada minimal 1 item
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'paid_amount' => 'required|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
        ]);
        
        // 2. Transaksi Database (Memastikan semua berjalan atau tidak sama sekali)
        DB::beginTransaction();

        try {
            // Cek Stok dan Hitung Ulang Total
            $total_amount_calculated = 0;
            $products_to_update = [];
            
            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                
                // Cek Stok
                if ($product->stock < $item['quantity']) {
                    throw ValidationException::withMessages([
                        'items' => "Stok untuk produk {$product->name} tidak mencukupi."
                    ]);
                }
                
                $subtotal = $product->price * $item['quantity'];
                $total_amount_calculated += $subtotal;

                $products_to_update[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'price_per_unit' => $product->price,
                    'subtotal' => $subtotal,
                ];
            }
            
            // 3. Cek Kesesuaian Harga
            if (abs($total_amount_calculated - $request->total_amount) > 0.01) {
                throw ValidationException::withMessages([
                    'total_amount' => 'Total harga yang dihitung tidak sesuai. Harap ulangi transaksi.'
                ]);
            }
            
            $change = $request->paid_amount - $total_amount_calculated;

            // 4. Buat Transaksi Utama (Sale)
            $sale = Sale::create([
                'invoice_code' => 'INV-' . time(),
                'total_amount' => $total_amount_calculated,
                'paid_amount' => $request->paid_amount,
                'change_amount' => $change,
                'user_id' => auth()->id(),
            ]);

            // 5. Buat Detail Transaksi dan Kurangi Stok
            foreach ($products_to_update as $data) {
                $sale->saleDetails()->create([
                    'product_id' => $data['product']->id,
                    'quantity' => $data['quantity'],
                    'price_per_unit' => $data['price_per_unit'],
                    'subtotal' => $data['subtotal'],
                ]);
                
                // Kurangi Stok Produk (Update Stock)
                $data['product']->decrement('stock', $data['quantity']);
            }

            DB::commit();

            // Beri respons sukses
            return redirect()->route('sales.create')->with('status', "Transaksi berhasil! Kembalian: Rp. " . number_format($change, 0, ',', '.'));

        } catch (\Exception $e) {
            DB::rollBack();
            // Jika ada error (termasuk ValidationException), tampilkan pesan error generik
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }
}