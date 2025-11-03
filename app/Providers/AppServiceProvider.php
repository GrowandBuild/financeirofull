<?php

namespace App\Providers;

use App\Models\Purchase;
use App\Models\Product;
use App\Observers\PurchaseObserver;
use App\Observers\ProductObserver;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Purchase::observe(PurchaseObserver::class);
        Product::observe(ProductObserver::class);
        
        // Configurar paginação para usar Bootstrap 5 (único framework)
        Paginator::useBootstrapFive();
    }
}
