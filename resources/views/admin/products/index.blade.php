@extends('layouts.app')

@section('content')
<div class="premium-content">
    <div class="max-w-6xl mx-auto px-4" style="max-width: 100%; overflow-x: hidden; box-sizing: border-box;">
        <!-- Header Premium -->
        <div class="premium-header mb-8">
            <div class="header-content">
                <div class="header-left">
                    <a href="{{ route('dashboard') }}" class="back-btn">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <div class="header-title">
                        <h1>Gerenciar Produtos</h1>
                        <p class="header-subtitle">Administre seu catálogo de produtos</p>
                    </div>
                </div>
                <div class="header-actions">
                    <a href="{{ route('admin.reset.index') }}" class="action-btn danger" title="⚠️ RESET PERIGOSO - Apaga TUDO!">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </a>
                    <a href="{{ route('admin.products.create') }}" class="action-btn" title="Novo Produto">
                        <i class="bi bi-plus-circle"></i>
                    </a>
                    <a href="{{ route('dashboard') }}" class="action-btn" title="Painel Principal">
                        <i class="bi bi-speedometer2"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="bg-emerald-500/20 border border-emerald-500/30 text-emerald-300 px-4 py-3 rounded-lg mb-6 backdrop-blur-sm">
                <div class="flex items-center gap-2">
                    <i class="bi bi-check-circle"></i>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        <!-- Stats Cards -->
        <div class="stats-grid mb-8">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="bi bi-box"></i>
                </div>
                <div class="stat-label">Total de Produtos</div>
                <div class="stat-value">{{ $products->total() }}</div>
                <div class="stat-subtitle">Produtos cadastrados</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="bi bi-layers"></i>
                </div>
                <div class="stat-label">Com Variantes</div>
                <div class="stat-value">{{ $products->where('has_variants', true)->count() }}</div>
                <div class="stat-subtitle">Produtos complexos</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="bi bi-currency-dollar"></i>
                </div>
                <div class="stat-label">Valor Médio</div>
                <div class="stat-value">R$ {{ number_format($products->avg('last_price'), 2, ',', '.') }}</div>
                <div class="stat-subtitle">Preço médio</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="bi bi-graph-up"></i>
                </div>
                <div class="stat-label">Compras</div>
                <div class="stat-value">{{ $products->sum('purchase_count') }}</div>
                <div class="stat-subtitle">Total de compras</div>
            </div>
        </div>

        <!-- Action Bar -->
        <div class="chart-section mb-6" style="max-width: 100%; overflow-x: hidden; word-wrap: break-word; box-sizing: border-box;">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4" style="flex-wrap: wrap; width: 100%; box-sizing: border-box;">
                <div style="flex: 1; min-width: 0; word-wrap: break-word; box-sizing: border-box;">
                    <h3 class="section-title" style="margin: 0; padding: 0;">
                        <i class="bi bi-list-ul"></i>
                        Lista de Produtos
                    </h3>
                    <p class="text-white/60 text-sm mt-1" style="word-wrap: break-word; margin-top: 0.25rem;">Gerencie todos os seus produtos</p>
                </div>
                <div class="flex gap-2" style="flex-wrap: wrap;">
                    <a href="{{ route('admin.products.create') }}" class="premium-btn primary">
                        <i class="bi bi-plus-circle"></i>
                        <span class="d-none d-sm-inline">Novo Produto</span>
                        <span class="d-sm-none">Novo</span>
                    </a>
                    <a href="{{ route('dashboard') }}" class="premium-btn outline">
                        <i class="bi bi-speedometer2"></i>
                        <span class="d-none d-sm-inline">Painel Principal</span>
                        <span class="d-sm-none">Painel</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="premium-product-grid">
            @forelse($products as $product)
                <div class="premium-product-card group">
                    <div class="premium-product-image">
                        @if($product->image || $product->image_path)
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}">
                            <div class="product-overlay">
                                <i class="bi bi-eye"></i>
                            </div>
                        @else
                            <div class="product-icon">
                                <i class="bi bi-box"></i>
                            </div>
                        @endif
                        @if($product->has_variants)
                            <div class="category-badge">
                                <i class="bi bi-layers"></i>
                                Variantes
                            </div>
                        @endif
                    </div>
                    
                    <div class="premium-product-info">
                        <div class="premium-product-name">{{ $product->name }}</div>
                        <div class="premium-product-category">
                            <i class="bi bi-tag"></i>
                            {{ $product->category ?: 'Sem categoria' }} • {{ $product->unit }}
                        </div>
                        
                        @if($product->last_price > 0)
                            <div class="premium-product-price">
                                <i class="bi bi-currency-dollar"></i>
                                R$ {{ number_format($product->last_price, 2, ',', '.') }}
                            </div>
                        @endif

                        @if($product->purchase_count > 0)
                            <div class="product-stats">
                                <div class="stat-item">
                                    <i class="bi bi-cart-check"></i>
                                    {{ $product->purchase_count }} compras
                                </div>
                                <div class="stat-item">
                                    <i class="bi bi-graph-up"></i>
                                    R$ {{ number_format($product->total_spent, 2, ',', '.') }}
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Action Buttons -->
                    <div class="product-actions">
                        <a href="{{ route('admin.products.show', $product) }}" 
                           class="premium-btn outline" title="Ver detalhes">
                            <i class="bi bi-eye"></i>
                            <span class="btn-text">Ver</span>
                        </a>
                        <a href="{{ route('admin.products.edit', $product) }}" 
                           class="premium-btn secondary" title="Editar produto">
                            <i class="bi bi-pencil"></i>
                            <span class="btn-text">Editar</span>
                        </a>
                        <form action="{{ route('admin.products.destroy', $product) }}" 
                              method="POST" 
                              class="inline delete-form"
                              onsubmit="return confirm('Tem certeza que deseja excluir este produto?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="premium-btn danger" title="Excluir produto">
                                <i class="bi bi-trash"></i>
                                <span class="btn-text">Excluir</span>
                            </button>
                        </form>
                    </div>
                </div>
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

        <!-- Pagination -->
        @if($products->hasPages())
            <div class="mt-6 flex justify-center">
                <div class="bg-white/10 backdrop-blur-sm rounded-lg p-3">
                    {{ $products->onEachSide(1)->links() }}
                </div>
            </div>
        @endif
    </div>
