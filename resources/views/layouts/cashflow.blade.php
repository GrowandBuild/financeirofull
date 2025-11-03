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
    
    <!-- Font Awesome para ícones adicionais -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    
    <!-- Custom CSS para Fluxo de Caixa -->
    <style>
        body {
            background: linear-gradient(135deg, #1e3a8a, #1e40af, #3b82f6) !important;
        }
        
        .cashflow-container {
            background: linear-gradient(135deg, #1e3a8a, #1e40af, #3b82f6);
            padding: 0 0.75rem 80px;
            min-height: 100vh;
        }
        
        /* Compactar cards de resumo para 2 colunas em mobile */
        @media (max-width: 768px) {
            .col-md-4 {
                flex: 0 0 50%;
                max-width: 50%;
                padding-left: 0.5rem;
                padding-right: 0.5rem;
            }
        }
        
        @media (max-width: 480px) {
            .col-md-4 {
                padding-left: 0.25rem;
                padding-right: 0.25rem;
            }
        }
        
        .card-cashflow {
            background: rgba(255, 255, 255, 0.1) !important;
            backdrop-filter: blur(10px);
            border-radius: 1rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            transition: all 0.3s ease;
        }
        
        .card-cashflow:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.2);
        }
        
        .btn-cashflow {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            border: none;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
        }
        
        .btn-cashflow:hover {
            background: linear-gradient(135deg, #1d4ed8, #1e40af);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
            color: white;
        }
        
        .btn-income {
            background: linear-gradient(135deg, #10b981, #059669);
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        }
        
        .btn-income:hover {
            background: linear-gradient(135deg, #059669, #047857);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
        }
        
        .btn-expense {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
        }
        
        .btn-expense:hover {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            box-shadow: 0 8px 25px rgba(239, 68, 68, 0.4);
        }
        
        .bottom-nav-cashflow {
            position: fixed !important;
            bottom: 0 !important;
            left: 0 !important;
            right: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
            background: rgba(30, 58, 138, 0.95) !important;
            backdrop-filter: blur(10px);
            padding: 0.5rem 0.5rem !important;
            z-index: 1000 !important;
            border-top: 1px solid rgba(255, 255, 255, 0.1) !important;
            box-sizing: border-box !important;
        }
        
        .nav-item-cashflow {
            text-decoration: none;
            color: white;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-size: 0.625rem;
            padding: 0.25rem;
            min-height: 60px;
        }
        
        .nav-item-cashflow:hover {
            color: #fbbf24;
            transform: translateY(-2px);
        }
        
        .nav-item-cashflow.active {
            color: #fbbf24;
        }
        
        .nav-item-cashflow.active .nav-icon-cashflow {
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
            color: #1e3a8a;
            box-shadow: 0 4px 15px rgba(251, 191, 36, 0.3);
        }
        
        .nav-icon-cashflow {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 0.5rem;
            padding: 0.5rem;
            margin-bottom: 0.25rem;
            width: 2rem;
            height: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.875rem;
            transition: all 0.3s ease;
        }
        
        /* Responsive bottom nav */
        @media (max-width: 575px) {
            .bottom-nav-cashflow {
                padding: 0.5rem 0.25rem !important;
            }
            
            .nav-item-cashflow {
                font-size: 0.5rem;
                min-height: 55px;
                padding: 0.125rem;
            }
            
            .nav-icon-cashflow {
                width: 1.75rem;
                height: 1.75rem;
                font-size: 0.75rem;
                padding: 0.375rem;
                margin-bottom: 0.125rem;
            }
        }
        
        @media (min-width: 576px) {
            .bottom-nav-cashflow {
                padding: 0.5rem 2rem !important;
            }
        }
        
        @media (min-width: 768px) {
            .bottom-nav-cashflow {
                padding: 0.75rem 3rem !important;
            }
            
            .nav-item-cashflow {
                font-size: 0.75rem;
            }
            
            .nav-icon-cashflow {
                width: 2rem;
                height: 2rem;
                font-size: 1rem;
            }
        }
        
        @media (min-width: 1024px) {
            .bottom-nav-cashflow {
                padding: 1rem 4rem !important;
            }
            
            .nav-item-cashflow {
                font-size: 0.875rem;
            }
            
            .nav-icon-cashflow {
                width: 2.25rem;
                height: 2.25rem;
                font-size: 1.125rem;
            }
        }
        
        @media (min-width: 1280px) {
            .bottom-nav-cashflow {
                padding: 1rem 6rem !important;
            }
        }
    </style>
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
    
    <!-- Bottom Navigation -->
    <div class="bottom-nav-cashflow">
        <div class="row w-100">
            <div class="col-3 text-center">
                <a href="{{ route('cashflow.dashboard') }}" class="nav-item-cashflow {{ request()->routeIs('cashflow.dashboard') ? 'active' : '' }}">
                    <div class="nav-icon-cashflow">
                        <i class="bi bi-speedometer2"></i>
                    </div>
                    <span>Dashboard</span>
                </a>
            </div>
            <div class="col-3 text-center">
                <a href="{{ route('cashflow.transactions') }}" class="nav-item-cashflow {{ request()->routeIs('cashflow.transactions') ? 'active' : '' }}">
                    <div class="nav-icon-cashflow">
                        <i class="bi bi-list-ul"></i>
                    </div>
                    <span>Transações</span>
                </a>
            </div>
            <div class="col-3 text-center">
                <a href="{{ route('cashflow.add') }}" class="nav-item-cashflow {{ request()->routeIs('cashflow.add') ? 'active' : '' }}">
                    <div class="nav-icon-cashflow">
                        <i class="bi bi-plus-circle"></i>
                    </div>
                    <span>Adicionar</span>
                </a>
            </div>
            <div class="col-3 text-center">
                <a href="{{ route('cashflow.reports') }}" class="nav-item-cashflow {{ request()->routeIs('cashflow.reports') ? 'active' : '' }}">
                    <div class="nav-icon-cashflow">
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
