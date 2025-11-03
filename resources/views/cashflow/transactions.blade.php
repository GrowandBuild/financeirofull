@extends('layouts.cashflow')

@section('title', 'Transações - Fluxo de Caixa')

@section('content')
<style>
.transaction-list-compact {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.transaction-item {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    border-radius: 0.75rem;
    padding: 1rem;
    border: 1px solid rgba(255, 255, 255, 0.25);
    transition: all 0.3s ease;
}

.transaction-item:hover {
    background: rgba(255, 255, 255, 0.22);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.transaction-badge {
    width: 2rem;
    height: 2rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    font-weight: bold;
    flex-shrink: 0;
}

.income-badge {
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.25), rgba(5, 150, 105, 0.15));
    color: #10b981;
    border: 2px solid rgba(16, 185, 129, 0.5);
}

.expense-badge {
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.25), rgba(220, 38, 38, 0.15));
    color: #ef4444;
    border: 2px solid rgba(239, 68, 68, 0.5);
}

.transaction-title {
    font-weight: 600;
    color: white;
    font-size: 0.95rem;
    line-height: 1.4;
}

.transaction-meta {
    font-size: 0.75rem;
    color: rgba(255, 255, 255, 0.7);
    margin-top: 0.125rem;
}

.transaction-desc {
    font-size: 0.8rem;
    color: rgba(255, 255, 255, 0.75);
    margin-top: 0.25rem;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

.transaction-value {
    font-weight: 700;
    font-size: 1rem;
    white-space: nowrap;
}

.form-select-sm,
.form-control-sm {
    font-size: 0.875rem;
}

.form-label {
    font-weight: 500;
}

/* Paginação estilizada */
.pagination {
    margin-bottom: 0;
    justify-content: center;
}

.pagination .page-link {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: white;
    padding: 0.5rem 0.75rem;
}

.pagination .page-link:hover {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    border-color: rgba(255, 255, 255, 0.3);
}

.pagination .page-item.active .page-link {
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    border-color: #3b82f6;
    color: white;
}

.pagination .page-item.disabled .page-link {
    background: rgba(255, 255, 255, 0.05);
    border-color: rgba(255, 255, 255, 0.1);
    color: rgba(255, 255, 255, 0.3);
}

/* Customização de inputs */
.form-control, .form-select {
    color: white !important;
}

.form-control::placeholder {
    color: rgba(255, 255, 255, 0.5) !important;
}

.form-control:focus, .form-select:focus {
    background: rgba(255, 255, 255, 0.2) !important;
    border-color: rgba(255, 255, 255, 0.4) !important;
    color: white !important;
    box-shadow: 0 0 0 0.25rem rgba(255, 255, 255, 0.1) !important;
}

@media (max-width: 576px) {
    .container-fluid {
        padding-left: 0.5rem !important;
        padding-right: 0.5rem !important;
        padding-top: 0.5rem !important;
    }
    
    .transaction-item {
        padding: 0.75rem;
        margin-bottom: 0.5rem !important;
    }
    
    .transaction-badge {
        width: 1.5rem;
        height: 1.5rem;
        font-size: 0.9rem;
    }
    
    .transaction-title {
        font-size: 0.875rem;
    }
    
    .transaction-value {
        font-size: 0.9rem;
    }
    
    .transaction-meta {
        font-size: 0.7rem;
    }
    
    .transaction-desc {
        font-size: 0.75rem;
    }
    
    .card-cashflow {
        padding: 0.75rem !important;
        margin-bottom: 0.75rem !important;
    }
    
    .pagination {
        font-size: 0.875rem;
    }
    
    .pagination .page-link {
        padding: 0.375rem 0.5rem;
    }
    
    .row {
        margin-bottom: 0 !important;
    }
    
    .mb-3 {
        margin-bottom: 0.5rem !important;
    }
}
</style>

<div class="container-fluid p-2 pb-5">
    <!-- Filtros Compactos -->
    <div class="row mb-2">
        <div class="col-12">
            <div class="card-cashflow p-3">
                <form method="GET" action="{{ route('cashflow.transactions') }}" id="filterForm">
                    <div class="row g-2">
                        <div class="col-6 col-md-3">
                            <label for="type" class="form-label text-white small mb-1">Tipo</label>
                            <select name="type" id="type" class="form-select form-select-sm" style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.3); color: white;">
                                <option value="">Todos</option>
                                <option value="income" {{ request('type') === 'income' ? 'selected' : '' }}>Receitas</option>
                                <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>Despesas</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-3">
                            <label for="category_id" class="form-label text-white small mb-1">Categoria</label>
                            <select name="category_id" id="category_id" class="form-select form-select-sm" style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.3); color: white;">
                                <option value="">Todas</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 col-md-3">
                            <label for="date_from" class="form-label text-white small mb-1">Início</label>
                            <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" class="form-control form-control-sm" style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.3); color: white;">
                        </div>
                        <div class="col-6 col-md-3">
                            <label for="date_to" class="form-label text-white small mb-1">Fim</label>
                            <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" class="form-control form-control-sm" style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.3); color: white;">
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-2">
                        <button type="submit" class="btn btn-sm" style="background: linear-gradient(135deg, #10b981, #059669); color: white; border: none; padding: 0.4rem 1rem;">
                            <i class="bi bi-funnel"></i> Filtrar
                        </button>
                        <a href="{{ route('cashflow.transactions') }}" class="btn btn-sm btn-secondary">
                            <i class="bi bi-x-circle"></i> Limpar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Lista de Transações -->
    <div class="row">
        <div class="col-12">
            @if($transactions->count() > 0)
                <div class="transaction-list-compact">
                    @foreach($transactions as $transaction)
                    <div class="transaction-item mb-2">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="transaction-badge {{ $transaction->type === 'income' ? 'income-badge' : 'expense-badge' }}">
                                        {{ $transaction->type === 'income' ? '↗' : '↙' }}
                                    </span>
                                    <div>
                                        <div class="transaction-title">{{ $transaction->title }}</div>
                                        <div class="transaction-meta">
                                            {{ $transaction->transaction_date->format('d/m/Y') }}
                                            @if($transaction->category)
                                                • <span style="color: {{ $transaction->category->color }};">{{ $transaction->category->name }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @if($transaction->description)
                                    <div class="transaction-desc">{{ $transaction->description }}</div>
                                @endif
                            </div>
                            <div class="d-flex flex-column align-items-end gap-1">
                                <span class="transaction-value {{ $transaction->type === 'income' ? 'text-success' : 'text-danger' }}">
                                    {{ $transaction->type === 'income' ? '+' : '-' }}R$ {{ number_format($transaction->amount, 2, ',', '.') }}
                                </span>
                                <form action="{{ route('cashflow.transactions.destroy', $transaction->id) }}" method="POST" class="d-inline" onsubmit="return confirmDeleteTransaction()">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-link text-danger p-0" style="font-size: 0.875rem;" title="Excluir">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Paginação Compacta -->
                <div class="mt-2">
                    {{ $transactions->onEachSide(1)->links() }}
                </div>
            @else
                <div class="card-cashflow p-4 text-center">
                    <i class="bi bi-inbox" style="font-size: 3rem; color: rgba(255,255,255,0.3);"></i>
                    <p class="mt-3 mb-2 text-white">Nenhuma transação encontrada</p>
                    <p class="mb-4 text-white-50 small">Tente ajustar os filtros ou adicione uma nova transação</p>
                    <a href="{{ route('cashflow.add') }}" class="btn btn-sm" style="background: linear-gradient(135deg, #10b981, #059669); color: white; border: none;">
                        <i class="bi bi-plus-circle me-2"></i>
                        Adicionar Transação
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function confirmDeleteTransaction() {
    return confirm('Tem certeza que deseja excluir esta transação?');
}
</script>
@endsection

