<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'supplier_id',
        'type',
        'quantity',
        'transaction_date',
        'description'
    ];

    // Relasi ke tabel Product (Many-to-One)
    // withTrashed() agar riwayat transaksi tetap menampilkan nama produk
    // walaupun produknya sudah dinonaktifkan (soft delete).
    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class)->withTrashed();
    }
}