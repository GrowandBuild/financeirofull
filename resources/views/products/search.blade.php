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
            <!-- Input de Busca Integrado -->
            <div class="modern-search-container">
                <div class="search-input-wrapper">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" 
                           class="modern-search-input" 
                           id="searchInput" 
                           name="q" 
                           value="{{ $query ?? '' }}"
                           placeholder="Digite o nome do produto..."
                           autocomplete="off">
                    @if(!empty($query))
                        <button type="button" class="clear-input-btn" onclick="clearInput()">
                            <i class="bi bi-x"></i>
                        </button>
                    @endif
                    <button type="submit" class="search-submit-btn">
                        <i class="bi bi-search"></i>
                        <span>Buscar</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Search Results -->
    @php
        $hasQuery = !empty($query ?? '');
        $hasCategory = !empty($category ?? '');
        $hasProducts = isset($products) && $products->count() > 0;
    @endphp

    @if($hasQuery || $hasCategory)
        <div class="results-header">
            <div class="results-info">
                <h3 class="results-title">
                    @if($hasProducts)
                        <i class="bi bi-check-circle text-success"></i>
                        {{ $products->count() }} produto(s) encontrado(s)
                    @else
                        <i class="bi bi-exclamation-circle text-warning"></i>
                        Nenhum produto encontrado
                    @endif
                </h3>
                @if($hasQuery)
                    <div class="search-term">
                        Busca por: <span class="highlight">"{{ $query }}"</span>
                    </div>
                @endif
                @if($hasCategory)
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

    @if($hasProducts)
        <!-- Premium Product Grid -->
        <div class="premium-product-grid search-grid">
            @foreach($products as $product)
                <div class="premium-product-card" onclick="viewProduct({{ $product->id }})">
                    <div class="premium-product-image">
                        @if($product->image_url && !str_contains($product->image_url, 'no-image'))
                            <img src="{{ $product->image_url }}" 
                                 alt="{{ $product->name }}" 
                                 class="img-fluid"
                                 loading="lazy"
                                 onerror="this.onerror=null; this.src='{{ asset('images/no-image.png') }}'; this.classList.add('no-image-fallback');">
                        @else
                            <img src="{{ asset('images/no-image.png') }}" 
                                 alt="{{ $product->name }}" 
                                 class="img-fluid no-image-fallback">
                        @endif
                        <div class="product-overlay">
                            <i class="bi bi-eye"></i>
                        </div>
                    </div>
                    <div class="premium-product-info">
                        <h5 class="premium-product-name">{{ $product->name }}</h5>
                        @if($product->category)
                            <div class="premium-product-category">{{ $product->category }}</div>
                        @endif
                        <div class="premium-product-price">
                            @if($product->monthly_spend > 0)
                                R$ {{ number_format($product->monthly_spend, 2, ',', '.') }}
                                <small style="display: block; color: rgba(255,255,255,0.5); font-size: 0.625rem; margin-top: 0.125rem;">Total do mês</small>
                            @else
                                <span style="color: rgba(255,255,255,0.5); font-size: 0.6875rem;">Sem gastos</span>
                            @endif
                        </div>
                        <div class="product-stats">
                            <div class="stat-item">
                                <i class="bi bi-bag-check"></i>
                                <span>{{ $product->purchases_count ?? 0 }} compras</span>
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
            <h4 class="empty-title">
                @if($hasQuery || $hasCategory)
                    Nenhum produto encontrado
                @else
                    Comece sua busca
                @endif
            </h4>
            <p class="empty-description">
                @if($hasQuery)
                    Não encontramos produtos com "{{ $query }}".<br>
                    Tente usar termos diferentes ou verifique a ortografia.
                @elseif($hasCategory)
                    Não encontramos produtos na categoria "{{ $category }}".
                @else
                    Digite o nome do produto que deseja encontrar
                @endif
            </p>
            <div class="empty-actions">
                @if($hasQuery || $hasCategory)
                    <button onclick="clearSearch()" class="premium-btn outline">
                        <i class="bi bi-arrow-counterclockwise"></i>
                        <span>Limpar busca</span>
                    </button>
                    <button onclick="document.getElementById('searchInput').focus()" class="premium-btn secondary">
                        <i class="bi bi-search"></i>
                        <span>Nova busca</span>
                    </button>
                @else
                    <button onclick="document.getElementById('searchInput').focus()" class="premium-btn secondary">
                        <i class="bi bi-search"></i>
                        <span>Buscar produtos</span>
                    </button>
                @endif
            </div>
        </div>
    @endif
</div>

@endsection

@section('styles')
<style>
/* ============================================
   CONTAINER DE BUSCA MODERNO
   ============================================ */

.search-form-container {
    position: relative;
    z-index: 1;
    margin-bottom: 1rem;
}

.modern-search-container {
    width: 100%;
}

