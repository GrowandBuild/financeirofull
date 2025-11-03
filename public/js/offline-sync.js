// Sistema de Sincronização Automática Offline
// Sincroniza dados salvos offline quando voltar online

(function() {
    'use strict';
    
    let isSyncing = false;
    
    async function init() {
        // Aguardar offlineStorage estar pronto
        let retries = 0;
        while (!window.offlineStorage && retries < 20) {
            await new Promise(resolve => setTimeout(resolve, 500));
            retries++;
        }
        
        if (!window.offlineStorage) {
            console.warn('⚠️ OfflineStorage não disponível para sincronização');
            return;
        }
        
        // Escutar eventos online/offline
        window.addEventListener('online', handleOnline);
        window.addEventListener('offline', handleOffline);
        
        // Verificar se já está online ao carregar
        if (navigator.onLine) {
            setTimeout(syncPendingData, 2000); // Aguardar 2 segundos após carregar
        }
    }
    
    function handleOnline() {
        console.log('🌐 Conexão restaurada - Iniciando sincronização...');
        syncPendingData();
    }
    
    function handleOffline() {
        console.log('📴 Conexão perdida');
    }
    
    async function syncPendingData() {
        if (isSyncing || !navigator.onLine) {
            return;
        }
        
        if (!window.offlineStorage) {
            return;
        }
        
        isSyncing = true;
        
        try {
            await window.offlineStorage.waitForInit();
            
            // Sincronizar compras pendentes
            await syncPurchases();
            
            console.log('✅ Sincronização concluída');
        } catch (error) {
            console.error('❌ Erro na sincronização:', error);
        } finally {
            isSyncing = false;
        }
    }
    
    async function syncPurchases() {
        try {
            const pendingPurchases = await window.offlineStorage.getPendingPurchases();
            
            if (pendingPurchases.length === 0) {
                return;
            }
            
            console.log(`🔄 Sincronizando ${pendingPurchases.length} compra(s) pendente(s)...`);
            
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            
            if (!csrfToken) {
                console.error('❌ Token CSRF não encontrado');
                return;
            }
            
            for (const purchase of pendingPurchases) {
                try {
                    const response = await fetch('/compra/save', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            items: purchase.items || [],
                            store: purchase.store || '',
                            date: purchase.date || purchase.purchase_date || new Date().toISOString(),
                            total: purchase.total || 0,
                            user_id: purchase.user_id || null
                        })
                    });
                    
                    if (response.ok) {
                        const data = await response.json();
                        if (data.success) {
                            console.log('✅ Compra sincronizada:', purchase.id);
                            // Remover do IndexedDB após sincronização bem-sucedida
                            await removeSyncedPurchase(purchase.id);
                        } else {
                            console.error('❌ Erro ao sincronizar compra:', data.message);
                        }
                    } else {
                        console.error('❌ Erro HTTP ao sincronizar compra:', response.status);
                    }
                } catch (error) {
                    console.error('❌ Erro ao sincronizar compra:', error);
                }
            }
        } catch (error) {
            console.error('❌ Erro ao buscar compras pendentes:', error);
        }
    }
    
    async function removeSyncedPurchase(purchaseId) {
        try {
            if (window.offlineStorage && typeof window.offlineStorage.removePurchase === 'function') {
                await window.offlineStorage.removePurchase(purchaseId);
                console.log('🗑️ Compra removida do cache offline:', purchaseId);
            } else {
                // Fallback: remover manualmente
                await window.offlineStorage.waitForInit();
                
                if (!window.offlineStorage.db) {
                    return;
                }
                
                const transaction = window.offlineStorage.db.transaction(['purchases'], 'readwrite');
                const store = transaction.objectStore('purchases');
                
                const request = store.delete(purchaseId);
                
                request.onsuccess = () => {
                    console.log('🗑️ Compra removida do cache offline:', purchaseId);
                };
                
                request.onerror = () => {
                    console.error('❌ Erro ao remover compra do cache:', request.error);
                };
            }
        } catch (error) {
            console.error('❌ Erro ao remover compra sincronizada:', error);
        }
    }
    
    // Sincronizar periodicamente quando online (a cada 30 segundos)
    setInterval(() => {
        if (navigator.onLine && !isSyncing) {
            syncPendingData();
        }
    }, 30000);
    
    // Inicializar quando DOM estiver pronto
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();

