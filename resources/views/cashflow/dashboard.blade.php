@extends('layouts.cashflow')

@section('title', 'Dashboard - Fluxo de Caixa')

@section('content')
<div class="container-fluid p-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card-cashflow p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-1 text-white">
                            <i class="bi bi-cash-coin me-2"></i>
                            Fluxo de Caixa
                        </h1>
                        <p class="text-white-50 mb-0">Controle suas finanças pessoais</p>
                    </div>
                    <div class="text-end">
                        <div class="text-white-50 small">Saldo do Mês</div>
                        <div class="h4 mb-0 {{ $monthlyBalance >= 0 ? 'text-success' : 'text-danger' }}">
                            R$ {{ number_format($monthlyBalance, 2, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cards de Resumo -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card-cashflow p-4 text-center">
                <div class="text-success mb-2">
                    <i class="bi bi-arrow-up-circle-fill" style="font-size: 2rem;"></i>
                </div>
                <h5 class="text-white mb-1">Receitas</h5>
                <h3 class="text-success mb-0">R$ {{ number_format($monthlyIncome, 2, ',', '.') }}</h3>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card-cashflow p-4 text-center">
                <div class="text-danger mb-2">
                    <i class="bi bi-arrow-down-circle-fill" style="font-size: 2rem;"></i>
                </div>
                <h5 class="text-white mb-1">Despesas</h5>
                <h3 class="text-danger mb-0">R$ {{ number_format($monthlyExpense, 2, ',', '.') }}</h3>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card-cashflow p-4 text-center">
                <div class="text-warning mb-2">
                    <i class="bi bi-pie-chart-fill" style="font-size: 2rem;"></i>
                </div>
                <h5 class="text-white mb-1">Categorias</h5>
                <h3 class="text-warning mb-0">{{ $topCategories->count() }}</h3>
            </div>
        </div>
    </div>

    <!-- Gráfico e Transações Recentes -->
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card-cashflow p-4">
                <h5 class="text-white mb-3">
                    <i class="bi bi-graph-up me-2"></i>
                    Evolução dos Últimos 6 Meses
                </h5>
                <canvas id="cashFlowChart" height="300"></canvas>
            </div>
        </div>
        <div class="col-lg-4 mb-4">
            <div class="card-cashflow p-4">
                <h5 class="text-white mb-3">
                    <i class="bi bi-clock-history me-2"></i>
                    Transações Recentes
                </h5>
                @if($recentTransactions->count() > 0)
                    <div class="space-y-3">
                        @foreach($recentTransactions as $transaction)
                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom border-white-10">
                            <div>
                                <div class="text-white fw-medium">{{ $transaction->title }}</div>
                                <div class="text-white-50 small">
                                    {{ $transaction->transaction_date->format('d/m/Y') }}
                                    @if($transaction->category)
                                        • {{ $transaction->category->name }}
                                    @endif
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold {{ $transaction->type === 'income' ? 'text-success' : 'text-danger' }}">
                                    {{ $transaction->type === 'income' ? '+' : '-' }}R$ {{ number_format($transaction->amount, 2, ',', '.') }}
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center text-white-50 py-4">
                        <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                        <p class="mt-2 mb-0">Nenhuma transação registrada</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Categorias Mais Usadas -->
    @if($topCategories->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card-cashflow p-4">
                <h5 class="text-white mb-3">
                    <i class="bi bi-tags me-2"></i>
                    Categorias Mais Usadas
                </h5>
                <div class="row">
                    @foreach($topCategories as $category)
                    <div class="col-md-4 col-sm-6 mb-3">
                        <div class="d-flex align-items-center p-3 rounded" style="background: rgba(255, 255, 255, 0.05);">
                            <div class="me-3" style="width: 12px; height: 12px; background: {{ $category->color }}; border-radius: 50%;"></div>
                            <div class="flex-grow-1">
                                <div class="text-white fw-medium">{{ $category->name }}</div>
                                <div class="text-white-50 small">{{ $category->cash_flows_count }} transações</div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
    // Gráfico de Evolução
    const ctx = document.getElementById('cashFlowChart').getContext('2d');
    const chartData = @json($chartData);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.map(item => item.month),
            datasets: [
                {
                    label: 'Receitas',
                    data: chartData.map(item => item.income),
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Despesas',
                    data: chartData.map(item => item.expense),
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Saldo',
                    data: chartData.map(item => item.balance),
                    borderColor: '#fbbf24',
                    backgroundColor: 'rgba(251, 191, 36, 0.1)',
                    tension: 0.4,
                    fill: false
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: {
                        color: 'white'
                    }
                }
            },
            scales: {
                x: {
                    ticks: {
                        color: 'rgba(255, 255, 255, 0.7)'
                    },
                    grid: {
                        color: 'rgba(255, 255, 255, 0.1)'
                    }
                },
                y: {
                    ticks: {
                        color: 'rgba(255, 255, 255, 0.7)',
                        callback: function(value) {
                            return 'R$ ' + value.toLocaleString('pt-BR');
                        }
                    },
                    grid: {
                        color: 'rgba(255, 255, 255, 0.1)'
                    }
                }
            }
        }
    });
</script>
@endpush
@endsection
