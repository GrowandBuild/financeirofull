<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PriceAlert;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ResetDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:reset {--force : Force reset without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset database keeping only users (removes all products, purchases, and price alerts)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('force')) {
            if (!$this->confirm('⚠️  ATENÇÃO: Isso irá apagar TODOS os produtos, compras e alertas de preço. Apenas os usuários serão mantidos. Continuar?')) {
                $this->info('Operação cancelada.');
                return Command::SUCCESS;
            }
        }

        $this->info('🔄 Iniciando reset do banco de dados...');
        
        try {
            // Desabilitar foreign key checks temporariamente
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            
            // Contar registros antes da exclusão
            $productsCount = Product::count();
            $purchasesCount = Purchase::count();
            $alertsCount = PriceAlert::count();
            
            $this->info("📊 Registros encontrados:");
            $this->info("   - Produtos: {$productsCount}");
            $this->info("   - Compras: {$purchasesCount}");
            $this->info("   - Alertas: {$alertsCount}");
            
            // Limpar tabelas (exceto users)
            $this->info('🗑️  Removendo produtos...');
            Product::truncate();
            
            $this->info('🗑️  Removendo compras...');
            Purchase::truncate();
            
            $this->info('🗑️  Removendo alertas de preço...');
            PriceAlert::truncate();
            
            // Resetar auto increment
            $this->info('🔄 Resetando contadores...');
            DB::statement('ALTER TABLE products AUTO_INCREMENT = 1');
            DB::statement('ALTER TABLE purchases AUTO_INCREMENT = 1');
            DB::statement('ALTER TABLE price_alerts AUTO_INCREMENT = 1');
            
            // Reabilitar foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            
            $this->info('✅ Reset concluído com sucesso!');
            $this->info('👥 Usuários mantidos: ' . DB::table('users')->count());
            $this->info('📦 Produtos restantes: ' . Product::count());
            $this->info('🛒 Compras restantes: ' . Purchase::count());
            $this->info('🔔 Alertas restantes: ' . PriceAlert::count());
            
        } catch (\Exception $e) {
            $this->error('❌ Erro durante o reset: ' . $e->getMessage());
            return Command::FAILURE;
        }
        
        return Command::SUCCESS;
    }
}