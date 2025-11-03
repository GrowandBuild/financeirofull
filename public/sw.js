// Service Worker para PWA - Versão Mobile Otimizada
const CACHE_NAME = 'produtos-app-v7';
const RUNTIME_CACHE = 'produtos-runtime-v7';

// Arquivos essenciais para cache
const CACHE_FILES = [
    '/',
    '/index.php',
    '/css/app.css',
    '/js/app.js',
    '/manifest.json'
];

// Instalar Service Worker
self.addEventListener('install', (event) => {
    console.log('Service Worker: Instalando v7...');
    event.waitUntil(
        // Limpar caches antigos primeiro
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName !== CACHE_NAME && cacheName !== RUNTIME_CACHE) {
                        console.log('Service Worker: Removendo cache antigo:', cacheName);
                        return caches.delete(cacheName);
                    }
                })
            );
        }).then(() => {
            // Abrir novo cache e adicionar arquivos
            return caches.open(CACHE_NAME).then((cache) => {
                console.log('Service Worker: Cacheando arquivos essenciais v7');
                // Não usar cache.addAll que falha se um arquivo falhar
                return Promise.all(
                    CACHE_FILES.map((file) => {
                        return fetch(file, { cache: 'no-store' })
                            .then((response) => {
                                if (response.ok) {
                                    return cache.put(file, response);
                                }
                            })
                            .catch((err) => {
                                console.warn('Erro ao cachear:', file, err);
                            });
                    })
                );
            });
        }).then(() => self.skipWaiting())
    );
});

// Ativar Service Worker
self.addEventListener('activate', (event) => {
    console.log('Service Worker: Ativando v7...');
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName !== CACHE_NAME && cacheName !== RUNTIME_CACHE) {
                        console.log('Service Worker: Removendo cache antigo:', cacheName);
                        return caches.delete(cacheName);
                    }
                })
            );
        }).then(() => {
            return self.clients.claim();
        })
    );
});

// Interceptar requisições - Network First Strategy
self.addEventListener('fetch', (event) => {
    // Ignorar requisições não GET
    if (event.request.method !== 'GET') {
        return;
    }
    
    const url = new URL(event.request.url);
    
    // Ignorar requisições de API (não cachear)
    if (url.pathname.includes('/api/')) {
        return;
    }
    
    // Para assets estáticos (CSS, JS, imagens)
    const isAsset = url.pathname.includes('/css/') || 
                    url.pathname.includes('/js/') || 
                    url.pathname.includes('/images/');
    
    if (isAsset) {
        event.respondWith(
            fetch(event.request, { cache: 'no-store' })
                .then((response) => {
                    if (response.ok) {
                        const responseToCache = response.clone();
                        caches.open(RUNTIME_CACHE).then((cache) => {
                            cache.put(event.request, responseToCache);
                        });
                    }
                    return response;
                })
                .catch(() => {
                    // Se offline, buscar do cache
                    return caches.match(event.request);
                })
        );
        return;
    }
    
    // Para navegação (páginas HTML)
    if (event.request.mode === 'navigate') {
        event.respondWith(
            fetch(event.request)
                .then((response) => {
                    // Cachear páginas quando online
                    if (response.ok && response.status === 200) {
                        const responseToCache = response.clone();
                        caches.open(RUNTIME_CACHE).then((cache) => {
                            cache.put(event.request, responseToCache);
                            console.log('Service Worker: Página cacheada:', event.request.url);
                        }).catch(err => {
                            console.error('Erro ao cachear página:', err);
                        });
                    }
                    return response;
                })
                .catch(() => {
                    // Se offline, buscar do cache
                    console.log('Service Worker: Offline, buscando no cache...');
                    
                    // Estratégia 1: Buscar página exata
                    return caches.match(event.request).then((cached) => {
                        if (cached) {
                            console.log('Service Worker: ✅ Página encontrada:', event.request.url);
                            return cached;
                        }
                        
                        // Estratégia 2: Buscar no runtime cache
                        return caches.open(RUNTIME_CACHE).then((cache) => {
                            return cache.match(event.request).then((cached) => {
                                if (cached) {
                                    console.log('Service Worker: ✅ Página encontrada no runtime cache');
                                    return cached;
                                }
                                
                                // Estratégia 3: Buscar página inicial
                                return caches.match('/').then((homePage) => {
                                    if (homePage) {
                                        console.log('Service Worker: ✅ Usando página inicial');
                                        return homePage;
                                    }
                                    
                                    // Fallback: página offline básica
                                    return new Response(
                                        '<!DOCTYPE html><html lang="pt-BR"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Offline</title><style>body{background:#1f2937;color:white;font-family:sans-serif;display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:100vh;margin:0;text-align:center;padding:2rem}h1{color:#10b981;margin-bottom:1rem}button{padding:0.75rem 2rem;background:#10b981;color:white;border:none;border-radius:0.5rem;cursor:pointer;font-size:1rem;margin-top:1rem}button:hover{background:#059669}</style></head><body><h1>📱 Você está offline</h1><p>Nenhuma página está disponível no cache. Por favor, verifique sua conexão.</p><button onclick="window.location.reload()">Tentar Novamente</button><script>setInterval(()=>{if(navigator.onLine)window.location.reload()},3000)</script></body></html>',
                                        {
                                            headers: { 'Content-Type': 'text/html' },
                                            status: 200
                                        }
                                    );
                                });
                            });
                        });
                    });
                })
        );
        return;
    }
    
    // Para outros recursos
    event.respondWith(
        fetch(event.request)
            .then((response) => {
                if (response.ok) {
                    const responseToCache = response.clone();
                    caches.open(RUNTIME_CACHE).then((cache) => {
                        cache.put(event.request, responseToCache);
                    });
                }
                return response;
            })
            .catch(() => {
                return caches.match(event.request);
            })
    );
});

// Mensagens do cliente
self.addEventListener('message', (event) => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
});
