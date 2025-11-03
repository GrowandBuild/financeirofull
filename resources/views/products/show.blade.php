@extends('layouts.app')

@section('title', 'Histórico do Produto')

@section('content')
<!-- Premium Header -->
<div class="premium-header">
    <div class="header-content">
        <div class="header-left">
            <button class="back-btn" onclick="goBack()">
                <i class="bi bi-arrow-left"></i>
            </button>
            <div class="header-title">
                <h1>{{ $product->name }}</h1>
                <span class="product-category">{{ $product->category ?? 'Sem categoria' }}</span>
            </div>
        </div>
        <div class="header-actions">
            <button class="action-btn" onclick="searchProducts()">
            <i class="bi bi-search"></i>
        </button>
            <button class="action-btn" onclick="openSettings()">
                <i class="bi bi-bell"></i>
        </button>
            <button class="action-btn" onclick="openMenu()">
            <i class="bi bi-three-dots-vertical"></i>
        </button>
        </div>
    </div>
</div>

<!-- Premium Content -->
<div class="premium-content">
    <!-- Product Hero Section -->
    <div class="product-hero">
        <div class="product-image-container">
                    <img src="{{ $product->image_url ?? asset('images/no-image.png') }}" 
                         alt="{{ $product->name }}" 
                         class="product-hero-image"
                         onerror="this.onerror=null; this.src='{{ asset('images/no-image.png') }}';">
            <div class="product-badge">
                <i class="bi bi-star-fill"></i>
                <span>Premium</span>
                </div>
                </div>
        <div class="product-info">
            <h2 class="product-title">{{ $product->name }}</h2>
            <p class="product-description">{{ $product->description ?? 'Produto de qualidade premium' }}</p>
            <div class="product-unit">
                <i class="bi bi-tag"></i>
                <span>Unidade: {{ $product->unit ?? 'kg' }}</span>
            </div>
        </div>
    </div>
    
    <!-- Premium Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="bi bi-wallet2"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Gasto Total</div>
                <div class="stat-value">R$ {{ number_format($product->total_spent ?? 0, 2, ',', '.') }}</div>
                <div class="stat-subtitle">Desde o início</div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="bi bi-graph-up"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Preço Médio</div>
                <div class="stat-value">R$ {{ number_format($product->average_price ?? 0, 2, ',', '.') }}/{{ $product->unit ?? 'L' }}</div>
                <div class="stat-subtitle">Últimos 30 dias</div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="bi bi-clock-history"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Último Preço</div>
                <div class="stat-value">R$ {{ number_format($product->last_price ?? 0, 2, ',', '.') }}</div>
                <div class="stat-subtitle">Compra recente</div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="bi bi-bag-check"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Compras</div>
                <div class="stat-value">{{ $product->purchase_count ?? 0 }}</div>
                <div class="stat-subtitle">Total de vezes</div>
            </div>
        </div>
    </div>
    
    <!-- Content Grid for Larger Screens -->
    <div class="content-grid">
        <!-- Premium Chart Section -->
        @if($hasPurchases)
        <div class="chart-section">
            <div class="section-header">
                <h3 class="section-title">
                    <i class="bi bi-graph-up-arrow"></i>
                    Evolução de Preços
                </h3>
            </div>
            
            <div class="chart-container">
                <canvas id="priceChart"></canvas>
            </div>
                
            <div class="chart-stats">
                <div class="chart-stat">
                    <span class="stat-label">Menor Preço</span>
                    <span class="stat-value text-success">R$ {{ number_format($priceStats['min_price'], 2, ',', '.') }}</span>
                </div>
                <div class="chart-stat">
                    <span class="stat-label">Maior Preço</span>
                    <span class="stat-value text-danger">R$ {{ number_format($priceStats['max_price'], 2, ',', '.') }}</span>
                </div>
                <div class="chart-stat">
                    <span class="stat-label">Tendência</span>
                    <span class="stat-value {{ $priceStats['trend'] === 'up' ? 'text-warning' : ($priceStats['trend'] === 'down' ? 'text-info' : 'text-muted') }}">
                        @if($priceStats['trend'] === 'up')
                            <i class="bi bi-arrow-up"></i> +{{ number_format($priceStats['trend_percent'], 1) }}%
                        @elseif($priceStats['trend'] === 'down')
                            <i class="bi bi-arrow-down"></i> -{{ number_format($priceStats['trend_percent'], 1) }}%
                        @else
                            <i class="bi bi-dash-lg"></i> Estável
                        @endif
                    </span>
                </div>
            </div>
        </div>
        @endif
                
        <!-- Premium Purchase History -->
        <div class="history-section">
        <div class="section-header">
            <h3 class="section-title">
                <i class="bi bi-clock-history"></i>
                Histórico de Compras
            </h3>
            <button class="filter-btn">
                <i class="bi bi-funnel"></i>
                Filtrar
            </button>
        </div>
        
        <div class="purchase-list">
            @if($product->purchases && $product->purchases->count() > 0)
                @foreach($product->purchases->take(5) as $purchase)
                    <div class="purchase-item">
                        <div class="purchase-icon">
                            <i class="bi bi-bag-check"></i>
                        </div>
                        <div class="purchase-details">
                            <div class="purchase-store">{{ $purchase->store ?? 'Loja não informada' }}</div>
                            <div class="purchase-date">{{ $purchase->purchase_date ? $purchase->purchase_date->format('d/m/Y') : 'Data não informada' }}</div>
                        </div>
                        <div class="purchase-quantity">
                            {{ number_format($purchase->quantity ?? 1, 1, ',', '.') }} {{ $product->unit ?? 'L' }}
                        </div>
                        <div class="purchase-price">
                            <div class="price-value">R$ {{ number_format($purchase->price ?? 0, 2, ',', '.') }}</div>
                            <div class="total-value">Total: R$ {{ number_format($purchase->total_value ?? 0, 2, ',', '.') }}</div>
                        </div>
                        <div class="purchase-actions">
                            <button type="button" class="btn btn-sm btn-danger" title="Excluir compra" onclick="deletePurchase({{ $purchase->id }})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="empty-state">
                    <i class="bi bi-bag-x"></i>
                    <p>Nenhuma compra registrada</p>
                </div>
            @endif
        </div>
        </div>
    </div>
    
    <!-- Premium Actions -->
    <div class="actions-section">
        <button class="premium-btn primary" onclick="createPriceAlert({{ $product->id }})">
            <i class="bi bi-bell-fill"></i>
            <span>Criar Alerta de Preço</span>
        </button>
        <a href="{{ route('products.compra') }}?product_id={{ $product->id }}" class="premium-btn secondary">
            <i class="bi bi-plus-circle"></i>
            <span>Adicionar Compra</span>
        </a>
        <button class="premium-btn outline" onclick="shareProduct()">
            <i class="bi bi-share"></i>
            <span>Compartilhar</span>
        </button>
    </div>
    
