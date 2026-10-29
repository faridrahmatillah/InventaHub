<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Mengambil semua data kategori dari database terbaru ke terlama
        $categories = Category::latest()->get();
        return view('categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Menampilkan form tambah
        return view('categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Validasi inputan (Wajib ada nama, maksimal 255 karakter)
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ], [
            // Pesan error custom biar lebih rapi (poin plus UAS)
            'name.required' => 'Nama kategori wajib diisi!',
        ]);

        // 2. Simpan ke database
        Category::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null
        ]);

        // 3. Kembalikan ke halaman index dengan pesan sukses
        return redirect()->route('categories.index')->with('success', 'Kategori baru berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        // Eager loading relasi produk supaya tidak terjadi query berulang (N+1)
        $category->load('products');

        return view('categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        // Kirim data kategori yang akan diedit ke view
        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        // Validasi inputan (sama seperti store)
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ], [
            'name.required' => 'Nama kategori wajib diisi!',
        ]);

        // Update data lewat Eloquent ORM
        $category->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null
        ]);

        return redirect()->route('categories.index')->with('success', 'Data kategori berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        // Hapus data lewat Eloquent ORM
        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Data kategori berhasil dihapus!');
    }
}
