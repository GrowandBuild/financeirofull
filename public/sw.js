// Service Worker para PWA - Sistema de Gestão de Produtos
const CACHE_NAME = 'produtos-app-v3';
const RUNTIME_CACHE = 'produtos-runtime-v3';

// Arquivos essenciais para cache
const CACHE_FILES = [
    '/',
    '/index.php',
    '/css/app.css',
    '/js/app.js',
    '/js/offline-storage.js',
    '/offline.html',
    '/manifest.json'
];

// Instalar Service Worker
self.addEventListener('install', (event) => {
    console.log('Service Worker: Instalando v3...');
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
                console.log('Service Worker: Cacheando arquivos essenciais v3');
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
    console.log('Service Worker: Ativando v3...');
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
            // Limpar cache de runtime para forçar atualização do CSS/JS
            return caches.delete('produtos-runtime-v2').catch(() => {});
        }).then(() => {
            return self.clients.claim();
        }).then(() => {
            // Notificar todos os clientes para recarregar
            return self.clients.matchAll().then((clients) => {
                clients.forEach((client) => {
                    client.postMessage({ type: 'SW_UPDATED', version: 'v3' });
                });
            });
        })
    );
});

// Escutar mensagens do cliente
self.addEventListener('message', (event) => {
    if (event.data && event.data.type === 'CLEAR_ASSET_CACHE') {
        // Limpar cache de assets (CSS/JS)
        caches.open(RUNTIME_CACHE).then((cache) => {
            return cache.keys().then((keys) => {
                return Promise.all(
                    keys
                        .filter((key) => {
                            const url = key.url || key;
                            return url.includes('/css/') || url.includes('/js/');
                        })
                        .map((key) => cache.delete(key))
                );
            });
        }).then(() => {
            console.log('Cache de assets limpo');
            event.ports && event.ports[0] && event.ports[0].postMessage({ success: true });
        });
    }
});

// Interceptar requisições (Network First Strategy - melhorada para funcionar offline)
self.addEventListener('fetch', (event) => {
    // Ignorar requisições não GET
    if (event.request.method !== 'GET') {
        return;
    }
    
    // Ignorar requisições de API (deixar passar sem cache)
    if (event.request.url.includes('/api/')) {
        return;
    }
    
    // Para CSS e JS, sempre buscar versão nova em produção
    const url = new URL(event.request.url);
    const isAsset = url.pathname.includes('/css/') || url.pathname.includes('/js/');
    
    // Se for asset, usar estratégia network-first com cache curto
    if (isAsset) {
        event.respondWith(
            fetch(event.request, { cache: 'no-store' })
                .then((response) => {
                    // Cachear por curto tempo
                    if (response.ok) {
                        const responseToCache = response.clone();
                        caches.open(RUNTIME_CACHE).then((cache) => {
                            cache.put(event.request, responseToCache);
                        });
                    }
                    return response;
                })
                .catch(() => {
                    // Se falhar, tentar do cache
                    return caches.match(event.request);
                })
        );
        return;
    }
    
    // Se for navegação (página HTML), usar estratégia especial
    if (event.request.mode === 'navigate') {
        event.respondWith(
            fetch(event.request)
                .then((response) => {
                    // Cachear todas as páginas visitadas
                    const responseToCache = response.clone();
                    if (response.status === 200 && response.type === 'basic') {
                        caches.open(RUNTIME_CACHE).then((cache) => {
                            cache.put(event.request, responseToCache);
                        });
                    }
                    return response;
                })
                .catch(() => {
                    // Quando offline, tentar múltiplas estratégias
                    return caches.match(event.request)
                        .then((cached) => {
                            if (cached) return cached;
                            
                            // Tentar buscar qualquer página visitada anteriormente
                            return caches.open(RUNTIME_CACHE).then((cache) => {
                                return cache.keys().then((keys) => {
                                    // Buscar qualquer página HTML no cache
                                    const pageKeys = keys.filter(key => {
                                        const keyUrl = key.url;
                                        return keyUrl.includes(url.origin) && 
                                               !keyUrl.includes('/api/') &&
                                               !keyUrl.endsWith('.css') &&
                                               !keyUrl.endsWith('.js') &&
                                               !keyUrl.endsWith('.png') &&
                                               !keyUrl.endsWith('.jpg') &&
                                               !keyUrl.endsWith('.json') &&
                                               !keyUrl.includes('/offline.html');
                                    });
                                    
                                    // Se encontrou páginas no cache, usar a primeira
                                    if (pageKeys.length > 0) {
                                        return cache.match(pageKeys[0]);
                                    }
                                    
                                    // Como último recurso, mostrar página offline
                                    return caches.match('/offline.html')
                                        .then((offlinePage) => {
                                            return offlinePage || new Response(
                                                '<html><body><h1>Offline</h1><p>O aplicativo não está disponível offline. Por favor, verifique sua conexão.</p><script>setTimeout(() => window.location.reload(), 3000);</script></body></html>',
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
    
    // Para outros recursos (CSS, JS, imagens, etc)
    event.respondWith(
        fetch(event.request)
            .then((response) => {
                // Cachear recursos estáticos
                const responseToCache = response.clone();
                if (response.status === 200) {
                    caches.open(RUNTIME_CACHE).then((cache) => {
                        cache.put(event.request, responseToCache);
                    });
                }
                return response;
            })
            .catch(() => {
                // Se offline, buscar do cache
                return caches.match(event.request)
                    .then((cachedResponse) => {
                        if (cachedResponse) {
                            return cachedResponse;
                        }
                        // Se não encontrar, retornar erro
                        return new Response('Recurso não disponível offline', {
                            status: 503,
                            statusText: 'Service Unavailable'
                        });
                    });
            })
    );
});

// Mensagens do cliente (para atualização)
self.addEventListener('message', (event) => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
});
