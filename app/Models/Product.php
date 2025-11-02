<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'goal_category',
        'unit',
        'description',
        'image',
        'image_path',
        'variants',
        'has_variants',
        'average_price',
        'last_price',
        'total_spent',
        'purchase_count',
    ];

    protected $casts = [
        'variants' => 'array',
        'has_variants' => 'boolean',
        'average_price' => 'decimal:2',
        'last_price' => 'decimal:2',
        'total_spent' => 'decimal:2',
    ];

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function priceAlerts()
    {
        return $this->hasMany(PriceAlert::class);
    }

    /**
     * Get monthly spend for current month
     */
    public function getMonthlySpendAttribute()
    {
        return $this->purchases()
            ->whereMonth('purchase_date', now()->month)
            ->whereYear('purchase_date', now()->year)
            ->sum('total_value');
    }

    /**
     * Get recent purchases (last 5)
     */
    public function getRecentPurchasesAttribute()
    {
        return $this->purchases()
            ->orderBy('purchase_date', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * Get price trend (up/down/stable)
     */
    public function getPriceTrendAttribute()
    {
        $lastTwo = $this->purchases()
            ->orderBy('purchase_date', 'desc')
            ->limit(2)
            ->pluck('price')
            ->toArray();

        if (count($lastTwo) < 2) {
            return 'stable';
        }

        if ($lastTwo[0] > $lastTwo[1]) {
            return 'up';
        } elseif ($lastTwo[0] < $lastTwo[1]) {
            return 'down';
        }

        return 'stable';
    }

    /**
     * Get the full URL for the product image
     */
    public function getImageUrlAttribute()
    {
        // Priorizar imagem enviada por upload
        if ($this->image_path) {
            return asset('storage/' . $this->image_path);
        }
        
        // Se não há upload, usar URL se existir
        if ($this->image) {
            return $this->image;
        }
        
        return asset('images/no-image.png'); // Imagem padrão
    }

    /**
     * Check if product has an image
     */
    public function hasImage()
    {
        return !empty($this->image_path) || !empty($this->image);
    }

    /**
     * Get the current image source (for display purposes)
     */
    public function getCurrentImageAttribute()
    {
        if ($this->image_path) {
            return asset('storage/' . $this->image_path);
        }
        
        if ($this->image) {
            return $this->image;
        }
        
        return null;
    }
}
