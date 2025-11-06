@extends('layouts.app')

@section('title', 'Meus Produtos')

@section('content')
<!-- Premium Header -->
<div class="premium-header">
    <div class="header-content">
        <div class="header-title">
            <h1>Meus Produtos</h1>
            <span class="header-subtitle">{{ $products->count() ?? 0 }} produtos cadastrados</span>
        </div>
        <div class="header-actions">
            <button class="action-btn" onclick="searchProducts()">
                <i class="bi bi-search"></i>
            </button>
            <button class="action-btn" onclick="openSettings()">
                <i class="bi bi-funnel"></i>
            </button>
            <button class="action-btn" onclick="openMenu()">
                <i class="bi bi-three-dots-vertical"></i>
            </button>
        </div>
    </div>
</div>

<!-- Premium Content -->
<div class="premium-content">
    <!-- Total Spend Hero Card -->
    <div class="spend-hero-card">
        <div class="spend-header">
            <div class="spend-icon">
                <i class="bi bi-wallet2"></i>
            </div>
            <div class="spend-info">
                <h3 class="spend-title">Gasto Total (Mês)</h3>
                <div class="spend-amount">R$ {{ number_format($totalMonthlySpend ?? 0, 2, ',', '.') }}</div>
            </div>
            <button class="add-product-btn">
                <i class="bi bi-plus-lg"></i>
            </button>
        </div>
    </div>

    <!-- Top Products Section -->
    <div class="section-header">
        <h3 class="section-title">
            <i class="bi bi-trophy"></i>
            Mais Gastos
        </h3>
    </div>
    
    <!-- Top 2 Premium Cards -->
    <div class="top-products-grid">
        @if($topProducts && $topProducts->count() > 0)
            @foreach($topProducts->take(2) as $product)
                <div class="top-product-card">
                    <div class="top-product-icon">
                        <i class="bi bi-star-fill"></i>
                    </div>
                    <div class="top-product-info">
                        <h4 class="top-product-name">{{ $product->name }}</h4>
                        <div class="top-product-amount">R$ {{ number_format($product->monthly_spend, 2, ',', '.') }}</div>
                        <div class="top-product-period">este mês</div>
                    </div>
                </div>
            @endforeach
        @elseif(isset($totalProductsCount) && $totalProductsCount > 0)
            <div class="no-data-card">
                <div class="no-data-icon">
                    <i class="bi bi-graph-up"></i>
                </div>
                <div class="no-data-content">
                    <h4>Nenhum gasto este mês</h4>
                    <p>Você tem {{ $totalProductsCount }} produto(s) cadastrado(s), mas ainda não há compras registradas este mês</p>
                </div>
            </div>
        @else
            <div class="no-data-card">
                <div class="no-data-icon">
                    <i class="bi bi-graph-up"></i>
                </div>
                <div class="no-data-content">
                    <h4>Nenhum produto cadastrado</h4>
                    <p>Adicione produtos para ver estatísticas de gastos</p>
                </div>
            </div>
        @endif
    </div>
    
    <!-- Recent Products Section -->
    @if(isset($recentProducts) && $recentProducts->count() > 0)
        <div class="section-header">
            <h3 class="section-title">
                <i class="bi bi-clock-history"></i>
                Produtos Comprados Recentemente
            </h3>
        </div>
        
        <!-- Recent Products Grid -->
        <div class="recent-products-grid">
            @foreach($recentProducts as $product)
                <div class="premium-product-card" onclick="viewProduct({{ $product->id }})">
                    <div class="premium-product-image">
                        <img src="{{ $product->image_url ?? asset('images/no-image.png') }}" 
                             alt="{{ $product->name }}" 
                             class="img-fluid"
                             loading="lazy"
                             onerror="this.onerror=null; this.src='{{ asset('images/no-image.png') }}';">
                        <div class="product-overlay">
                            <i class="bi bi-eye"></i>
                        </div>
                    </div>
                    <div class="premium-product-info">
                        <h5 class="premium-product-name">{{ $product->name }}</h5>
                        <div class="premium-product-category">{{ $product->category ?? 'Sem categoria' }}</div>
                        @if($product->monthly_spend > 0)
                            <div class="premium-product-price">
                                R$ {{ number_format($product->monthly_spend, 2, ',', '.') }}
                                <small class="text-white/60 text-xs block">Total do mês</small>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
    
    <!-- All Products Section -->
    <div class="section-header">
        <h3 class="section-title">
            <i class="bi bi-grid-3x3-gap"></i>
            Todos os Produtos
        </h3>
    </div>
    
    <!-- Premium Product Grid -->
    <div class="premium-product-grid">
        @if($products && $products->count() > 0)
            @foreach($products as $product)
                <div class="premium-product-card" onclick="viewProduct({{ $product->id }})">
                    <div class="premium-product-image">
                        <img src="{{ $product->image_url ?? asset('images/no-image.png') }}" 
                             alt="{{ $product->name }}" 
                             class="img-fluid"
                             loading="lazy"
                             onerror="this.onerror=null; this.src='{{ asset('images/no-image.png') }}';">
                        <div class="product-overlay">
                            <i class="bi bi-eye"></i>
                        </div>
                    </div>
                    <div class="premium-product-info">
                        <h5 class="premium-product-name">{{ $product->name }}</h5>
                        <div class="premium-product-category">{{ $product->category ?? 'Sem categoria' }}</div>
                        <div class="premium-product-price">
                            @if($product->monthly_spend > 0)
                                R$ {{ number_format($product->monthly_spend, 2, ',', '.') }}
                                <small class="text-white/60 text-xs block">Total do mês</small>
                            @else
                                Sem gastos
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="no-products-card">
                <div class="no-products-icon">
                    <i class="bi bi-box"></i>
                </div>
                <div class="no-products-content">
                    <h4>Nenhum produto cadastrado</h4>
                    <p>Comece adicionando seus primeiros produtos</p>
                    <a href="{{ route('admin.products.create') }}" class="premium-btn primary">
                        <i class="bi bi-plus-lg"></i>
                        Adicionar Produto
                    </a>
                </div>
            </div>
        @endif
    </div>
    
    <!-- Paginação -->
    @if($products && $products->count() > 0)
        <div class="mt-4">
            {{ $products->onEachSide(1)->links() }}
        </div>
    @endif
