<?php

namespace App\Observers;

use App\Models\Purchase;
use App\Models\Product;

class PurchaseObserver
{
    /**
     * Handle the Purchase "created" event.
     */
    public function created(Purchase $purchase): void
    {
        $this->updateProductStats($purchase);
    }

    /**
     * Handle the Purchase "updated" event.
     */
    public function updated(Purchase $purchase): void
    {
        $this->updateProductStats($purchase);
    }

    /**
     * Handle the Purchase "deleted" event.
     */
    public function deleted(Purchase $purchase): void
    {
        $this->updateProductStats($purchase);
    }

    /**
     * Update product statistics when purchase changes
     */
    private function updateProductStats(Purchase $purchase): void
    {
        $product = $purchase->product;
        
        // Recalcular todas as estatísticas
        $purchases = $product->purchases();
        
        $product->update([
            'total_spent' => $purchases->sum('total_value'),
            'purchase_count' => $purchases->count(),
            'average_price' => $purchases->avg('price') ?? 0,
            'last_price' => $purchases->latest('purchase_date')->first()?->price ?? 0,
        ]);
    }
}
