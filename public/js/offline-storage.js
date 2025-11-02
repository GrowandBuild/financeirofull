// Sistema de armazenamento offline com IndexedDB
class OfflineStorage {
    constructor() {
        this.dbName = 'ProdutosAppDB';
        this.dbVersion = 2; // Versão atualizada para incluir CashFlow e Schedule
        this.db = null;
        this.isOnline = navigator.onLine;
        this.initialized = false;
        this.initPromise = null;
        
        // Inicializar assincronamente
        this.initPromise = this.init();
        this.setupOnlineStatusListener();
    }
    
    // Aguardar inicialização
    async waitForInit() {
        if (this.initialized) {
            return this.db;
        }
        
        if (this.initPromise) {
            await this.initPromise;
            return this.db;
        }
        
        // Se não houver promise, inicializar agora
        this.initPromise = this.init();
        await this.initPromise;
        return this.db;
    }
    
    // Inicializar IndexedDB
    async init() {
        return new Promise((resolve, reject) => {
            const request = indexedDB.open(this.dbName, this.dbVersion);
            
            request.onerror = () => {
                console.error('Erro ao abrir IndexedDB:', request.error);
                reject(request.error);
            };
            
            request.onsuccess = () => {
                this.db = request.result;
                this.initialized = true;
                console.log('✅ IndexedDB inicializado com sucesso');
                resolve(this.db);
            };
            
            request.onupgradeneeded = (event) => {
                const db = event.target.result;
                
                // Store para produtos
                if (!db.objectStoreNames.contains('products')) {
                    const productStore = db.createObjectStore('products', { keyPath: 'id' });
                    productStore.createIndex('name', 'name', { unique: false });
                    productStore.createIndex('category', 'category', { unique: false });
                    productStore.createIndex('isPending', 'isPending', { unique: false });
                }
                
                // Store para compras
                if (!db.objectStoreNames.contains('purchases')) {
                    const purchaseStore = db.createObjectStore('purchases', { keyPath: 'id' });
                    purchaseStore.createIndex('product_id', 'product_id', { unique: false });
                    purchaseStore.createIndex('date', 'purchase_date', { unique: false });
                    purchaseStore.createIndex('isPending', 'isPending', { unique: false });
                }
                
                // Store para transações financeiras (CashFlow)
                if (!db.objectStoreNames.contains('cashflows')) {
                    const cashflowStore = db.createObjectStore('cashflows', { keyPath: 'id' });
                    cashflowStore.createIndex('type', 'type', { unique: false });
                    cashflowStore.createIndex('date', 'transaction_date', { unique: false });
                    cashflowStore.createIndex('isPending', 'isPending', { unique: false });
                }
                
                // Store para agenda financeira (FinancialSchedule)
                if (!db.objectStoreNames.contains('schedules')) {
                    const scheduleStore = db.createObjectStore('schedules', { keyPath: 'id' });
                    scheduleStore.createIndex('date', 'scheduled_date', { unique: false });
                    scheduleStore.createIndex('isPending', 'isPending', { unique: false });
                }
                
                // Store para fila de sincronização (operações pendentes)
                if (!db.objectStoreNames.contains('syncQueue')) {
                    const queueStore = db.createObjectStore('syncQueue', { keyPath: 'id', autoIncrement: true });
                    queueStore.createIndex('type', 'type', { unique: false });
                    queueStore.createIndex('operation', 'operation', { unique: false });
                    queueStore.createIndex('timestamp', 'timestamp', { unique: false });
                    queueStore.createIndex('status', 'status', { unique: false });
                }
                
                // Store para dados pendentes de sincronização (deprecated, usando syncQueue)
                if (!db.objectStoreNames.contains('pendingSync')) {
                    const pendingStore = db.createObjectStore('pendingSync', { keyPath: 'id', autoIncrement: true });
                    pendingStore.createIndex('type', 'type', { unique: false });
                    pendingStore.createIndex('timestamp', 'timestamp', { unique: false });
                }
                
                console.log('Estrutura do IndexedDB criada');
            };
        });
    }
    
