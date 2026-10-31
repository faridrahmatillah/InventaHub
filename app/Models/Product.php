<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'code',
        'name',
        'stock',
        'price',
        'description',
    ];

    // Relasi: Produk milik 1 Kategori
    // withTrashed() agar data produk lama tetap bisa menampilkan nama kategori
    // walaupun kategorinya sudah dinonaktifkan (soft delete).
    public function category()
    {
        return $this->belongsTo(Category::class)->withTrashed();
    }

    // Relasi: 1 Produk memiliki banyak Transaksi (Barang Masuk/Keluar)
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function stockOpnames()
    {
        return $this->hasMany(StockOpname::class);
    }
}