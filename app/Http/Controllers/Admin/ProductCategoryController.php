<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = ProductCategory::withCount(['products' => function($query) {
                $query->whereNotNull('category');
            }])
            ->orderBy('usage_count', 'desc')
            ->orderBy('name', 'asc')
            ->paginate(20);
        
        // Calcular estatísticas de gastos por categoria
        foreach ($categories as $category) {
            // Buscar produtos dessa categoria
            $productIds = Product::where('category', $category->name)->pluck('id');
            
            // Calcular gastos totais
            $totalSpent = $productIds->count() > 0 
                ? Purchase::whereIn('product_id', $productIds)->sum('total_value') 
                : 0;
            
            // Calcular gastos do mês atual
            $monthlySpent = $productIds->count() > 0
                ? Purchase::whereIn('product_id', $productIds)
                    ->whereMonth('purchase_date', now()->month)
                    ->whereYear('purchase_date', now()->year)
                    ->sum('total_value')
                : 0;
            
            // Calcular média mensal (últimos 3 meses)
            $last3Months = $productIds->count() > 0
                ? Purchase::whereIn('product_id', $productIds)
                    ->where('purchase_date', '>=', now()->subMonths(3))
                    ->selectRaw('SUM(total_value) as total, COUNT(DISTINCT MONTH(purchase_date)) as months')
                    ->first()
                : null;
            
            $avgMonthly = $last3Months && $last3Months->months > 0 
                ? $last3Months->total / $last3Months->months 
                : 0;
            
            $category->total_spent = $totalSpent;
            $category->monthly_spent = $monthlySpent;
            $category->avg_monthly_spent = $avgMonthly;
            $category->purchase_count = $productIds->count() > 0 
                ? Purchase::whereIn('product_id', $productIds)->count() 
                : 0;
        }
        
        return view('admin.product-categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.product-categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:product_categories,name',
        ]);
        
        $category = ProductCategory::findOrCreate($request->name);
        
        // Se já existir, atualiza apenas se necessário
        if ($category->wasRecentlyCreated) {
            return redirect()
                ->route('admin.product-categories.index')
                ->with('success', 'Categoria criada com sucesso!');
        } else {
            return redirect()
                ->route('admin.product-categories.index')
                ->with('info', 'Categoria já existia e foi vinculada corretamente!');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductCategory $productCategory)
    {
        // Buscar produtos dessa categoria
        $products = Product::where('category', $productCategory->name)
            ->orderBy('total_spent', 'desc')
            ->orderBy('name', 'asc')
            ->get();
        $productIds = $products->pluck('id');
        
        // Estatísticas detalhadas
        $totalSpent = Purchase::whereIn('product_id', $productIds)->sum('total_value');
        $monthlySpent = Purchase::whereIn('product_id', $productIds)
            ->whereMonth('purchase_date', now()->month)
            ->whereYear('purchase_date', now()->year)
            ->sum('total_value');
        
        // Gastos por mês (últimos 12 meses)
        $monthlyStats = Purchase::whereIn('product_id', $productIds)
            ->where('purchase_date', '>=', now()->subMonths(12))
            ->selectRaw('
                YEAR(purchase_date) as year,
                MONTH(purchase_date) as month,
                SUM(total_value) as total
            ')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();
        
        // Top produtos dessa categoria
        $topProducts = Product::whereIn('id', $productIds)
            ->withCount(['purchases' => function($query) {
                $query->select(DB::raw('SUM(total_value)'));
            }])
            ->orderBy('total_spent', 'desc')
            ->limit(10)
            ->get();
        
        return view('admin.product-categories.show', compact(
            'productCategory', 
            'products', 
            'totalSpent',
            'monthlySpent',
            'monthlyStats',
            'topProducts'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductCategory $productCategory)
    {
        return view('admin.product-categories.edit', compact('productCategory'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductCategory $productCategory)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:product_categories,name,' . $productCategory->id,
            'is_active' => 'boolean',
        ]);
        
        $oldName = $productCategory->name;
        
        // Atualizar categoria
        $productCategory->update([
            'name' => $request->name,
            'normalized_name' => ProductCategory::normalizeCategoryName($request->name),
            'slug' => \Illuminate\Support\Str::slug($request->name),
            'is_active' => $request->has('is_active') ? $request->is_active : true,
        ]);
        
        // Se mudou o nome, atualizar todos os produtos
        if ($oldName !== $request->name) {
            Product::where('category', $oldName)
                ->update(['category' => $request->name]);
        }
        
        return redirect()
            ->route('admin.product-categories.index')
            ->with('success', 'Categoria atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductCategory $productCategory)
    {
        // Verificar se há produtos usando essa categoria
        $productsCount = Product::where('category', $productCategory->name)->count();
        
        if ($productsCount > 0) {
            return redirect()
                ->route('admin.product-categories.index')
                ->with('error', "Não é possível deletar a categoria. Ela está sendo usada por {$productsCount} produto(s).");
        }
        
        $productCategory->delete();
        
        return redirect()
            ->route('admin.product-categories.index')
            ->with('success', 'Categoria deletada com sucesso!');
    }

    /**
     * Migrar categorias existentes de produtos
     */
    public function migrate()
    {
        $products = Product::whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->pluck('category')
            ->filter();
        
        $migrated = 0;
        foreach ($products as $categoryName) {
            ProductCategory::findOrCreate($categoryName);
            $migrated++;
        }
        
        return redirect()
            ->route('admin.product-categories.index')
            ->with('success', "Migradas {$migrated} categorias com sucesso!");
    }
}
