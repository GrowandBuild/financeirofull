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
                    <a href="{{ route('admin.product-categories.index') }}" class="action-btn" title="Gerenciar Categorias">
                        <i class="bi bi-tags"></i>
                    </a>
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
        <div class="product-list-section mb-6">
            <div class="section-header">
                <div class="section-title-wrapper">
                    <h3 class="section-title">
                        <i class="bi bi-grid-3x3-gap"></i>
                        Lista de Produtos
                    </h3>
                    <p class="section-subtitle">Gerencie todos os seus produtos</p>
                </div>
                <div class="filter-section">
                    <a href="{{ route('admin.products.create') }}" class="premium-btn primary new-product-btn">
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
        <div class="premium-product-grid" id="productGrid">
            @forelse($products as $product)
                <div class="premium-product-card admin-product-card">
                    <div class="premium-product-image">
                        @if($product->image || $product->image_path)
                            <img src="{{ $product->image_url ?? asset('images/no-image.png') }}" 
                                 alt="{{ $product->name }}"
                                 onerror="this.onerror=null; this.src='{{ asset('images/no-image.png') }}';">
                        @else
                            <div class="product-icon">
                                <i class="bi bi-box"></i>
                            </div>
                        @endif
                        @if($product->has_variants)
                            <a href="{{ route('admin.products.show', $product) }}" class="variants-btn" title="Ver Variantes">
                                <i class="bi bi-layers"></i>
                                <span>Variantes</span>
                            </a>
                        @endif
                        <div class="product-overlay">
                            <div class="overlay-actions">
                                <a href="{{ route('admin.products.show', $product) }}" 
                                   class="overlay-action-btn" title="Ver detalhes">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.products.edit', $product) }}" 
                                   class="overlay-action-btn edit" title="Editar produto">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.products.destroy', $product) }}" 
                                      method="POST" 
                                      class="inline delete-form"
                                      onsubmit="return confirm('Tem certeza que deseja excluir este produto?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="overlay-action-btn delete" title="Excluir produto">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="premium-product-info">
                        <h5 class="premium-product-name">{{ $product->name }}</h5>
                        <div class="premium-product-category">
                            {{ $product->category ?: 'Sem categoria' }}
                        </div>
                        
                        @if($product->last_price > 0)
                            <div class="premium-product-price">
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
.product-list-section {
    margin-bottom: 1.5rem;
}

.section-header {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    align-items: flex-start;
    gap: 1rem;
    flex-wrap: wrap;
}

.section-title-wrapper {
    flex: 1;
    min-width: 0;
}

.section-title {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: white;
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0;
}

.section-title i {
    color: #10b981;
    font-size: 1.5rem;
}

.section-subtitle {
    color: rgba(255, 255, 255, 0.6);
    font-size: 0.875rem;
    margin: 0.25rem 0 0 0;
    display: block;
}

.filter-section {
    display: flex;
    gap: 0.75rem;
    align-items: center;
    flex-wrap: wrap;
    flex-shrink: 0;
}

@media (max-width: 768px) {
    .section-header {
        flex-direction: column;
    }
    
    .filter-section {
        width: 100%;
        justify-content: flex-start;
    }
    
    .new-product-btn,
    .premium-btn {
        flex: 1;
        min-width: 0;
    }
}

.new-product-btn {
    background: linear-gradient(135deg, #10b981, #059669) !important;
    border: none !important;
    color: white !important;
    padding: 0.75rem 1.5rem !important;
    border-radius: 12px !important;
    font-weight: 600 !important;
    display: flex !important;
    align-items: center !important;
    gap: 0.5rem !important;
    transition: all 0.3s ease !important;
    box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3) !important;
}

.new-product-btn:hover {
    background: linear-gradient(135deg, #059669, #047857) !important;
    transform: translateY(-2px) !important;
    box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4) !important;
    color: white !important;
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
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 1.5rem;
    width: 100%;
    box-sizing: border-box;
    overflow: visible;
}

.admin-product-card {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.05) 0%, rgba(255, 255, 255, 0.02) 100%);
    border-radius: 16px;
    border: 2px solid rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    padding: 0;
    text-decoration: none;
    width: 100%;
    max-width: 100%;
    min-width: 0;
    overflow: hidden;
    word-wrap: break-word;
    overflow-wrap: break-word;
    box-sizing: border-box;
    position: relative;
    cursor: pointer;
}

