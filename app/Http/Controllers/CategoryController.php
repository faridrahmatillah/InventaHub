<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Mengambil semua data kategori dari database terbaru ke terlama
        $categories = \App\Models\Category::latest()->get(); 
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
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ], [
            // Pesan error custom biar lebih rapi (poin plus UAS)
            'name.required' => 'Nama kategori wajib diisi!',
        ]);

        // 2. Simpan ke database
        \App\Models\Category::create([
            'name' => $request->name,
            'description' => $request->description
        ]);

        // 3. Kembalikan ke halaman index dengan pesan sukses
        return redirect()->route('categories.index')->with('success', 'Kategori baru berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
