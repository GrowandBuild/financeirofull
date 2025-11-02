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
        // Aguardar inicialização do offlineStorage
        let retries = 0;
        while (!window.offlineStorage && retries < 20) {
            await new Promise(resolve => setTimeout(resolve, 500));
            retries++;
        }
        
        if (!window.offlineStorage) {
            console.warn('⚠️ OfflineStorage não disponível - formulários não funcionarão offline');
            console.warn('Verifique se o arquivo offline-storage.js está sendo carregado corretamente');
            return;
        }
        
        // Verificar se métodos existem
        if (typeof window.offlineStorage.waitForInit !== 'function') {
            console.error('❌ Erro: waitForInit não é uma função');
            console.error('OfflineStorage disponível:', window.offlineStorage);
            console.error('Métodos disponíveis:', Object.getOwnPropertyNames(Object.getPrototypeOf(window.offlineStorage)));
            
            // Tentar aguardar mais um pouco
            await new Promise(resolve => setTimeout(resolve, 1000));
            
            if (typeof window.offlineStorage.waitForInit !== 'function') {
                console.error('❌ waitForInit ainda não está disponível após aguardar');
                return;
            }
        }
        
        // Aguardar inicialização do IndexedDB
        try {
            await window.offlineStorage.waitForInit();
            console.log('✅ OfflineForms: Sistema offline pronto');
        } catch (error) {
            console.error('❌ Erro ao inicializar sistema offline:', error);
            console.error('Stack:', error.stack);
            return;
        }
        
        // Interceptar todos os formulários
        interceptForms();
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
                
                // Aguardar inicialização se necessário
                try {
                    await window.offlineStorage.waitForInit();
                } catch (error) {
                    console.error('❌ Erro ao aguardar inicialização:', error);
                    showErrorMessage('Erro ao inicializar sistema offline: ' + error.message);
                    
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

