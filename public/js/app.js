/**
 * MEUS PRODUTOS - JavaScript Otimizado
 * Performance e funcionalidades essenciais
 */

// Cache de elementos DOM
const DOMCache = {
    searchInput: null,
    productGrid: null,
    init() {
        this.searchInput = document.querySelector('.premium-search-input');
        this.productGrid = document.querySelector('.premium-product-grid');
    }
};

// Performance Monitor
const PerformanceMonitor = {
    startTime: 0,
    start() {
        this.startTime = performance.now();
    },
    end(label = 'Operation') {
        const duration = performance.now() - this.startTime;
        console.log(`${label} took ${duration.toFixed(2)}ms`);
        return duration;
    }
};

// Service Worker Manager
const ServiceWorkerManager = {
    async register() {
        if ('serviceWorker' in navigator) {
            try {
                const registration = await navigator.serviceWorker.register('/sw.js?v=5');
                console.log('Service Worker registrado:', registration.scope);
                
                // Escutar mensagens do Service Worker
                navigator.serviceWorker.addEventListener('message', (event) => {
                    if (event.data && event.data.type === 'SW_UPDATED') {
                        console.log('Service Worker atualizado para:', event.data.version);
                        this.forceReloadAssets();
                    }
                });
                
                // Verificar atualizações imediatamente
                if (registration.waiting) {
                    // Service Worker já está aguardando atualização
                    console.log('Service Worker aguardando atualização, forçando ativação...');
                    registration.waiting.postMessage({ type: 'SKIP_WAITING' });
                }
                
                // Verificar se há nova versão disponível
                registration.addEventListener('updatefound', () => {
                    const newWorker = registration.installing;
                    newWorker.addEventListener('statechange', () => {
                        if (newWorker.state === 'installed') {
                            if (navigator.serviceWorker.controller) {
                                // Há uma nova versão, mas ainda não está ativa
                                console.log('Nova versão do Service Worker instalada!');
                                // Forçar ativação imediata
                                newWorker.postMessage({ type: 'SKIP_WAITING' });
                                // Recarregar após ativação
                                newWorker.addEventListener('statechange', () => {
                                    if (newWorker.state === 'activated') {
                                        console.log('Service Worker ativado, recarregando página...');
                                        window.location.reload();
                                    }
                                });
                            } else {
                                // Primeira instalação
                                console.log('Service Worker instalado pela primeira vez!');
                            }
                        }
                    });
                });
                
                // Verificar periodicamente por atualizações
                setInterval(() => {
                    registration.update();
                }, 60000); // A cada 1 minuto
                
                return registration;
            } catch (error) {
                console.error('Erro ao registrar Service Worker:', error);
            }
        }
    },
    
    forceReloadAssets() {
        // Forçar recarregamento do CSS
        const cssLink = document.getElementById('main-css') || document.querySelector('link[href*="app.css"]');
        if (cssLink) {
            const href = cssLink.href.split('?')[0];
            const timestamp = new Date().getTime();
            cssLink.href = `${href}?v=${timestamp}`;
            console.log('CSS recarregado:', cssLink.href);
        }
        
        // Limpar cache de CSS/JS no Service Worker
        if ('serviceWorker' in navigator && navigator.serviceWorker.controller) {
            navigator.serviceWorker.controller.postMessage({
                type: 'CLEAR_ASSET_CACHE'
            });
        }
    },
    
    showUpdateNotification() {
        // Forçar atualização automaticamente
        console.log('Atualizando Service Worker automaticamente...');
        if ('serviceWorker' in navigator && navigator.serviceWorker.controller) {
            navigator.serviceWorker.controller.postMessage({ type: 'SKIP_WAITING' });
            // Recarregar após um pequeno delay
            setTimeout(() => {
                window.location.reload();
            }, 500);
        }
    }
};


