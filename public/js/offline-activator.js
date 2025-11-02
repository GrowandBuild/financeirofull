/**
 * Ativador de IndexedDB - Botão e Diagnóstico
 * Permite ativar manualmente o IndexedDB e diagnosticar problemas
 */

(function() {
    'use strict';
    
    // Criar botão de ativação
    function createActivatorButton() {
        // Verificar se já existe
        if (document.getElementById('offline-activator-btn')) {
            return;
        }
        
        const button = document.createElement('button');
        button.id = 'offline-activator-btn';
        button.innerHTML = '<i class="bi bi-database"></i> Ativar Offline';
        button.style.cssText = `
            position: fixed;
            bottom: 20px;
            left: 20px;
            background: #3b82f6;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 25px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            z-index: 9998;
            font-weight: 600;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
        `;
        
        button.onclick = async () => {
            await activateOfflineStorage();
        };
        
        button.onmouseover = () => {
            button.style.background = '#2563eb';
            button.style.transform = 'translateY(-2px)';
        };
        
        button.onmouseout = () => {
            button.style.background = '#3b82f6';
            button.style.transform = 'translateY(0)';
        };
        
        document.body.appendChild(button);
        
        // Verificar status inicial
        checkStatus();
        
        // Atualizar status periodicamente
        setInterval(checkStatus, 5000);
    }
    
    function updateButtonStatus(status) {
        const button = document.getElementById('offline-activator-btn');
        if (!button) return;
        
        if (status === 'ready') {
            button.innerHTML = '<i class="bi bi-check-circle"></i> Offline Ativo';
            button.style.background = '#10b981';
            button.title = 'IndexedDB está funcionando';
        } else if (status === 'loading') {
            button.innerHTML = '<i class="bi bi-hourglass-split"></i> Ativando...';
            button.style.background = '#f59e0b';
            button.disabled = true;
        } else if (status === 'error') {
            button.innerHTML = '<i class="bi bi-exclamation-triangle"></i> Erro';
            button.style.background = '#ef4444';
            button.title = 'Clique para tentar ativar';
            button.disabled = false;
        } else {
            button.innerHTML = '<i class="bi bi-database"></i> Ativar Offline';
            button.style.background = '#3b82f6';
            button.disabled = false;
        }
    }
    
    async function checkStatus() {
        if (window.offlineStorage && window.offlineStorage.db) {
            updateButtonStatus('ready');
        } else if (window.offlineStorage && window.offlineStorage.initPromise) {
            updateButtonStatus('loading');
            try {
                await window.offlineStorage.waitForInit();
                if (window.offlineStorage.db) {
                    updateButtonStatus('ready');
                }
            } catch (error) {
                updateButtonStatus('error');
            }
        } else if (window.offlineStorage) {
            // Existe mas não está inicializado
            updateButtonStatus('error');
        } else {
            // Não existe
            updateButtonStatus('error');
        }
    }
    
    async function activateOfflineStorage() {
        updateButtonStatus('loading');
        
        const log = [];
        function addLog(message, type = 'info') {
            log.push({ message, type, time: new Date().toLocaleTimeString() });
            console.log(`[Offline Activator] ${message}`);
        }
        
        addLog('Iniciando ativação do IndexedDB...', 'info');
        
        try {
            // 1. Verificar se IndexedDB está disponível
            if (!window.indexedDB) {
                addLog('❌ IndexedDB não está disponível neste navegador', 'error');
                showDiagnostic(log);
                updateButtonStatus('error');
                return;
            }
            addLog('✅ IndexedDB está disponível', 'success');
            
            // 2. Verificar se offlineStorage existe
            if (!window.offlineStorage) {
                addLog('❌ window.offlineStorage não está disponível', 'error');
                addLog('Verificando se o arquivo offline-storage.js foi carregado...', 'info');
                
                // Tentar carregar o arquivo
                const script = document.createElement('script');
                script.src = '/js/offline-storage.js';
                script.onload = () => {
                    addLog('✅ Arquivo offline-storage.js carregado', 'success');
                    setTimeout(() => activateOfflineStorage(), 500);
                };
                script.onerror = () => {
                    addLog('❌ Erro ao carregar offline-storage.js', 'error');
                    showDiagnostic(log);
                    updateButtonStatus('error');
                };
                document.head.appendChild(script);
                return;
            }
            addLog('✅ window.offlineStorage encontrado', 'success');
            
            // 3. Aguardar inicialização
            addLog('Aguardando inicialização do IndexedDB...', 'info');
            try {
                await window.offlineStorage.waitForInit();
                
                if (window.offlineStorage.db) {
                    addLog('✅ IndexedDB inicializado com sucesso!', 'success');
                    addLog(`📦 Banco: ${window.offlineStorage.dbName}`, 'info');
                    addLog(`📊 Versão: ${window.offlineStorage.dbVersion}`, 'info');
                    
                    const storeNames = Array.from(window.offlineStorage.db.objectStoreNames);
                    addLog(`🗃️ Stores: ${storeNames.join(', ')}`, 'info');
                    
                    updateButtonStatus('ready');
                    showSuccessMessage('✅ Sistema offline ativado com sucesso!');
                    
                    // Testar salvamento
                    addLog('Testando salvamento...', 'info');
                    try {
                        const testData = {
                            name: 'Teste ' + Date.now(),
                            test: true
                        };
                        await window.offlineStorage.saveProduct(testData);
                        addLog('✅ Teste de salvamento bem-sucedido!', 'success');
                    } catch (testError) {
                        addLog('⚠️ Erro no teste de salvamento: ' + testError.message, 'warning');
                    }
                } else {
                    addLog('❌ IndexedDB não foi inicializado', 'error');
                    showDiagnostic(log);
                    updateButtonStatus('error');
                }
            } catch (initError) {
                addLog('❌ Erro ao inicializar: ' + initError.message, 'error');
                console.error('Erro completo:', initError);
                showDiagnostic(log);
                updateButtonStatus('error');
            }
        } catch (error) {
            addLog('❌ Erro geral: ' + error.message, 'error');
            console.error('Erro completo:', error);
            showDiagnostic(log);
            updateButtonStatus('error');
        }
    }
    
    function showDiagnostic(log) {
        const modal = document.createElement('div');
        modal.id = 'offline-diagnostic-modal';
        modal.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.8);
            z-index: 10000;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        `;
        
        const content = document.createElement('div');
        content.style.cssText = `
            background: #1f2937;
            color: white;
            padding: 30px;
            border-radius: 12px;
            max-width: 600px;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
        `;
        
        const title = document.createElement('h2');
        title.textContent = '🔍 Diagnóstico do Sistema Offline';
        title.style.cssText = 'margin-bottom: 20px; color: white;';
        
        const logDiv = document.createElement('div');
        logDiv.style.cssText = 'font-family: monospace; font-size: 0.9rem; line-height: 1.6;';
        
        log.forEach(item => {
            const p = document.createElement('p');
            p.textContent = `[${item.time}] ${item.message}`;
            p.style.color = item.type === 'error' ? '#ef4444' : 
                           item.type === 'success' ? '#10b981' : 
                           item.type === 'warning' ? '#f59e0b' : '#9ca3af';
            logDiv.appendChild(p);
        });
        
        const closeBtn = document.createElement('button');
        closeBtn.textContent = 'Fechar';
        closeBtn.style.cssText = `
            margin-top: 20px;
            padding: 10px 20px;
            background: #10b981;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            width: 100%;
        `;
        closeBtn.onclick = () => modal.remove();
        
        content.appendChild(title);
        content.appendChild(logDiv);
        content.appendChild(closeBtn);
        modal.appendChild(content);
        document.body.appendChild(modal);
        
        // Fechar ao clicar fora
        modal.onclick = (e) => {
            if (e.target === modal) {
                modal.remove();
            }
        };
    }
    
    function showSuccessMessage(message) {
        const notification = document.createElement('div');
        notification.style.cssText = `
            position: fixed;
            top: 80px;
            right: 20px;
            background: #10b981;
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 9999;
            max-width: 300px;
        `;
        notification.textContent = message;
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.transition = 'opacity 0.3s';
            notification.style.opacity = '0';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }
    
    // Inicializar quando DOM estiver pronto
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(createActivatorButton, 1000);
        });
    } else {
        setTimeout(createActivatorButton, 1000);
    }
    
    // Escutar evento de IndexedDB pronto
    window.addEventListener('indexeddb-ready', () => {
        updateButtonStatus('ready');
    });
    
    console.log('🔧 Offline Activator carregado');
})();

