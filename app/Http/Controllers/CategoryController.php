<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use PDOException;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // withCount mencegah query tambahan (N+1) saat menampilkan jumlah produk.
            $categories = Category::withCount('products')->latest()->get();

            return view('categories.index', compact('categories'));
        } catch (PDOException $exception) {
            return $this->databaseErrorResponse($exception, 'categories.index');
        }
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
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
            ], [
                'name.required' => 'Nama kategori wajib diisi!',
            ]);

            Category::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
            ]);

            return redirect()->route('categories.index')
                ->with('success', 'Kategori baru berhasil ditambahkan!');
        } catch (PDOException $exception) {
            return $this->databaseErrorRedirect($exception, 'categories.store');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        try {
            // Eager loading relasi produk supaya tidak terjadi query berulang (N+1)
            $category->load('products');

            return view('categories.show', compact('category'));
        } catch (PDOException $exception) {
            return $this->databaseErrorResponse($exception, 'categories.show');
        }
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
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
            ], [
                'name.required' => 'Nama kategori wajib diisi!',
            ]);

            $category->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
            ]);

            return redirect()->route('categories.index')
                ->with('success', 'Data kategori berhasil diperbarui!');
        } catch (PDOException $exception) {
            return $this->databaseErrorRedirect($exception, 'categories.update');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        try {
            // Cegah kategori dinonaktifkan jika masih memiliki produk aktif.
            if ($category->products()->exists()) {
                return redirect()->route('categories.index')
                    ->with('error', 'Kategori tidak bisa dihapus karena masih memiliki produk aktif.');
            }

            // Soft delete, supaya data lama tidak langsung hilang dari database.
            $category->delete();

            return redirect()->route('categories.index')
                ->with('success', 'Kategori berhasil dinonaktifkan.');
        } catch (PDOException $exception) {
            return $this->databaseErrorRedirect($exception, 'categories.destroy');
        }
    }

}
