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
            // Contar registros antes da exclusão
            $productsCount = Product::count();
            $purchasesCount = Purchase::count();
            $alertsCount = PriceAlert::count();
            
            // Desabilitar foreign key checks temporariamente
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            
            // Limpar tabelas (exceto users) usando truncate (mais rápido e confiável)
            // NOTA: truncate() não funciona dentro de transações, mas é mais eficiente
            Product::truncate();
            Purchase::truncate();
            PriceAlert::truncate();
            
            // Resetar auto increment
            DB::statement('ALTER TABLE products AUTO_INCREMENT = 1');
            DB::statement('ALTER TABLE purchases AUTO_INCREMENT = 1');
            DB::statement('ALTER TABLE price_alerts AUTO_INCREMENT = 1');
            
            // Reabilitar foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            
            // Verificar se os dados foram realmente apagados
            $remainingProducts = Product::count();
            $remainingPurchases = Purchase::count();
            $remainingAlerts = PriceAlert::count();
            
            if ($remainingProducts > 0 || $remainingPurchases > 0 || $remainingAlerts > 0) {
                \Log::error('Reset falhou: dados ainda presentes após truncate', [
                    'products' => $remainingProducts,
                    'purchases' => $remainingPurchases,
                    'alerts' => $remainingAlerts
                ]);
                return redirect()->back()
                    ->with('error', "❌ Erro: Os dados não foram completamente removidos. Produtos: {$remainingProducts}, Compras: {$remainingPurchases}, Alertas: {$remainingAlerts}");
            }
            
            // LIMPAR TODOS OS CACHES APÓS RESET
            $this->clearAllCaches();
            
            \Log::info('Reset do banco de dados concluído com sucesso', [
                'removed_products' => $productsCount,
                'removed_purchases' => $purchasesCount,
                'removed_alerts' => $alertsCount,
                'remaining_products' => $remainingProducts,
                'remaining_purchases' => $remainingPurchases,
                'remaining_alerts' => $remainingAlerts
            ]);
            
            return redirect()->route('admin.products.index')
                ->with('success', "✅ Reset concluído! Removidos: {$productsCount} produtos, {$purchasesCount} compras e {$alertsCount} alertas. Usuários mantidos. Cache limpo.");
                
        } catch (\Exception $e) {
            \Log::error('Erro durante reset do banco de dados', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
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
            // Flush completo do cache (remove tudo)
            \Illuminate\Support\Facades\Cache::flush();
            
            // Limpar cache do Laravel via artisan
            \Illuminate\Support\Facades\Artisan::call('cache:clear');
            \Illuminate\Support\Facades\Artisan::call('config:clear');
            \Illuminate\Support\Facades\Artisan::call('view:clear');
            \Illuminate\Support\Facades\Artisan::call('route:clear');
            \Illuminate\Support\Facades\Artisan::call('optimize:clear');
            
            // Limpar cache específico de produtos (chaves exatas)
            $cacheKeys = [
                'api_products',
                'products_index_guest',
            ];
            
            foreach ($cacheKeys as $key) {
                \Illuminate\Support\Facades\Cache::forget($key);
            }
            
            // Limpar cache de todos os usuários (chaves exatas)
            $users = \App\Models\User::pluck('id');
            foreach ($users as $userId) {
                \Illuminate\Support\Facades\Cache::forget("products_index_{$userId}");
                \Illuminate\Support\Facades\Cache::forget("products_compra_{$userId}");
                \Illuminate\Support\Facades\Cache::forget("monthly_stats_{$userId}");
                \Illuminate\Support\Facades\Cache::forget("top_products_{$userId}");
            }
            
        } catch (\Exception $e) {
            \Log::error('Erro ao limpar cache após reset: ' . $e->getMessage());
        }
    }
}