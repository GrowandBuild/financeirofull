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
        <link rel="apple-touch-icon" sizes="72x72" href="{{ asset('images/icon-72x72.png') }}">
        <link rel="apple-touch-icon" sizes="96x96" href="{{ asset('images/icon-96x96.png') }}">
        <link rel="apple-touch-icon" sizes="128x128" href="{{ asset('images/icon-128x128.png') }}">
        <link rel="apple-touch-icon" sizes="144x144" href="{{ asset('images/icon-144x144.png') }}">
        <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('images/icon-152x152.png') }}">
        <link rel="apple-touch-icon" sizes="192x192" href="{{ asset('images/icon-192x192.png') }}">
        <link rel="apple-touch-icon" sizes="384x384" href="{{ asset('images/icon-384x384.png') }}">
        <link rel="apple-touch-icon" sizes="512x512" href="{{ asset('images/icon-512x512.png') }}">
        
        <!-- Manifest PWA -->

        <title>{{ config('app.name', 'Laravel') }} - @yield('title', 'Meus Produtos')</title>
        
        <!-- Bootstrap 5.3 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        
        <!-- Bootstrap Icons -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
        
        <!-- Custom CSS -->
        <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}" id="main-css">
        
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
        
        <!-- Custom JavaScript -->
        <script src="{{ asset('js/app.js') }}?v={{ filemtime(public_path('js/app.js')) }}"></script>
        
        <!-- Estilos para Frases de Gestão Financeira (Mobile) -->
        <style>
        /* Frases de Gestão Financeira - Apenas Mobile */
        .finance-quotes-mobile {
            display: none;
            flex: 1;
            margin: 0 0.75rem;
            overflow: hidden;
            min-width: 0;
        }
        
        .quote-container {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.375rem 0.75rem;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 10px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            overflow: hidden;
            animation: fadeInQuote 0.5s ease-in-out;
            width: 100%;
            max-width: 100%;
            box-sizing: border-box;
            min-height: 2rem;
        }
        
        .quote-icon {
            color: #10b981;
            font-size: 0.875rem;
            flex-shrink: 0;
        }
        
        .quote-text-wrapper {
            flex: 1;
            min-width: 0;
            overflow: hidden;
            position: relative;
        }
        
        .quote-text {
            color: white;
            font-size: 0.7rem;
            font-weight: 500;
            opacity: 0.9;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            word-wrap: break-word;
            overflow-wrap: break-word;
            hyphens: auto;
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        /* Scroll automático quando texto não cabe em 2 linhas */
        .quote-text-wrapper.scroll-mode {
            overflow: hidden;
        }
        
        .quote-text-wrapper.scroll-mode .quote-text {
            display: inline-block;
            white-space: nowrap;
            -webkit-line-clamp: unset;
            -webkit-box-orient: unset;
            animation: scrollHorizontal 15s linear infinite;
            animation-delay: 2s;
            cursor: pointer;
            padding-right: 2rem;
        }
        
        @keyframes scrollHorizontal {
            0% {
                transform: translateX(0);
            }
            20% {
                transform: translateX(0);
            }
            80% {
                transform: translateX(var(--scroll-amount, -200px));
            }
            100% {
                transform: translateX(0);
            }
        }
        
        @keyframes fadeInQuote {
            from {
                opacity: 0;
                transform: translateX(-10px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        @media (max-width: 768px) {
            .finance-quotes-mobile {
                display: flex;
            }
            
            .system-switcher {
                display: flex;
                align-items: center;
            }
        }
        
        @media (max-width: 480px) {
            .quote-text {
                font-size: 0.65rem;
                max-width: 150px;
            }
            
            .quote-icon {
                font-size: 0.75rem;
            }
            
            .quote-container {
                padding: 0.25rem 0.5rem;
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
    
    <!-- PWA Service Worker DESABILITADO - estava causando problemas -->
    <!-- Service Worker desabilitado para evitar interferências com navegação e cliques -->
    <script>
        // Service Worker desabilitado
        // Desregistrar qualquer Service Worker existente
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.getRegistrations().then(function(registrations) {
                for(let registration of registrations) {
                    registration.unregister().then(function(boolean) {
                        console.log('Service Worker desregistrado:', boolean);
                    });
                }
            });
        }
        
        // REFATORAÇÃO COMPLETA - Sistema limpo sem interceptações problemáticas
        document.addEventListener('DOMContentLoaded', function() {
            // PROTEÇÃO: Garantir que formulários NUNCA sejam interceptados
            // Não adicionar NENHUM listener em formulários ou seus botões
            
            // PROTEÇÃO: Garantir que links da navegação inferior funcionem
            // Usar delegation simples e específico apenas para links de navegação
            const bottomNav = document.querySelector('.bottom-nav');
            if (bottomNav) {
                // Apenas para links da navegação - não interfere com nada mais
                bottomNav.addEventListener('click', function(e) {
                    const clickedElement = e.target;
                    // Verificar se é um link da navegação
                    if (clickedElement.tagName === 'A' && clickedElement.classList.contains('nav-item-custom')) {
                        // Link da navegação - permitir comportamento padrão
                        // Não fazer nada - deixar o navegador processar normalmente
                        return true;
                    }
                    // Se não for link de navegação, permitir comportamento padrão
                    return true;
                }, false);
            }
        });
    </script>
    
    <!-- Script para Rotacionar Frases de Gestão Financeira -->
    <script>
    (function() {
        const financeQuotes = [
            'Pague-se primeiro: guarde pelo menos 10% da sua renda',
            'Não gaste mais do que você ganha',
            'Planeje cada compra antes de executá-la',
            'Tenha uma reserva de emergência',
            'Invista em conhecimento, é o melhor ativo',
            'Evite dívidas desnecessárias',
            'Controle seus gastos diariamente',
            'Estabeleça metas financeiras claras',
            'Compare preços antes de comprar',
            'Priorize necessidades sobre desejos',
            'Registre todas as suas transações',
            'Revise seus gastos mensalmente',
            'Aprenda a dizer não a compras impulsivas',
            'Construa múltiplas fontes de renda',
            'Pense a longo prazo, mas comece pequeno'
        ];
        
        const quoteText = document.getElementById('financeQuoteText');
        const quoteContainer = document.getElementById('financeQuoteContainer');
        
        if (quoteText && quoteContainer) {
            let currentQuoteIndex = Math.floor(Math.random() * financeQuotes.length);
            
            function updateQuoteText() {
                quoteText.textContent = financeQuotes[currentQuoteIndex];
                
                // Verifica se o texto precisa de scroll
                setTimeout(() => {
                    const wrapper = quoteText.parentElement;
                    const containerWidth = wrapper.offsetWidth;
                    
                    // Primeiro verifica se cabe em 2 linhas
                    quoteText.style.display = '-webkit-box';
                    const textHeight = quoteText.scrollHeight;
                    const containerHeight = wrapper.offsetHeight;
                    
                    // Se não couber em 2 linhas ou for muito largo, ativa scroll
                    if (textHeight > containerHeight * 2.5) {
                        quoteText.style.display = 'inline-block';
                        const textWidth = quoteText.scrollWidth;
                        
                        if (textWidth > containerWidth) {
                            wrapper.classList.add('scroll-mode');
                            // Calcula o deslocamento necessário
                            const scrollAmount = textWidth - containerWidth + 20; // 20px de margem
                            quoteText.style.setProperty('--scroll-amount', `-${scrollAmount}px`);
                        } else {
                            wrapper.classList.remove('scroll-mode');
                        }
                    } else {
                        quoteText.style.display = '-webkit-box';
                        wrapper.classList.remove('scroll-mode');
                    }
                }, 200);
            }
            
            updateQuoteText();
            
            // Função para trocar frase com animação
            function rotateQuote() {
                if (quoteContainer) {
                    const wrapper = quoteText.parentElement;
                    wrapper.classList.remove('scroll-mode');
                    quoteContainer.style.opacity = '0';
                    quoteContainer.style.transform = 'translateX(-10px)';
                    
                    setTimeout(() => {
                        currentQuoteIndex = (currentQuoteIndex + 1) % financeQuotes.length;
                        updateQuoteText();
                        
                        quoteContainer.style.opacity = '1';
                        quoteContainer.style.transform = 'translateX(0)';
                    }, 300);
                }
            }
            
            // Trocar frase a cada 8 segundos
            setInterval(rotateQuote, 8000);
        }
    })();
    </script>
        
    @yield('scripts')
    @stack('scripts')
</body>
</html>