</div>

<style>
/* Admin specific styles */
.chart-section {
    width: 100%;
    box-sizing: border-box;
    overflow-x: hidden;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
    width: 100%;
    box-sizing: border-box;
}

.premium-product-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 1.5rem;
    width: 100%;
    box-sizing: border-box;
    overflow: visible;
}

.premium-product-card {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%);
    border-radius: 12px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    transition: all 0.3s ease;
    display: grid;
    grid-template-columns: auto 1fr auto;
    grid-template-rows: auto 1fr;
    gap: 1rem;
    padding: 1.5rem;
    text-decoration: none;
    width: 100%;
    max-width: 100%;
    min-width: 0;
    overflow: visible;
    word-wrap: break-word;
    overflow-wrap: break-word;
    box-sizing: border-box;
    align-items: start;
    position: relative;
}

.premium-product-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
    border-color: rgba(16, 185, 129, 0.3);
}

.premium-product-image {
    position: relative;
    width: 100px;
    height: 100px;
    min-width: 100px;
    flex-shrink: 0;
    border-radius: 12px;
    overflow: hidden;
    grid-row: 1 / -1;
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
    font-size: 2rem;
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
    font-size: 1.5rem;
}

.premium-product-info {
    flex: 1;
    min-width: 0;
    max-width: 100%;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    overflow: hidden;
}

.product-actions {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    min-width: 110px;
    max-width: 140px;
    grid-row: 1 / -1;
    align-self: start;
    flex-shrink: 0;
    overflow: visible;
}