</div>

@endsection

@section('scripts')
<script>
function goBack() {
    window.history.back();
}

function searchProducts() {
    window.location.href = "{{ route('products.search') }}";
}

async function openSettings() {
    if (typeof window.showInfo === 'function') {
        window.showInfo('Configurações em desenvolvimento');
    } else {
        console.log('Configurações');
    }
}

async function openMenu() {
    if (typeof window.showInfo === 'function') {
        window.showInfo('Menu de opções em desenvolvimento');
    } else {
        console.log('Menu de opções');
    }
}

async function deletePurchase(purchaseId) {
    if (typeof window.confirmAction === 'function') {
        const confirmed = await window.confirmAction(
            'Tem certeza que deseja excluir esta compra? A transação também será removida do fluxo de caixa.',
            'Confirmar Exclusão'
        );
        if (confirmed) {
            const token = document.querySelector('meta[name="csrf-token"]').content;
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/purchases/${purchaseId}`;
            form.innerHTML = `
                <input type="hidden" name="_token" value="${token}">
                <input type="hidden" name="_method" value="DELETE">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    }
}

async function createPriceAlert(productId) {
    if (typeof window.confirmAction === 'function') {
        const confirmed = await window.confirmAction(
            'Funcionalidade de alerta de preço em desenvolvimento. Deseja continuar?',
            'Alerta de Preço'
        );
        if (confirmed && typeof window.showInfo === 'function') {
            window.showInfo('Funcionalidade será implementada em breve');
        }
    }
}

async function shareProduct() {
    if (navigator.share) {
        try {
            await navigator.share({
                title: '{{ $product->name }}',
                text: 'Confira este produto: {{ $product->name }}',
                url: window.location.href
            });
        } catch (err) {
            if (err.name !== 'AbortError') {
                console.log('Erro ao compartilhar:', err);
            }
        }
    } else if (navigator.clipboard) {
        try {
            await navigator.clipboard.writeText(window.location.href);
            if (typeof window.showSuccess === 'function') {
                window.showSuccess('Link copiado para a área de transferência!');
            }
        } catch (err) {
            console.error('Erro ao copiar:', err);
        }
    }
}

// Premium Chart.js Configuration
@if($hasPurchases && !empty($priceStats['chart_data']))
const ctx = document.getElementById('priceChart');
if (ctx) {
    const priceChart = new Chart(ctx.getContext('2d'), {
        type: 'line',
        data: {
            labels: @json($priceStats['chart_data']['labels']),
            datasets: [{
                label: 'Preço',
                data: @json($priceStats['chart_data']['prices']),
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                borderWidth: 3,
                pointRadius: 6,
                pointBackgroundColor: '#10b981',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    grid: {
                        color: 'rgba(107, 114, 128, 0.3)',
                        borderColor: 'rgba(107, 114, 128, 0.5)'
                    },
                    ticks: {
                        color: '#9ca3af',
                        font: {
                            size: 11,
                            weight: '500'
                        }
                    }
                },
                y: {
                    grid: {
                        color: 'rgba(107, 114, 128, 0.3)',
                        borderColor: 'rgba(107, 114, 128, 0.5)'
                    },
                    ticks: {
                        color: '#9ca3af',
                        font: {
                            size: 11,
                            weight: '500'
                        }
                    }
                }
            },
            elements: {
                point: {
                    hoverRadius: 8
                }
            }
        }
    });
}
@endif

// Chart period controls
document.querySelectorAll('.chart-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.chart-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        // Here you would update the chart data based on the selected period
    });
});
</script>
@endsection

