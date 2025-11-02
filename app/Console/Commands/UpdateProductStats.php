<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class UpdateProductStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:update-stats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Atualiza as estatísticas de todos os produtos (total_spent, average_price, last_price, purchase_count)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Atualizando estatísticas dos produtos...');
        
        $products = Product::all();
        $bar = $this->output->createProgressBar($products->count());
        $bar->start();

        foreach ($products as $product) {
            $purchases = $product->purchases();
            
            $product->update([
                'total_spent' => $purchases->sum('total_value'),
                'purchase_count' => $purchases->count(),
                'average_price' => $purchases->avg('price') ?? 0,
                'last_price' => $purchases->latest('purchase_date')->first()?->price ?? 0,
            ]);
            
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Estatísticas atualizadas com sucesso!');
        
        return Command::SUCCESS;
    }
}