@extends('layouts.cashflow')

@section('title', 'Relatórios - Fluxo de Caixa')

@section('content')
<div class="container-fluid p-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card-cashflow p-4">
                <h1 class="h3 mb-1 text-white">
                    <i class="bi bi-graph-up me-2"></i>
                    Relatórios Financeiros
                </h1>
                <p class="text-white-50 mb-0">Análise detalhada das suas finanças</p>
            </div>
        </div>
    </div>

    <!-- Gráfico Anual -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card-cashflow p-4">
                <h5 class="text-white mb-3">
                    <i class="bi bi-calendar-year me-2"></i>
                    Visão Geral Anual {{ date('Y') }}
                </h5>
                <canvas id="yearlyChart" height="100"></canvas>
            </div>
        </div>
    </div>

    <!-- Análise por Categorias -->
    @if($categoryExpenses->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card-cashflow p-4">
                <h5 class="text-white mb-3">
                    <i class="bi bi-pie-chart me-2"></i>
                    Gasto por Categoria
                </h5>
                <div class="row">
                    @foreach($categoryExpenses->take(6) as $category)
                    <div class="col-md-4 col-sm-6 mb-3">
                        <div class="d-flex align-items-center p-3 rounded" style="background: rgba(255, 255, 255, 0.05);">
                            <div class="me-3" style="width: 16px; height: 16px; background: {{ $category->color }}; border-radius: 50%;"></div>
                            <div class="flex-grow-1">
                                <div class="text-white fw-medium">{{ $category->name }}</div>
                                <div class="text-success fw-bold">
                                    R$ {{ number_format($category->cash_flows_sum_amount ?? 0, 2, ',', '.') }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Resumo Mensal -->
    <div class="row">
        <div class="col-12">
            <div class="card-cashflow p-4">
                <h5 class="text-white mb-3">
                    <i class="bi bi-table me-2"></i>
                    Resumo Mensal
                </h5>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th class="text-white">Mês</th>
                                <th class="text-white text-end">Receitas</th>
                                <th class="text-white text-end">Despesas</th>
                                <th class="text-white text-end">Saldo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($yearlyData as $data)
                            <tr>
                                <td class="text-white fw-medium">{{ $data['month'] }}</td>
                                <td class="text-end text-success">
                                    R$ {{ number_format($data['income'], 2, ',', '.') }}
                                </td>
                                <td class="text-end text-danger">
                                    R$ {{ number_format($data['expense'], 2, ',', '.') }}
                                </td>
                                <td class="text-end fw-bold {{ $data['balance'] >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $data['balance'] >= 0 ? '+' : '' }}R$ {{ number_format($data['balance'], 2, ',', '.') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="border-top border-white-50">
                                <td class="text-white fw-bold">Total</td>
                                <td class="text-end text-success fw-bold">
                                    R$ {{ number_format($yearlyData->sum('income'), 2, ',', '.') }}
                                </td>
                                <td class="text-end text-danger fw-bold">
                                    R$ {{ number_format($yearlyData->sum('expense'), 2, ',', '.') }}
                                </td>
                                <td class="text-end fw-bold {{ $yearlyData->sum('balance') >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $yearlyData->sum('balance') >= 0 ? '+' : '' }}R$ {{ number_format($yearlyData->sum('balance'), 2, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Gráfico Anual
    const yearlyCtx = document.getElementById('yearlyChart').getContext('2d');
    const yearlyData = @json($yearlyData);
    
    new Chart(yearlyCtx, {
        type: 'bar',
        data: {
            labels: yearlyData.map(item => item.month),
            datasets: [
                {
                    label: 'Receitas',
                    data: yearlyData.map(item => item.income),
                    backgroundColor: 'rgba(16, 185, 129, 0.5)',
                    borderColor: '#10b981',
                    borderWidth: 2
                },
                {
                    label: 'Despesas',
                    data: yearlyData.map(item => item.expense),
                    backgroundColor: 'rgba(239, 68, 68, 0.5)',
                    borderColor: '#ef4444',
                    borderWidth: 2
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

