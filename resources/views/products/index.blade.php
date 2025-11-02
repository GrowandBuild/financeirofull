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
                <div class="spend-amount">R$ {{ number_format($totalMonthlySpend ?? 1250, 2, ',', '.') }}</div>
                <div class="spend-trend">
                    <i class="bi bi-arrow-down text-success"></i>
                    <span>5% abaixo da média</span>
                </div>
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
    
    <!-- All Products Section -->
    <div class="section-header">
        <h3 class="section-title">
            <i class="bi bi-grid-3x3-gap"></i>
            Todos os Produtos
        </h3>
        <button class="view-all-btn">
            <span>Ver todos</span>
            <i class="bi bi-arrow-right"></i>
        </button>
    </div>
    
    <!-- Premium Product Grid -->
    <div class="premium-product-grid">
        @if($products && $products->count() > 0)
            @foreach($products->take(6) as $product)
                <div class="premium-product-card" onclick="viewProduct({{ $product->id }})">
                    <div class="premium-product-image">
                        <img data-src="{{ $product->image ?: '/alimentos/steaak.jpg' }}" 
                             src="{{ asset('images/no-image.png') }}"
                             alt="{{ $product->name }}" 
                             class="img-fluid lazy"
                             loading="lazy">
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
</div>


@endsection

@section('styles')
<style>
/* Estilos para cards de "sem dados" */
.no-data-card, .no-products-card {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 16px;
    padding: 2rem;
    text-align: center;
    backdrop-filter: blur(10px);
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
}

.no-data-icon, .no-products-icon {
    width: 64px;
    height: 64px;
    background: rgba(16, 185, 129, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #10b981;
    font-size: 2rem;
}

.no-data-content h4, .no-products-content h4 {
    color: white;
    font-size: 1.25rem;
    font-weight: 600;
    margin: 0 0 0.5rem 0;
}

.no-data-content p, .no-products-content p {
    color: rgba(255, 255, 255, 0.7);
    margin: 0 0 1rem 0;
    line-height: 1.6;
}

.premium-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    text-decoration: none;
    border-radius: 12px;
    font-weight: 600;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}

.premium-btn:hover {
    background: linear-gradient(135deg, #059669, #047857);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(16, 185, 129, 0.3);
    color: white;
    text-decoration: none;
}

.premium-btn i {
    font-size: 1rem;
}
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