    // Escutar mudanças de status online/offline
    setupOnlineStatusListener() {
        window.addEventListener('online', () => {
            this.isOnline = true;
            console.log('Conexão restaurada - iniciando sincronização');
            this.syncPendingData();
        });
        
        window.addEventListener('offline', () => {
            this.isOnline = false;
            console.log('Conexão perdida - modo offline ativado');
        });
    }
    
    // ========== PRODUTOS ==========
    
    // Salvar produto
    async saveProduct(product) {
        if (this.isOnline) {
            try {
                const response = await this.syncToServer('POST', '/api/products', product);
                // Salvar também localmente para cache
                await this.saveProductOffline(response);
                return response;
            } catch (error) {
                console.log('Erro ao salvar online, salvando localmente e adicionando à fila:', error);
                const saved = await this.saveProductOffline(product);
                // Adicionar à fila de sincronização
                await this.addToSyncQueue('product', 'create', saved);
                return saved;
            }
        } else {
            const saved = await this.saveProductOffline(product);
            // Adicionar à fila de sincronização
            await this.addToSyncQueue('product', 'create', saved);
            return saved;
        }
    }
    
    // Salvar produto offline
    async saveProductOffline(product) {
        const transaction = this.db.transaction(['products'], 'readwrite');
        const store = transaction.objectStore('products');
        
        // Gerar ID temporário se não existir
        if (!product.id) {
            product.id = 'temp_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
            product.isPending = true;
        }
        
        return new Promise((resolve, reject) => {
            const request = store.put(product);
            request.onsuccess = () => {
                console.log('Produto salvo offline:', product.id);
                resolve(product);
            };
            request.onerror = () => reject(request.error);
        });
    }
    
    // Buscar produtos
    async getProducts() {
        // Aguardar inicialização se necessário
        await this.waitForInit();
        
        if (!this.db) {
            console.warn('IndexedDB não disponível, retornando array vazio');
            return [];
        }
        
        const transaction = this.db.transaction(['products'], 'readonly');
        const store = transaction.objectStore('products');
        
        return new Promise((resolve, reject) => {
            const request = store.getAll();
            request.onsuccess = () => {
                console.log('✅ Produtos carregados do cache:', request.result.length);
                resolve(request.result);
            };
            request.onerror = () => {
                console.error('❌ Erro ao buscar produtos:', request.error);
                resolve([]); // Retornar array vazio em vez de rejeitar
            };
        });
    }
    
    // ========== COMPRAS ==========
    
    // Salvar compra
    async savePurchase(purchase) {
        if (this.isOnline) {
            try {
                const response = await this.syncToServer('POST', '/api/purchases', purchase);
                // Salvar também localmente para cache
                await this.savePurchaseOffline(response);
                return response;
            } catch (error) {
                console.log('Erro ao salvar compra online, salvando localmente e adicionando à fila:', error);
                const saved = await this.savePurchaseOffline(purchase);
                // Adicionar à fila de sincronização
                await this.addToSyncQueue('purchase', 'create', saved);
                return saved;
            }
        } else {
            const saved = await this.savePurchaseOffline(purchase);
            // Adicionar à fila de sincronização
            await this.addToSyncQueue('purchase', 'create', saved);
            return saved;
        }
    }
    
