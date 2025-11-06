@extends('layouts.app')

@section('content')
<div class="premium-content">
    <div class="max-w-6xl mx-auto px-4" style="max-width: 100%; overflow-x: hidden; box-sizing: border-box;">
        <!-- Header Premium -->
        <div class="premium-header mb-8">
            <div class="header-content">
                <div class="header-left">
                    <a href="{{ route('admin.product-categories.index') }}" class="back-btn">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <div class="header-title">
                        <h1>{{ $productCategory->name }}</h1>
                        <p class="header-subtitle">Detalhes e estatísticas da categoria</p>
                    </div>
                </div>
                <div class="header-actions">
                    <a href="{{ route('admin.product-categories.edit', $productCategory) }}" class="action-btn" title="Editar Categoria">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <a href="{{ route('admin.product-categories.index') }}" class="action-btn" title="Lista de Categorias">
                        <i class="bi bi-list-ul"></i>
                    </a>
                    <a href="{{ route('dashboard') }}" class="action-btn" title="Painel Principal">
                        <i class="bi bi-speedometer2"></i>
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
                <div class="stat-label">Produtos</div>
                <div class="stat-value">{{ $products->count() }}</div>
                <div class="stat-subtitle">Nesta categoria</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="bi bi-currency-dollar"></i>
                </div>
                <div class="stat-label">Gasto Total</div>
                <div class="stat-value">R$ {{ number_format($totalSpent, 2, ',', '.') }}</div>
                <div class="stat-subtitle">Todas as compras</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="bi bi-calendar-month"></i>
                </div>
                <div class="stat-label">Este Mês</div>
                <div class="stat-value">R$ {{ number_format($monthlySpent, 2, ',', '.') }}</div>
                <div class="stat-subtitle">Gastos do mês atual</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="bi bi-graph-up"></i>
                </div>
                <div class="stat-label">Média Mensal</div>
                <div class="stat-value">R$ {{ number_format($monthlyStats->avg('total') ?? 0, 2, ',', '.') }}</div>
                <div class="stat-subtitle">Últimos 12 meses</div>
            </div>
        </div>

        <!-- Gastos por Mês -->
        @if($monthlyStats->count() > 0)
        <div class="chart-section mb-8">
            <h3 class="section-title">
                <i class="bi bi-bar-chart"></i>
                Gastos por Mês (Últimos 12 Meses)
            </h3>
            <div class="overflow-x-auto">
                <table class="w-full text-white">
                    <thead>
                        <tr class="border-b border-white/20">
                            <th class="text-left py-3 px-4">Mês</th>
                            <th class="text-right py-3 px-4">Gasto</th>
                            <th class="text-center py-3 px-4">% do Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($monthlyStats as $stat)
                        @php
                            $monthName = \Carbon\Carbon::create($stat->year, $stat->month, 1)->locale('pt_BR')->translatedFormat('F Y');
                            $percentage = $totalSpent > 0 ? ($stat->total / $totalSpent) * 100 : 0;
                        @endphp
                        <tr class="border-b border-white/10 hover:bg-white/5 transition-colors">
                            <td class="py-3 px-4">{{ ucfirst($monthName) }}</td>
                            <td class="text-right py-3 px-4 font-semibold text-emerald-400">
                                R$ {{ number_format($stat->total, 2, ',', '.') }}
                            </td>
                            <td class="text-center py-3 px-4">
                                <div class="flex items-center justify-center gap-2">
                                    <div class="flex-1 bg-white/10 rounded-full h-2 overflow-hidden">
                                        <div class="bg-emerald-500 h-full" style="width: {{ min($percentage, 100) }}%"></div>
                                    </div>
                                    <span class="text-sm text-white/70">{{ number_format($percentage, 1) }}%</span>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- Top Produtos -->
        @if($topProducts->count() > 0)
        <div class="chart-section mb-8">
            <h3 class="section-title">
                <i class="bi bi-star-fill"></i>
                Produtos Mais Gastos Nesta Categoria
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($topProducts->take(9) as $product)
                <div class="bg-white/5 rounded-lg p-4 border border-white/10 hover:border-emerald-500/50 transition-colors">
                    <div class="flex items-start justify-between mb-2">
                        <h4 class="text-white font-semibold">{{ $product->name }}</h4>
                        <span class="text-emerald-400 font-bold">
                            R$ {{ number_format($product->total_spent, 2, ',', '.') }}
                        </span>
                    </div>
                    <div class="flex items-center gap-4 text-sm text-white/60">
                        <span><i class="bi bi-cart-check"></i> {{ $product->purchase_count }} compras</span>
                        @if($product->last_price > 0)
                            <span><i class="bi bi-currency-dollar"></i> R$ {{ number_format($product->last_price, 2, ',', '.') }}</span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Produtos da Categoria -->
        <div class="chart-section">
            <h3 class="section-title">
                <i class="bi bi-grid-3x3-gap"></i>
                Produtos Nesta Categoria ({{ $products->count() }})
            </h3>
            <div class="premium-product-grid">
                @forelse($products as $product)
                <div class="premium-product-card">
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
                    </div>
                    
                    <div class="premium-product-info">
                        <h5 class="premium-product-name">{{ $product->name }}</h5>
                        
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
                                    <i class="bi bi-currency-dollar"></i>
                                    R$ {{ number_format($product->total_spent, 2, ',', '.') }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                @empty
                <div class="col-span-full text-center py-8 text-white/60">
                    <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                    <p class="mt-4">Nenhum produto nesta categoria ainda</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<style>
.chart-section {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.08) 0%, rgba(255, 255, 255, 0.04) 100%);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 16px;
    padding: 2rem;
    backdrop-filter: blur(20px);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
}

.section-title {
    color: white;
    font-size: 1.25rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.section-title i {
    color: #10b981;
    font-size: 1.5rem;
}

table {
    min-width: 100%;
}

table th {
    font-weight: 600;
    color: rgba(255, 255, 255, 0.9);
}

table td {
    color: rgba(255, 255, 255, 0.8);
}
</style>
@endsection

