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
        $products = Product::select([
            'id', 'name', 'category', 'unit', 'description', 
            'image', 'image_path', 'variants', 'has_variants',
            'average_price', 'last_price', 'total_spent', 'purchase_count'
        ])
        ->withCount('purchases')
        ->get();

        // Calcular estatísticas em uma única consulta
        $monthlyStats = DB::table('purchases')
            ->select('product_id')
            ->selectRaw('SUM(total_value) as monthly_spend')
            ->whereMonth('purchase_date', now()->month)
            ->whereYear('purchase_date', now()->year)
            ->groupBy('product_id')
            ->pluck('monthly_spend', 'product_id');

        // Aplicar estatísticas calculadas
        $products->each(function ($product) use ($monthlyStats) {
            $product->monthly_spend = $monthlyStats[$product->id] ?? 0;
        });

        // Top produtos usando dados já calculados
        $topProducts = $products->sortByDesc('monthly_spend')->take(2);
        
        // Gasto total mensal
        $totalMonthlySpend = $products->sum('monthly_spend');

        return view('products.index', compact('products', 'topProducts', 'totalMonthlySpend'));
    }

    public function show(Product $product)
    {
        $product->load(['purchases' => function($query) {
            $query->select('id', 'product_id', 'quantity', 'price', 'total_value', 
                          'store', 'purchase_date', 'notes')
                  ->orderBy('purchase_date', 'desc');
        }]);

        return view('products.show', compact('product'));
    }

    public function search()
    {
        $query = request('q', '');
        $category = request('category', '');
        $ajax = request('ajax', false);
        
        $productsQuery = Product::select([
            'id', 'name', 'category', 'unit', 'description',
            'image', 'image_path', 'variants', 'has_variants',
            'average_price', 'last_price', 'total_spent', 'purchase_count'
        ]);
        
        if ($query) {
            $productsQuery->where(function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('description', 'LIKE', "%{$query}%");
            });
        }
        
        if ($category) {
            $productsQuery->where('category', $category);
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
            'products' => $products,
            'categories' => $categories,
            'query' => $query,
            'category' => $category
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
            
            // Criar as compras e agrupar por departamento
            $purchasesByDepartment = [];
            
            foreach ($request->items as $item) {
                $purchase = Purchase::create([
                    'user_id' => $userId,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total_value' => $item['quantity'] * $item['price'],
                    'store' => $request->store ?? 'Loja Teste',
                    'purchase_date' => $request->date ?? now(),
                    'notes' => isset($item['variant']) ? "Variante: {$item['variant']}" : null
                ]);
                
                $purchases[] = $purchase;
                $totalAmount += $purchase->total_value;
                
                // Buscar departamento do produto
                $product = Product::find($item['product_id']);
                $department = $product->goal_category ?? null;
                
                if (!isset($purchasesByDepartment[$department])) {
                    $purchasesByDepartment[$department] = 0;
                }
                $purchasesByDepartment[$department] += $purchase->total_value;
            }
            
            // Criar entrada(s) no Fluxo de Caixa por departamento
            try {
                $cashFlows = [];
                
                foreach ($purchasesByDepartment as $department => $amount) {
                    $cashFlow = CashFlow::create([
                        'user_id' => $userId,
                        'type' => 'expense',
                        'title' => "Compra - {$request->store}",
                        'description' => "Compra de " . count($request->items) . " item(ns) - " . implode(', ', array_column($request->items, 'variant')),
                        'amount' => $amount,
                        'category_id' => $purchaseCategory->id,
                        'goal_category' => $department,
                        'transaction_date' => $request->date ?? now(),
                        'payment_method' => 'cash',
                        'reference' => 'Compra via sistema',
                        'is_confirmed' => true
                    ]);
                    
                    $cashFlows[] = $cashFlow;
                    
                    \Log::info('Entrada criada no Fluxo de Caixa', [
                        'cash_flow_id' => $cashFlow->id,
                        'amount' => $amount,
                        'department' => $department,
                        'store' => $request->store
                    ]);
                }
                
            } catch (\Exception $e) {
                \Log::error('Erro ao criar entrada no Fluxo de Caixa', [
                    'error' => $e->getMessage(),
                    'amount' => $totalAmount
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao registrar no Fluxo de Caixa: ' . $e->getMessage()
                ], 500);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Compra salva com sucesso e registrada no Fluxo de Caixa!',
                'purchases' => $purchases,
                'cash_flow_id' => $cashFlow->id
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
