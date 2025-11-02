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
        <link rel="manifest" href="{{ asset('manifest.json') }}">

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
        
        <!-- Offline Storage -->
        <script src="{{ asset('js/offline-storage.js') }}?v={{ filemtime(public_path('js/offline-storage.js')) }}"></script>
        
        <!-- Mobile Fixes (deve vir após offline-storage) -->
        <script src="{{ asset('js/mobile-offline-fix.js') }}?v={{ filemtime(public_path('js/mobile-offline-fix.js')) }}"></script>
        
        <!-- Offline Forms Interceptor -->
        <script src="{{ asset('js/offline-forms.js') }}?v={{ filemtime(public_path('js/offline-forms.js')) }}"></script>
        
        <!-- Custom JavaScript -->
        <script src="{{ asset('js/app.js') }}?v={{ filemtime(public_path('js/app.js')) }}"></script>
        
        <!-- Verificar inicialização do OfflineStorage -->
        <script>
            // Aguardar inicialização do OfflineStorage
            document.addEventListener('DOMContentLoaded', () => {
                setTimeout(() => {
                    if (window.offlineStorage) {
                        console.log('✅ OfflineStorage inicializado:', window.offlineStorage);
                        console.log('📡 Status:', window.offlineStorage.isOnlineStatus() ? 'Online' : 'Offline');
                    } else {
                        console.error('❌ OfflineStorage não está disponível!');
                        console.log('Verifique se o arquivo public/js/offline-storage.js existe e está sendo carregado.');
                    }
                }, 1000);
            });
        </script>
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
    
    <!-- PWA Service Worker Registration -->
    <script>
        // Registrar Service Worker
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', async () => {
                try {
                    const registration = await navigator.serviceWorker.register('/sw.js');
                    console.log('Service Worker registrado:', registration.scope);
                    
                    // Verificar atualizações
                    registration.addEventListener('updatefound', () => {
                        const newWorker = registration.installing;
                        newWorker.addEventListener('statechange', () => {
                            if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                                // Nova versão disponível
                                if (confirm('Nova versão disponível! Deseja atualizar?')) {
                                    newWorker.postMessage({ type: 'SKIP_WAITING' });
                                    window.location.reload();
                                }
                            }
                        });
                    });
                } catch (error) {
                    console.error('Erro ao registrar Service Worker:', error);
                }
            });
        }
        
        // Detectar quando o app pode ser instalado (PWA Install Prompt)
        let deferredPrompt;
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            
            // Mostrar botão de instalação (opcional)
            showInstallButton();
        });
        
        function showInstallButton() {
            // Criar botão de instalação se não existir
            if (!document.getElementById('install-button')) {
                const installBtn = document.createElement('button');
                installBtn.id = 'install-button';
                installBtn.innerHTML = '<i class="bi bi-download"></i> Instalar App';
                installBtn.style.cssText = `
                    position: fixed;
                    bottom: 80px;
                    right: 20px;
                    background: #10b981;
                    color: white;
                    border: none;
                    padding: 12px 20px;
                    border-radius: 25px;
                    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                    cursor: pointer;
                    z-index: 9999;
                    font-weight: 600;
                    transition: all 0.3s;
                `;
                installBtn.onclick = async () => {
                    if (deferredPrompt) {
                        deferredPrompt.prompt();
                        const { outcome } = await deferredPrompt.userChoice;
                        console.log(`Instalação: ${outcome}`);
                        deferredPrompt = null;
                        installBtn.remove();
                    }
                };
                installBtn.onmouseover = () => {
                    installBtn.style.background = '#059669';
                };
                installBtn.onmouseout = () => {
                    installBtn.style.background = '#10b981';
                };
                document.body.appendChild(installBtn);
            }
        }
        
        // Quando o app é instalado
        window.addEventListener('appinstalled', () => {
            console.log('App instalado com sucesso!');
            const installBtn = document.getElementById('install-button');
            if (installBtn) {
                installBtn.remove();
            }
        });
    </script>
    
    @yield('scripts')
    @stack('scripts')
</body>
</html>