</div>


@endsection

@section('styles')
<style>
/* Estilos para cards de "sem dados" */
.no-data-card, .no-products-card {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    padding: 1.25rem;
    text-align: center;
    /* backdrop-filter removido para melhor performance */
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.75rem;
}

.no-data-icon, .no-products-icon {
    width: 48px;
    height: 48px;
    background: rgba(16, 185, 129, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #10b981;
    font-size: 1.5rem;
}

.no-data-content h4, .no-products-content h4 {
    color: white;
    font-size: 1rem;
    font-weight: 600;
    margin: 0 0 0.375rem 0;
}

.no-data-content p, .no-products-content p {
    color: rgba(255, 255, 255, 0.7);
    margin: 0 0 0.75rem 0;
    line-height: 1.5;
    font-size: 0.875rem;
}

.premium-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.4375rem;
    padding: 0.625rem 1.25rem;
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    text-decoration: none;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.875rem;
    transition: transform 0.2s ease, opacity 0.2s ease;
    border: none;
    cursor: pointer;
}

.premium-btn:hover {
    background: linear-gradient(135deg, #059669, #047857);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    color: white;
    text-decoration: none;
}

.premium-btn i {
    font-size: 1rem;
}

/* Grid de Produtos Recentes */
.recent-products-grid {
    display: grid;
    grid-template-columns: repeat(8, 1fr);
    gap: 1rem;
    margin-bottom: 2rem;
    width: 100%;
    box-sizing: border-box;
}

.recent-products-grid .premium-product-card {
    display: flex;
    flex-direction: column;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.05) 0%, rgba(255, 255, 255, 0.02) 100%);
    border-radius: 16px;
    border: 2px solid rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    transition: all 0.3s ease;
    padding: 0;
    overflow: hidden;
    cursor: pointer;
}

.recent-products-grid .premium-product-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
    border-color: rgba(16, 185, 129, 0.4);
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(5, 150, 105, 0.05) 100%);
}

.recent-products-grid .premium-product-image {
    position: relative;
    width: 100%;
    aspect-ratio: 1;
    min-height: 150px;
    flex-shrink: 0;
    border-radius: 0;
    overflow: hidden;
    background: rgba(255, 255, 255, 0.05);
}

.recent-products-grid .premium-product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.recent-products-grid .premium-product-card:hover .premium-product-image img {
    transform: scale(1.05);
}

.recent-products-grid .product-overlay {
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

.recent-products-grid .premium-product-card:hover .product-overlay {
    opacity: 1;
}

.recent-products-grid .product-overlay i {
    color: white;
    font-size: 1.5rem;
}

.recent-products-grid .premium-product-info {
    padding: 0.75rem;
    display: flex;
    flex-direction: column;
    gap: 0.375rem;
}

.recent-products-grid .premium-product-name {
    color: white;
    font-size: 0.875rem;
    font-weight: 600;
    margin: 0;
    line-height: 1.3;
    word-wrap: break-word;
    overflow-wrap: break-word;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.recent-products-grid .premium-product-category {
    color: rgba(255, 255, 255, 0.6);
    font-size: 0.75rem;
    margin: 0;
}

.recent-products-grid .premium-product-price {
    color: #10b981;
    font-size: 0.875rem;
    font-weight: 700;
    margin-top: 0.25rem;
}

.recent-products-grid .premium-product-price small {
    display: block;
    color: rgba(255, 255, 255, 0.5);
    font-size: 0.625rem;
    font-weight: 400;
    margin-top: 0.125rem;
}

/* Responsividade - Mobile: 4 produtos */
@media (max-width: 768px) {
    .recent-products-grid {
        grid-template-columns: repeat(4, 1fr);
        gap: 0.75rem;
    }
    
    .recent-products-grid .premium-product-image {
        min-height: 120px;
    }
    
    .recent-products-grid .premium-product-info {
        padding: 0.625rem;
    }
    
    .recent-products-grid .premium-product-name {
        font-size: 0.75rem;
    }
    
    .recent-products-grid .premium-product-category {
        font-size: 0.625rem;
    }
    
    .recent-products-grid .premium-product-price {
        font-size: 0.75rem;
    }
}

/* Responsividade - Tablets: 6 produtos */
@media (min-width: 769px) and (max-width: 1024px) {
    .recent-products-grid {
        grid-template-columns: repeat(6, 1fr);
    }
}

/* Paginação agora está no app.css otimizado para melhor performance */
</style>
@endsection

@section('scripts')
<script>
function viewProduct(productId) {
    window.location.href = `/products/${productId}`;
}

function searchProducts() {
    window.location.href = "{{ route('products.search') }}";
}

function openSettings() {
    alert('Configurações abertas!');
}

function openMenu() {
    alert('Menu de opções aberto!');
}
</script>
@endsection