.search-input-wrapper {
    display: flex;
    align-items: center;
    gap: 0.5625rem;
    background: rgba(255, 255, 255, 0.08);
    border: 2px solid rgba(16, 185, 129, 0.3);
    border-radius: 12px;
    padding: 0.75rem 0.875rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    backdrop-filter: blur(10px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

/* ============================================
   CABEÇALHO DE RESULTADOS
   ============================================ */

.results-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding: 1.25rem;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 16px;
    backdrop-filter: blur(10px);
}

.results-info {
    flex: 1;
}

.results-title {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 1.25rem;
    font-weight: 600;
    color: white;
    margin: 0 0 0.5rem 0;
}

.results-title i {
    font-size: 1.5rem;
}

.search-term, .filter-term {
    font-size: 0.875rem;
    color: rgba(255, 255, 255, 0.7);
    margin-top: 0.25rem;
}

.highlight {
    color: #10b981;
    font-weight: 600;
}

.clear-search-btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.625rem 1rem;
    background: rgba(239, 68, 68, 0.15);
    border: 1px solid rgba(239, 68, 68, 0.3);
    border-radius: 10px;
    color: #ef4444;
    font-size: 0.875rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    white-space: nowrap;
}

.clear-search-btn:hover {
    background: rgba(239, 68, 68, 0.25);
    border-color: rgba(239, 68, 68, 0.5);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);
}

.search-input-wrapper:focus-within {
    background: rgba(255, 255, 255, 0.12);
    border-color: #10b981;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15), 0 4px 24px rgba(16, 185, 129, 0.2);
    transform: translateY(-2px);
}

.search-icon {
    color: rgba(16, 185, 129, 0.8);
    font-size: 1.125rem;
    flex-shrink: 0;
    transition: all 0.3s ease;
}

.search-input-wrapper:focus-within .search-icon {
    color: #10b981;
    transform: scale(1.1);
}

.modern-search-input {
    flex: 1;
    background: transparent;
    border: none;
    color: white;
    font-size: 1rem;
    font-weight: 500;
    padding: 0;
    outline: none;
    min-width: 0;
}

.modern-search-input::placeholder {
    color: rgba(255, 255, 255, 0.5);
}

.clear-input-btn {
    width: 2rem;
    height: 2rem;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    color: rgba(255, 255, 255, 0.7);
    cursor: pointer;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    flex-shrink: 0;
}

.clear-input-btn:hover {
    background: rgba(239, 68, 68, 0.25);
    color: #ef4444;
    border-color: rgba(239, 68, 68, 0.3);
    transform: scale(1.1) rotate(90deg);
    box-shadow: 0 2px 8px rgba(239, 68, 68, 0.2);
}

.clear-input-btn:active {
    transform: scale(0.95) rotate(90deg);
}

.search-submit-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.625rem 1.25rem;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    color: white;
    font-size: 0.9375rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25), inset 0 1px 0 rgba(255, 255, 255, 0.2);
    flex-shrink: 0;
    white-space: nowrap;
    position: relative;
    overflow: hidden;
}

.search-submit-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s;
}

.search-submit-btn:hover::before {
    left: 100%;
}

.search-submit-btn:hover {
    background: linear-gradient(135deg, #059669 0%, #047857 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(16, 185, 129, 0.35), inset 0 1px 0 rgba(255, 255, 255, 0.25);
    border-color: rgba(255, 255, 255, 0.3);
}

.search-submit-btn:active {
    transform: translateY(0);
    box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3), inset 0 1px 0 rgba(255, 255, 255, 0.15);
}

.search-submit-btn i {
    font-size: 1rem;
    position: relative;
    z-index: 1;
}

.search-submit-btn span {
    position: relative;
    z-index: 1;
}

/* ============================================
   GRID DE PRODUTOS - RESPONSIVO PARA DESK/NOTE
   ============================================ */

/* Desktop grande (1600px+) - 4 colunas */
.premium-product-grid.search-grid {
    display: grid !important;
    grid-template-columns: repeat(4, 1fr) !important;
    gap: 1.25rem;
    width: 100%;
    box-sizing: border-box;
    margin-bottom: 1.5rem;
}

/* Desktop médio (1280px - 1599px) - 4 colunas */
@media (max-width: 1599px) and (min-width: 1280px) {
    .premium-product-grid.search-grid {
        grid-template-columns: repeat(4, 1fr) !important;
        gap: 1rem;
    }
}

/* Notebook grande (1024px - 1279px) - 3 colunas */
@media (max-width: 1279px) and (min-width: 1024px) {
    .premium-product-grid.search-grid {
        grid-template-columns: repeat(3, 1fr) !important;
        gap: 0.875rem;
    }
}

/* Tablet (768px - 1023px) - 2 colunas */
@media (max-width: 1023px) and (min-width: 768px) {
    .premium-product-grid.search-grid {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 0.75rem;
    }
}

/* Mobile (< 768px) - 2 colunas */
@media (max-width: 767px) {
    .premium-product-grid.search-grid {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 0.625rem;
    }
}

@media (max-width: 575px) {
    .premium-product-grid.search-grid {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 0.4375rem;
    }
}

/* ============================================
   AJUSTES DE IMAGEM E CATEGORIA
   ============================================ */

.premium-product-image {
    position: relative !important;
    overflow: hidden !important;
    margin-bottom: 0.375rem !important;
}

