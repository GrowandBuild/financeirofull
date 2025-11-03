<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\CashFlow;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index()
    {
        // Calcular estatísticas em uma única consulta para todos os produtos
        $monthlyStats = DB::table('purchases')
            ->select('product_id')
            ->selectRaw('SUM(total_value) as monthly_spend')
            ->whereMonth('purchase_date', now()->month)
            ->whereYear('purchase_date', now()->year)
            ->groupBy('product_id')
            ->pluck('monthly_spend', 'product_id');

        // Top produtos usando dados já calculados
        $topProducts = Product::select(['id', 'name', 'category'])
            ->get()
            ->map(function ($product) use ($monthlyStats) {
                $product->monthly_spend = $monthlyStats[$product->id] ?? 0;
                return $product;
            })
            ->sortByDesc('monthly_spend')
            ->take(2);

        // Paginação de produtos
        $products = Product::select([
            'id', 'name', 'category', 'unit', 'description', 
            'image', 'image_path', 'variants', 'has_variants',
            'average_price', 'last_price', 'total_spent', 'purchase_count'
        ])
        ->withCount('purchases')
        ->paginate(12);

        // Aplicar estatísticas calculadas
        $products->each(function ($product) use ($monthlyStats) {
            $product->monthly_spend = $monthlyStats[$product->id] ?? 0;
        });
        
        // Gasto total mensal
        $totalMonthlySpend = $monthlyStats->sum();

        return view('products.index', compact('products', 'topProducts', 'totalMonthlySpend'));
    }

    public function show(Product $product)
    {
        $product->load(['purchases' => function($query) {
            $query->select('id', 'product_id', 'quantity', 'price', 'total_value', 
                          'store', 'purchase_date', 'notes')
                  ->orderBy('purchase_date', 'desc');
        }]);

        // Calcular estatísticas reais
        $hasPurchases = $product->purchases->count() > 0;
        
        $priceStats = [
            'min_price' => 0,
            'max_price' => 0,
            'avg_price' => 0,
            'trend' => 'stable',
            'trend_percent' => 0,
            'chart_data' => []
        ];
        
        if ($hasPurchases) {
            $priceStats['min_price'] = $product->purchases->min('price');
            $priceStats['max_price'] = $product->purchases->max('price');
            $priceStats['avg_price'] = $product->purchases->avg('price');
            
            // Calcular tendência (purchases já vem ordenado por desc)
            $recentPrices = $product->purchases->take(2)->pluck('price')->toArray();
            if (count($recentPrices) >= 2) {
                if ($recentPrices[0] > $recentPrices[1]) {
                    $priceStats['trend'] = 'up';
                    $priceStats['trend_percent'] = (($recentPrices[0] - $recentPrices[1]) / $recentPrices[1]) * 100;
                } elseif ($recentPrices[0] < $recentPrices[1]) {
                    $priceStats['trend'] = 'down';
                    $priceStats['trend_percent'] = (($recentPrices[1] - $recentPrices[0]) / $recentPrices[1]) * 100;
                }
            }
            
            // Preparar dados para o gráfico (últimos 7 registros)
            $chartPurchases = $product->purchases->take(7)->reverse()->values();
            $priceStats['chart_data'] = [
                'labels' => $chartPurchases->pluck('purchase_date')->map(function($date) {
                    return $date->format('d/m');
                })->toArray(),
                'prices' => $chartPurchases->pluck('price')->toArray()
            ];
        }

        return view('products.show', compact('product', 'hasPurchases', 'priceStats'));
    }

    public function search()
    {
        $query = request('q', '');
        $category = request('category', '');
        $sort = request('sort', 'relevance');
        $priceRange = request('price_range', '');
        $ajax = request('ajax', false);
        
        $productsQuery = Product::select([
            'id', 'name', 'category', 'unit', 'description',
            'image', 'image_path', 'variants', 'has_variants',
            'average_price', 'last_price', 'total_spent', 'purchase_count'
        ]);
        
        if ($query) {
            $query = trim($query);
            $productsQuery->where(function($q) use ($query) {
                $q->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($query) . '%'])
                  ->orWhereRaw('LOWER(description) LIKE ?', ['%' . strtolower($query) . '%']);
            });
        }
        
        if ($category) {
            $productsQuery->where('category', $category);
        }
        
        // Filtrar por faixa de preço baseado no average_price
        if ($priceRange) {
            if ($priceRange === '100+') {
                $productsQuery->where('average_price', '>=', 100);
            } else {
                $range = explode('-', $priceRange);
                if (count($range) === 2) {
                    $min = (float) $range[0];
                    $max = (float) $range[1];
                    $productsQuery->whereBetween('average_price', [$min, $max]);
                }
            }
        }
        
        $products = $productsQuery->withCount('purchases')->get();

        // Calcular estatísticas mensais em lote
        $productIds = $products->pluck('id');
        $monthlyStats = DB::table('purchases')
            ->select('product_id')
            ->selectRaw('SUM(total_value) as monthly_spend')
            ->whereIn('product_id', $productIds)
            ->whereMonth('purchase_date', now()->month)
            ->whereYear('purchase_date', now()->year)
            ->groupBy('product_id')
            ->pluck('monthly_spend', 'product_id');

        // Aplicar estatísticas
        $products->each(function ($product) use ($monthlyStats) {
            $product->monthly_spend = $monthlyStats[$product->id] ?? 0;
        });

        // Aplicar ordenação
        switch ($sort) {
            case 'name_asc':
                $products = $products->sortBy('name')->values();
                break;
            case 'name_desc':
                $products = $products->sortByDesc('name')->values();
                break;
            case 'price_asc':
                $products = $products->sortBy(function ($product) {
                    return $product->average_price ?? 0;
                })->values();
                break;
            case 'price_desc':
                $products = $products->sortByDesc(function ($product) {
                    return $product->average_price ?? 0;
                })->values();
                break;
            case 'most_bought':
                $products = $products->sortByDesc(function ($product) {
                    return $product->purchases_count ?? 0;
                })->values();
                break;
            case 'recent':
                $products = $products->sortByDesc('id')->values();
                break;
            case 'relevance':
            default:
                // Manter ordem original (relevância para busca)
                break;
        }

        // Categorias únicas para filtros
        $categories = Product::select('category')
            ->distinct()
            ->whereNotNull('category')
            ->orderBy('category')
            ->pluck('category');

        // Se for requisição AJAX, retorna JSON
        if ($ajax) {
            return response()->json([
                'products' => $products->take(5),
                'total' => $products->count()
            ]);
        }

        return view('products.search', [
            'products' => $products ?? collect(),
            'categories' => $categories ?? collect(),
            'query' => $query ?? '',
            'category' => $category ?? ''
        ]);
    }

    public function compra()
    {
        $products = Product::select([
            'id', 'name', 'category', 'unit', 'description',
            'image', 'image_path', 'variants', 'has_variants',
            'average_price', 'last_price', 'total_spent', 'purchase_count'
        ])->get();

        // Calcular estatísticas mensais em lote
        $productIds = $products->pluck('id');
        $monthlyStats = DB::table('purchases')
            ->select('product_id')
            ->selectRaw('SUM(total_value) as monthly_spend')
            ->whereIn('product_id', $productIds)
            ->whereMonth('purchase_date', now()->month)
            ->whereYear('purchase_date', now()->year)
            ->groupBy('product_id')
            ->pluck('monthly_spend', 'product_id');
        
        $products->each(function ($product) use ($monthlyStats) {
            $product->monthly_spend = $monthlyStats[$product->id] ?? 0;
        });

        $categories = Product::select('category')
            ->distinct()
            ->whereNotNull('category')
            ->orderBy('category')
            ->pluck('category');

        return view('products.compra', compact('products', 'categories'));
    }

    /**
     * API endpoint para buscar produtos
     */
    public function apiProducts()
    {
        return Product::select('id', 'name', 'category', 'unit', 'variants', 'has_variants', 'image', 'image_path')
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'category' => $product->category,
                    'unit' => $product->unit,
                    'variants' => $product->variants ?? [],
                    'has_variants' => $product->has_variants,
                    'image_url' => $product->image_url
                ];
            });
    }
    
    public function apiStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'unit' => 'required|string|max:10',
            'description' => 'nullable|string',
            'image' => 'nullable|string|max:255',
            'variants' => 'nullable|array',
            'has_variants' => 'boolean'
        ]);
        
        $product = Product::create($request->all());
        
        return response()->json($product, 201);
    }
    
    public function apiUpdate(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'category' => 'nullable|string|max:255',
            'unit' => 'sometimes|required|string|max:10',
            'description' => 'nullable|string',
            'image' => 'nullable|string|max:255',
            'variants' => 'nullable|array',
            'has_variants' => 'boolean'
        ]);
        
        $product->update($request->all());
        
        return response()->json($product);
    }
    
    public function apiDestroy(Product $product)
    {
        $product->delete();
        
        return response()->json(['message' => 'Produto deletado com sucesso']);
    }
    
    /**
     * Salvar compra realizada - VERSÃO SIMPLIFICADA PARA TESTE
     */
    public function savePurchase(Request $request)
    {
        \Log::info('SavePurchase called', ['request' => $request->all()]);
        
        // Validação simplificada para teste
        if (!$request->has('items') || empty($request->items)) {
            \Log::error('Nenhum item na compra', ['request_data' => $request->all()]);
            return response()->json([
                'success' => false,
                'message' => 'Nenhum item na compra'
            ], 400);
        }
        
        // Debug detalhado dos itens
        \Log::info('Items recebidos:', ['items' => $request->items, 'count' => count($request->items)]);
        
        // Validar se todos os itens têm product_id
        foreach ($request->items as $index => $item) {
            \Log::info("Validando item {$index}", ['item' => $item, 'has_product_id' => isset($item['product_id'])]);
            
            if (!isset($item['product_id']) || empty($item['product_id'])) {
                \Log::error('Item sem product_id válido', [
                    'item' => $item, 
                    'index' => $index,
                    'product_id_value' => $item['product_id'] ?? 'NOT_SET',
                    'product_id_type' => gettype($item['product_id'] ?? null)
                ]);
                return response()->json([
                    'success' => false,
                    'message' => "Item {$index} não possui product_id válido"
                ], 400);
            }
        }
        
        try {
            $purchases = [];
            $totalAmount = 0;
            $userId = auth()->id() ?? 1; // Fallback para teste
            
            \Log::info('User ID para Fluxo de Caixa', ['user_id' => $userId, 'auth_check' => auth()->check()]);
            
            // Buscar ou criar categoria de compras
            $purchaseCategory = Category::where('name', 'Compras')
                ->where('user_id', $userId)
                ->first();
            
            if (!$purchaseCategory) {
                try {
                    $purchaseCategory = Category::create([
                        'name' => 'Compras',
                        'type' => 'expense',
                        'user_id' => $userId,
                        'is_active' => true
                    ]);
                    \Log::info('Categoria de compras criada', ['category_id' => $purchaseCategory->id]);
                } catch (\Exception $e) {
                    \Log::error('Erro ao criar categoria de compras', ['error' => $e->getMessage()]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Erro ao criar categoria: ' . $e->getMessage()
                    ], 500);
                }
            }
            
            // Criar as compras e seus cashflows
            $cashFlows = [];
            
            foreach ($request->items as $item) {
                // Buscar departamento do produto
                $product = Product::find($item['product_id']);
                $department = $product->goal_category ?? null;
                
                // Criar cashflow primeiro
                $cashFlow = CashFlow::create([
                    'user_id' => $userId,
                    'type' => 'expense',
                    'title' => "Compra - {$request->store}",
                    'description' => isset($item['variant']) ? "Variante: {$item['variant']}" : $product->name,
                    'amount' => $item['quantity'] * $item['price'],
                    'category_id' => $purchaseCategory->id,
                    'goal_category' => $department,
                    'transaction_date' => $request->date ?? now(),
                    'payment_method' => 'cash',
                    'reference' => 'Compra via sistema',
                    'is_confirmed' => true
                ]);
                
                // Criar purchase com cashflow_id
                $purchase = Purchase::create([
                    'user_id' => $userId,
                    'cashflow_id' => $cashFlow->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total_value' => $item['quantity'] * $item['price'],
                    'store' => $request->store ?? 'Loja Teste',
                    'purchase_date' => $request->date ?? now(),
                    'notes' => isset($item['variant']) ? "Variante: {$item['variant']}" : null
                ]);
                
                $purchases[] = $purchase;
                $cashFlows[] = $cashFlow;
                $totalAmount += $purchase->total_value;
                
                \Log::info('Compra e cashflow criados', [
                    'purchase_id' => $purchase->id,
                    'cash_flow_id' => $cashFlow->id,
                    'amount' => $purchase->total_value,
                    'department' => $department,
                    'store' => $request->store
                ]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Compra salva com sucesso e registrada no Fluxo de Caixa!',
                'purchases' => $purchases,
                'cashflows' => $cashFlows
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Erro ao salvar compra', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao salvar compra: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Excluir uma compra e seu fluxo de caixa associado
     */
    public function destroyPurchase(Purchase $purchase)
    {
        try {
            // Verificar se o usuário tem permissão
            if ($purchase->user_id !== auth()->id()) {
                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Você não tem permissão para excluir esta compra'
                    ], 403);
                }
                return redirect()->back()->with('error', 'Você não tem permissão para excluir esta compra');
            }

            // Excluir o cashflow associado se existir
            if ($purchase->cashflow_id) {
                $cashFlow = CashFlow::find($purchase->cashflow_id);
                if ($cashFlow) {
                    $cashFlow->delete();
                    \Log::info('Cashflow excluído junto com purchase', [
                        'cashflow_id' => $purchase->cashflow_id,
                        'purchase_id' => $purchase->id
                    ]);
                }
            }

            // Excluir a compra
            $productId = $purchase->product_id;
            $purchase->delete();

            // Atualizar estatísticas do produto
            $this->updateProductStats([$productId]);

            \Log::info('Compra excluída com sucesso', ['purchase_id' => $purchase->id]);

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Compra excluída com sucesso'
                ]);
            }

            return redirect()->back()->with('success', 'Compra excluída com sucesso');
        } catch (\Exception $e) {
            \Log::error('Erro ao excluir compra', ['error' => $e->getMessage()]);

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao excluir compra: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Erro ao excluir compra');
        }
    }

    /**
     * Atualizar estatísticas dos produtos em lote
     */
    private function updateProductStats($productIds)
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
