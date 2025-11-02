/**
 * Correções específicas para mobile em produção
 * Garante que o sistema offline funcione corretamente em dispositivos móveis
 */

(function() {
    'use strict';
    
    // Detectar se é mobile
    const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    
    if (!isMobile) {
        return; // Só executar em mobile
    }
    
    console.log('📱 Mobile detectado - Aplicando correções...');
    
    // Função para garantir que offlineStorage está pronto
    async function ensureOfflineStorageReady() {
        const maxAttempts = 10;
        let attempt = 0;
        
        while (attempt < maxAttempts) {
            if (window.offlineStorage) {
                // Verificar se está realmente funcional
                if (window.offlineStorage.db || typeof window.offlineStorage.waitForInit === 'function') {
                    console.log('✅ OfflineStorage pronto em mobile após', attempt, 'tentativas');
                    return true;
                }
            }
            
            await new Promise(resolve => setTimeout(resolve, 500));
            attempt++;
        }
        
        console.error('❌ OfflineStorage não ficou pronto após', maxAttempts, 'tentativas');
        return false;
    }
    
    // Aguardar carregamento completo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(async () => {
                await ensureOfflineStorageReady();
            }, 1000);
        });
    } else {
        setTimeout(async () => {
            await ensureOfflineStorageReady();
        }, 1000);
    }
    
    // Forçar limpeza de cache antigo do IndexedDB em mobile
    if ('indexedDB' in window) {
        const dbName = 'ProdutosAppDB';
        
        // Verificar se há versão antiga sem waitForInit
        indexedDB.databases().then(databases => {
            databases.forEach(db => {
                if (db.name === dbName && db.version < 2) {
                    console.log('🔄 Detectada versão antiga do IndexedDB, forçando upgrade...');
                    // Deletar e recriar
                    indexedDB.deleteDatabase(dbName).onsuccess = () => {
                        console.log('✅ Versão antiga deletada');
                        // Recarregar página após 1 segundo
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    };
                }
            });
        }).catch(error => {
            console.log('Não foi possível verificar databases:', error);
        });
    }
    
    // Monitorar erros de IndexedDB em mobile
    window.addEventListener('error', (event) => {
        if (event.message && (event.message.includes('indexedDB') || event.message.includes('IndexedDB'))) {
            console.error('❌ Erro relacionado a IndexedDB:', event.message);
            console.error('Tentando recriar...');
            
            setTimeout(() => {
                if (window.offlineStorage && typeof OfflineStorage !== 'undefined') {
                    try {
                        delete window.offlineStorage;
                        window.offlineStorage = new OfflineStorage();
                        console.log('✅ OfflineStorage recriado após erro');
                    } catch (error) {
                        console.error('❌ Erro ao recriar:', error);
                    }
                } else {
                    console.warn('⚠️ OfflineStorage ou classe não disponível para recriar');
                }
            }, 2000);
        }
    });
    
    console.log('📱 Correções mobile aplicadas');
})();