// Search Manager
const SearchManager = {
    debounceTimer: null,
    cache: new Map(),
    
    init() {
        if (DOMCache.searchInput) {
            DOMCache.searchInput.addEventListener('input', (e) => {
                this.handleSearch(e.target.value);
            });
        }
    },
    
    handleSearch(query) {
        clearTimeout(this.debounceTimer);
        
        if (query.length < 2) {
            this.clearResults();
            return;
        }
        
        this.debounceTimer = setTimeout(() => {
            this.performSearch(query);
        }, 300);
    },
    
    async performSearch(query) {
        PerformanceMonitor.start();
        
        // Verificar cache primeiro
        if (this.cache.has(query)) {
            this.displayResults(this.cache.get(query));
            PerformanceMonitor.end('Search (cached)');
            return;
        }
        
        try {
            const response = await fetch(`/products/search?q=${encodeURIComponent(query)}&ajax=1`);
            const data = await response.json();
            
            // Cache do resultado
            this.cache.set(query, data);
            
            this.displayResults(data);
            PerformanceMonitor.end('Search (API)');
        } catch (error) {
            console.error('Erro na busca:', error);
        }
    },
    
    displayResults(data) {
        // Implementar exibição dos resultados
        console.log('Resultados da busca:', data);
    },
    
    clearResults() {
        // Limpar resultados da busca
    }
};

// Image Lazy Loading
const LazyImageLoader = {
    observer: null,
    
    init() {
        if ('IntersectionObserver' in window) {
            this.observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        this.loadImage(entry.target);
                    }
                });
            }, {
                rootMargin: '50px 0px',
                threshold: 0.1
            });
            
            this.observeImages();
        }
    },
    
    observeImages() {
        const images = document.querySelectorAll('img[data-src]');
        images.forEach(img => this.observer.observe(img));
    },
    
    loadImage(img) {
        img.src = img.dataset.src;
        img.classList.remove('lazy');
        this.observer.unobserve(img);
    }
};

// Cache Manager
const CacheManager = {
    storage: localStorage,
    prefix: 'meus_produtos_',
    
    set(key, value, ttl = 300000) { // 5 minutos por padrão
        const item = {
            value,
            timestamp: Date.now(),
            ttl
        };
        this.storage.setItem(this.prefix + key, JSON.stringify(item));
    },
    
    get(key) {
        const item = this.storage.getItem(this.prefix + key);
        if (!item) return null;
        
        const parsed = JSON.parse(item);
        const now = Date.now();
        
        if (now - parsed.timestamp > parsed.ttl) {
            this.storage.removeItem(this.prefix + key);
            return null;
        }
        
        return parsed.value;
    },
    
    clear() {
        const keys = Object.keys(this.storage);
        keys.forEach(key => {
            if (key.startsWith(this.prefix)) {
                this.storage.removeItem(key);
            }
        });
    }
};

// Animation Manager
const AnimationManager = {
    animateIn(element, animation = 'fadeIn') {
        element.style.opacity = '0';
        element.style.transform = 'translateY(20px)';
        
        requestAnimationFrame(() => {
            element.style.transition = 'all 0.3s ease';
            element.style.opacity = '1';
            element.style.transform = 'translateY(0)';
        });
    },
    
    staggerIn(elements, delay = 100) {
        elements.forEach((element, index) => {
            setTimeout(() => {
                this.animateIn(element);
            }, index * delay);
        });
    }
};

// Error Handler
const ErrorHandler = {
    init() {
        window.addEventListener('error', (event) => {
            console.error('Erro JavaScript:', event.error);
            this.reportError(event.error);
        });
        
        window.addEventListener('unhandledrejection', (event) => {
            console.error('Promise rejeitada:', event.reason);
            this.reportError(event.reason);
        });
    },
    
    reportError(error) {
        // Implementar relatório de erros
        console.log('Erro reportado:', error);
    }
};

