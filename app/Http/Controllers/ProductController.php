<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use PDOException;

class ProductController extends Controller
{

    private function denyUnlessAdmin()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Akses ditolak! Hanya admin yang boleh mengelola barang.');
        }
    }

    public function index()
    {
        try {
            // Produk dengan stok 0 tetap ditampilkan.
            $products = Product::with('category')
                ->latest()
                ->get();

            return view('products.index', compact('products'));
        } catch (PDOException $exception) {
            return $this->databaseErrorResponse($exception, 'products.index');
        }
    }

    public function create()
    {
        try {
            $categories = Category::all();

            return view('products.create', compact('categories'));
        } catch (PDOException $exception) {
            return $this->databaseErrorResponse($exception, 'products.create');
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'category_id' => [
                    'required',
                    Rule::exists('categories', 'id')->whereNull('deleted_at'),
                ],
                'code'        => 'required|string|unique:products,code|max:50',
                'name'        => 'required|string|max:255',
                'stock'       => 'required|integer|min:0',
                'price'       => 'required|numeric|min:0',
                'description' => 'nullable|string',
            ], [
                'code.unique' => 'Kode barang ini sudah terdaftar!',
                'category_id.required' => 'Pilih kategori terlebih dahulu!',
            ]);

            Product::create($validated);

            return redirect()->route('products.index')
                ->with('success', 'Barang baru berhasil ditambahkan!');
        } catch (PDOException $exception) {
            return $this->databaseErrorRedirect($exception, 'products.store');
        }
    }

    public function show(Product $product)
    {
        try {
            // Eager loading kategori & riwayat transaksi milik produk ini
            $product->load(['category', 'transactions']);

            return view('products.show', compact('product'));
        } catch (PDOException $exception) {
            return $this->databaseErrorResponse($exception, 'products.show');
        }
    }

    public function edit(Product $product)
    {
        try {
            $categories = Category::all();

            return view('products.edit', compact('product', 'categories'));
        } catch (PDOException $exception) {
            return $this->databaseErrorResponse($exception, 'products.edit');
        }
    }

    public function update(Request $request, Product $product)
    {
        try {
            $validated = $request->validate([
                'category_id' => [
                    'required',
                    Rule::exists('categories', 'id')->whereNull('deleted_at'),
                ],
                'code'        => 'required|string|max:50|unique:products,code,' . $product->id,
                'name'        => 'required|string|max:255',
                'price'       => 'required|numeric|min:0',
                'description' => 'nullable|string',
            ]);

            $product->update($validated);

            return redirect()->route('products.index')
                ->with('success', 'Data barang berhasil diperbarui!');
        } catch (PDOException $exception) {
            return $this->databaseErrorRedirect($exception, 'products.update');
        }
    }

    public function destroy(Product $product)
    {
        $this->denyUnlessAdmin();

        try {
            // Produk dengan stok tersisa tidak boleh dihapus.
            if ($product->stock > 0) {
                return redirect()->route('products.index')
                    ->with('error', 'Kosongkan stok terlebih dahulu melalui transaksi keluar.');
                }

            // Soft delete agar data produk lama masih tersimpan.
            $product->delete();

            return redirect()->route('products.index')
                ->with('success', 'Data barang berhasil dinonaktifkan.');
        } catch (PDOException $exception) {
            return $this->databaseErrorRedirect($exception, 'products.destroy');
        }
}

}
