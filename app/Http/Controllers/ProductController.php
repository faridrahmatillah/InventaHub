<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        // Ambil data produk beserta nama kategorinya (Eager Loading) dari yang terbaru
        $products = Product::with('category')->latest()->get();
        return view('products.index', compact('products'));
    }

    public function create()
    {
        // Ambil data kategori untuk ditampilkan di pilihan Dropdown
        $categories = Category::all();
        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        // Validasi inputan (Kode barang tidak boleh sama)
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'code'        => 'required|string|unique:products,code|max:50',
            'name'        => 'required|string|max:255',
            'stock'       => 'required|integer|min:0',
            'price'       => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ], [
            'code.unique' => 'Kode barang ini sudah terdaftar!',
            'category_id.required' => 'Pilih kategori terlebih dahulu!',
        ]);

        Product::create($request->all());

        return redirect()->route('products.index')->with('success', 'Barang baru berhasil ditambahkan!');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'code'        => 'required|string|max:50|unique:products,code,' . $product->id,
            'name'        => 'required|string|max:255',
            'stock'       => 'required|integer|min:0',
            'price'       => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $product->update($request->all());

        return redirect()->route('products.index')->with('success', 'Data barang berhasil diperbarui!');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Data barang berhasil dihapus!');
    }
}