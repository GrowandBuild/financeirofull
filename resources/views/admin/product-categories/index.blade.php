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
                        <h1>Gerenciar Categorias</h1>
                        <p class="header-subtitle">Administre categorias de produtos e veja seus gastos</p>
                    </div>
                </div>
                <div class="header-actions">
                    <a href="{{ route('admin.products.index') }}" class="action-btn" title="Gerenciar Produtos">
                        <i class="bi bi-box"></i>
                    </a>
                    <a href="{{ route('admin.product-categories.create') }}" class="action-btn" title="Nova Categoria">
                        <i class="bi bi-plus-circle"></i>
                    </a>
                    <form action="{{ route('admin.product-categories.migrate') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="action-btn" title="Migrar Categorias Existentes">
                            <i class="bi bi-arrow-repeat"></i>
                        </button>
                    </form>
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

        @if(session('info'))
            <div class="bg-blue-500/20 border border-blue-500/30 text-blue-300 px-4 py-3 rounded-lg mb-6 backdrop-blur-sm">
                <div class="flex items-center gap-2">
                    <i class="bi bi-info-circle"></i>
                    <span class="font-medium">{{ session('info') }}</span>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-500/20 border border-red-500/30 text-red-300 px-4 py-3 rounded-lg mb-6 backdrop-blur-sm">
                <div class="flex items-center gap-2">
                    <i class="bi bi-exclamation-circle"></i>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <!-- Stats Cards -->
        <div class="stats-grid mb-8">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="bi bi-tags"></i>
                </div>
                <div class="stat-label">Total de Categorias</div>
                <div class="stat-value">{{ $categories->total() }}</div>
                <div class="stat-subtitle">Categorias cadastradas</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="bi bi-box"></i>
                </div>
                <div class="stat-label">Total de Produtos</div>
                <div class="stat-value">{{ $categories->sum('products_count') }}</div>
                <div class="stat-subtitle">Produtos categorizados</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="bi bi-currency-dollar"></i>
                </div>
                <div class="stat-label">Gasto Total</div>
                <div class="stat-value">R$ {{ number_format($categories->sum('total_spent'), 2, ',', '.') }}</div>
                <div class="stat-subtitle">Em todas categorias</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="bi bi-calendar-month"></i>
                </div>
                <div class="stat-label">Gasto do Mês</div>
                <div class="stat-value">R$ {{ number_format($categories->sum('monthly_spent'), 2, ',', '.') }}</div>
                <div class="stat-subtitle">Este mês</div>
            </div>
        </div>

        <!-- Categories Grid -->
        <div class="premium-product-grid">
            @forelse($categories as $category)
                <div class="premium-product-card admin-product-card">
                    <div class="premium-product-image">
                        <div class="product-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                            <i class="bi bi-tag-fill" style="font-size: 2rem;"></i>
                        </div>
                        <div class="product-overlay">
                            <div class="overlay-actions">
                                <a href="{{ route('admin.product-categories.show', $category) }}" 
                                   class="overlay-action-btn" title="Ver detalhes">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.product-categories.edit', $category) }}" 
                                   class="overlay-action-btn edit" title="Editar categoria">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.product-categories.destroy', $category) }}" 
                                      method="POST" 
                                      class="inline delete-form"
                                      onsubmit="return confirm('Tem certeza que deseja excluir esta categoria?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="overlay-action-btn delete" title="Excluir categoria">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="premium-product-info">
                        <h5 class="premium-product-name">{{ $category->name }}</h5>
                        
                        <div class="product-stats">
                            <div class="stat-item">
                                <i class="bi bi-box"></i>
                                {{ $category->products_count }} produtos
                            </div>
                            @if($category->usage_count > 0)
                                <div class="stat-item">
                                    <i class="bi bi-cart-check"></i>
                                    {{ $category->usage_count }} usos
                                </div>
                            @endif
                        </div>

                        @if(isset($category->total_spent) && $category->total_spent > 0)
                            <div class="premium-product-price">
                                <div class="price-label">Gasto Total:</div>
                                <div class="price-value">R$ {{ number_format($category->total_spent, 2, ',', '.') }}</div>
                            </div>
                        @endif

                        @if(isset($category->monthly_spent) && $category->monthly_spent > 0)
                            <div class="product-stats mt-2">
                                <div class="stat-item">
                                    <i class="bi bi-calendar-month"></i>
                                    Este mês: R$ {{ number_format($category->monthly_spent, 2, ',', '.') }}
                                </div>
                            </div>
                        @endif

                        @if(isset($category->avg_monthly_spent) && $category->avg_monthly_spent > 0)
                            <div class="product-stats">
                                <div class="stat-item">
                                    <i class="bi bi-graph-up"></i>
                                    Média mensal: R$ {{ number_format($category->avg_monthly_spent, 2, ',', '.') }}
                                </div>
                            </div>
                        @endif

                        @if(!$category->is_active)
                            <div class="mt-2">
                                <span class="badge badge-warning">Inativa</span>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="premium-empty-state">
                    <div class="empty-icon">
                        <i class="bi bi-tags"></i>
                    </div>
                    <div class="empty-title">Nenhuma categoria encontrada</div>
                    <div class="empty-description">
                        Comece criando categorias para organizar seus produtos e acompanhar seus gastos.
                    </div>
                    <div class="empty-actions">
                        <a href="{{ route('admin.product-categories.create') }}" class="premium-btn primary">
                            <i class="bi bi-plus"></i>
                            Criar Categoria
                        </a>
                        <form action="{{ route('admin.product-categories.migrate') }}" method="POST" class="inline mt-2">
                            @csrf
                            <button type="submit" class="premium-btn outline">
                                <i class="bi bi-arrow-repeat"></i>
                                Migrar Categorias Existentes
                            </button>
                        </form>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($categories->hasPages())
            <div class="mt-6 flex justify-center">
                <div class="bg-white/10 backdrop-blur-sm rounded-lg p-3">
                    {{ $categories->onEachSide(1)->links() }}
                </div>
            </div>
        @endif
    </div>
</div>

<style>
/* Reusar estilos de produtos para categorias */
.premium-product-card {
    position: relative;
}

.badge {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 600;
}

.badge-warning {
    background: rgba(251, 191, 36, 0.2);
    color: #fbbf24;
    border: 1px solid rgba(251, 191, 36, 0.3);
}

.price-label {
    font-size: 0.75rem;
    color: rgba(255, 255, 255, 0.6);
    margin-bottom: 0.25rem;
}

.price-value {
    font-size: 1.25rem;
    font-weight: 700;
    color: #10b981;
}
</style>
@endsection