// Performance Optimizations
const PerformanceOptimizer = {
    prefetchedPages: new Set(),
    
    init() {
        this.preloadCriticalResources();
        this.optimizeImages();
        // this.setupPrefetching(); // DESABILITADO - pode interferir
        // this.setupPagePrefetching(); // DESABILITADO - pode interferir (adiciona event listeners que podem causar problemas)
        this.optimizePageTransitions(); // Apenas setupLoadingStates, sem navegação AJAX
    },
    
    preloadCriticalResources() {
        // Preload de recursos críticos
        const criticalResources = [
            '/css/app.css',
            '/js/app.js'
        ];
        
        criticalResources.forEach(resource => {
            const link = document.createElement('link');
            link.rel = 'preload';
            link.href = resource;
            link.as = resource.endsWith('.css') ? 'style' : 'script';
            document.head.appendChild(link);
        });
    },
    
    optimizeImages() {
        // Converter imagens para WebP se suportado
        if (this.supportsWebP()) {
            const images = document.querySelectorAll('img[src$=".jpg"], img[src$=".png"]');
            images.forEach(img => {
                const webpSrc = img.src.replace(/\.(jpg|png)$/, '.webp');
                const webpImg = new Image();
                webpImg.onload = () => {
                    img.src = webpSrc;
                };
                webpImg.src = webpSrc;
            });
        }
    },
    
    supportsWebP() {
        const canvas = document.createElement('canvas');
        canvas.width = 1;
        canvas.height = 1;
        return canvas.toDataURL('image/webp').indexOf('data:image/webp') === 0;
    },
    
    setupPrefetching() {
        // Prefetch de páginas prováveis
        const prefetchLinks = [
            '/products/search',
            '/products/compra'
        ];
        
        prefetchLinks.forEach(link => {
            const prefetchLink = document.createElement('link');
            prefetchLink.rel = 'prefetch';
            prefetchLink.href = link;
            document.head.appendChild(prefetchLink);
        });
    },
    
    // setupPagePrefetching() DESABILITADO - estava adicionando listeners em todos os links
    // Isso poderia interferir com formulários e outros elementos
    setupPagePrefetching() {
        // DESABILITADO - não adicionar listeners que possam interferir
        return;
    },
    
    prefetchPage(url) {
        // Verificar se já foi prefetchado
        if (this.prefetchedPages.has(url)) return;
        
        // Prefetch da página
        const prefetchLink = document.createElement('link');
        prefetchLink.rel = 'prefetch';
        prefetchLink.href = url;
        prefetchLink.onload = () => {
            this.prefetchedPages.add(url);
            console.log('Página prefetchada:', url);
        };
        document.head.appendChild(prefetchLink);
    },
    
    optimizePageTransitions() {
        // NAVEGAÇÃO AJAX DESABILITADA - usando navegação normal do navegador
        // Isso evita problemas com event listeners e funcionalidades que dependem de recarregamento da página
        // Transições suaves entre páginas
        // this.setupPageTransition(); // DESABILITADO - causava problemas
        this.setupLoadingStates();
    },
    
    // setupPageTransition() DESABILITADO - navegação AJAX causava problemas
    // Todos os links agora usam navegação normal do navegador (window.location.href)
    setupPageTransition() {
        // CÓDIGO DESABILITADO - usando navegação normal do navegador
        // A navegação AJAX estava interferindo com event listeners e funcionalidades
        return;
    },
    
    // Navegação AJAX DESABILITADA - sempre usar navegação normal do navegador
    async navigateToPage(url) {
        // Navegação AJAX desabilitada - usar sempre navegação normal
        // Isso evita problemas com event listeners e funcionalidades
        window.location.href = url;
    },
    
    // Métodos de cache DESABILITADOS - estavam causando problemas
    async getFromCache(url) {
        // Cache desabilitado - retornar null para usar sempre navegação normal
        return null;
    },
    
    async cachePage(url, response) {
        // Cache de páginas desabilitado - não fazer nada
        return;
    },
    
    updatePageContent(html) {
        // Extrair apenas o conteúdo principal
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        
        // Atualizar conteúdo
        const newContent = doc.querySelector('.premium-content') || doc.querySelector('.mobile-container');
        const currentContent = document.querySelector('.premium-content') || document.querySelector('.mobile-container');
        
        if (newContent && currentContent) {
            currentContent.innerHTML = newContent.innerHTML;
        }
        
        // Re-executar scripts se necessário
        this.reinitializeComponents();
    },
    
    reinitializeComponents() {
        // Re-inicializar componentes após mudança de página
        if (window.MeusProdutos && window.MeusProdutos.App) {
            window.MeusProdutos.App.animatePageElements();
        }
    },
    
    showPageLoading() {
        // Criar indicador de loading
        if (!document.getElementById('page-loading')) {
            const loading = document.createElement('div');
            loading.id = 'page-loading';
            loading.innerHTML = `
                <div style="
                    position: fixed;
                    top: 0;
                    left: 0;
                    right: 0;
                    height: 3px;
                    background: linear-gradient(90deg, #10b981, #059669);
                    z-index: 9999;
                    animation: loading 1s ease-in-out infinite;
                "></div>
                <style>
                    @keyframes loading {
                        0% { transform: translateX(-100%); }
                        100% { transform: translateX(100%); }
                    }
                </style>
            `;
            document.body.appendChild(loading);
        }
    },
    
    hidePageLoading() {
        const loading = document.getElementById('page-loading');
        if (loading) {
            loading.remove();
        }
    }
};