    // Salvar compra offline
    async savePurchaseOffline(purchase) {
        // Aguardar inicialização se necessário
        await this.waitForInit();
        
        if (!this.db) {
            throw new Error('IndexedDB não está disponível');
        }
        
        const transaction = this.db.transaction(['purchases'], 'readwrite');
        const store = transaction.objectStore('purchases');
        
        // Garantir que purchase tem uma estrutura válida
        if (!purchase.id) {
            purchase.id = 'temp_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
        }
        
        // Garantir que tem isPending
        if (purchase.isPending === undefined) {
            purchase.isPending = true;
        }
        
        // Se purchase.items é um array, precisamos estruturar corretamente
        // A compra pode ser salva como um objeto único ou como múltiplas entradas
        const purchaseToSave = {
            id: purchase.id,
            isPending: purchase.isPending,
            items: purchase.items || [],
            store: purchase.store || purchase.purchase_store || null,
            purchase_date: purchase.purchase_date || purchase.date || new Date().toISOString(),
            total: purchase.total || 0,
            timestamp: purchase.timestamp || new Date().toISOString(),
            user_id: purchase.user_id || null
        };
        
        return new Promise((resolve, reject) => {
            const request = store.put(purchaseToSave);
            request.onsuccess = () => {
                console.log('✅ Compra salva offline:', purchaseToSave.id);
                resolve(purchaseToSave);
            };
            request.onerror = () => {
                console.error('❌ Erro ao salvar compra offline:', request.error);
                reject(request.error);
            };
        });
    }
    
    // Buscar compras
    async getPurchases() {
        // Aguardar inicialização se necessário
        await this.waitForInit();
        
        if (!this.db) {
            console.warn('IndexedDB não disponível, retornando array vazio');
            return [];
        }
        
        const transaction = this.db.transaction(['purchases'], 'readonly');
        const store = transaction.objectStore('purchases');
        
        return new Promise((resolve, reject) => {
            const request = store.getAll();
            request.onsuccess = () => {
                console.log('✅ Compras carregadas do cache:', request.result.length);
                resolve(request.result);
            };
            request.onerror = () => {
                console.error('❌ Erro ao buscar compras:', request.error);
                resolve([]); // Retornar array vazio em vez de rejeitar
            };
        });
    }
    
    // ========== FILA DE SINCRONIZAÇÃO ==========
    
    // Adicionar operação à fila de sincronização
    async addToSyncQueue(type, operation, data) {
        const transaction = this.db.transaction(['syncQueue'], 'readwrite');
        const store = transaction.objectStore('syncQueue');
        
        const queueItem = {
            type: type, // 'product', 'purchase', 'cashflow', 'schedule'
            operation: operation, // 'create', 'update', 'delete'
            data: data,
            timestamp: Date.now(),
            status: 'pending',
            retries: 0
        };
        
        return new Promise((resolve, reject) => {
            const request = store.add(queueItem);
            request.onsuccess = () => {
                console.log(`Operação ${operation} de ${type} adicionada à fila`);
                resolve(request.result);
            };
            request.onerror = () => reject(request.error);
        });
    }
    
    // Processar fila de sincronização
    async processSyncQueue() {
        if (!this.isOnline) {
            console.log('Offline - não é possível processar fila');
            return;
        }
        
        const transaction = this.db.transaction(['syncQueue'], 'readwrite');
        const store = transaction.objectStore('syncQueue');
        
        return new Promise((resolve, reject) => {
            const request = store.index('status').getAll('pending');
            request.onsuccess = async () => {
                const queueItems = request.result;
                console.log(`Processando ${queueItems.length} itens na fila de sincronização...`);
                
                for (const item of queueItems) {
                    try {
                        await this.processQueueItem(item);
                    } catch (error) {
                        console.error(`Erro ao processar item da fila:`, error);
                        // Incrementar retries
                        item.retries++;
                        if (item.retries < 3) {
                            // Tentar novamente depois
                            store.put(item);
                        } else {
                            // Marcar como erro após 3 tentativas
                            item.status = 'error';
                            store.put(item);
                        }
                    }
                }
                
                console.log('Fila de sincronização processada');
                resolve();
            };
            request.onerror = () => reject(request.error);
        });
    }
    
