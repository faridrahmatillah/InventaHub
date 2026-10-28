<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'code',
        'name',
        'stock',
        'price',
        'description',
    ];

    // Relasi: Produk milik 1 Kategori
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}