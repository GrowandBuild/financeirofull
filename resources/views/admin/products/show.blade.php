@extends('layouts.app')

@section('title', 'Detalhes do Produto')

@section('content')
<div class="premium-content">
    <!-- Header com navegação -->
    <div class="premium-header">
        <div class="header-content">
            <div class="header-left">
                <a href="{{ route('admin.products.index') }}" class="back-btn">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div class="header-title">
                    <h1>{{ $product->name }}</h1>
                    <p class="header-subtitle">Detalhes e estatísticas do produto</p>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.products.edit', $product) }}" class="premium-btn primary">
                    <i class="bi bi-pencil"></i>
                    Editar
                </a>
                <a href="{{ route('admin.products.index') }}" class="premium-btn outline">
                    <i class="bi bi-list"></i>
                    Lista
                </a>
            </div>
        </div>
    </div>

    <!-- Conteúdo principal -->
    <div class="product-details-container">
        <!-- Informações básicas -->
        <div class="product-info-section">
            <div class="product-image-container">
                @if($product->hasImage())
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="product-main-image">
                @else
                    <div class="no-image-placeholder">
                        <i class="bi bi-image"></i>
                        <span>Sem imagem</span>
                    </div>
                @endif
            </div>
            
            <div class="product-basic-info">
                <div class="info-card">
                    <h3 class="card-title">Informações Básicas</h3>
                    <div class="info-grid">
                        <div class="info-item">
                            <label>Nome</label>
                            <span>{{ $product->name }}</span>
                        </div>
                        <div class="info-item">
                            <label>Categoria</label>
                            <span>{{ $product->category ?? 'Não definida' }}</span>
                        </div>
                        <div class="info-item">
                            <label>Unidade</label>
                            <span>{{ $product->unit }}</span>
                        </div>
                        <div class="info-item">
                            <label>Descrição</label>
                            <span>{{ $product->description ?? 'Sem descrição' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Variantes -->
        @if($product->has_variants && $product->variants)
        <div class="variants-section">
            <div class="section-header">
                <h3 class="section-title">
                    <i class="bi bi-collection"></i>
                    Variantes do Produto
                </h3>
                <span class="variant-count">{{ count($product->variants) }} variante(s)</span>
            </div>
            
            <div class="variants-grid">
                @foreach($product->variants as $index => $variant)
                <div class="variant-card">
                    <div class="variant-header">
                        <h4 class="variant-name">{{ $variant['name'] }}</h4>
                        <span class="variant-unit">{{ $variant['unit'] }}</span>
                    </div>
                    <div class="variant-details">
                        @if(isset($variant['price']) && $variant['price'] !== null && $variant['price'] !== '')
                            <div class="variant-price">
                                <span class="price-label">Preço:</span>
                                <span class="price-value">R$ {{ number_format($variant['price'], 2, ',', '.') }}</span>
                            </div>
                        @else
                            <div class="variant-price">
                                <span class="price-label">Preço:</span>
                                <span class="price-varies">Varia por mercado</span>
                            </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Estatísticas -->
        <div class="stats-section">
            <div class="section-header">
                <h3 class="section-title">
                    <i class="bi bi-graph-up"></i>
                    Estatísticas de Compra
                </h3>
            </div>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="bi bi-currency-dollar"></i>
                    </div>
                    <div class="stat-content">
                        <h4>Gasto Total</h4>
                        <span class="stat-value">R$ {{ number_format($product->total_spent, 2, ',', '.') }}</span>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="bi bi-cart-check"></i>
                    </div>
                    <div class="stat-content">
                        <h4>Compras Realizadas</h4>
                        <span class="stat-value">{{ $product->purchase_count }}</span>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                    <div class="stat-content">
                        <h4>Preço Médio</h4>
                        <span class="stat-value">R$ {{ number_format($product->average_price, 2, ',', '.') }}</span>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="bi bi-tag"></i>
                    </div>
                    <div class="stat-content">
                        <h4>Último Preço</h4>
                        <span class="stat-value">R$ {{ number_format($product->last_price, 2, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Histórico de compras recentes -->
        @if($purchases && $purchases->count() > 0)
        <div class="purchases-section">
            <div class="section-header">
                <h3 class="section-title">
                    <i class="bi bi-clock-history"></i>
                    Compras Recentes
                </h3>
                <span class="purchase-count">{{ $purchases->count() }} compra(s)</span>
            </div>
            
            <div class="purchases-list">
                @foreach($purchases->take(5) as $purchase)
                <div class="purchase-item">
                    <div class="purchase-date">
                        <i class="bi bi-calendar"></i>
                        {{ $purchase->purchase_date->format('d/m/Y') }}
                    </div>
                    <div class="purchase-details">
                        <span class="purchase-quantity">{{ $purchase->quantity }} {{ $product->unit }}</span>
                        <span class="purchase-price">R$ {{ number_format($purchase->price, 2, ',', '.') }}</span>
                    </div>
                    @if($purchase->store)
                    <div class="purchase-store">
                        <i class="bi bi-shop"></i>
                        {{ $purchase->store }}
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

<style>
/* Estilos para a página de visualização do produto */
.product-details-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.product-info-section {
    display: grid;
    grid-template-columns: minmax(250px, 350px) 1fr;
    gap: 2rem;
    align-items: start;
}

@media (max-width: 768px) {
    .product-info-section {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .product-image-container {
        max-width: 100%;
        margin: 0 auto;
    }
    
    .product-main-image,
    .no-image-placeholder {
        height: 250px;
    }
}

.product-image-container {
    position: relative;
    border-radius: 16px;
    overflow: hidden;
    background: rgba(255, 255, 255, 0.05);
    border: 2px solid rgba(255, 255, 255, 0.1);
}

.product-main-image {
    width: 100%;
    height: 300px;
    object-fit: cover;
    display: block;
}

.no-image-placeholder {
    width: 100%;
    height: 300px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: rgba(255, 255, 255, 0.5);
    background: rgba(255, 255, 255, 0.05);
}

.no-image-placeholder i {
    font-size: 3rem;
    margin-bottom: 1rem;
}

.info-card {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 16px;
    padding: 1.5rem;
    backdrop-filter: blur(10px);
}

.card-title {
    color: #10b981;
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

@media (max-width: 768px) {
    .info-grid {
        grid-template-columns: 1fr;
    }
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.info-item label {
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.875rem;
    font-weight: 500;
}

.info-item span {
    color: white;
    font-size: 1rem;
    font-weight: 500;
}

.variants-section, .stats-section, .purchases-section {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 16px;
    padding: 1.5rem;
    backdrop-filter: blur(10px);
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.section-title {
    color: #10b981;
    font-size: 1.25rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin: 0;
}

.variant-count, .purchase-count {
    background: rgba(16, 185, 129, 0.2);
    color: #10b981;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 500;
}

.variants-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1rem;
}

@media (max-width: 640px) {
    .variants-grid {
        grid-template-columns: 1fr;
    }
}

.variant-card {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    padding: 1rem;
    transition: all 0.3s ease;
}

.variant-card:hover {
    background: rgba(255, 255, 255, 0.08);
    transform: translateY(-2px);
}

.variant-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.75rem;
}

.variant-name {
    color: white;
    font-size: 1rem;
    font-weight: 600;
    margin: 0;
}

.variant-unit {
    background: rgba(16, 185, 129, 0.2);
    color: #10b981;
    padding: 0.125rem 0.5rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 500;
}

.variant-price {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.price-label {
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.875rem;
}

.price-value {
    color: #10b981;
    font-weight: 600;
}

.price-varies {
    color: rgba(255, 255, 255, 0.5);
    font-style: italic;
    font-size: 0.875rem;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.stat-card {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: all 0.3s ease;
}

.stat-card:hover {
    background: rgba(255, 255, 255, 0.08);
    transform: translateY(-2px);
}

.stat-icon {
    width: 48px;
    height: 48px;
    background: rgba(16, 185, 129, 0.2);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #10b981;
    font-size: 1.5rem;
}

.stat-content h4 {
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.875rem;
    font-weight: 500;
    margin: 0 0 0.25rem 0;
}

.stat-value {
    color: white;
    font-size: 1.25rem;
    font-weight: 600;
}

.purchases-list {
    display: grid;
    gap: 0.75rem;
}

.purchase-item {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    padding: 1rem;
    display: grid;
    grid-template-columns: auto 1fr auto;
    gap: 1rem;
    align-items: center;
    transition: all 0.3s ease;
}

@media (max-width: 640px) {
    .purchase-item {
        grid-template-columns: 1fr;
        gap: 0.75rem;
    }
    
    .purchase-details {
        align-items: flex-start !important;
    }
}

.purchase-item:hover {
    background: rgba(255, 255, 255, 0.08);
}

.purchase-date {
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.875rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.purchase-details {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 0.25rem;
}

.purchase-quantity {
    color: white;
    font-size: 0.875rem;
    font-weight: 500;
}

.purchase-price {
    color: #10b981;
    font-weight: 600;
}

.purchase-store {
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.875rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

/* Responsividade adicional */
@media (max-width: 480px) {
    .product-details-container {
        padding: 1rem;
        gap: 1.5rem;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .section-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
}
</style>
@endsection
@endsection