    // Processar item individual da fila
    async processQueueItem(item) {
        const transaction = this.db.transaction(['syncQueue'], 'readwrite');
        const store = transaction.objectStore('syncQueue');
        
        try {
            let response;
            
            switch (item.type) {
                case 'product':
                    response = await this.syncProductOperation(item.operation, item.data);
                    break;
                case 'purchase':
                    response = await this.syncPurchaseOperation(item.operation, item.data);
                    break;
                case 'cashflow':
                    response = await this.syncCashFlowOperation(item.operation, item.data);
                    break;
                case 'schedule':
                    response = await this.syncScheduleOperation(item.operation, item.data);
                    break;
                default:
                    throw new Error(`Tipo desconhecido: ${item.type}`);
            }
            
            // Marcar como concluído
            item.status = 'completed';
            item.syncedAt = Date.now();
            item.serverId = response.id;
            store.put(item);
            
            console.log(`${item.type} ${item.operation} sincronizado com sucesso`);
            
        } catch (error) {
            throw error;
        }
    }
    
    // ========== SINCRONIZAÇÃO ==========
    
    // Sincronizar dados pendentes
    async syncPendingData() {
        if (!this.isOnline) {
            console.log('Offline - sincronização não disponível');
            return;
        }
        
        try {
            console.log('Iniciando sincronização completa de dados pendentes...');
            
            // Processar fila de sincronização primeiro
            await this.processSyncQueue();
            
            // Sincronizar produtos pendentes (legado)
            await this.syncPendingProducts();
            
            // Sincronizar compras pendentes (legado)
            await this.syncPendingPurchases();
            
            // Sincronizar transações financeiras pendentes
            await this.syncPendingCashFlows();
            
            // Sincronizar agenda pendente
            await this.syncPendingSchedules();
            
            console.log('Sincronização completa concluída');
            this.showSyncNotification('Sincronização concluída com sucesso!');
        } catch (error) {
            console.error('Erro na sincronização:', error);
            this.showSyncNotification('Erro na sincronização. Alguns dados podem não ter sido sincronizados.', 'error');
        }
    }
    
    // Sincronizar produtos pendentes
    async syncPendingProducts() {
        const products = await this.getProducts();
        const pendingProducts = products.filter(p => p.isPending);
        
        for (const product of pendingProducts) {
            try {
                const response = await this.syncToServer('POST', '/api/products', product);
                
                // Atualizar produto local com ID real
                await this.updateProductId(product.id, response.id);
                
                console.log('Produto sincronizado:', product.name);
            } catch (error) {
                console.error('Erro ao sincronizar produto:', product.name, error);
            }
        }
    }
    
    // Sincronizar compras pendentes
    async syncPendingPurchases() {
        const purchases = await this.getPurchases();
        const pendingPurchases = purchases.filter(p => p.isPending);
        
        for (const purchase of pendingPurchases) {
            try {
                const response = await this.syncToServer('POST', '/api/purchases', purchase);
                
                // Atualizar compra local com ID real
                await this.updatePurchaseId(purchase.id, response.id);
                
                console.log('Compra sincronizada:', purchase.id);
            } catch (error) {
                console.error('Erro ao sincronizar compra:', purchase.id, error);
            }
        }
    }
    
    // Atualizar ID do produto após sincronização
    async updateProductId(oldId, newId) {
        const transaction = this.db.transaction(['products'], 'readwrite');
        const store = transaction.objectStore('products');
        
        return new Promise((resolve, reject) => {
            const getRequest = store.get(oldId);
            getRequest.onsuccess = () => {
                const product = getRequest.result;
                if (product) {
                    product.id = newId;
                    product.isPending = false;
                    
                    const putRequest = store.put(product);
                    putRequest.onsuccess = () => {
                        store.delete(oldId);
                        resolve();
                    };
                    putRequest.onerror = () => reject(putRequest.error);
                } else {
                    resolve();
                }
            };
            getRequest.onerror = () => reject(getRequest.error);
        });
    }
    
