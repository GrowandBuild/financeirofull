<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class ProductStatsService
{
    /**
     * Calcular estatísticas mensais para múltiplos produtos de uma vez
     */
    public function getMonthlyStatsForProducts(Collection $products): Collection
    {
        $productIds = $products->pluck('id');
        
        if ($productIds->isEmpty()) {
            return collect();
        }
        
        $stats = DB::table('purchases')
            ->select('product_id')
            ->selectRaw('SUM(total_value) as monthly_spend')
            ->whereIn('product_id', $productIds)
            ->whereMonth('purchase_date', now()->month)
            ->whereYear('purchase_date', now()->year)
            ->groupBy('product_id')
            ->pluck('monthly_spend', 'product_id');
        
        return $stats;
    }
    
    /**
     * Calcular estatísticas mensais para um único produto
     */
    public function getMonthlySpendForProduct(int $productId): float
    {
        return Purchase::where('product_id', $productId)
            ->whereMonth('purchase_date', now()->month)
            ->whereYear('purchase_date', now()->year)
            ->sum('total_value') ?? 0;
    }
    
    /**
     * Obter top produtos por gasto mensal
     */
    public function getTopProductsByMonthlySpend(int $limit = 2): Collection
    {
        $monthlyStats = DB::table('purchases')
            ->select('product_id')
            ->selectRaw('SUM(total_value) as monthly_spend')
            ->whereMonth('purchase_date', now()->month)
            ->whereYear('purchase_date', now()->year)
            ->groupBy('product_id')
            ->orderByDesc('monthly_spend')
            ->limit($limit)
            ->pluck('monthly_spend', 'product_id');
        
        if ($monthlyStats->isEmpty()) {
            return collect();
        }
        
        $topProducts = Product::select(['id', 'name', 'category'])
            ->whereIn('id', $monthlyStats->keys())
            ->get()
            ->map(function ($product) use ($monthlyStats) {
                $product->monthly_spend = $monthlyStats[$product->id] ?? 0;
                return $product;
            })
            ->sortByDesc('monthly_spend');
        
        return $topProducts;
    }
    
    /**
     * Calcular estatísticas de preço para um produto
     */
    public function getPriceStats(Product $product): array
    {
        $purchases = $product->purchases()
            ->select('price', 'purchase_date')
            ->orderBy('purchase_date', 'desc')
            ->get();
        
        if ($purchases->isEmpty()) {
            return [
                'min_price' => 0,
                'max_price' => 0,
                'avg_price' => 0,
                'trend' => 'stable',
                'trend_percent' => 0,
                'chart_data' => []
            ];
        }
        
        $minPrice = $purchases->min('price');
        $maxPrice = $purchases->max('price');
        $avgPrice = $purchases->avg('price');
        
        // Calcular tendência
        $trend = 'stable';
        $trendPercent = 0;
        $recentPrices = $purchases->take(2)->pluck('price')->toArray();
        
        if (count($recentPrices) >= 2) {
            if ($recentPrices[0] > $recentPrices[1]) {
                $trend = 'up';
                $trendPercent = (($recentPrices[0] - $recentPrices[1]) / $recentPrices[1]) * 100;
            } elseif ($recentPrices[0] < $recentPrices[1]) {
                $trend = 'down';
                $trendPercent = (($recentPrices[1] - $recentPrices[0]) / $recentPrices[1]) * 100;
            }
        }
        
        // Dados para gráfico (últimos 7)
        $chartPurchases = $purchases->take(7)->reverse()->values();
        $chartData = [
            'labels' => $chartPurchases->pluck('purchase_date')->map(function($date) {
                return $date->format('d/m');
            })->toArray(),
            'prices' => $chartPurchases->pluck('price')->toArray()
        ];
        
        return [
            'min_price' => $minPrice,
            'max_price' => $maxPrice,
            'avg_price' => $avgPrice,
            'trend' => $trend,
            'trend_percent' => $trendPercent,
            'chart_data' => $chartData
        ];
    }
    
    /**
     * Calcular gasto total mensal
     */
    public function getTotalMonthlySpend(): float
    {
        return Purchase::whereMonth('purchase_date', now()->month)
            ->whereYear('purchase_date', now()->year)
            ->sum('total_value') ?? 0;
    }
    
    /**
     * Atualizar estatísticas de produtos em lote
     */
    public function updateProductStats(array $productIds): void
    {
        foreach ($productIds as $productId) {
            $stats = DB::table('purchases')
                ->where('product_id', $productId)
                ->selectRaw('
                    AVG(price) as avg_price,
                    SUM(total_value) as total_spent,
                    COUNT(*) as purchase_count,
                    MAX(price) as last_price
                ')
                ->first();
            
            Product::where('id', $productId)->update([
                'average_price' => $stats->avg_price ?? 0,
                'total_spent' => $stats->total_spent ?? 0,
                'purchase_count' => $stats->purchase_count ?? 0,
                'last_price' => $stats->last_price ?? 0,
            ]);
        }
    }
}

