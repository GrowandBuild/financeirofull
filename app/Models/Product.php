<?php

namespace App\Models;

use App\Helpers\ImageHelper;
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

    protected $appends = ['image_url'];

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function priceAlerts()
    {
        return $this->hasMany(PriceAlert::class);
    }

    /**
     * Get the full URL for the product image
     * Usa ImageHelper para evitar código duplicado
     */
    public function getImageUrlAttribute()
    {
        return ImageHelper::getProductImageUrl($this->image_path, $this->image);
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