    // Atualizar ID da compra após sincronização
    async updatePurchaseId(oldId, newId) {
        const transaction = this.db.transaction(['purchases'], 'readwrite');
        const store = transaction.objectStore('purchases');
        
        return new Promise((resolve, reject) => {
            const getRequest = store.get(oldId);
            getRequest.onsuccess = () => {
                const purchase = getRequest.result;
                if (purchase) {
                    purchase.id = newId;
                    purchase.isPending = false;
                    
                    const putRequest = store.put(purchase);
                    putRequest.onsuccess = () => {
                        store.delete(oldId);
                        resolve();
                    };
                    putRequest.onerror = () => reject(putRequest.error);
                } else {
                    resolve();
                }
            };
            getRequest.onerror = () => reject(getRequest.error);
        });
    }
    
    // ========== TRANSAÇÕES FINANCEIRAS (CASHFLOW) ==========
    
    // Salvar transação financeira
    async saveCashFlow(cashflow) {
        if (this.isOnline) {
            try {
                const response = await this.syncToServer('POST', '/api/cashflows', cashflow);
                await this.saveCashFlowOffline(response);
                return response;
            } catch (error) {
                console.log('Erro ao salvar transação online, salvando localmente e adicionando à fila:', error);
                const saved = await this.saveCashFlowOffline(cashflow);
                await this.addToSyncQueue('cashflow', 'create', saved);
                return saved;
            }
        } else {
            const saved = await this.saveCashFlowOffline(cashflow);
            await this.addToSyncQueue('cashflow', 'create', saved);
            return saved;
        }
    }
    
    // Salvar transação financeira offline
    async saveCashFlowOffline(cashflow) {
        const transaction = this.db.transaction(['cashflows'], 'readwrite');
        const store = transaction.objectStore('cashflows');
        
        if (!cashflow.id) {
            cashflow.id = 'temp_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
            cashflow.isPending = true;
        }
        
        return new Promise((resolve, reject) => {
            const request = store.put(cashflow);
            request.onsuccess = () => {
                console.log('Transação financeira salva offline:', cashflow.id);
                resolve(cashflow);
            };
            request.onerror = () => reject(request.error);
        });
    }
    
    // Buscar transações financeiras
    async getCashFlows() {
        const transaction = this.db.transaction(['cashflows'], 'readonly');
        const store = transaction.objectStore('cashflows');
        
        return new Promise((resolve, reject) => {
            const request = store.getAll();
            request.onsuccess = () => {
                console.log('Transações financeiras carregadas do cache:', request.result.length);
                resolve(request.result);
            };
            request.onerror = () => reject(request.error);
        });
    }
    
    // ========== AGENDA FINANCEIRA (SCHEDULE) ==========
    
    // Salvar agenda financeira
    async saveSchedule(schedule) {
        if (this.isOnline) {
            try {
                const response = await this.syncToServer('POST', '/api/schedules', schedule);
                await this.saveScheduleOffline(response);
                return response;
            } catch (error) {
                console.log('Erro ao salvar agenda online, salvando localmente e adicionando à fila:', error);
                const saved = await this.saveScheduleOffline(schedule);
                await this.addToSyncQueue('schedule', 'create', saved);
                return saved;
            }
        } else {
            const saved = await this.saveScheduleOffline(schedule);
            await this.addToSyncQueue('schedule', 'create', saved);
            return saved;
        }
    }
    
    // Salvar agenda offline
    async saveScheduleOffline(schedule) {
        const transaction = this.db.transaction(['schedules'], 'readwrite');
        const store = transaction.objectStore('schedules');
        
        if (!schedule.id) {
            schedule.id = 'temp_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
            schedule.isPending = true;
        }
        
        return new Promise((resolve, reject) => {
            const request = store.put(schedule);
            request.onsuccess = () => {
                console.log('Agenda salva offline:', schedule.id);
                resolve(schedule);
            };
            request.onerror = () => reject(request.error);
        });
    }
    
