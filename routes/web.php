<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TransactionController;

// URL Utama diarahkan ke produk (nantinya akan dihadang oleh middleware auth kalau belum login)
Route::redirect('/', '/products');

// Route khusus untuk orang yang BELUM LOGIN (Guest)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    // throttle:5,1 = maksimal 5 percobaan login per 1 menit per IP (anti brute force)
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

// Route khusus untuk orang yang SUDAH LOGIN (Auth) - Syarat CPMK4
Route::middleware('auth')->group(function () {
    Route::resource('categories', CategoryController::class);
    Route::resource('products', ProductController::class);
    
    // Route untuk Transaksi (Barang Masuk/Keluar)
    Route::resource('transactions', TransactionController::class)->only(['index', 'create', 'store']);
    
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});