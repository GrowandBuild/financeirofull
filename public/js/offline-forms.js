// Interceptor de Formulários para Funcionamento Offline
// Otimizado para Laravel Herd + Mobile

(function() {
    'use strict';
    
    let isReady = false;
    
    async function init() {
        console.log('🔧 OfflineForms: Iniciando...');
        
        // Aguardar offlineStorage estar pronto
        let retries = 0;
        while (!window.offlineStorage && retries < 20) {
            await new Promise(resolve => setTimeout(resolve, 500));
            retries++;
        }
        
        if (!window.offlineStorage) {
            console.warn('⚠️ OfflineStorage não disponível');
            return;
        }
        
        // Aguardar inicialização
        try {
            await window.offlineStorage.waitForInit();
            console.log('✅ OfflineForms: Sistema offline pronto');
            isReady = true;
        } catch (error) {
            console.error('❌ Erro ao inicializar sistema offline:', error);
            return;
        }
        
        // Interceptar formulários
        interceptForms();
    }
    
    function interceptForms() {
        // Interceptar formulários existentes
        document.querySelectorAll('form').forEach(form => {
            if (!form.hasAttribute('data-offline-processed')) {
                form.setAttribute('data-offline-processed', 'true');
                form.addEventListener('submit', handleSubmit);
            }
        });
        
        // Usar MutationObserver para novos formulários
        const observer = new MutationObserver(mutations => {
            mutations.forEach(mutation => {
                mutation.addedNodes.forEach(node => {
                    if (node.nodeType === 1) {
                        if (node.tagName === 'FORM' && !node.hasAttribute('data-offline-processed')) {
                            node.setAttribute('data-offline-processed', 'true');
                            node.addEventListener('submit', handleSubmit);
                        }
                        node.querySelectorAll('form:not([data-offline-processed])').forEach(form => {
                            form.setAttribute('data-offline-processed', 'true');
                            form.addEventListener('submit', handleSubmit);
                        });
                    }
                });
            });
        });
        
        observer.observe(document.body, { childList: true, subtree: true });
    }
    
    async function handleSubmit(event) {
        const form = event.target;
        
        // Verificar se deve processar offline
        if (form.hasAttribute('data-no-offline')) {
            return; // Deixar submit normal
        }
        
        // Verificar se está offline
        if (!navigator.onLine || !window.offlineStorage?.isOnlineStatus()) {
            event.preventDefault();
            
            // Preparar dados do formulário
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());
            
            // Determinar tipo de operação
            const action = form.action || form.getAttribute('action') || '';
            
            if (action.includes('/compra') || action.includes('/purchase')) {
                // É uma compra
                try {
                    await window.offlineStorage.savePurchase({
                        items: data.items || [],
                        store: data.store || '',
                        date: data.date || new Date().toISOString(),
                        total: parseFloat(data.total || 0)
                    });
                    
                    alert('✅ Compra salva offline!\n\nA compra será sincronizada automaticamente quando você voltar online.');
                    form.reset();
                } catch (error) {
                    alert('❌ Erro ao salvar offline: ' + error.message);
                }
            } else {
                alert('⚠️ Você está offline. Os dados serão salvos localmente e sincronizados quando voltar online.');
                // Salvar no localStorage como fallback
                const key = 'offline_form_' + Date.now();
                localStorage.setItem(key, JSON.stringify({
                    action: action,
                    data: data,
                    timestamp: new Date().toISOString()
                }));
            }
            
            return false;
        }
    }
    
    // Inicializar quando DOM estiver pronto
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();

