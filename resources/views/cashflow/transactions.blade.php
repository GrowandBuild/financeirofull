@extends('layouts.cashflow')

@section('title', 'Transações - Fluxo de Caixa')

@section('content')
<div class="container-fluid p-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card-cashflow p-4">
                <h1 class="h3 mb-1 text-white">
                    <i class="bi bi-list-ul me-2"></i>
                    Transações
                </h1>
                <p class="text-white-50 mb-0">Histórico completo de suas movimentações financeiras</p>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card-cashflow p-4">
                <form method="GET" action="{{ route('cashflow.transactions') }}" id="filterForm">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="type" class="form-label text-white">Tipo</label>
                            <select name="type" id="type" class="form-control" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white;">
                                <option value="">Todos</option>
                                <option value="income" {{ request('type') === 'income' ? 'selected' : '' }}>Receitas</option>
                                <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>Despesas</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="category_id" class="form-label text-white">Categoria</label>
                            <select name="category_id" id="category_id" class="form-control" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white;">
                                <option value="">Todas</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="date_from" class="form-label text-white">Data Inicial</label>
                            <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" class="form-control" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white;">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="date_to" class="form-label text-white">Data Final</label>
                            <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" class="form-control" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white;">
                        </div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn-cashflow">
                            <i class="bi bi-funnel"></i> Filtrar
                        </button>
                        <a href="{{ route('cashflow.transactions') }}" class="btn btn-secondary">
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
            <div class="card-cashflow p-4">
                @if($transactions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th class="text-white">Data</th>
                                    <th class="text-white">Descrição</th>
                                    <th class="text-white">Categoria</th>
                                    <th class="text-white">Tipo</th>
                                    <th class="text-white text-end">Valor</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transactions as $transaction)
                                <tr>
                                    <td class="text-white-50">{{ $transaction->transaction_date->format('d/m/Y') }}</td>
                                    <td>
                                        <div class="text-white fw-medium">{{ $transaction->title }}</div>
                                        @if($transaction->description)
                                            <div class="text-white-50 small">{{ $transaction->description }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        @if($transaction->category)
                                            <span class="badge rounded-pill" style="background: {{ $transaction->category->color }};">
                                                {{ $transaction->category->name }}
                                            </span>
                                        @else
                                            <span class="text-white-50 small">Sem categoria</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($transaction->type === 'income')
                                            <span class="badge bg-success">Receita</span>
                                        @else
                                            <span class="badge bg-danger">Despesa</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <span class="fw-bold {{ $transaction->type === 'income' ? 'text-success' : 'text-danger' }}">
                                            {{ $transaction->type === 'income' ? '+' : '-' }}R$ {{ number_format($transaction->amount, 2, ',', '.') }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginação -->
                    <div class="mt-4">
                        {{ $transactions->links() }}
                    </div>
                @else
                    <div class="text-center text-white-50 py-5">
                        <i class="bi bi-inbox" style="font-size: 4rem;"></i>
                        <p class="mt-3 mb-0 h5">Nenhuma transação encontrada</p>
                        <p class="mt-2 mb-4">Tente ajustar os filtros ou adicione uma nova transação</p>
                        <a href="{{ route('cashflow.add') }}" class="btn-cashflow">
                            <i class="bi bi-plus-circle me-2"></i>
                            Adicionar Transação
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

