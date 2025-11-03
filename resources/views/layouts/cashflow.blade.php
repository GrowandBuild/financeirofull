<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        
        <!-- PWA Meta Tags -->
        <meta name="theme-color" content="#10b981">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <meta name="apple-mobile-web-app-title" content="Meus Produtos">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="msapplication-TileColor" content="#10b981">
        <meta name="msapplication-TileImage" content="{{ asset('images/icon-192x192.png') }}">
        
        <!-- Favicon e Apple Touch Icons -->
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon.png') }}">
        <link rel="apple-touch-icon" sizes="192x192" href="{{ asset('images/icon-192x192.png') }}">
        <link rel="apple-touch-icon" sizes="512x512" href="{{ asset('images/icon-512x512.png') }}">
        

    <title>{{ config('app.name', 'Laravel') }} - @yield('title', 'Fluxo de Caixa')</title>
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}" id="main-css">
    
    <!-- Chart.js para gráficos -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    
    </head>
<body>
    <!-- Switcher de Sistemas -->
    <div class="system-switcher">
        <!-- Menu Hambúrguer -->
        <button class="hamburger-menu" id="hamburgerMenu" onclick="toggleHamburgerMenu()" aria-label="Menu">
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
            <!-- Fallback ícone caso CSS não carregue -->
            <i class="bi bi-list hamburger-fallback" style="display: none;"></i>
        </button>
        
        <!-- Frases de Gestão Financeira (Apenas Mobile) -->
        <div class="finance-quotes-mobile">
            <div class="quote-container" id="financeQuoteContainer">
                <i class="bi bi-lightbulb quote-icon"></i>
                <div class="quote-text-wrapper">
                    <span class="quote-text" id="financeQuoteText">Pague-se primeiro: guarde pelo menos 10% da sua renda</span>
                </div>
            </div>
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
    
    <!-- Menu Lateral (Offcanvas) -->
    <div class="hamburger-overlay" id="hamburgerOverlay" onclick="toggleHamburgerMenu()"></div>
    <div class="hamburger-menu-panel" id="hamburgerMenuPanel">
        <div class="hamburger-header">
            <h3>Departamentos</h3>
            <button class="hamburger-close" onclick="toggleHamburgerMenu()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="hamburger-content">
            <a href="{{ route('products.index') }}" class="hamburger-menu-item {{ request()->routeIs('products.*') ? 'active' : '' }}">
                <div class="menu-item-icon">
                    <i class="bi bi-box-seam"></i>
                </div>
                <div class="menu-item-content">
                    <span class="menu-item-title">Produtos</span>
                    <span class="menu-item-subtitle">Gerenciar produtos</span>
                </div>
            </a>
            <a href="{{ route('cashflow.dashboard') }}" class="hamburger-menu-item {{ request()->routeIs('cashflow.*') ? 'active' : '' }}">
                <div class="menu-item-icon">
                    <i class="bi bi-cash-coin"></i>
                </div>
                <div class="menu-item-content">
                    <span class="menu-item-title">Fluxo de Caixa</span>
                    <span class="menu-item-subtitle">Controle financeiro</span>
                </div>
            </a>
            @auth
            <a href="{{ route('financial-schedule.index') }}" class="hamburger-menu-item {{ request()->routeIs('financial-schedule.*') ? 'active' : '' }}">
                <div class="menu-item-icon">
                    <i class="bi bi-calendar-event"></i>
                    @php
                        $notificationCount = \App\Models\FinancialSchedule::where('user_id', auth()->id())
                            ->where('is_confirmed', false)
                            ->where('scheduled_date', '<=', now()->addDays(7))
                            ->count();
                    @endphp
                    @if($notificationCount > 0)
                    <span class="menu-badge">{{ $notificationCount }}</span>
                    @endif
                </div>
                <div class="menu-item-content">
                    <span class="menu-item-title">Agenda</span>
                    <span class="menu-item-subtitle">Lembretes e eventos</span>
                </div>
            </a>
            <a href="{{ route('goals.index') }}" class="hamburger-menu-item {{ request()->routeIs('goals.*') ? 'active' : '' }}">
                <div class="menu-item-icon">
                    <i class="bi bi-graph-up"></i>
                </div>
                <div class="menu-item-content">
                    <span class="menu-item-title">Monitoramento</span>
                    <span class="menu-item-subtitle">Objetivos e metas</span>
                </div>
            </a>
            <a href="{{ route('books.index') }}" class="hamburger-menu-item {{ request()->routeIs('books.*') ? 'active' : '' }}">
                <div class="menu-item-icon">
                    <i class="bi bi-book"></i>
                </div>
                <div class="menu-item-content">
                    <span class="menu-item-title">Sabedoria</span>
                    <span class="menu-item-subtitle">Livros e textos</span>
                </div>
            </a>
            @endauth
        </div>
    </div>
    
    <div class="cashflow-container">
        @yield('content')
    </div>
    
    <!-- Bottom Navigation Unificada -->
    <div class="bottom-nav">
        <div class="row w-100">
            <div class="col-3 text-center">
                <a href="{{ route('cashflow.dashboard') }}" class="nav-item-custom {{ request()->routeIs('cashflow.dashboard') ? 'active' : '' }}">
                    <div class="nav-icon-custom">
                        <i class="bi bi-speedometer2"></i>
                    </div>
                    <span>Dashboard</span>
                </a>
            </div>
            <div class="col-3 text-center">
                <a href="{{ route('cashflow.transactions') }}" class="nav-item-custom {{ request()->routeIs('cashflow.transactions') ? 'active' : '' }}">
                    <div class="nav-icon-custom">
                        <i class="bi bi-list-ul"></i>
                    </div>
                    <span>Transações</span>
                </a>
            </div>
            <div class="col-3 text-center">
                <a href="{{ route('cashflow.add') }}" class="nav-item-custom {{ request()->routeIs('cashflow.add') ? 'active' : '' }}">
                    <div class="nav-icon-custom">
                        <i class="bi bi-plus-circle"></i>
                    </div>
                    <span>Adicionar</span>
                </a>
            </div>
            <div class="col-3 text-center">
                <a href="{{ route('cashflow.reports') }}" class="nav-item-custom {{ request()->routeIs('cashflow.reports') ? 'active' : '' }}">
                    <div class="nav-icon-custom">
                        <i class="bi bi-graph-up"></i>
                    </div>
                    <span>Relatórios</span>
                </a>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap 5.3 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script src="{{ asset('js/app.js') }}?v={{ filemtime(public_path('js/app.js')) }}"></script>
    
    @yield('scripts')
    @stack('scripts')
    
    </body>
</html>
