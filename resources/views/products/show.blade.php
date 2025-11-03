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
                    <img src="{{ $product->image ?: '/alimentos/steaak.jpg' }}" 
                         alt="{{ $product->name }}" 
                 class="product-hero-image">
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
                <div class="stat-value">R$ {{ number_format($product->total_spent ?? 520, 2, ',', '.') }}</div>
                <div class="stat-subtitle">Desde o início</div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="bi bi-graph-up"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Preço Médio</div>
                <div class="stat-value">R$ {{ number_format($product->average_price ?? 6.25, 2, ',', '.') }}/{{ $product->unit ?? 'L' }}</div>
                <div class="stat-subtitle">Últimos 30 dias</div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="bi bi-clock-history"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Último Preço</div>
                <div class="stat-value">R$ {{ number_format($product->last_price ?? 6.80, 2, ',', '.') }}</div>
                <div class="stat-subtitle">Compra recente</div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="bi bi-bag-check"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Compras</div>
                <div class="stat-value">{{ $product->purchase_count ?? 12 }}</div>
                <div class="stat-subtitle">Total de vezes</div>
            </div>
        </div>
    </div>
    
    <!-- Premium Chart Section -->
    <div class="chart-section">
        <div class="section-header">
            <h3 class="section-title">
                <i class="bi bi-graph-up-arrow"></i>
                Evolução de Preços
            </h3>
            <div class="chart-controls">
                <button class="chart-btn active" data-period="7d">7D</button>
                <button class="chart-btn" data-period="30d">30D</button>
                <button class="chart-btn" data-period="90d">90D</button>
            </div>
        </div>
        
        <div class="chart-container">
            <canvas id="priceChart"></canvas>
            </div>
            
        <div class="chart-stats">
            <div class="chart-stat">
                <span class="stat-label">Menor Preço</span>
                <span class="stat-value text-success">R$ 5,80</span>
            </div>
            <div class="chart-stat">
                <span class="stat-label">Maior Preço</span>
                <span class="stat-value text-danger">R$ 6,90</span>
            </div>
            <div class="chart-stat">
                <span class="stat-label">Tendência</span>
                <span class="stat-value text-warning">
                    <i class="bi bi-arrow-up"></i> +2.3%
                </span>
            </div>
        </div>
            </div>
            
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
                            <form action="{{ route('purchases.destroy', $purchase->id) }}" method="POST" class="d-inline" onsubmit="return confirmDeletePurchase()">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Excluir compra">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
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
    
    <!-- Premium Actions -->
    <div class="actions-section">
        <button class="premium-btn primary">
            <i class="bi bi-bell-fill"></i>
            <span>Criar Alerta de Preço</span>
        </button>
        <button class="premium-btn secondary">
            <i class="bi bi-plus-circle"></i>
            <span>Adicionar Compra</span>
        </button>
        <button class="premium-btn outline">
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

function openSettings() {
    alert('Configurações abertas!');
}

function openMenu() {
    alert('Menu de opções aberto!');
}

function confirmDeletePurchase() {
    return confirm('Tem certeza que deseja excluir esta compra? A transação também será removida do fluxo de caixa.');
}

// Premium Chart.js Configuration
const ctx = document.getElementById('priceChart').getContext('2d');
const priceChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['10/01', '10/03', '10/05', '10/08', '10/10', '10/12', '10/15'],
        datasets: [{
            label: 'Preço',
            data: [6.2, 6.0, 6.1, 5.8, 6.0, 5.9, 6.8],
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
