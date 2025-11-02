/**
 * Interceptor Genérico de Formulários para Funcionamento Offline
 * Intercepta todos os formulários do sistema e integra com offlineStorage
 */

(function() {
    'use strict';
    
    // Aguardar carregamento completo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
    
    async function init() {
        console.log('🔧 OfflineForms: Iniciando...');
        
        // Aguardar inicialização do offlineStorage com mais tempo para mobile
        let retries = 0;
        const maxRetries = 30; // 15 segundos no total
        while (!window.offlineStorage && retries < maxRetries) {
            await new Promise(resolve => setTimeout(resolve, 500));
            retries++;
            
            if (retries % 5 === 0) {
                console.log(`⏳ Aguardando offlineStorage... (${retries}/${maxRetries})`);
            }
        }
        
        if (!window.offlineStorage) {
            console.error('❌ OfflineStorage não disponível após aguardar');
            console.error('Isso pode ser um problema de carregamento de arquivos');
            
            // Tentar carregar manualmente
            const script = document.createElement('script');
            script.src = '/js/offline-storage.js?v=' + Date.now();
            script.onload = () => {
                console.log('✅ offline-storage.js carregado manualmente');
                setTimeout(init, 500);
            };
            script.onerror = () => {
                console.error('❌ Erro ao carregar offline-storage.js manualmente');
            };
            document.head.appendChild(script);
            return;
        }
        
        console.log('✅ window.offlineStorage encontrado');
        
        // Aguardar um pouco mais para garantir que métodos estejam disponíveis
        await new Promise(resolve => setTimeout(resolve, 300));
        
        // Verificar se métodos existem
        if (typeof window.offlineStorage.waitForInit !== 'function') {
            console.error('❌ waitForInit não é uma função');
            console.error('Tipo de offlineStorage:', typeof window.offlineStorage);
            console.error('É uma instância?', window.offlineStorage instanceof OfflineStorage);
            
            // Tentar aguardar mais
            await new Promise(resolve => setTimeout(resolve, 1000));
            
            if (typeof window.offlineStorage.waitForInit !== 'function') {
                console.error('❌ waitForInit ainda não disponível após aguardar mais');
                // Mesmo assim, tentar continuar - pode funcionar
                console.warn('⚠️ Continuando sem waitForInit - pode haver problemas');
            }
        }
        
        // Aguardar inicialização do IndexedDB
        try {
            if (typeof window.offlineStorage.waitForInit === 'function') {
                await window.offlineStorage.waitForInit();
                console.log('✅ OfflineForms: Sistema offline pronto');
            } else {
                // Tentar aguardar initPromise diretamente
                if (window.offlineStorage.initPromise) {
                    await window.offlineStorage.initPromise;
                    console.log('✅ IndexedDB inicializado via initPromise');
                } else {
                    console.warn('⚠️ Não foi possível aguardar inicialização formalmente');
                }
            }
        } catch (error) {
            console.error('❌ Erro ao inicializar sistema offline:', error);
            console.error('Stack:', error.stack);
            // Continuar mesmo assim - pode funcionar parcialmente
            console.warn('⚠️ Continuando mesmo com erro de inicialização');
        }
        
        // Interceptar todos os formulários
        interceptForms();
        console.log('✅ OfflineForms: Interceptação de formulários ativada');
    }
    
    function interceptForms() {
        // Interceptar formulários existentes
        document.querySelectorAll('form').forEach(form => {
            if (!form.hasAttribute('data-offline-processed')) {
                attachOfflineHandler(form);
                form.setAttribute('data-offline-processed', 'true');
            }
        });
        
        // Interceptar formulários criados dinamicamente (MutationObserver)
        const observer = new MutationObserver(mutations => {
            mutations.forEach(mutation => {
                mutation.addedNodes.forEach(node => {
                    if (node.nodeType === 1) { // Element node
                        if (node.tagName === 'FORM') {
                            if (!node.hasAttribute('data-offline-processed')) {
                                attachOfflineHandler(node);
                                node.setAttribute('data-offline-processed', 'true');
                            }
                        } else {
                            // Verificar se contém formulários
                            node.querySelectorAll('form').forEach(form => {
                                if (!form.hasAttribute('data-offline-processed')) {
                                    attachOfflineHandler(form);
                                    form.setAttribute('data-offline-processed', 'true');
                                }
                            });
                        }
                    }
                });
            });
        });
        
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }
    
    function attachOfflineHandler(form) {
        // Não interceptar formulários que não devem ser offline (ex: busca)
        if (form.hasAttribute('data-no-offline')) {
            return;
        }
        
        // Identificar tipo de formulário pela action
        const action = form.getAttribute('action') || '';
        const method = form.getAttribute('method')?.toUpperCase() || 'GET';
        
        // Apenas interceptar POST/PUT/PATCH
        if (!['POST', 'PUT', 'PATCH'].includes(method)) {
            return;
        }
        
        // Adicionar handler de submit
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
            const originalText = submitBtn?.innerHTML || submitBtn?.value || 'Salvando...';
            
            // Desabilitar botão
            if (submitBtn) {
                submitBtn.disabled = true;
                if (submitBtn.tagName === 'BUTTON') {
                    submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Processando...';
                } else {
                    submitBtn.value = 'Processando...';
                }
            }
            
            try {
                // Verificar se offlineStorage está pronto
                if (!window.offlineStorage || typeof window.offlineStorage.waitForInit !== 'function') {
                    console.error('❌ OfflineStorage não está disponível ou não foi inicializado corretamente');
                    showErrorMessage('Sistema offline não está disponível. Recarregue a página.');
                    
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        if (submitBtn.tagName === 'BUTTON') {
                            submitBtn.innerHTML = originalText;
                        } else {
                            submitBtn.value = originalText;
                        }
                    }
                    return;
                }
                
                // Aguardar inicialização se necessário (com fallback)
                try {
                    if (typeof window.offlineStorage.waitForInit === 'function') {
                        await window.offlineStorage.waitForInit();
                    } else if (window.offlineStorage.initPromise) {
                        await window.offlineStorage.initPromise;
                    } else {
                        // Aguardar um pouco e verificar se db existe
                        await new Promise(resolve => setTimeout(resolve, 500));
                        if (!window.offlineStorage.db) {
                            throw new Error('IndexedDB não inicializado');
                        }
                    }
                } catch (error) {
                    console.error('❌ Erro ao aguardar inicialização:', error);
                    // Tentar continuar mesmo assim - pode funcionar
                    if (!window.offlineStorage.db) {
                        showErrorMessage('Sistema offline não está pronto. Aguarde alguns segundos e tente novamente.');
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            if (submitBtn.tagName === 'BUTTON') {
                                submitBtn.innerHTML = originalText;
                            } else {
                                submitBtn.value = originalText;
                            }
                        }
                        return;
                    }
                    // Se db existe, continuar mesmo com erro
                    console.warn('⚠️ Continuando mesmo com erro de inicialização - db existe');
                }
                
                // Verificar se está online
                const isOnline = navigator.onLine && window.offlineStorage.isOnlineStatus();
                
                // Preparar dados do formulário
                const formData = new FormData(form);
                const data = Object.fromEntries(formData.entries());
                const jsonData = {};
                
                // Converter FormData para objeto JSON
                for (let [key, value] of formData.entries()) {
                    // Lidar com arrays (ex: variants[])
                    if (key.endsWith('[]')) {
                        const arrayKey = key.slice(0, -2);
                        if (!jsonData[arrayKey]) {
                            jsonData[arrayKey] = [];
                        }
                        jsonData[arrayKey].push(value);
                    } else if (jsonData[key]) {
                        // Se já existe, converter para array
                        if (!Array.isArray(jsonData[key])) {
                            jsonData[key] = [jsonData[key]];
                        }
                        jsonData[key].push(value);
                    } else {
                        jsonData[key] = value;
                    }
                }
                
                // Adicionar CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                if (csrfToken) {
                    jsonData._token = csrfToken;
                }
                
                // Tentar enviar se online
                if (isOnline) {
                    try {
                        const response = await fetch(action, {
                            method: method,
                            headers: {
                                'X-CSRF-TOKEN': csrfToken || '',
                                'Accept': 'application/json',
                                'Content-Type': form.enctype === 'multipart/form-data' ? undefined : 'application/json'
                            },
                            body: form.enctype === 'multipart/form-data' ? formData : JSON.stringify(jsonData)
                        });
                        
                        if (response.ok) {
                            const result = await response.json().catch(() => ({}));
                            
                            // Salvar também no cache offline se necessário
                            await saveToOfflineCache(action, jsonData, result);
                            
                            // Redirecionar ou mostrar sucesso
                            if (response.redirected) {
                                window.location.href = response.url;
                            } else if (result.redirect) {
                                window.location.href = result.redirect;
                            } else {
                                showSuccessMessage('Salvo com sucesso!');
                                // Recarregar página após 1 segundo
                                setTimeout(() => {
                                    if (!result.preventReload) {
                                        window.location.reload();
                                    }
                                }, 1000);
                            }
                            
                            return;
                        } else {
                            throw new Error(`HTTP ${response.status}`);
                        }
                    } catch (error) {
                        console.error('Erro ao enviar online, salvando offline:', error);
                        // Continuar para salvar offline
                    }
                }
                
                // Salvar offline
                await saveOffline(action, jsonData, form);
                
                showSuccessMessage('Salvo offline! ✅\n\nSerá sincronizado automaticamente quando voltar online.');
                
                // Recarregar página após 2 segundos
                setTimeout(() => {
                    if (!form.hasAttribute('data-no-reload')) {
                        window.location.reload();
                    }
                }, 2000);
                
            } catch (error) {
                console.error('❌ Erro ao processar formulário:', error);
                showErrorMessage('Erro ao salvar: ' + error.message);
                
                if (submitBtn) {
                    submitBtn.disabled = false;
                    if (submitBtn.tagName === 'BUTTON') {
                        submitBtn.innerHTML = originalText;
                    } else {
                        submitBtn.value = originalText;
                    }
                }
            }
        });
    }
    
    async function saveToOfflineCache(action, data, serverResponse) {
        try {
            // Identificar tipo pelo action
            if (action.includes('/cashflow/store')) {
                const cashflowData = {
                    ...data,
                    id: serverResponse.id || null,
                    isPending: false
                };
                await window.offlineStorage.saveCashFlow(cashflowData);
            } else if (action.includes('/financial-schedule/store')) {
                const scheduleData = {
                    ...data,
                    id: serverResponse.id || null,
                    isPending: false
                };
                await window.offlineStorage.saveSchedule(scheduleData);
            } else if (action.includes('/products/store') || action.includes('/admin/products/store')) {
                const productData = {
                    ...data,
                    id: serverResponse.id || null,
                    isPending: false
                };
                await window.offlineStorage.saveProduct(productData);
            }
            // Outros tipos podem ser adicionados aqui
        } catch (error) {
            console.log('Erro ao salvar no cache offline (não crítico):', error);
        }
    }
    
    async function saveOffline(action, data, form) {
        // Identificar tipo pelo action e salvar apropriadamente
        if (action.includes('/cashflow/store')) {
            const cashflowData = {
                type: data.type,
                title: data.title,
                description: data.description || null,
                amount: parseFloat(data.amount),
                category_id: data.category_id || null,
                goal_category: data.goal_category || null,
                transaction_date: data.transaction_date || new Date().toISOString(),
                payment_method: data.payment_method || null,
                reference: data.reference || null,
                is_recurring: data.is_recurring === '1' || data.is_recurring === true,
                is_confirmed: data.is_confirmed === '1' || data.is_confirmed === true
            };
            await window.offlineStorage.saveCashFlow(cashflowData);
        } else if (action.includes('/financial-schedule/store')) {
            const scheduleData = {
                title: data.title,
                description: data.description || null,
                amount: parseFloat(data.amount),
                category_id: data.category_id || null,
                goal_category: data.goal_category || null,
                scheduled_date: data.scheduled_date || null,
                scheduled_day: data.scheduled_day || null,
                recurring_frequency: data.recurring_frequency || null
            };
            await window.offlineStorage.saveSchedule(scheduleData);
        } else if (action.includes('/products/store') || action.includes('/admin/products/store')) {
            const productData = {
                name: data.name,
                description: data.description || null,
                category: data.category || null,
                goal_category: data.goal_category,
                unit: data.unit,
                image: data.image || null
            };
            await window.offlineStorage.saveProduct(productData);
        } else if (action.includes('/books/store')) {
            // Books não tem integração offline ainda, mas pode ser adicionada
            console.log('📚 Livros não têm suporte offline ainda');
        } else {
            // Formulário genérico - adicionar à fila de sincronização
            await window.offlineStorage.addToSyncQueue('generic', 'create', {
                action: action,
                data: data,
                method: form.method
            });
        }
    }
    
    function showSuccessMessage(message) {
        // Criar notificação de sucesso
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
            white-space: pre-line;
        `;
        notification.textContent = message;
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.transition = 'opacity 0.3s';
            notification.style.opacity = '0';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }
    
    function showErrorMessage(message) {
        // Criar notificação de erro
        const notification = document.createElement('div');
        notification.style.cssText = `
            position: fixed;
            top: 80px;
            right: 20px;
            background: #ef4444;
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
        }, 5000);
    }
    
    console.log('📝 OfflineForms: Interceptor de formulários carregado');
})();

