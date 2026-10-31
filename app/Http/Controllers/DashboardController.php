<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard utama.
     */
    
public function index()
{
    $totalCategories = Category::count();

    // Produk dengan stok 0 tetap dihitung.
    $totalProducts = Product::count();

    $totalTransactions = Transaction::count();

    $recentTransactions = Transaction::with('product')
        ->latest()
        ->limit(5)
        ->get();

    // Tampilkan produk dengan stok habis atau menipis.
    $lowStockProducts = Product::with('category')
        ->where('stock', '<', 5)
        ->orderBy('stock', 'asc')
        ->get();

    return view('dashboard', compact(
        'totalCategories',
        'totalProducts',
        'totalTransactions',
        'recentTransactions',
        'lowStockProducts'
    ));
}

}