.product-actions .premium-btn {
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
    width: 100%;
    min-width: 0;
    text-align: center;
    justify-content: center;
    white-space: nowrap;
    overflow: visible;
    text-overflow: ellipsis;
}

.product-actions .premium-btn .btn-text {
    display: inline-block;
    white-space: nowrap;
    overflow: visible;
    text-overflow: clip;
}

@media (max-width: 640px) {
    .product-actions .premium-btn .btn-text {
        display: none;
    }
    
    .product-actions .premium-btn {
        min-width: 2.5rem;
        width: auto;
        padding: 0.5rem;
    }
}

.premium-btn.danger {
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.2), rgba(220, 38, 38, 0.2));
    color: #fca5a5;
    border: 1px solid rgba(239, 68, 68, 0.3);
}

.premium-btn.danger:hover {
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.3), rgba(220, 38, 38, 0.3));
    color: #fee2e2;
    border-color: rgba(239, 68, 68, 0.5);
}

.delete-form {
    width: 100%;
    margin: 0;
}

.delete-form button {
    width: 100%;
}

.premium-product-name {
    color: white;
    font-size: 1.125rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    line-height: 1.3;
    word-wrap: break-word;
    overflow-wrap: break-word;
}

.premium-product-category {
    color: #9ca3af;
    font-size: 0.875rem;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.25rem;
    flex-wrap: wrap;
    word-wrap: break-word;
    overflow-wrap: break-word;
}

.premium-product-price {
    color: #10b981;
    font-size: 1rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 0.25rem;
    margin-bottom: 0.5rem;
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
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.product-stats {
    display: flex;
    gap: 1rem;
    margin-top: 0.5rem;
    flex-wrap: wrap;
    word-wrap: break-word;
    overflow-wrap: break-word;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    color: #9ca3af;
    font-size: 0.75rem;
}

.stat-item i {
    color: #10b981;
}

.premium-empty-state {
    text-align: center;
    padding: 3rem 1rem;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%);
    border-radius: 12px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
}

.empty-icon {
    width: 64px;
    height: 64px;
    background: linear-gradient(135deg, #6366f1, #4f46e5);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    color: white;
    font-size: 2rem;
}

.empty-title {
    color: white;
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 0.75rem;
}

.empty-description {
    color: #9ca3af;
    font-size: 1rem;
    margin-bottom: 1.5rem;
    line-height: 1.5;
}

.empty-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
}

/* Responsive adjustments */
@media (max-width: 1024px) {
    .premium-product-grid {
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1rem;
        width: 100%;
    }
    
    .premium-product-card {
        width: 100%;
        max-width: 100%;
    }
}

@media (max-width: 768px) {
    .premium-product-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
        width: 100%;
    }
    
    .premium-product-card {
        grid-template-columns: 80px 1fr;
        grid-template-rows: auto auto;
        padding: 1rem !important;
        gap: 0.75rem !important;
        width: 100%;
        max-width: 100%;
    }
    
    .premium-product-image {
        width: 80px;
        height: 80px;
        grid-row: 1 / 2;
    }
    
    .premium-product-info {
        grid-column: 2 / -1;
        grid-row: 1 / 2;
    }
    
    .product-actions {
        grid-column: 1 / -1;
        grid-row: 2 / -1;
        flex-direction: row;
        gap: 0.5rem;
        width: 100%;
        margin-top: 0.5rem;
    }
    
    .product-actions .premium-btn {
        flex: 1;
        min-width: 0;
    }
    
    .stats-grid {
        grid-template-columns: 1fr 1fr;
        gap: 0.75rem;
    }
    
    .premium-product-name,
    .premium-product-category,
    .premium-product-price {
        word-wrap: break-word;
        overflow-wrap: break-word;
    }
    
    .chart-section {
        padding: 1rem !important;
        width: 100%;
        box-sizing: border-box;
    }
    
    .section-title {
        margin: 0;
        padding: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
}