    // Buscar agenda
    async getSchedules() {
        const transaction = this.db.transaction(['schedules'], 'readonly');
        const store = transaction.objectStore('schedules');
        
        return new Promise((resolve, reject) => {
            const request = store.getAll();
            request.onsuccess = () => {
                console.log('Agenda carregada do cache:', request.result.length);
                resolve(request.result);
            };
            request.onerror = () => reject(request.error);
        });
    }
    
    // ========== OPERAÇÕES DE SINCRONIZAÇÃO ==========
    
    // Sincronizar operação de produto
    async syncProductOperation(operation, data) {
        switch (operation) {
            case 'create':
                return await this.syncToServer('POST', '/api/products', data);
            case 'update':
                return await this.syncToServer('PUT', `/api/products/${data.id}`, data);
            case 'delete':
                return await this.syncToServer('DELETE', `/api/products/${data.id}`, {});
            default:
                throw new Error(`Operação desconhecida: ${operation}`);
        }
    }
    
    // Sincronizar operação de compra
    async syncPurchaseOperation(operation, data) {
        switch (operation) {
            case 'create':
                return await this.syncToServer('POST', '/api/purchases', data);
            case 'update':
                return await this.syncToServer('PUT', `/api/purchases/${data.id}`, data);
            case 'delete':
                return await this.syncToServer('DELETE', `/api/purchases/${data.id}`, {});
            default:
                throw new Error(`Operação desconhecida: ${operation}`);
        }
    }
    
    // Sincronizar operação de transação financeira
    async syncCashFlowOperation(operation, data) {
        switch (operation) {
            case 'create':
                return await this.syncToServer('POST', '/api/cashflows', data);
            case 'update':
                return await this.syncToServer('PUT', `/api/cashflows/${data.id}`, data);
            case 'delete':
                return await this.syncToServer('DELETE', `/api/cashflows/${data.id}`, {});
            default:
                throw new Error(`Operação desconhecida: ${operation}`);
        }
    }
    
    // Sincronizar operação de agenda
    async syncScheduleOperation(operation, data) {
        switch (operation) {
            case 'create':
                return await this.syncToServer('POST', '/api/schedules', data);
            case 'update':
                return await this.syncToServer('PUT', `/api/schedules/${data.id}`, data);
            case 'delete':
                return await this.syncToServer('DELETE', `/api/schedules/${data.id}`, {});
            default:
                throw new Error(`Operação desconhecida: ${operation}`);
        }
    }
    
    // Sincronizar transações financeiras pendentes
    async syncPendingCashFlows() {
        const cashflows = await this.getCashFlows();
        const pendingCashFlows = cashflows.filter(cf => cf.isPending);
        
        for (const cashflow of pendingCashFlows) {
            try {
                const response = await this.syncToServer('POST', '/api/cashflows', cashflow);
                
                // Atualizar transação local com ID real
                await this.updateCashFlowId(cashflow.id, response.id);
                
                console.log('Transação financeira sincronizada:', cashflow.id);
            } catch (error) {
                console.error('Erro ao sincronizar transação financeira:', cashflow.id, error);
            }
        }
    }
    
    // Sincronizar agenda pendente
    async syncPendingSchedules() {
        const schedules = await this.getSchedules();
        const pendingSchedules = schedules.filter(s => s.isPending);
        
        for (const schedule of pendingSchedules) {
            try {
                const response = await this.syncToServer('POST', '/api/schedules', schedule);
                
                // Atualizar agenda local com ID real
                await this.updateScheduleId(schedule.id, response.id);
                
                console.log('Agenda sincronizada:', schedule.id);
            } catch (error) {
                console.error('Erro ao sincronizar agenda:', schedule.id, error);
            }
        }
    }
    
