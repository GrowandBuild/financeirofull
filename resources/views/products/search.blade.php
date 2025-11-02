@extends('layouts.app')

@section('title', 'Buscar Produtos')

@section('content')
<!-- Premium Header -->
<div class="premium-header">
    <div class="header-content">
        <div class="header-left">
            <button class="back-btn" onclick="goBack()">
                <i class="bi bi-arrow-left"></i>
            </button>
            <div class="header-title">
                <h1>Buscar Produtos</h1>
                <span class="header-subtitle">Encontre o que precisa</span>
            </div>
        </div>
        <div class="header-actions">
            <button class="action-btn" onclick="clearSearch()">
                <i class="bi bi-x-circle"></i>
            </button>
        </div>
    </div>
</div>

<!-- Premium Content -->
<div class="premium-content">
    <!-- Search Form -->
    <div class="search-form-container">
        <form id="searchForm" method="GET" action="{{ route('products.search') }}">
            <div class="search-input-group">
                <div class="search-icon">
                    <i class="bi bi-search"></i>
                </div>
                <input type="text" 
                       class="premium-search-input" 
                       id="searchInput" 
                       name="q" 
                       value="{{ $query }}"
                       placeholder="Digite o nome do produto..."
                       autocomplete="off">
                @if($query)
                    <button type="button" class="clear-input-btn" onclick="clearInput()">
                        <i class="bi bi-x"></i>
                    </button>
                @endif
            </div>
            
            <div class="filter-section">
                <div class="filter-header">
                    <i class="bi bi-funnel"></i>
                    <span>Filtros</span>
                </div>
                <select class="premium-select" id="categoryFilter" name="category">
                    <option value="">Todas as categorias</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ $category === $cat ? 'selected' : '' }}>
                            {{ $cat }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    <!-- Search Results -->
    @if($query || $category)
        <div class="results-header">
            <div class="results-info">
                <h3 class="results-title">
                    @if($products->count() > 0)
                        <i class="bi bi-check-circle text-success"></i>
                        {{ $products->count() }} produto(s) encontrado(s)
                    @else
                        <i class="bi bi-exclamation-circle text-warning"></i>
                        Nenhum produto encontrado
                    @endif
                </h3>
                @if($query)
                    <div class="search-term">
                        Busca por: <span class="highlight">"{{ $query }}"</span>
                    </div>
                @endif
                @if($category)
                    <div class="filter-term">
                        Categoria: <span class="highlight">{{ $category }}</span>
                    </div>
                @endif
            </div>
            <button onclick="clearSearch()" class="clear-search-btn">
                <i class="bi bi-x"></i>
                <span>Limpar</span>
            </button>
        </div>
    @endif

    @if($products->count() > 0)
        <!-- Premium Product Grid -->
        <div class="premium-product-grid search-grid">
            @foreach($products as $product)
                <div class="premium-product-card" onclick="viewProduct({{ $product->id }})">
                    <div class="premium-product-image">
                        <img src="{{ $product->image ?: '/alimentos/steaak.jpg' }}" 
                             alt="{{ $product->name }}" 
                             class="img-fluid">
                        <div class="product-overlay">
                            <i class="bi bi-eye"></i>
                        </div>
                        @if($product->category)
                            <div class="category-badge">{{ $product->category }}</div>
                        @endif
                    </div>
                    <div class="premium-product-info">
                        <h5 class="premium-product-name">{{ $product->name }}</h5>
                        <div class="premium-product-price">
                            @if($product->monthly_spend > 0)
                                R$ {{ number_format($product->monthly_spend, 2, ',', '.') }}
                                <small class="text-white/60 text-xs block">Total do mês</small>
                            @else
                                Sem gastos
                            @endif
                        </div>
                        <div class="product-stats">
                            <div class="stat-item">
                                <i class="bi bi-bag-check"></i>
                                <span>{{ $product->purchase_count ?? 0 }} compras</span>
                            </div>
                            <div class="stat-item">
                                <i class="bi bi-graph-up"></i>
                                <span>R$ {{ number_format($product->total_spent ?? 0, 2, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- Premium Empty State -->
        <div class="premium-empty-state">
            <div class="empty-icon">
                <i class="bi bi-search"></i>
            </div>
            <h4 class="empty-title">Nenhum produto encontrado</h4>
            <p class="empty-description">
                @if($query)
                    Não encontramos produtos com "{{ $query }}"
                @elseif($category)
                    Não encontramos produtos na categoria "{{ $category }}"
                @else
                    Digite um termo para buscar produtos
                @endif
            </p>
            <div class="empty-actions">
                <button onclick="clearSearch()" class="premium-btn outline">
                    <i class="bi bi-arrow-clockwise"></i>
                    <span>Tentar novamente</span>
                </button>
                <button onclick="document.getElementById('searchInput').focus()" class="premium-btn secondary">
                    <i class="bi bi-search"></i>
                    <span>Nova busca</span>
                </button>
            </div>
        </div>
    @endif
</div>

@endsection

@section('scripts')
<script>
function goBack() {
    window.history.back();
}

function clearSearch() {
    document.getElementById('searchInput').value = '';
    document.getElementById('categoryFilter').value = '';
    document.getElementById('searchForm').submit();
}

function clearInput() {
    document.getElementById('searchInput').value = '';
    document.getElementById('searchForm').submit();
}

function viewProduct(productId) {
    window.location.href = `/products/${productId}`;
}

// Auto-submit form when category changes
document.getElementById('categoryFilter').addEventListener('change', function() {
    document.getElementById('searchForm').submit();
});

// Focus on search input when page loads
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput && !searchInput.value) {
        searchInput.focus();
    }
});

// Real-time search with debounce
let searchTimeout;
document.getElementById('searchInput').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        if (this.value.length >= 2 || this.value.length === 0) {
            document.getElementById('searchForm').submit();
        }
    }, 500);
});

// Add loading state during search
document.getElementById('searchForm').addEventListener('submit', function() {
    const searchInput = document.getElementById('searchInput');
    searchInput.style.opacity = '0.7';
    searchInput.disabled = true;
    
    setTimeout(() => {
        searchInput.style.opacity = '1';
        searchInput.disabled = false;
    }, 1000);
});
</script>
@endsection
