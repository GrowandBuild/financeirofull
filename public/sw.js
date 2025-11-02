// Service Worker para PWA - Sistema de Gestão de Produtos
const CACHE_NAME = 'produtos-app-v4';
const RUNTIME_CACHE = 'produtos-runtime-v4';

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
    console.log('Service Worker: Instalando v4...');
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
                console.log('Service Worker: Cacheando arquivos essenciais v4');
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
    console.log('Service Worker: Ativando v4...');
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
            // Limpar caches antigos de runtime para forçar atualização do CSS/JS
            return Promise.all([
                caches.delete('produtos-runtime-v2').catch(() => {}),
                caches.delete('produtos-runtime-v3').catch(() => {})
            ]);
        }).then(() => {
            return self.clients.claim();
        }).then(() => {
            // Notificar todos os clientes para recarregar
            return self.clients.matchAll().then((clients) => {
                clients.forEach((client) => {
                    client.postMessage({ type: 'SW_UPDATED', version: 'v4' });
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
            fetch(event.request, { cache: 'no-store' })
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
                    const requestUrl = new URL(event.request.url);
                    
                    // Estratégia 1: Buscar a página exata solicitada no cache
                    return caches.match(event.request)
                        .then((cached) => {
                            if (cached) {
                                console.log('Service Worker: Página encontrada no cache:', event.request.url);
                                return cached;
                            }
                            
                            // Estratégia 2: Buscar no cache principal
                            return caches.open(CACHE_NAME).then((cache) => {
                                return cache.match(event.request)
                                    .then((cached) => {
                                        if (cached) {
                                            console.log('Service Worker: Página encontrada no cache principal:', event.request.url);
                                            return cached;
                                        }
                                        
                                        // Estratégia 3: Buscar qualquer página HTML válida no cache
                                        return caches.open(RUNTIME_CACHE).then((runtimeCache) => {
                                            return runtimeCache.keys().then((keys) => {
                                                // Filtrar apenas páginas HTML válidas
                                                const htmlPages = keys.filter(key => {
                                                    const keyUrl = key.url || key;
                                                    const keyUrlObj = new URL(keyUrl);
                                                    
                                                    // Página válida se:
                                                    // - É HTML (não tem extensão de arquivo ou é .html)
                                                    // - Não é API
                                                    // - Não é offline.html
                                                    // - Não é asset (CSS, JS, imagens)
                                                    return keyUrlObj.origin === requestUrl.origin &&
                                                           !keyUrlObj.pathname.includes('/api/') &&
                                                           !keyUrlObj.pathname.includes('/offline.html') &&
                                                           !keyUrlObj.pathname.endsWith('.css') &&
                                                           !keyUrlObj.pathname.endsWith('.js') &&
                                                           !keyUrlObj.pathname.endsWith('.png') &&
                                                           !keyUrlObj.pathname.endsWith('.jpg') &&
                                                           !keyUrlObj.pathname.endsWith('.jpeg') &&
                                                           !keyUrlObj.pathname.endsWith('.gif') &&
                                                           !keyUrlObj.pathname.endsWith('.svg') &&
                                                           !keyUrlObj.pathname.endsWith('.json') &&
                                                           !keyUrlObj.pathname.endsWith('.ico');
                                                });
                                                
                                                // Se encontrou páginas válidas, usar a primeira
                                                if (htmlPages.length > 0) {
                                                    console.log('Service Worker: Usando página alternativa do cache:', htmlPages[0].url || htmlPages[0]);
                                                    return runtimeCache.match(htmlPages[0]);
                                                }
                                                
                                                // Estratégia 4: Tentar buscar página inicial (/ ou /index.php)
                                                const homePages = ['/', '/index.php'];
                                                return Promise.all(
                                                    homePages.map(homePage => {
                                                        const homeRequest = new Request(homePage);
                                                        return caches.match(homeRequest)
                                                            .then((homeCached) => homeCached ? { found: true, response: homeCached } : { found: false });
                                                    })
                                                ).then((results) => {
                                                    const found = results.find(r => r.found);
                                                    if (found && found.response) {
                                                        console.log('Service Worker: Usando página inicial do cache');
                                                        return found.response;
                                                    }
                                                    
                                                    // Como último recurso, mostrar página offline
                                                    return caches.match('/offline.html')
                                                        .then((offlinePage) => {
                                                            if (offlinePage) {
                                                                console.log('Service Worker: Mostrando página offline');
                                                                return offlinePage;
                                                            }
                                                            
                                                            // Gerar página offline básica se não encontrar
                                                            return new Response(
                                                                '<!DOCTYPE html><html lang="pt-BR"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Offline</title><style>body{background:#1f2937;color:white;font-family:sans-serif;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0}body{text-align:center;padding:2rem}</style></head><body><h1>Você está offline</h1><p>Nenhuma página está disponível no cache. Por favor, verifique sua conexão.</p><button onclick="window.location.reload()">Tentar Novamente</button><script>setInterval(()=>{if(navigator.onLine)window.location.reload()},3000)</script></body></html>',
                                                                {
                                                                    headers: { 'Content-Type': 'text/html' },
                                                                    status: 200
                                                                }
                                                            );
                                                        });
                                                });
                                            });
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
