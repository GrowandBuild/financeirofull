// Script para limpar cache do navegador
(function() {
    'use strict';
    
    console.log('🧹 Limpando cache do navegador...');
    
    // 1. Limpar localStorage
    try {
        localStorage.clear();
        console.log('✅ localStorage limpo');
    } catch (e) {
        console.warn('⚠️ Erro ao limpar localStorage:', e);
    }
    
    // 2. Limpar sessionStorage
    try {
        sessionStorage.clear();
        console.log('✅ sessionStorage limpo');
    } catch (e) {
        console.warn('⚠️ Erro ao limpar sessionStorage:', e);
    }
    
    // 3. Limpar IndexedDB
    if ('indexedDB' in window) {
        try {
            indexedDB.databases().then(databases => {
                databases.forEach(db => {
                    indexedDB.deleteDatabase(db.name);
                });
                console.log('✅ IndexedDB limpo');
            });
        } catch (e) {
            console.warn('⚠️ Erro ao limpar IndexedDB:', e);
        }
    }
    
    // 4. Limpar Service Worker cache
    if ('caches' in window) {
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cacheName => {
                    console.log('🗑️ Removendo cache:', cacheName);
                    return caches.delete(cacheName);
                })
            );
        }).then(() => {
            console.log('✅ Service Worker cache limpo');
        }).catch(e => {
            console.warn('⚠️ Erro ao limpar Service Worker cache:', e);
        });
    }
    
    // 5. Forçar reload sem cache
    setTimeout(() => {
        console.log('🔄 Recarregando página sem cache...');
        window.location.reload(true);
    }, 1000);
    
})();
