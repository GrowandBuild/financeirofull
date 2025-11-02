<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PriceAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class ResetController extends Controller
{
    /**
     * Show the reset confirmation page
     */
    public function index()
    {
        $stats = [
            'products' => Product::count(),
            'purchases' => Purchase::count(),
            'alerts' => PriceAlert::count(),
            'users' => DB::table('users')->count(),
        ];
        
        return view('admin.reset', compact('stats'));
    }
    
    /**
     * Execute the database reset
     */
    public function reset(Request $request)
    {
        $request->validate([
            'confirmation' => 'required|in:RESETAR',
        ], [
            'confirmation.required' => 'Você deve digitar "RESETAR" para confirmar.',
            'confirmation.in' => 'Você deve digitar exatamente "RESETAR" para confirmar.',
        ]);
        
        try {
            // Desabilitar foreign key checks temporariamente
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            
            // Contar registros antes da exclusão
            $productsCount = Product::count();
            $purchasesCount = Purchase::count();
            $alertsCount = PriceAlert::count();
            
            // Limpar tabelas (exceto users)
            Product::truncate();
            Purchase::truncate();
            PriceAlert::truncate();
            
            // Resetar auto increment
            DB::statement('ALTER TABLE products AUTO_INCREMENT = 1');
            DB::statement('ALTER TABLE purchases AUTO_INCREMENT = 1');
            DB::statement('ALTER TABLE price_alerts AUTO_INCREMENT = 1');
            
            // Reabilitar foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            
            // LIMPAR TODOS OS CACHES APÓS RESET
            $this->clearAllCaches();
            
            return redirect()->route('admin.products.index')
                ->with('success', "✅ Reset concluído! Removidos: {$productsCount} produtos, {$purchasesCount} compras e {$alertsCount} alertas. Usuários mantidos. Cache limpo.");
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', '❌ Erro durante o reset: ' . $e->getMessage());
        }
    }
    
    /**
     * Limpar TODOS os caches do sistema
     */
    private function clearAllCaches()
    {
        try {
            // Limpar cache do Laravel
            \Illuminate\Support\Facades\Artisan::call('cache:clear');
            \Illuminate\Support\Facades\Artisan::call('config:clear');
            \Illuminate\Support\Facades\Artisan::call('view:clear');
            \Illuminate\Support\Facades\Artisan::call('route:clear');
            
            // Limpar cache específico de produtos
            $patterns = [
                'products_index_*',
                'product_show_*',
                'search_*',
                'products_compra_*',
                'api_products',
                'monthly_stats_*',
                'top_products_*'
            ];
            
            foreach ($patterns as $pattern) {
                \Illuminate\Support\Facades\Cache::forget($pattern);
            }
            
            // Limpar cache de todos os usuários
            $users = \App\Models\User::pluck('id');
            foreach ($users as $userId) {
                \Illuminate\Support\Facades\Cache::forget("products_index_{$userId}");
                \Illuminate\Support\Facades\Cache::forget("search_*_{$userId}");
                \Illuminate\Support\Facades\Cache::forget("products_compra_{$userId}");
            }
            
            // Limpar cache de guest
            \Illuminate\Support\Facades\Cache::forget('products_index_guest');
            \Illuminate\Support\Facades\Cache::forget('search_*_guest');
            \Illuminate\Support\Facades\Cache::forget('products_compra_guest');
            
        } catch (\Exception $e) {
            \Log::error('Erro ao limpar cache após reset: ' . $e->getMessage());
        }
    }
}