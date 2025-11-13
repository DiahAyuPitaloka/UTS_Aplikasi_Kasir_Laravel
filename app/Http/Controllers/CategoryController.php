<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // READ (Index)
    public function index()
    {
        $categories = Category::latest()->paginate(10);
        return view('categories.index', compact('categories'));
    }

    // CREATE (Form)
    public function create()
    {
        return view('categories.create');
    }

    // CREATE (Store)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
        ]);

        Category::create($validated);
        return redirect()->route('categories.index')->with('status', 'Kategori berhasil ditambahkan!');
    }

    // UPDATE (Form Edit)
    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    // UPDATE (Store)
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
        ]);

        $category->update($validated);
        return redirect()->route('categories.index')->with('status', 'Kategori berhasil diperbarui!');
    }

    // DELETE (Destroy)
    public function destroy(Category $category)
    {
        // Perlu dicek apakah kategori sedang digunakan oleh produk, jika tidak ada relasi, hapus langsung
        $category->delete(); 
        return redirect()->route('categories.index')->with('status', 'Kategori berhasil dihapus!');
    }
}