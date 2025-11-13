<?php

// app/Http/Controllers/ProductController.php

namespace App\Http\Controllers;

use App\Models\Product; // <-- Import Model Product
use App\Models\Category; // <-- Import Model Category
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; // <-- Import Rule untuk validasi unique

class ProductController extends Controller
{
    // ---------------------- READ (Index) ----------------------
    public function index()
    {
        $products = Product::with('category')->latest()->paginate(10);
        return view('products.index', compact('products'));
    }

    // ---------------------- CREATE (Form) ----------------------
    public function create()
    {
        $categories = Category::all();
        return view('products.create', compact('categories'));
    }

    // ---------------------- CREATE (Store) ----------------------
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products,sku', // Validasi Wajib
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
        ]);

        Product::create($validated);
        return redirect()->route('products.index')->with('status', 'Produk berhasil ditambahkan!');
    }

    // ---------------------- UPDATE (Form Edit) ----------------------
    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('products.edit', compact('product', 'categories'));
    }

    // ---------------------- UPDATE (Store) ----------------------
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => ['required', 'string', Rule::unique('products', 'sku')->ignore($product->id)], // Unique kecuali diri sendiri
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
        ]);

        $product->update($validated);
        return redirect()->route('products.index')->with('status', 'Produk berhasil diperbarui!');
    }

    // ---------------------- DELETE (Destroy) ----------------------
    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('status', 'Produk berhasil dihapus!');
    }

    // Metode Show tidak dibutuhkan untuk CRUD sederhana
    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }
}