    // Atualizar ID da transação financeira após sincronização
    async updateCashFlowId(oldId, newId) {
        const transaction = this.db.transaction(['cashflows'], 'readwrite');
        const store = transaction.objectStore('cashflows');
        
        return new Promise((resolve, reject) => {
            const getRequest = store.get(oldId);
            getRequest.onsuccess = () => {
                const cashflow = getRequest.result;
                if (cashflow) {
                    cashflow.id = newId;
                    cashflow.isPending = false;
                    
                    const putRequest = store.put(cashflow);
                    putRequest.onsuccess = () => {
                        store.delete(oldId);
                        resolve();
                    };
                    putRequest.onerror = () => reject(putRequest.error);
                } else {
                    resolve();
                }
            };
            getRequest.onerror = () => reject(getRequest.error);
        });
    }
    
    // Atualizar ID da agenda após sincronização
    async updateScheduleId(oldId, newId) {
        const transaction = this.db.transaction(['schedules'], 'readwrite');
        const store = transaction.objectStore('schedules');
        
        return new Promise((resolve, reject) => {
            const getRequest = store.get(oldId);
            getRequest.onsuccess = () => {
                const schedule = getRequest.result;
                if (schedule) {
                    schedule.id = newId;
                    schedule.isPending = false;
                    
                    const putRequest = store.put(schedule);
                    putRequest.onsuccess = () => {
                        store.delete(oldId);
                        resolve();
                    };
                    putRequest.onerror = () => reject(putRequest.error);
                } else {
                    resolve();
                }
            };
            getRequest.onerror = () => reject(getRequest.error);
        });
    }
    
    // ========== COMUNICAÇÃO COM SERVIDOR ==========
    
    // Sincronizar com servidor
    async syncToServer(method, url, data) {
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json'
            },
            body: method !== 'DELETE' ? JSON.stringify(data) : undefined
        });
        
        if (!response.ok) {
            const errorData = await response.json().catch(() => ({ message: 'Erro desconhecido' }));
            throw new Error(`Erro HTTP ${response.status}: ${errorData.message || response.statusText}`);
        }
        
        // Para DELETE, retornar um objeto com id
        if (method === 'DELETE') {
            return { id: data.id, deleted: true };
        }
        
        return await response.json();
    }
    
    // ========== UTILITÁRIOS ==========
    
    // Verificar se está online
    isOnlineStatus() {
        return this.isOnline;
    }
    
    // Mostrar notificação de sincronização
    showSyncNotification(message, type = 'success') {
        // Criar elemento de notificação
        const notification = document.createElement('div');
        notification.className = `sync-notification ${type}`;
        notification.style.cssText = `
            position: fixed;
            top: 80px;
            right: 20px;
            background: ${type === 'success' ? '#10b981' : '#ef4444'};
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 9999;
            animation: slideIn 0.3s ease;
            max-width: 300px;
        `;
        notification.textContent = message;
        
        // Adicionar animação CSS se não existir
        if (!document.querySelector('#sync-notification-style')) {
            const style = document.createElement('style');
            style.id = 'sync-notification-style';
            style.textContent = `
                @keyframes slideIn {
                    from {
                        transform: translateX(100%);
                        opacity: 0;
                    }
                    to {
                        transform: translateX(0);
                        opacity: 1;
                    }
                }
            `;
            document.head.appendChild(style);
        }
        
        document.body.appendChild(notification);
        
        // Remover após 3 segundos
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }
    
    // Limpar cache
    async clearCache() {
        const stores = ['products', 'purchases', 'cashflows', 'schedules', 'syncQueue', 'pendingSync'];
        const transaction = this.db.transaction(stores, 'readwrite');
        
        await Promise.all(
            stores.map(storeName => 
                new Promise((resolve) => {
                    transaction.objectStore(storeName).clear().onsuccess = resolve;
                })
            )
        );
        
        console.log('Cache limpo');
    }
}

// Instanciar storage global
window.offlineStorage = new OfflineStorage();

// Exportar para uso em outros scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = OfflineStorage;
}