@section('styles')
<style>
/* ============================================
   ESTILOS BASE - NOTEBOOKS/DESKTOPS (PADRÃO)
   ============================================ */

/* Container principal - layout desktop por padrão */
.premium-content {
    max-width: 1600px !important;
    width: 100% !important;
    margin: 0 auto !important;
    padding: 2rem !important;
    padding-bottom: 2rem !important;
    padding-top: 1.5rem !important;
    box-sizing: border-box !important;
}

/* Header premium */
.premium-header {
    position: relative;
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.header-actions {
    display: flex;
    gap: 0.5rem;
}

/* Content Grid - layout em 2 colunas para telas maiores */
.content-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

/* Grid de Estatísticas - 4 colunas para desktop */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
    margin-bottom: 1.5rem;
}

/* Ações */
.actions-section {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
    margin-top: 1.5rem;
    max-width: 1000px;
    margin-left: auto;
    margin-right: auto;
}

.purchase-list {
    display: grid;
    gap: 0.75rem;
}

.purchase-item {
    display: grid;
    grid-template-columns: auto 1fr auto auto auto;
    gap: 1rem;
    align-items: center;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    padding: 1rem;
    transition: all 0.3s ease;
}

.purchase-item:hover {
    background: rgba(255, 255, 255, 0.08);
    transform: translateY(-2px);
}

/* Product Hero - desktop layout */
.product-hero {
    display: grid;
    grid-template-columns: 280px 1fr;
    gap: 2rem;
    align-items: center;
    margin-bottom: 1.5rem;
    background: rgba(255, 255, 255, 0.03);
    border-radius: 16px;
    padding: 1.5rem;
}

.product-image-container {
    position: relative;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}

.product-hero-image {
    width: 100%;
    height: 280px;
    object-fit: cover;
    display: block;
}

