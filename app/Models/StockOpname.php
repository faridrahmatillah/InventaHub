<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockOpname extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'system_stock',
        'counted_stock',
        'difference',
        'counted_at',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'counted_at' => 'date',
        ];
    }

    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
