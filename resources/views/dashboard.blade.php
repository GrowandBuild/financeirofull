@extends('layouts.app')

@section('content')
<div class="premium-content">
    <div class="max-w-4xl mx-auto">
        <!-- Header Premium -->
        <div class="premium-header mb-8">
            <div class="header-content">
                <div class="header-left">
                    <div class="header-title">
                        <h1>Dashboard</h1>
                        <p class="header-subtitle">Bem-vindo de volta, {{ Auth::user()->name }}!</p>
                    </div>
                </div>
                <div class="header-actions">
                    <a href="{{ route('admin.products.index') }}" class="action-btn" title="Gerenciar Produtos">
                        <i class="bi bi-gear"></i>
                    </a>
                    <a href="{{ route('products.index') }}" class="action-btn" title="Ver Produtos">
                        <i class="bi bi-house"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid mb-8">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="bi bi-box"></i>
                </div>
                <div class="stat-label">Total de Produtos</div>
                <div class="stat-value">{{ \App\Models\Product::count() }}</div>
                <div class="stat-subtitle">Produtos cadastrados</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="bi bi-cart-check"></i>
                </div>
                <div class="stat-label">Compras Realizadas</div>
                <div class="stat-value">{{ \App\Models\Purchase::count() }}</div>
                <div class="stat-subtitle">Histórico de compras</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="bi bi-currency-dollar"></i>
                </div>
                <div class="stat-label">Valor Total Gasto</div>
                <div class="stat-value">R$ {{ number_format(\App\Models\Purchase::sum('total_value'), 2, ',', '.') }}</div>
                <div class="stat-subtitle">Em todas as compras</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="bi bi-graph-up"></i>
                </div>
                <div class="stat-label">Produtos com Variantes</div>
                <div class="stat-value">{{ \App\Models\Product::where('has_variants', true)->count() }}</div>
                <div class="stat-subtitle">Produtos complexos</div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="chart-section mb-8">
            <div class="section-header">
                <h3 class="section-title">
                    <i class="bi bi-lightning"></i>
                    Ações Rápidas
                </h3>
            </div>
            
            <div class="actions-section">
                <a href="{{ route('admin.products.create') }}" class="premium-btn primary">
                    <i class="bi bi-plus-circle"></i>
                    Novo Produto
                </a>
                
                <a href="{{ route('admin.products.index') }}" class="premium-btn secondary">
                    <i class="bi bi-list-ul"></i>
                    Gerenciar Produtos
                </a>
                
                <a href="{{ route('products.search') }}" class="premium-btn outline">
                    <i class="bi bi-search"></i>
                    Buscar Produtos
                </a>
                
                <a href="{{ route('products.compra') }}" class="premium-btn outline">
                    <i class="bi bi-cart-plus"></i>
                    Registrar Compra
                </a>
            </div>
        </div>

        <!-- Recent Products -->
        <div class="chart-section mb-8">
            <div class="section-header">
                <h3 class="section-title">
                    <i class="bi bi-clock-history"></i>
                    Produtos Recentes
                </h3>
            </div>
            
            <div class="products-carousel-wrapper">
                <div class="products-carousel">
                    @forelse(\App\Models\Product::latest()->get() as $product)
                        <a href="{{ route('admin.products.show', $product) }}" class="premium-product-card">
                            <div class="premium-product-image">
                                @if($product->image)
                                    <img src="{{ $product->image }}" alt="{{ $product->name }}">
                                    <div class="product-overlay">
                                        <i class="bi bi-eye"></i>
                                    </div>
                                @else
                                    <div class="product-icon">
                                        <i class="bi bi-box"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="premium-product-info">
                                <div class="premium-product-name">{{ $product->name }}</div>
                                <div class="premium-product-category">{{ $product->category }} • {{ $product->unit }}</div>
                                @if($product->last_price > 0)
                                    <div class="premium-product-price">
                                        R$ {{ number_format($product->last_price, 2, ',', '.') }}
                                    </div>
                                @endif
                                @if($product->has_variants)
                                    <div class="category-badge">Variantes</div>
                                @endif
                            </div>
                        </a>
                    @empty
                        <div class="premium-empty-state">
                            <div class="empty-icon">
                                <i class="bi bi-box"></i>
                            </div>
                            <div class="empty-title">Nenhum produto encontrado</div>
                            <div class="empty-description">
                                Comece adicionando seu primeiro produto para gerenciar suas compras.
                            </div>
                            <div class="empty-actions">
                                <a href="{{ route('admin.products.create') }}" class="premium-btn primary">
                                    <i class="bi bi-plus"></i>
                                    Adicionar Produto
                                </a>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Recent Purchases -->
        <div class="history-section">
            <div class="section-header">
                <h3 class="section-title">
                    <i class="bi bi-receipt"></i>
                    Compras Recentes
                </h3>
                <a href="{{ route('products.compra') }}" class="view-all-btn">
                    Ver Todas
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            
            <div class="purchase-list">
                @forelse(\App\Models\Purchase::with('product')->latest()->take(5)->get() as $purchase)
                    <div class="purchase-item">
                        <div class="purchase-icon">
                            <i class="bi bi-cart-check"></i>
                        </div>
                        <div class="purchase-details">
                            <div class="purchase-store">{{ $purchase->product->name }}</div>
                            <div class="purchase-date">{{ $purchase->purchase_date->format('d/m/Y') }} • {{ $purchase->store }}</div>
                            <div class="purchase-quantity">{{ $purchase->quantity }} {{ $purchase->product->unit }}</div>
                        </div>
                        <div class="purchase-price">
                            <div class="price-value">R$ {{ number_format($purchase->price, 2, ',', '.') }}</div>
                            <div class="total-value">Total: R$ {{ number_format($purchase->total_value, 2, ',', '.') }}</div>
                        </div>
                    </div>
                @empty
                    <div class="premium-empty-state">
                        <div class="empty-icon">
                            <i class="bi bi-cart"></i>
                        </div>
                        <div class="empty-title">Nenhuma compra registrada</div>
                        <div class="empty-description">
                            Registre suas compras para acompanhar gastos e preços.
                        </div>
                        <div class="empty-actions">
                            <a href="{{ route('products.compra') }}" class="premium-btn primary">
                                <i class="bi bi-plus"></i>
                                Registrar Compra
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<style>
/* Dashboard specific styles */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.actions-section {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.premium-product-grid {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.premium-product-card {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%);
    border-radius: 12px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    transition: all 0.3s ease;
    cursor: pointer;
    display: flex;
    align-items: center;
    padding: 1rem;
    gap: 1rem;
    text-decoration: none;
}

.premium-product-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
    border-color: rgba(16, 185, 129, 0.3);
}

