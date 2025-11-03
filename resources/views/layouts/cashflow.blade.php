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
    
    <!-- Chart.js para gráficos -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Font Awesome para ícones adicionais -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    
    <!-- Custom CSS para Fluxo de Caixa -->
    <style>
        body {
            background: linear-gradient(135deg, #1e3a8a, #1e40af, #3b82f6) !important;
            color: white;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }
        
        .cashflow-container {
            max-width: 100%;
            width: 100%;
            margin: 0;
            min-height: 100vh;
            background: linear-gradient(135deg, #1e3a8a, #1e40af, #3b82f6);
            position: relative;
            padding-bottom: 80px;
            box-sizing: border-box;
        }
        
        .header-cashflow {
            background: rgba(30, 58, 138, 0.9) !important;
            backdrop-filter: blur(10px);
            padding: 0.75rem 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            position: relative;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .online-indicator {
            position: fixed;
            top: 1rem;
            right: 1rem;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 600;
            z-index: 1000;
            transition: all 0.3s ease;
        }
        
        .online-indicator.online {
            background: #10b981;
            color: white;
        }
        
        .online-indicator.offline {
            background: #ef4444;
            color: white;
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
        
        .btn-gold {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
        }
        
        .btn-gold:hover {
            background: linear-gradient(135deg, #d97706, #b45309);
            box-shadow: 0 8px 25px rgba(245, 158, 11, 0.4);
        }
        
        .bottom-nav-cashflow {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            width: 100%;
            max-width: 100%;
            background: rgba(30, 58, 138, 0.95) !important;
            backdrop-filter: blur(10px);
            padding: 0.5rem 0.5rem;
            z-index: 1000;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            box-sizing: border-box;
        }
        
        .nav-item-cashflow {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: white;
            font-size: 0.625rem;
            padding: 0.25rem;
            min-height: 60px;
            transition: all 0.3s ease;
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
        
        .system-switcher {
            position: fixed;
            top: 1rem;
            left: 1rem;
            z-index: 1000;
        }
        
        .system-switcher .btn {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .system-switcher .btn:hover {
            background: rgba(255, 255, 255, 0.2);
            color: #fbbf24;
            transform: translateY(-1px);
        }
        
        .system-switcher .btn.active {
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
            color: #1e3a8a;
            border-color: #fbbf24;
        }
        
        /* Responsive adjustments */
        @media (max-width: 575px) {
            .bottom-nav-cashflow {
                padding: 0.5rem 0.25rem;
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
                padding: 0.5rem 2rem;
            }
        }
        
        @media (min-width: 768px) {
            .bottom-nav-cashflow {
                padding: 0.75rem 3rem;
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
                padding: 1rem 4rem;
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
                padding: 1rem 6rem;
            }
        }
        
        /* Header Fixo */
        .fixed-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: linear-gradient(135deg, #1f2937, #374151);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            z-index: 1100;
            padding: 1rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .header-title h1 {
            color: white;
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
        }
        
        /* Sub-Header Integrado */
        .sub-header-compact {
            position: fixed;
            top: calc(1rem + 1.5rem + 1rem + 0.25rem); /* header + pequeno espaçamento */
            left: 0;
            right: 0;
            background: linear-gradient(135deg, #374151, #4b5563);
            border-top: none;
            border-bottom: 1px solid rgba(255, 255, 255, 0.15);
            z-index: 1050;
            padding: 0.75rem 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 3rem;
            box-sizing: border-box;
            backdrop-filter: blur(10px);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin: 0;
            transform: translateY(0);
        }
        
        .sub-header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .nav-buttons {
            display: flex;
            gap: 0.5rem;
        }
        
        .nav-btn {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            padding: 0.35rem 0.75rem;
            border-radius: 0.375rem;
            font-size: 0.8rem;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.375rem;
            height: 1.75rem;
            box-sizing: border-box;
        }
        
        .nav-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            color: #10b981;
            transform: translateY(-1px);
        }
        
        .nav-btn.active {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            border-color: #10b981;
        }
        
        .header-controls {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .status-indicator {
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 600;
            transition: all 0.3s ease;
            background: #10b981;
            color: white;
            position: relative !important;
            top: auto !important;
            right: auto !important;
            left: auto !important;
            z-index: auto !important;
        }
        
        .status-indicator.offline {
            background: #ef4444;
        }
        
        .cache-btn {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            padding: 0.5rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .cache-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-1px);
        }
        
        .cache-btn.dev:hover {
            background: rgba(245, 158, 11, 0.8);
            border-color: #f59e0b;
        }
        
        /* Ajustar padding do body para o novo layout integrado */
        body {
            padding-top: calc(1rem + 1.5rem + 1rem + 0.25rem + 3rem) !important; /* Header + espaçamento + Sub-header */
        }
        
        /* Esconder painéis antigos que causam sobreposição */
        .cache-control-panel,
        .simple-cache-control {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
            pointer-events: none !important;
        }
        
        /* Debug - garantir que o sub-header seja visível */
        .sub-header * {
            display: block !important;
            visibility: visible !important;
        }
        
        .nav-btn {
            display: flex !important;
            visibility: visible !important;
        }
    </style>
    </head>
<body>
    <!-- Header Fixo -->
    <header class="fixed-header">
        <div class="header-content">
            <div class="header-title">
                <h1>@yield('title', 'Fluxo de Caixa')</h1>
            </div>
        </div>
    </header>
    
    <!-- Sub-Header Compacto -->
    <div class="sub-header-compact">
        <div class="nav-buttons">
            <a href="{{ route('products.index') }}" class="nav-btn {{ request()->routeIs('products.*') ? 'active' : '' }}">
                <i class="bi bi-box-seam"></i> Produtos
            </a>
            <a href="{{ route('cashflow.dashboard') }}" class="nav-btn {{ request()->routeIs('cashflow.*') ? 'active' : '' }}">
                <i class="bi bi-cash-coin"></i> Fluxo de Caixa
            </a>
        </div>
        
        <div class="header-controls">
            <div id="online-status" class="status-indicator">
                <i class="bi bi-wifi"></i> Online
            </div>
            <button onclick="clearAllCachesSimple()" class="cache-btn" title="Limpar Cache">
                <i class="bi bi-arrow-clockwise"></i>
            </button>
            <button onclick="toggleDevModeSimple()" class="cache-btn dev" id="simpleDevBtn" title="Modo Dev">
                <i class="bi bi-code-slash"></i>
            </button>
        </div>
    </div>
    
    <div class="cashflow-container">
        @yield('content')
        
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
    </div>
    
    <!-- Bootstrap 5.3 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    @yield('scripts')
    
    </body>
</html>
