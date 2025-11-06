<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductCategoryController extends Controller
{
    /**
     * Busca categorias para autocomplete
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->input('q', '');
        
        if (empty($query)) {
            // Retorna todas as categorias ativas (não apenas mais usadas)
            // Se não houver categorias ativas, retorna todas as categorias (para garantir que apareçam)
            $categories = ProductCategory::where('is_active', true)
                ->orderBy('usage_count', 'desc')
                ->orderBy('name', 'asc')
                ->get();
            
            // Se não houver categorias ativas, pega todas (pode ser que algumas não estejam marcadas como ativas)
            if ($categories->isEmpty()) {
                $categories = ProductCategory::orderBy('usage_count', 'desc')
                    ->orderBy('name', 'asc')
                    ->get();
            }
            
            $categories = $categories->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'normalized_name' => $category->normalized_name,
                    'usage_count' => $category->usage_count
                ];
            })->toArray();
        } else {
            $categories = ProductCategory::searchSimilar($query, 20);
        }
        
        return response()->json([
            'success' => true,
            'categories' => $categories
        ]);
    }

    /**
     * Busca ou cria uma categoria
     */
    public function findOrCreate(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);
        
        $categoryName = trim($request->input('name'));
        
        if (empty($categoryName)) {
            return response()->json([
                'success' => false,
                'message' => 'Nome da categoria é obrigatório'
            ], 400);
        }
        
        $category = ProductCategory::findOrCreate($categoryName);
        
        return response()->json([
            'success' => true,
            'category' => [
                'id' => $category->id,
                'name' => $category->name,
                'normalized_name' => $category->normalized_name,
                'slug' => $category->slug
            ]
        ]);
    }

    /**
     * Lista todas as categorias ativas
     */
    public function index(): JsonResponse
    {
        $categories = ProductCategory::where('is_active', true)
            ->orderBy('usage_count', 'desc')
            ->orderBy('name', 'asc')
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'normalized_name' => $category->normalized_name,
                    'slug' => $category->slug,
                    'usage_count' => $category->usage_count
                ];
            });
        
        return response()->json([
            'success' => true,
            'categories' => $categories
        ]);
    }

    /**
     * Migra categorias existentes de produtos para o sistema normalizado
     */
    public function migrateExistingCategories(): JsonResponse
    {
        $products = \App\Models\Product::whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->pluck('category')
            ->filter();
        
        $migrated = 0;
        foreach ($products as $categoryName) {
            $category = ProductCategory::findOrCreate($categoryName);
            // Garantir que a categoria esteja ativa
            if (!$category->is_active) {
                $category->update(['is_active' => true]);
            }
            $migrated++;
        }
        
        // Garantir que todas as categorias existentes estejam ativas
        ProductCategory::where('is_active', false)->update(['is_active' => true]);
        
        return response()->json([
            'success' => true,
            'message' => "Migradas {$migrated} categorias",
            'migrated_count' => $migrated
        ]);
    }
}