// Main App Initializer
const App = {
    init() {
        PerformanceMonitor.start();
        
        // Inicializar componentes
        DOMCache.init();
        SearchManager.init();
        LazyImageLoader.init();
        ErrorHandler.init();
        PerformanceOptimizer.init(); // Reativado, mas navegação AJAX está desabilitada
        HamburgerMenuManager.init();
        
        // Service Worker completamente desabilitado - estava causando problemas
        // ServiceWorkerManager.register(); // DESABILITADO
        
        // Animar elementos na página
        this.animatePageElements();
        
        PerformanceMonitor.end('App Initialization');
    },
    
    animatePageElements() {
        const cards = document.querySelectorAll('.premium-product-card, .top-product-card');
        if (cards.length > 0) {
            AnimationManager.staggerIn(Array.from(cards), 50);
        }
    }
};

// Função para forçar verificação de CSS em produção
function forceReloadCSS() {
    const cssLink = document.getElementById('main-css') || document.querySelector('link[href*="app.css"]');
    if (cssLink) {
        // Verificar se CSS está carregado corretamente
        setTimeout(() => {
            // Verificar se elementos críticos estão visíveis
            const hamburger = document.getElementById('hamburgerMenu');
            const menuPanel = document.querySelector('.hamburger-menu-panel');
            
            if (hamburger && menuPanel) {
                const hamburgerStyle = window.getComputedStyle(hamburger);
                const panelStyle = window.getComputedStyle(menuPanel);
                
                // Se estiver visível quando não deveria estar ou vice-versa
                const shouldBeHidden = !menuPanel.classList.contains('active');
                const isHidden = panelStyle.visibility === 'hidden' || panelStyle.left === '-100%' || panelStyle.left.includes('-100%');
                
                if ((shouldBeHidden && !isHidden) || (!shouldBeHidden && isHidden)) {
                    // CSS pode estar desatualizado
                    console.warn('CSS pode estar desatualizado, forçando recarregamento...');
                    const href = cssLink.href.split('?')[0];
                    const timestamp = new Date().getTime();
                    cssLink.href = `${href}?v=${timestamp}`;
                    
                    // Também limpar cache no Service Worker
                    if ('serviceWorker' in navigator && navigator.serviceWorker.controller) {
                        navigator.serviceWorker.controller.postMessage({
                            type: 'CLEAR_ASSET_CACHE'
                        });
                    }
                }
            }
        }, 1500);
    }
}

// Inicializar quando DOM estiver pronto
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        App.init();
        forceReloadCSS();
    });
} else {
    App.init();
    forceReloadCSS();
}

// Cache Simple Functions
function clearAllCachesSimple() {
    if (confirm('Tem certeza que deseja limpar todos os caches?')) {
        try {
            // Limpar localStorage
            localStorage.clear();
            
            // Limpar caches do Service Worker
            if ('caches' in window) {
                caches.keys().then(names => {
                    names.forEach(name => {
                        caches.delete(name);
                    });
                });
            }
            
            // Limpar sessionStorage
            sessionStorage.clear();
            
            alert('Todos os caches foram limpos com sucesso!');
            window.location.reload();
        } catch (error) {
            console.error('Erro ao limpar caches:', error);
            alert('Erro ao limpar caches. Por favor, tente novamente.');
        }
    }
}

// Dev Mode Toggle
let devMode = false;