/* Garantir que a categoria dentro do info não seja cortada */
.premium-product-info {
    min-height: auto !important;
    overflow: visible !important;
}

.premium-product-image img {
    border-radius: 6px !important;
}

/* Garantir que o card tenha espaço suficiente */
.premium-product-card {
    overflow: visible !important;
}

/* Placeholder melhorado para imagens sem foto */
.premium-product-image img.no-image-fallback,
.premium-product-image img[src*="no-image"] {
    background: linear-gradient(135deg, rgba(31, 41, 55, 0.8), rgba(55, 65, 81, 0.6)) !important;
    object-fit: contain !important;
    padding: 1rem !important;
    filter: brightness(0.6) !important;
}

.premium-product-image img.no-image-fallback::after {
    content: '📦' !important;
    position: absolute !important;
    top: 50% !important;
    left: 50% !important;
    transform: translate(-50%, -50%) !important;
    font-size: 2rem !important;
    opacity: 0.5 !important;
}

/* Ajuste para stats */
.product-stats {
    margin-top: 0.375rem !important;
    display: flex !important;
    gap: 0.5rem !important;
    flex-wrap: wrap !important;
    justify-content: center !important;
}

.stat-item {
    display: flex !important;
    align-items: center !important;
    gap: 0.25rem !important;
    color: rgba(255, 255, 255, 0.7) !important;
    font-size: 0.6875rem !important;
}

.stat-item i {
    color: #10b981 !important;
    font-size: 0.75rem !important;
}

/* ============================================
   RESPONSIVO - DESKTOP/NOTEBOOK
   ============================================ */

/* Desktop - melhor aproveitamento do espaço */
@media (min-width: 1280px) {
    .premium-content {
        max-width: 1600px !important;
        margin: 0 auto !important;
        padding: 2rem !important;
    }
    
    .search-form-container {
        max-width: 100%;
        margin-bottom: 1.5rem;
    }
    
    .search-input-wrapper {
        padding: 1rem 1.25rem;
        border-radius: 16px;
    }
    
    .modern-search-input {
        font-size: 1.125rem;
    }
    
    .search-icon {
        font-size: 1.25rem;
    }
    
    .search-submit-btn {
        padding: 0.75rem 1.5rem;
        font-size: 1rem;
        border-radius: 12px;
    }
    
    .results-header {
        margin-bottom: 1.5rem;
    }
}

/* Notebook médio */
@media (min-width: 1024px) and (max-width: 1279px) {
    .premium-content {
        max-width: 100%;
        padding: 1.5rem !important;
    }
    
    .search-form-container {
        margin-bottom: 1.25rem;
    }
    
    .search-input-wrapper {
        padding: 0.875rem 1rem;
    }
}

@media (max-width: 768px) {
    .search-input-wrapper {
        padding: 0.625rem 0.75rem;
        gap: 0.4375rem;
    }
    
    .search-submit-btn {
        padding: 0.5rem 0.875rem;
        font-size: 0.8125rem;
    }
    
    .search-submit-btn span {
        display: none;
    }
}

@media (max-width: 480px) {
    .search-input-wrapper {
        padding: 0.5625rem 0.625rem;
        gap: 0.3125rem;
        border-radius: 10px;
    }
    
    .search-icon {
        font-size: 0.9375rem;
    }
    
    .modern-search-input {
        font-size: 0.875rem;
    }
    
    .search-submit-btn {
        padding: 0.4375rem 0.75rem;
        font-size: 0.75rem;
        border-radius: 8px;
    }
    
    .search-submit-btn span {
        display: none;
    }
    
    .search-submit-btn i {
        font-size: 1rem;
    }
    
    .clear-input-btn {
        width: 1.625rem;
        height: 1.625rem;
    }
    
}

@media (max-width: 375px) {
    .search-input-wrapper {
        padding: 0.5625rem 0.625rem;
        gap: 0.25rem;
    }
    
    .search-submit-btn {
        padding: 0.4375rem 0.75rem;
        font-size: 0.75rem;
    }
}
</style>
@endsection

@section('scripts')
<script>
function goBack() {
    window.history.back();
}

function clearSearch() {
    document.getElementById('searchInput').value = '';
    document.getElementById('searchForm').submit();
}

function clearInput() {
    document.getElementById('searchInput').value = '';
    document.getElementById('searchForm').submit();
}

function viewProduct(productId) {
    window.location.href = `/products/${productId}`;
}

// Focus on search input when page loads
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const searchForm = document.getElementById('searchForm');
    
    // Focus no input de busca
    if (searchInput && !searchInput.value) {
        searchInput.focus();
    }
    
    // Real-time search with debounce
    if (searchInput && searchForm) {
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();
            searchTimeout = setTimeout(() => {
                if (query.length >= 2 || query.length === 0) {
                    searchForm.submit();
                }
            }, 500);
        });
    }
    
    // Add loading state during search
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.style.opacity = '0.7';
                searchInput.disabled = true;
                
                setTimeout(() => {
                    searchInput.style.opacity = '1';
                    searchInput.disabled = false;
                }, 1000);
            }
        });
    }
});
</script>
@endsection
