<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\StockOpnameController;

// URL Utama diarahkan ke dashboard (nantinya akan dihadang oleh middleware auth kalau belum login)
Route::redirect('/', '/dashboard');

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
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware('role:admin')->group(function () {
        Route::resource('categories', CategoryController::class);
        Route::resource('suppliers', SupplierController::class);
        Route::resource('products', ProductController::class)
            ->only(['edit', 'update']);
    });

    Route::resource('stock-opnames', StockOpnameController::class)
        ->only(['index', 'create', 'store', 'show']);
    Route::resource('products', ProductController::class)
        ->except(['edit', 'update']);
    
    // Route untuk Transaksi (Barang Masuk/Keluar)
    Route::resource('transactions', TransactionController::class)->only(['index', 'create', 'store', 'destroy']);
    
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});