function toggleDevModeSimple() {
    devMode = !devMode;
    const btn = document.getElementById('simpleDevBtn');
    
    if (devMode) {
        btn.classList.add('active');
        btn.style.background = 'rgba(245, 158, 11, 0.8)';
        console.log('%c🔧 MODO DEV ATIVADO', 'font-weight: bold; font-size: 16px; color: #f59e0b;');
        console.log('Cache Manager:', window.MeusProdutos?.CacheManager);
        console.log('Performance Monitor:', window.MeusProdutos?.PerformanceMonitor);
    } else {
        btn.classList.remove('active');
        btn.style.background = '';
        console.log('%c✅ MODO DEV DESATIVADO', 'font-weight: bold; font-size: 16px; color: #10b981;');
    }
}

// Hamburger Menu Manager
const HamburgerMenuManager = {
    isOpen: false,
    
    init() {
        // Fechar menu ao clicar fora ou ao pressionar ESC
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.isOpen) {
                this.close();
            }
        });
        
        // Fechar menu ao clicar em um link
        const menuItems = document.querySelectorAll('.hamburger-menu-item');
        menuItems.forEach(item => {
            item.addEventListener('click', () => {
                setTimeout(() => this.close(), 200);
            });
        });
    },
    
    toggle() {
        if (this.isOpen) {
            this.close();
        } else {
            this.open();
        }
    },
    
    open() {
        const menu = document.getElementById('hamburgerMenu');
        const panel = document.getElementById('hamburgerMenuPanel');
        const overlay = document.getElementById('hamburgerOverlay');
        
        if (menu && panel && overlay) {
            menu.classList.add('active');
            panel.classList.add('active');
            overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
            this.isOpen = true;
        }
    },
    
    close() {
        const menu = document.getElementById('hamburgerMenu');
        const panel = document.getElementById('hamburgerMenuPanel');
        const overlay = document.getElementById('hamburgerOverlay');
        
        if (menu && panel && overlay) {
            menu.classList.remove('active');
            panel.classList.remove('active');
            overlay.classList.remove('active');
            document.body.style.overflow = '';
            this.isOpen = false;
        }
    }
};

// Função global para toggle do menu
function toggleHamburgerMenu() {
    HamburgerMenuManager.toggle();
}

// Exportar para uso global se necessário
window.MeusProdutos = {
    App,
    CacheManager,
    PerformanceMonitor,
    SearchManager,
    HamburgerMenuManager,
    clearAllCachesSimple,
    toggleDevModeSimple,
    toggleHamburgerMenu
};

// Inicializar menu hambúrguer
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        HamburgerMenuManager.init();
        // Garantir que hamburger lines sejam visíveis
        ensureHamburgerMenuVisible();
    });
} else {
    HamburgerMenuManager.init();
    // Garantir que hamburger lines sejam visíveis
    ensureHamburgerMenuVisible();
}

// Função para garantir que o menu hambúrguer seja visível
function ensureHamburgerMenuVisible() {
    const hamburgerMenu = document.getElementById('hamburgerMenu');
    if (!hamburgerMenu) return;
    
    const hamburgerLines = hamburgerMenu.querySelectorAll('.hamburger-line');
    const fallbackIcon = hamburgerMenu.querySelector('.hamburger-fallback');
    
    // Verificar se as linhas estão visíveis
    let hasVisibleLines = false;
    hamburgerLines.forEach(line => {
        const style = window.getComputedStyle(line);
        if (style.display !== 'none' && style.visibility !== 'hidden' && style.opacity !== '0') {
            hasVisibleLines = true;
            // Forçar visibilidade
            line.style.display = 'block';
            line.style.visibility = 'visible';
            line.style.opacity = '1';
            line.style.background = 'white';
        }
    });
    
    // Se não houver linhas visíveis, mostrar fallback
    if (!hasVisibleLines && fallbackIcon) {
        fallbackIcon.style.display = 'block';
    } else if (fallbackIcon) {
        fallbackIcon.style.display = 'none';
    }
}

// Executar verificação após um pequeno delay para garantir que CSS foi carregado
setTimeout(ensureHamburgerMenuVisible, 100);