.admin-product-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
    border-color: rgba(16, 185, 129, 0.4);
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(5, 150, 105, 0.05) 100%);
}

.premium-product-image {
    position: relative;
    width: 100%;
    aspect-ratio: 1;
    min-height: 200px;
    flex-shrink: 0;
    border-radius: 0;
    overflow: hidden;
    background: rgba(255, 255, 255, 0.05);
}

.premium-product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.admin-product-card:hover .premium-product-image img {
    transform: scale(1.05);
}

.product-icon {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #6366f1, #4f46e5);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 3rem;
}

.product-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(to bottom, rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.7));
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: all 0.3s ease;
    backdrop-filter: blur(2px);
}

.admin-product-card:hover .product-overlay {
    opacity: 1;
}

.overlay-actions {
    display: flex;
    gap: 0.75rem;
    align-items: center;
    justify-content: center;
}

.overlay-action-btn {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    border: 2px solid rgba(255, 255, 255, 0.3);
    color: white;
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    text-decoration: none;
    cursor: pointer;
}

.overlay-action-btn:hover {
    background: rgba(255, 255, 255, 0.25);
    border-color: rgba(255, 255, 255, 0.5);
    transform: scale(1.1);
    color: white;
}

.overlay-action-btn.edit {
    background: rgba(59, 130, 246, 0.3);
    border-color: rgba(59, 130, 246, 0.5);
}

.overlay-action-btn.edit:hover {
    background: rgba(59, 130, 246, 0.5);
    border-color: rgba(59, 130, 246, 0.7);
}

.overlay-action-btn.delete {
    background: rgba(239, 68, 68, 0.3);
    border-color: rgba(239, 68, 68, 0.5);
}

.overlay-action-btn.delete:hover {
    background: rgba(239, 68, 68, 0.5);
    border-color: rgba(239, 68, 68, 0.7);
}

.overlay-action-btn i {
    font-size: 1.25rem;
}

.variants-btn {
    position: absolute;
    top: 0.75rem;
    left: 0.75rem;
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    padding: 0.5rem 0.75rem;
    border-radius: 10px;
    font-size: 0.75rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.375rem;
    text-decoration: none;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
    transition: all 0.3s ease;
    z-index: 10;
    border: 2px solid rgba(255, 255, 255, 0.2);
}

.variants-btn:hover {
    background: linear-gradient(135deg, #059669, #047857);
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(16, 185, 129, 0.5);
    color: white;
}

.variants-btn i {
    font-size: 0.875rem;
}

.premium-product-info {
    flex: 1;
    min-width: 0;
    max-width: 100%;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    padding: 1rem;
    overflow: hidden;
}

.delete-form {
    margin: 0;
    display: inline-block;
}

.delete-form button {
    border: none;
    background: transparent;
    padding: 0;
    margin: 0;
}

.premium-product-name {
    color: white;
    font-size: 1.125rem;
    font-weight: 600;
    margin: 0;
    line-height: 1.3;
    word-wrap: break-word;
    overflow-wrap: break-word;
}

.premium-product-category {
    color: rgba(255, 255, 255, 0.6);
    font-size: 0.875rem;
    margin: 0;
    word-wrap: break-word;
    overflow-wrap: break-word;
}

.premium-product-price {
    color: #10b981;
    font-size: 1rem;
    font-weight: 700;
    margin-top: 0.25rem;
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
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 1rem;
        width: 100%;
    }
    
    .admin-product-card {
        width: 100%;
        max-width: 100%;
    }
    
    .premium-product-image {
        min-height: 150px;
    }
    
    .premium-product-info {
        padding: 0.75rem;
    }
    
    .stats-grid {
        grid-template-columns: 1fr 1fr;
        gap: 0.75rem;
    }
    
    .overlay-actions {
        gap: 0.5rem;
    }
    
    .overlay-action-btn {
        width: 40px;
        height: 40px;
    }
    
    .overlay-action-btn i {
        font-size: 1rem;
    }
    
    .variants-btn {
        padding: 0.375rem 0.5rem;
        font-size: 0.625rem;
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
    
    .premium-product-grid {
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        gap: 0.75rem;
    }
    
    .admin-product-card {
        width: 100%;
        max-width: 100%;
    }
    
    .premium-product-image {
        min-height: 140px;
    }
    
    .premium-product-info {
        padding: 0.625rem;
    }
    
    .premium-product-name {
        font-size: 1rem;
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
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 1.5rem;
        width: 100%;
    }
    
    .admin-product-card {
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
