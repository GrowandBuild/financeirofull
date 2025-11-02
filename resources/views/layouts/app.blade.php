<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }} - @yield('title', 'Meus Produtos')</title>
        
        <!-- Bootstrap 5.3 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        
        <!-- Bootstrap Icons -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
        
        <!-- Custom CSS -->
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        
        <!-- Chart.js para gráficos (carregamento diferido) -->
        <script>
            // Carregar Chart.js apenas quando necessário
            window.loadChartJS = function() {
                return new Promise((resolve) => {
                    if (window.Chart) {
                        resolve(window.Chart);
                        return;
                    }
                    const script = document.createElement('script');
                    script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
                    script.onload = () => resolve(window.Chart);
                    document.head.appendChild(script);
                });
            };
        </script>
        
        <!-- Offline Storage -->
        <script src="{{ asset('js/offline-storage.js') }}"></script>
        
        <!-- Custom JavaScript -->
        <script src="{{ asset('js/app.js') }}"></script>
    </head>
<body>
    <!-- Switcher de Sistemas -->
    <div class="system-switcher">
        <div style="display: flex; gap: 0.5rem; align-items: center;">
            <a href="{{ route('products.index') }}" class="btn {{ request()->routeIs('products.*') ? 'active' : '' }}">
                <i class="bi bi-box-seam"></i> Produtos
            </a>
            <a href="{{ route('cashflow.dashboard') }}" class="btn {{ request()->routeIs('cashflow.*') ? 'active' : '' }}">
                <i class="bi bi-cash-coin"></i> Fluxo de Caixa
            </a>
            @auth
            <a href="{{ route('financial-schedule.index') }}" class="btn {{ request()->routeIs('financial-schedule.*') ? 'active' : '' }}" style="position: relative;">
                <i class="bi bi-calendar-event"></i> Agenda
                @php
                    $notificationCount = \App\Models\FinancialSchedule::where('user_id', auth()->id())
                        ->where('is_confirmed', false)
                        ->where('scheduled_date', '<=', now()->addDays(7))
                        ->count();
                @endphp
                @if($notificationCount > 0)
                <span class="badge bg-danger" style="position: absolute; top: -5px; right: -5px; border-radius: 50%; min-width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; font-size: 10px;">
                    {{ $notificationCount }}
                </span>
                @endif
            </a>
            <a href="{{ route('goals.index') }}" class="btn {{ request()->routeIs('goals.*') ? 'active' : '' }}">
                <i class="bi bi-graph-up"></i> Monitoramento
            </a>
            @endauth
        </div>
        
        <!-- Total Mensal e Status -->
        <div class="monthly-info">
            @php
                $currentMonth = \Carbon\Carbon::now();
                $startOfMonth = $currentMonth->copy()->startOfMonth();
                $endOfMonth = $currentMonth->copy()->endOfMonth();
                
                $monthlyIncome = auth()->check() 
                    ? \App\Models\CashFlow::where('user_id', auth()->id())
                        ->where('type', 'income')
                        ->where('is_confirmed', true)
                        ->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
                        ->sum('amount')
                    : 0;
                
                $monthlyExpense = auth()->check()
                    ? \App\Models\CashFlow::where('user_id', auth()->id())
                        ->where('type', 'expense')
                        ->where('is_confirmed', true)
                        ->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
                        ->sum('amount')
                    : 0;
                
                $monthlyBalance = $monthlyIncome - $monthlyExpense;
            @endphp
            <div class="monthly-balance">
                <div class="balance-label">Total (Mês)</div>
                <div class="balance-value">
                    <i class="bi bi-cash-coin"></i>
                    R$ {{ number_format($monthlyBalance, 2, ',', '.') }}
                </div>
            </div>
            <div id="online-status" class="online-indicator">
                <i class="bi bi-wifi"></i> Online
            </div>
        </div>
    </div>
    
    <div class="mobile-container">
        @yield('content')
        
        <!-- Bottom Navigation -->
        <div class="bottom-nav">
            <div class="row w-100">
                <div class="col-3 text-center">
                    <a href="{{ route('products.index') }}" class="nav-item-custom {{ request()->routeIs('products.index') ? 'active' : '' }}">
                        <div class="nav-icon-custom">
                            <i class="bi bi-house-door"></i>
                        </div>
                        <span>Início</span>
                    </a>
                </div>
                <div class="col-3 text-center">
                    <a href="{{ route('products.search') }}" class="nav-item-custom {{ request()->routeIs('products.search') ? 'active' : '' }}">
                        <div class="nav-icon-custom">
                            <i class="bi bi-search"></i>
                        </div>
                        <span>Buscar</span>
                    </a>
                </div>
                <div class="col-3 text-center">
                    <a href="{{ route('products.compra') }}" class="nav-item-custom {{ request()->routeIs('products.compra') ? 'active' : '' }}">
                        <div class="nav-icon-custom">
                            <i class="bi bi-cart3"></i>
                        </div>
                        <span>Comprar</span>
                    </a>
                </div>
                @auth
                <div class="col-3 text-center">
                    <a href="{{ route('admin.products.index') }}" class="nav-item-custom {{ request()->routeIs('admin.*') ? 'active' : '' }}">
                        <div class="nav-icon-custom">
                            <i class="bi bi-gear"></i>
                        </div>
                        <span>Admin</span>
                    </a>
                </div>
                @else
                <div class="col-3 text-center">
                    <a href="{{ route('login') }}" class="nav-item-custom">
                        <div class="nav-icon-custom">
                            <i class="bi bi-person"></i>
                        </div>
                        <span>Login</span>
                    </a>
                </div>
                @endauth
            </div>
        </div>
    </div>
    
    <!-- Bootstrap 5.3 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    @yield('scripts')
    @stack('scripts')
</body>
</html>