.premium-product-image {
    position: relative;
    width: 60px;
    height: 60px;
    flex-shrink: 0;
    border-radius: 10px;
    overflow: hidden;
}

.premium-product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.product-icon {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #6366f1, #4f46e5);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
}

.product-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: all 0.3s ease;
}

.premium-product-card:hover .product-overlay {
    opacity: 1;
}

.product-overlay i {
    color: white;
    font-size: 1rem;
}

.premium-product-info {
    flex: 1;
    min-width: 0;
}

.premium-product-name {
    color: white;
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: 0.25rem;
    line-height: 1.3;
}

.premium-product-category {
    color: #9ca3af;
    font-size: 0.875rem;
    margin-bottom: 0.25rem;
}

.premium-product-price {
    color: #10b981;
    font-size: 0.875rem;
    font-weight: 700;
}

.category-badge {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
    background: rgba(16, 185, 129, 0.9);
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 6px;
    font-size: 0.625rem;
    font-weight: 600;
}

.purchase-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.purchase-item {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 10px;
    padding: 1rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    border: 1px solid rgba(255, 255, 255, 0.1);
    transition: all 0.3s ease;
}

.purchase-item:hover {
    background: rgba(255, 255, 255, 0.1);
    transform: translateY(-1px);
}

.purchase-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #10b981, #059669);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1rem;
}

.purchase-details {
    flex: 1;
}

.purchase-store {
    color: white;
    font-weight: 600;
    font-size: 0.875rem;
    margin-bottom: 0.25rem;
}

.purchase-date {
    color: #9ca3af;
    font-size: 0.75rem;
    margin-bottom: 0.25rem;
}

.purchase-quantity {
    color: #d1d5db;
    font-size: 0.75rem;
    font-weight: 500;
}

.purchase-price {
    text-align: right;
}

.price-value {
    color: white;
    font-weight: 700;
    font-size: 0.875rem;
    margin-bottom: 0.125rem;
}

.total-value {
    color: #9ca3af;
    font-size: 0.75rem;
}

.view-all-btn {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #d1d5db;
    padding: 0.5rem 0.75rem;
    border-radius: 6px;
    font-size: 0.75rem;
    display: flex;
    align-items: center;
    gap: 0.375rem;
    transition: all 0.3s ease;
    text-decoration: none;
}

.view-all-btn:hover {
    background: rgba(255, 255, 255, 0.2);
    color: white;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: 1fr 1fr;
    }
    
    .actions-section {
        grid-template-columns: 1fr;
    }
}

/* Carousel Styles */
.products-carousel-wrapper {
    position: relative;
    width: 100%;
    overflow: hidden;
    margin-bottom: 1rem;
}

.products-carousel {
    display: flex;
    gap: 0.75rem;
    overflow-x: auto;
    overflow-y: hidden;
    scroll-behavior: smooth;
    padding-bottom: 0.5rem;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: thin;
    scrollbar-color: rgba(255, 255, 255, 0.3) transparent;
}

/* Custom Scrollbar */
.products-carousel::-webkit-scrollbar {
    height: 6px;
}

.products-carousel::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 10px;
}

.products-carousel::-webkit-scrollbar-thumb {
    background: rgba(16, 185, 129, 0.5);
    border-radius: 10px;
}

.products-carousel::-webkit-scrollbar-thumb:hover {
    background: rgba(16, 185, 129, 0.7);
}

/* Cards no carrossel */
.products-carousel .premium-product-card {
    flex: 0 0 calc(50% - 0.375rem);
    min-width: 280px;
    max-width: 100%;
}

/* Mobile adjustments */
@media (max-width: 575px) {
    .products-carousel {
        gap: 0.5rem;
    }
    
    .products-carousel .premium-product-card {
        flex: 0 0 calc(50% - 0.25rem);
        min-width: 260px;
    }
}

/* Tablet adjustments */
@media (min-width: 576px) and (max-width: 768px) {
    .products-carousel .premium-product-card {
        flex: 0 0 calc(50% - 0.5rem);
        min-width: 270px;
    }
}

/* Desktop adjustments */
@media (min-width: 769px) {
    .products-carousel .premium-product-card {
        flex: 0 0 calc(33.333% - 0.6rem);
        min-width: 300px;
    }
}
</style>
@endsection