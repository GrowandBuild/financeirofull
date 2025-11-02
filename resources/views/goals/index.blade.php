@extends('layouts.app')

@section('title', 'Monitoramento Financeiro')

@section('content')
<!-- Premium Header -->
<div class="premium-header">
    <div class="header-content">
        <div class="header-title">
            <h1>Monitoramento Financeiro</h1>
            <span class="header-subtitle">Distribuição Padrão 40/10/30/10/10</span>
        </div>
    </div>
</div>

<!-- Premium Content -->
<div class="premium-content">
    <!-- Monitoramento Padrão -->
    <div class="premium-product-card" style="margin-bottom: 20px; flex-direction: column; align-items: stretch; cursor: auto;">
        <div class="mb-3">
            <h3 class="mb-1" style="color: white; font-size: 1.2rem;">
                <i class="bi bi-pie-chart me-2"></i>Monitoramento do Mês Atual
            </h3>
            <p class="mb-0" style="color: rgba(255,255,255,0.7); font-size: 0.9rem;">
                Acompanhe sua distribuição financeira seguindo o padrão: 40% Despesas Fixas, 10% Recursos Profissionais, 30% Reservas, 10% Lazer, 10% Dívidas
            </p>
        </div>
        
        <!-- Distribuição em Pizza -->
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="chart-section" style="padding: 1rem;">
                    <h4 style="color: white; font-size: 1rem; margin-bottom: 1rem;">
                        <i class="bi bi-bullseye text-success"></i> Distribuição Ideal
                    </h4>
                    <div style="height: 300px; display: flex; align-items: center; justify-content: center;">
                        <svg width="200" height="200" style="transform: rotate(-90deg);">
                            @php
                                $cumulative = 0;
                                $colorIndex = 0;
                                $colors = ['#3b82f6', '#10b981', '#f59e0b', '#ec4899', '#8b5cf6'];
                                $radius = 85;
                                $center = 100;
                            @endphp
                            @foreach($defaultGoalData['distribution'] as $key => $category)
                                @php
                                    $color = $colors[$colorIndex % count($colors)];
                                    $start = $cumulative;
                                    $cumulative += $category['percentage'];
                                    $end = $cumulative;
                                    
                                    $startAngle = ($start * 3.6) * M_PI / 180;
                                    $endAngle = ($end * 3.6) * M_PI / 180;
                                    
                                    $x1 = $center + $radius * cos($startAngle);
                                    $y1 = $center + $radius * sin($startAngle);
                                    $x2 = $center + $radius * cos($endAngle);
                                    $y2 = $center + $radius * sin($endAngle);
                                    
                                    $largeArc = ($end - $start) > 50 ? 1 : 0;
                                    
                                    $colorIndex++;
                                @endphp
                                <path d="M {{ $center }} {{ $center }} L {{ $x1 }} {{ $y1 }} A {{ $radius }} {{ $radius }} 0 {{ $largeArc }} 1 {{ $x2 }} {{ $y2 }} Z" 
                                      fill="{{ $color }}" 
                                      stroke="#1f2937" 
                                      stroke-width="2"/>
                            @endforeach
                        </svg>
                    </div>
                    <div class="mt-3">
                        @foreach($defaultGoalData['distribution'] as $key => $category)
                            <div class="d-flex align-items-center mb-2">
                                <div style="width: 20px; height: 20px; background: {{ ['#3b82f6', '#10b981', '#f59e0b', '#ec4899', '#8b5cf6'][$loop->index] }}; border-radius: 4px; margin-right: 10px;"></div>
                                <span style="color: white; font-size: 0.9rem;">{{ $category['label'] }} ({{ $category['percentage'] }}%)</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="chart-section" style="padding: 1rem;">
                    <h4 style="color: white; font-size: 1rem; margin-bottom: 1rem;">
                        <i class="bi bi-cash text-warning"></i> Gastos Reais
                    </h4>
                    <div style="height: 300px; display: flex; align-items: center; justify-content: center;">
                        @php
                            $totalExpenses = $defaultGoalData['total_expenses'] ?? 0;
                            $hasExpenses = $totalExpenses > 0;
                        @endphp
                        @if($hasExpenses)
                        <svg width="200" height="200" style="transform: rotate(-90deg);">
                            @php
                                $cumulative = 0;
                                $colorIndex = 0;
                                $radius = 85;
                                $center = 100;
                            @endphp
                            @foreach($defaultGoalData['distribution'] as $key => $category)
                                @php
                                    $color = ['#3b82f6', '#10b981', '#f59e0b', '#ec4899', '#8b5cf6'][$colorIndex % 5];
                                    $percentage = ($category['actual_amount'] / $totalExpenses) * 100;
                                    $start = $cumulative;
                                    $cumulative += $percentage;
                                    $end = $cumulative;
                                    
                                    if ($percentage > 0) {
                                        $startAngle = ($start * 3.6) * M_PI / 180;
                                        $endAngle = ($end * 3.6) * M_PI / 180;
                                        
                                        $x1 = $center + $radius * cos($startAngle);
                                        $y1 = $center + $radius * sin($startAngle);
                                        $x2 = $center + $radius * cos($endAngle);
                                        $y2 = $center + $radius * sin($endAngle);
                                        
                                        $largeArc = ($end - $start) > 50 ? 1 : 0;
                                    }
                                    $colorIndex++;
                                @endphp
                                @if($percentage > 0)
                                <path d="M {{ $center }} {{ $center }} L {{ $x1 }} {{ $y1 }} A {{ $radius }} {{ $radius }} 0 {{ $largeArc }} 1 {{ $x2 }} {{ $y2 }} Z" 
                                      fill="{{ $color }}" 
                                      stroke="#1f2937" 
                                      stroke-width="2"/>
                                @endif
                            @endforeach
                        </svg>
                        @else
                        <div class="text-center">
                            <i class="bi bi-pie-chart" style="font-size: 80px; color: rgba(255,255,255,0.2);"></i>
                            <p style="color: rgba(255,255,255,0.6); margin-top: 10px;">Nenhum gasto registrado</p>
                        </div>
                        @endif
                    </div>
                    @if($hasExpenses)
                    <div class="mt-3">
                        @foreach($defaultGoalData['distribution'] as $key => $category)
                            @if($category['actual_amount'] > 0)
                            <div class="d-flex align-items-center mb-2">
                                <div style="width: 20px; height: 20px; background: {{ ['#3b82f6', '#10b981', '#f59e0b', '#ec4899', '#8b5cf6'][$loop->index] }}; border-radius: 4px; margin-right: 10px;"></div>
                                <span style="color: white; font-size: 0.9rem;">{{ $category['label'] }}: R$ {{ number_format($category['actual_amount'], 2, ',', '.') }}</span>
                            </div>
                            @endif
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Resumo Financeiro -->
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="chart-section">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <div style="color: #9ca3af; font-size: 0.85rem; margin-bottom: 0.25rem;">
                                <i class="bi bi-arrow-down-circle text-success"></i> Total Receitas
                            </div>
                            <div style="color: #10b981; font-size: 1.2rem; font-weight: 600;">
                                R$ {{ number_format($defaultGoalData['total_income'], 2, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div style="color: #9ca3af; font-size: 0.85rem; margin-bottom: 0.25rem;">
                                <i class="bi bi-arrow-up-circle text-danger"></i> Total Despesas
                            </div>
                            <div style="color: #ef4444; font-size: 1.2rem; font-weight: 600;">
                                R$ {{ number_format($defaultGoalData['total_expenses'], 2, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div style="color: #9ca3af; font-size: 0.85rem; margin-bottom: 0.25rem;">
                                <i class="bi bi-wallet"></i> Saldo Disponível
                            </div>
                            <div style="color: #3b82f6; font-size: 1.2rem; font-weight: 600;">
                                R$ {{ number_format($defaultGoalData['total_balance'], 2, ',', '.') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Lista de Categorias -->
        <div class="mb-3">
            <h4 style="color: white; font-size: 1rem; margin-bottom: 1rem;">
                <i class="bi bi-list-check"></i> Detalhamento por Departamento
            </h4>
            <div class="row">
                @foreach($defaultGoalData['distribution'] as $key => $category)
                <div class="col-md-6 mb-3">
                    <div class="chart-section" style="padding: 1rem;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span style="color: white; font-size: 1rem; font-weight: 600;">{{ $category['label'] }}</span>
                            <span style="color: #10b981; font-size: 0.9rem; font-weight: 700;">{{ $category['percentage'] }}%</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="text-center flex-grow-1">
                                <div style="color: #3b82f6; font-size: 1rem; font-weight: 600;">
                                    R$ {{ number_format($category['available_amount'], 2, ',', '.') }}
                                </div>
                                <div style="color: #9ca3af; font-size: 0.75rem;">Disponível</div>
                            </div>
                            <div class="text-center flex-grow-1" style="border-left: 1px solid rgba(255,255,255,0.2); border-right: 1px solid rgba(255,255,255,0.2);">
                                <div style="color: #f59e0b; font-size: 1rem; font-weight: 600;">
                                    R$ {{ number_format($category['actual_amount'], 2, ',', '.') }}
                                </div>
                                <div style="color: #9ca3af; font-size: 0.75rem;">Gasto</div>
                            </div>
                            <div class="text-center flex-grow-1">
                                <div style="color: {{ $category['available_amount'] >= $category['actual_amount'] ? '#10b981' : '#ef4444' }}; font-size: 1rem; font-weight: 600;">
                                    R$ {{ number_format($category['available_amount'] - $category['actual_amount'], 2, ',', '.') }}
                                </div>
                                <div style="color: #9ca3af; font-size: 0.75rem;">Restante</div>
                            </div>
                        </div>
                        @if($category['available_amount'] > 0)
                        <div class="progress" style="height: 8px; background: rgba(255,255,255,0.1); border-radius: 6px;">
                            @php
                                $percentage = min(100, ($category['actual_amount'] / $category['available_amount']) * 100);
                            @endphp
                            <div class="progress-bar" role="progressbar" 
                                 style="width: {{ $percentage }}%; 
                                        background: linear-gradient(90deg, 
                                            {{ $percentage < 80 ? '#10b981' : ($percentage < 100 ? '#f59e0b' : '#ef4444') }}, 
                                            {{ $percentage < 80 ? '#059669' : ($percentage < 100 ? '#d97706' : '#dc2626') }}); 
                                        border-radius: 6px;"></div>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>


@endsection