.product-info {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.product-title {
    font-size: 2rem;
    font-weight: 700;
    margin: 0;
    color: #fff;
}

.product-description {
    color: rgba(255, 255, 255, 0.7);
    margin: 0;
    line-height: 1.6;
    font-size: 1rem;
}

.product-unit {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: rgba(255, 255, 255, 0.6);
    font-size: 0.95rem;
}

/* Seções */
.history-section,
.chart-section {
    background: rgba(255, 255, 255, 0.03);
    border-radius: 16px;
    padding: 1.5rem;
    height: fit-content;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.section-title {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 1.25rem;
    font-weight: 600;
    color: #fff;
    margin: 0;
}

.chart-container {
    position: relative;
    height: 320px;
    margin-bottom: 1rem;
}

.chart-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0.75rem;
}

.chart-stat {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
    text-align: center;
    font-size: 0.9rem;
}

.chart-stat .stat-label {
    font-size: 0.8rem;
    color: rgba(255, 255, 255, 0.6);
}

.chart-stat .stat-value {
    font-size: 1rem;
    font-weight: 600;
}

/* Cards de Estatísticas */
.stat-card {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    padding: 1.25rem;
    transition: all 0.3s ease;
}

.stat-card:hover {
    background: rgba(255, 255, 255, 0.08);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.stat-value {
    font-size: 1.75rem;
    font-weight: 700;
    color: #10b981;
}

.stat-label {
    font-size: 0.875rem;
    color: rgba(255, 255, 255, 0.7);
    margin-bottom: 0.5rem;
}

.stat-subtitle {
    font-size: 0.75rem;
    color: rgba(255, 255, 255, 0.5);
    margin-top: 0.25rem;
}

/* Botões de ação */
.actions-section .premium-btn {
    width: 100%;
    justify-content: center;
    padding: 1rem 2rem;
    font-size: 1.05rem;
}

/* ============================================
   MEDIA QUERIES - NOTEBOOKS PEQUENOS (1025px - 1200px)
   ============================================ */
@media (max-width: 1200px) and (min-width: 1025px) {
    .premium-content {
        padding: 1.5rem !important;
        padding-bottom: 2rem !important;
        max-width: 1400px !important;
    }
    
    .content-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .stats-grid {
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
    }
    
    .product-hero {
        grid-template-columns: 240px 1fr;
        gap: 1.5rem;
        padding: 1.5rem;
    }
    
    .product-hero-image {
        height: 240px;
    }
    
    .product-title {
        font-size: 1.875rem;
    }
    
    .actions-section {
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
    }
    
    .chart-container {
        height: 300px;
    }
}

/* ============================================
   MEDIA QUERIES - TABLETS (768px - 1024px)
   ============================================ */
@media (max-width: 1024px) and (min-width: 769px) {
    .premium-content {
        padding: 1.5rem !important;
        padding-bottom: 80px !important;
        max-width: 100% !important;
    }
    
    .content-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 0.75rem;
    }
    
    .product-hero {
        grid-template-columns: 200px 1fr;
        gap: 1.5rem;
        padding: 1.25rem;
    }
    
    .product-hero-image {
        height: 200px;
    }
    
    .product-title {
        font-size: 1.75rem;
    }
    
    .actions-section {
        grid-template-columns: repeat(3, 1fr);
        gap: 0.75rem;
    }
    
    .chart-container {
        height: 280px;
    }
}

/* ============================================
   MEDIA QUERIES - MOBILE (< 768px)
   ============================================ */
@media (max-width: 768px) {
    .premium-content {
        padding: 0.75rem !important;
        padding-bottom: 80px !important;
        max-width: 100% !important;
    }
    
    .content-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 0.75rem;
    }
    
    .actions-section {
        grid-template-columns: 1fr;
        gap: 0.75rem;
    }
    
    .purchase-item {
        grid-template-columns: auto 1fr;
        grid-template-rows: auto auto auto;
        gap: 0.75rem;
    }
    
    .purchase-details {
        grid-column: 1 / -1;
    }
    
    .purchase-quantity {
        grid-column: 1;
        grid-row: 2;
    }
    
    .purchase-price {
        grid-column: 2;
        grid-row: 2;
    }
    
    .purchase-actions {
        grid-column: 1 / -1;
        grid-row: 3;
        justify-self: end;
    }
    
    .product-hero {
        grid-template-columns: 1fr;
        gap: 1rem;
        text-align: center;
        padding: 1rem;
    }
    
    .product-image-container {
        max-width: 200px;
        margin: 0 auto;
    }
    
    .product-hero-image {
        height: 200px;
    }
    
    .product-title {
        font-size: 1.5rem;
    }
    
    .history-section,
    .chart-section {
        padding: 1rem;
    }
    
    .section-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
    
    .chart-container {
        height: 250px;
    }
    
    .stat-card {
        padding: 1rem;
    }
    
    .stat-value {
        font-size: 1.5rem;
    }
    
    .actions-section .premium-btn {
        padding: 0.875rem 1.5rem;
        font-size: 1rem;
    }
}

/* ============================================
   MEDIA QUERIES - MOBILE PEQUENO (< 480px)
   ============================================ */
@media (max-width: 480px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .purchase-item {
        grid-template-columns: 1fr;
    }
    
    .purchase-quantity,
    .purchase-price {
        grid-column: 1 / -1;
        grid-row: auto;
    }
    
    .chart-container {
        height: 200px;
    }
    
    .stat-value {
        font-size: 1.25rem;
    }
}
</style>
@endsection
