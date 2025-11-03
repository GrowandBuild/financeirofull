<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\CashFlow;
use App\Models\Category;
use App\Services\ProductStatsService;
use App\Policies\PurchasePolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    protected $statsService;

    public function __construct(ProductStatsService $statsService)
    {
        $this->statsService = $statsService;
    }

    public function index()
    {
        // Verificar se há produtos cadastrados
        $totalProductsCount = Product::count();
        
        // Obter top produtos usando Service
        $topProducts = $this->statsService->getTopProductsByMonthlySpend(2);

        // Paginação de produtos
        $products = Product::select([
            'id', 'name', 'category', 'unit', 'description', 
            'image', 'image_path', 'variants', 'has_variants',
            'average_price', 'last_price', 'total_spent', 'purchase_count'
        ])
        ->withCount('purchases')
        ->paginate(12);

        // Calcular estatísticas mensais para produtos da página atual usando Service
        // Converter o paginator para Collection para passar ao serviço
        $monthlyStats = $this->statsService->getMonthlyStatsForProducts($products->getCollection());
        
        $products->each(function ($product) use ($monthlyStats) {
            $product->monthly_spend = $monthlyStats[$product->id] ?? 0;
        });
        
        // Gasto total mensal usando Service
        $totalMonthlySpend = $this->statsService->getTotalMonthlySpend();

        return view('products.index', compact('products', 'topProducts', 'totalMonthlySpend', 'totalProductsCount'));
    }

    public function show(Product $product)
    {
        $product->load(['purchases' => function($query) {
            $query->select('id', 'product_id', 'quantity', 'price', 'total_value', 
                          'store', 'purchase_date', 'notes')
                  ->orderBy('purchase_date', 'desc');
        }]);

        // Calcular estatísticas usando Service
        $hasPurchases = $product->purchases->count() > 0;
        $priceStats = $this->statsService->getPriceStats($product);

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

        // Calcular estatísticas mensais em lote usando Service
        $monthlyStats = $this->statsService->getMonthlyStatsForProducts($products);

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
        // Carregar produtos paginados para melhor performance
        $products = Product::select([
            'id', 'name', 'category', 'unit', 'description',
            'image', 'image_path', 'variants', 'has_variants',
            'average_price', 'last_price', 'total_spent', 'purchase_count'
        ])
        ->orderBy('name')
        ->paginate(100); // Usar paginação em vez de limit arbitrário

        // Calcular estatísticas mensais usando Service
        // Converter o paginator para Collection para passar ao serviço
        $monthlyStats = $this->statsService->getMonthlyStatsForProducts($products->getCollection());
        
        $products->each(function ($product) use ($monthlyStats) {
            $product->monthly_spend = $monthlyStats[$product->id] ?? 0;
        });

        // Buscar categorias
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
        $products = Product::select('id', 'name', 'category', 'unit', 'variants', 'has_variants', 'image', 'image_path')
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'category' => $product->category,
                    'unit' => $product->unit,
                    'variants' => $product->variants ?? [],
                    'has_variants' => $product->has_variants ?? false,
                    'image_url' => $product->image_url
                ];
            });
        
        return response()->json($products);
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
     * Salvar compra realizada
     */
    public function savePurchase(Request $request)
    {
        // Validação completa
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.price' => 'required|numeric|min:0.01',
            'items.*.variant' => 'nullable|string|max:255',
            'store' => 'required|string|max:255',
            'date' => 'required|date'
        ]);
        
        if (empty($request->items)) {
            return response()->json([
                'success' => false,
                'message' => 'Nenhum item na compra'
            ], 400);
        }
        
        // Validar se todos os itens têm product_id válido
        foreach ($request->items as $index => $item) {
            if (!isset($item['product_id']) || empty($item['product_id']) || !Product::find($item['product_id'])) {
                return response()->json([
                    'success' => false,
                    'message' => "Item {$index} não possui product_id válido"
                ], 400);
            }
        }
        
        try {
            $purchases = [];
            $totalAmount = 0;
            $userId = Auth::id();
            
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário não autenticado'
                ], 401);
            }
            
            // Buscar ou criar categoria de compras
            $purchaseCategory = Category::firstOrCreate(
                [
                    'name' => 'Compras',
                    'user_id' => $userId
                ],
                [
                    'type' => 'expense',
                    'is_active' => true
                ]
            );
            
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
            }
            
            // Atualizar estatísticas dos produtos em lote usando Service
            $productIds = array_unique(array_column($request->items, 'product_id'));
            $this->statsService->updateProductStats($productIds);
            
            return response()->json([
                'success' => true,
                'message' => 'Compra salva com sucesso e registrada no Fluxo de Caixa!',
                'purchases' => $purchases,
                'cashflows' => $cashFlows,
                'total_amount' => $totalAmount
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Erro ao salvar compra', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao salvar compra. Por favor, tente novamente.'
            ], 500);
        }
    }
    
    /**
     * Excluir uma compra e seu fluxo de caixa associado
     */
    public function destroyPurchase(Purchase $purchase)
    {
        try {
            // Verificar autorização usando Policy
            if ($purchase->user_id !== Auth::id()) {
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
                if ($cashFlow && $cashFlow->user_id === Auth::id()) {
                    $cashFlow->delete();
                }
            }

            // Excluir a compra
            $productId = $purchase->product_id;
            $purchase->delete();

            // Atualizar estatísticas do produto usando Service
            $this->statsService->updateProductStats([$productId]);

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Compra excluída com sucesso'
                ]);
            }

            return redirect()->back()->with('success', 'Compra excluída com sucesso');
        } catch (\Exception $e) {
            \Log::error('Erro ao excluir compra', [
                'error' => $e->getMessage(),
                'purchase_id' => $purchase->id ?? null
            ]);

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao excluir compra. Por favor, tente novamente.'
                ], 500);
            }

            return redirect()->back()->with('error', 'Erro ao excluir compra');
        }
    }
}
