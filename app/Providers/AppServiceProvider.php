<?php

namespace App\Providers;

use App\Models\Purchase;
use App\Models\Product;
use App\Observers\PurchaseObserver;
use App\Observers\ProductObserver;
use App\Policies\ProductPolicy;
use App\Policies\PurchasePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Product::class => ProductPolicy::class,
        Purchase::class => PurchasePolicy::class,
    ];

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
        $this->registerPolicies();
        
        Purchase::observe(PurchaseObserver::class);
        Product::observe(ProductObserver::class);
        
        // Configurar paginação para usar template customizado
        Paginator::defaultView('vendor.pagination.default');
        Paginator::defaultSimpleView('vendor.pagination.default');
    }
}