@media (max-width: 480px) {
    .stats-grid {
        grid-template-columns: 1fr;
        gap: 0.75rem;
    }
    
    .premium-product-card {
        grid-template-columns: 60px 1fr;
        padding: 0.875rem !important;
    }
    
    .premium-product-image {
        width: 60px;
        height: 60px;
    }
    
    .product-actions {
        flex-direction: column;
    }
    
    .product-actions .premium-btn {
        width: 100%;
    }
    
    .header-actions {
        flex-wrap: wrap;
        gap: 0.25rem;
    }
    
    .action-btn {
        min-width: 2.25rem;
        padding: 0.4rem !important;
    }
}

@media (min-width: 1200px) {
    .premium-product-grid {
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 1.5rem;
        width: 100%;
    }
    
    .premium-product-card {
        width: 100%;
        max-width: 100%;
    }
}

/* Override mobile-container for admin pages */
.premium-content {
    max-width: none !important;
    width: 100% !important;
    overflow-x: hidden !important;
    box-sizing: border-box !important;
}

.premium-content .max-w-6xl {
    max-width: 1200px !important;
    overflow-x: hidden !important;
    width: 100% !important;
    box-sizing: border-box !important;
}

/* Ensure proper spacing on larger screens */
@media (min-width: 1024px) {
    .premium-content {
        padding: 2rem 1rem;
        width: 100%;
        box-sizing: border-box;
    }
    
    .chart-section {
        padding: 1.5rem;
        width: 100%;
        box-sizing: border-box;
    }
    
    .premium-product-card {
        padding: 1.5rem;
        width: 100%;
        max-width: 100%;
    }
}

/* Grid improvements for larger screens */
@media (min-width: 768px) {
    .stats-grid {
        grid-template-columns: repeat(4, 1fr);
        gap: 1.5rem;
    }
}

/* Fix buttons getting stuck behind bottom nav */
.premium-content {
    padding-bottom: 120px !important;
    padding-left: 1rem !important;
    padding-right: 1rem !important;
    padding-top: 1rem !important;
    max-width: 100%;
    width: 100%;
    overflow-x: hidden;
    box-sizing: border-box;
}

/* Mobile adjustments */
@media (max-width: 768px) {
    .premium-content {
        padding-bottom: 140px !important;
        padding-left: 0.75rem !important;
        padding-right: 0.75rem !important;
    }
    
    .max-w-6xl {
        max-width: 100% !important;
        padding-left: 0.5rem !important;
        padding-right: 0.5rem !important;
    }
}

@media (max-width: 480px) {
    .premium-content {
        padding-bottom: 140px !important;
        padding-left: 0.5rem !important;
        padding-right: 0.5rem !important;
    }
    
    .max-w-6xl {
        padding-left: 0.25rem !important;
        padding-right: 0.25rem !important;
    }
    
    .premium-header {
        padding: 0.75rem 0.75rem !important;
    }
}

/* BOTÃO DE PERIGO - RESET AGRESSIVO */
       .action-btn.danger {
           background: linear-gradient(135deg, #ef4444, #dc2626) !important;
           color: white !important;
           border: 2px solid #dc2626 !important;
           box-shadow: 0 4px 15px rgba(239, 68, 68, 0.4) !important;
           position: relative !important;
           font-weight: bold !important;
       }


.action-btn.danger:hover {
    background: linear-gradient(135deg, #dc2626, #b91c1c) !important;
    color: white !important;
    transform: translateY(-3px) scale(1.05) !important;
    box-shadow: 0 8px 25px rgba(239, 68, 68, 0.6) !important;
    animation: dangerShake 0.5s ease-in-out !important;
}

.action-btn.danger:active {
    transform: translateY(-1px) scale(0.98) !important;
    box-shadow: 0 2px 10px rgba(239, 68, 68, 0.8) !important;
}



@keyframes dangerShake {
    0%, 100% { transform: translateY(-3px) scale(1.05); }
    25% { transform: translateY(-3px) scale(1.05) translateX(-2px); }
    75% { transform: translateY(-3px) scale(1.05) translateX(2px); }
}

/* Paginação agora está no app.css otimizado para melhor performance */
</style>
@endsection
