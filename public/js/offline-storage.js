// Sistema de armazenamento offline com IndexedDB
class OfflineStorage {
    constructor() {
        this.dbName = 'ProdutosAppDB';
        this.dbVersion = 1;
        this.db = null;
        this.isOnline = navigator.onLine;
        
        this.init();
        this.setupOnlineStatusListener();
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
                console.log('IndexedDB inicializado com sucesso');
                resolve(this.db);
            };
            
            request.onupgradeneeded = (event) => {
                const db = event.target.result;
                
                // Store para produtos
                if (!db.objectStoreNames.contains('products')) {
                    const productStore = db.createObjectStore('products', { keyPath: 'id' });
                    productStore.createIndex('name', 'name', { unique: false });
                    productStore.createIndex('category', 'category', { unique: false });
                }
                
                // Store para compras
                if (!db.objectStoreNames.contains('purchases')) {
                    const purchaseStore = db.createObjectStore('purchases', { keyPath: 'id' });
                    purchaseStore.createIndex('product_id', 'product_id', { unique: false });
                    purchaseStore.createIndex('date', 'purchase_date', { unique: false });
                }
                
                // Store para dados pendentes de sincronização
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
                return await this.syncToServer('POST', '/api/products', product);
            } catch (error) {
                console.log('Erro ao salvar online, salvando localmente:', error);
                return await this.saveProductOffline(product);
            }
        } else {
            return await this.saveProductOffline(product);
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
        const transaction = this.db.transaction(['products'], 'readonly');
        const store = transaction.objectStore('products');
        
        return new Promise((resolve, reject) => {
            const request = store.getAll();
            request.onsuccess = () => {
                console.log('Produtos carregados do cache:', request.result.length);
                resolve(request.result);
            };
            request.onerror = () => reject(request.error);
        });
    }
    
    // ========== COMPRAS ==========
    
    // Salvar compra
    async savePurchase(purchase) {
        if (this.isOnline) {
            try {
                return await this.syncToServer('POST', '/api/purchases', purchase);
            } catch (error) {
                console.log('Erro ao salvar compra online, salvando localmente:', error);
                return await this.savePurchaseOffline(purchase);
            }
        } else {
            return await this.savePurchaseOffline(purchase);
        }
    }
    
    // Salvar compra offline
    async savePurchaseOffline(purchase) {
        const transaction = this.db.transaction(['purchases'], 'readwrite');
        const store = transaction.objectStore('purchases');
        
        // Gerar ID temporário se não existir
        if (!purchase.id) {
            purchase.id = 'temp_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
            purchase.isPending = true;
        }
        
        return new Promise((resolve, reject) => {
            const request = store.put(purchase);
            request.onsuccess = () => {
                console.log('Compra salva offline:', purchase.id);
                resolve(purchase);
            };
            request.onerror = () => reject(request.error);
        });
    }
    
    // Buscar compras
    async getPurchases() {
        const transaction = this.db.transaction(['purchases'], 'readonly');
        const store = transaction.objectStore('purchases');
        
        return new Promise((resolve, reject) => {
            const request = store.getAll();
            request.onsuccess = () => {
                console.log('Compras carregadas do cache:', request.result.length);
                resolve(request.result);
            };
            request.onerror = () => reject(request.error);
        });
    }
    
    // ========== SINCRONIZAÇÃO ==========
    
    // Sincronizar dados pendentes
    async syncPendingData() {
        if (!this.isOnline) {
            console.log('Offline - sincronização não disponível');
            return;
        }
        
        try {
            console.log('Iniciando sincronização de dados pendentes...');
            
            // Sincronizar produtos pendentes
            await this.syncPendingProducts();
            
            // Sincronizar compras pendentes
            await this.syncPendingPurchases();
            
            console.log('Sincronização concluída');
        } catch (error) {
            console.error('Erro na sincronização:', error);
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
    
    // ========== COMUNICAÇÃO COM SERVIDOR ==========
    
    // Sincronizar com servidor
    async syncToServer(method, url, data) {
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        });
        
        if (!response.ok) {
            throw new Error(`Erro HTTP: ${response.status}`);
        }
        
        return await response.json();
    }
    
    // ========== UTILITÁRIOS ==========
    
    // Verificar se está online
    isOnlineStatus() {
        return this.isOnline;
    }
    
    // Limpar cache
    async clearCache() {
        const transaction = this.db.transaction(['products', 'purchases', 'pendingSync'], 'readwrite');
        
        await Promise.all([
            new Promise((resolve) => {
                transaction.objectStore('products').clear().onsuccess = resolve;
            }),
            new Promise((resolve) => {
                transaction.objectStore('purchases').clear().onsuccess = resolve;
            }),
            new Promise((resolve) => {
                transaction.objectStore('pendingSync').clear().onsuccess = resolve;
            })
        ]);
        
        console.log('Cache limpo');
    }
}

// Instanciar storage global
window.offlineStorage = new OfflineStorage();

// Exportar para uso em outros scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = OfflineStorage;
}
