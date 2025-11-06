<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'cashflow_id',
        'purchase_date',
        'price',
        'quantity',
        'subquantity',
        'total_value',
        'store',
        'notes',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'price' => 'decimal:2',
        'quantity' => 'decimal:2',
        'subquantity' => 'decimal:2',
        'total_value' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cashflow()
    {
        return $this->belongsTo(CashFlow::class, 'cashflow_id');
    }
}
