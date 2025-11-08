<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseDraft extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'cart_key',
        'product_id',
        'product_name',
        'variant',
        'unit',
        'quantity',
        'subquantity',
        'price',
        'total',
        'metadata',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'subquantity' => 'decimal:4',
        'price' => 'decimal:2',
        'total' => 'decimal:2',
        'metadata' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

