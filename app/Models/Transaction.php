<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'type',
        'quantity',
        'transaction_date',
        'description'
    ];

    // Relasi ke tabel Product (Many-to-One)
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}