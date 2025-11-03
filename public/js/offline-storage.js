// Sistema de Armazenamento Offline Simples e Robusto
// Otimizado para Laravel Herd + Mobile

class OfflineStorage {
    constructor() {
        this.dbName = 'ProdutosAppDB';
        this.dbVersion = 1;
        this.db = null;
        this.initialized = false;
        this.initPromise = null;
    }
    
    // Aguardar inicialização
    async waitForInit() {
        if (this.initialized && this.db) {
            return this.db;
        }
        
        if (this.initPromise) {
            await this.initPromise;
            return this.db;
        }
        
        this.initPromise = this.init();
        await this.initPromise;
        return this.db;
    }
    
    // Inicializar IndexedDB
    async init() {
        if (!window.indexedDB) {
            throw new Error('IndexedDB não está disponível neste navegador');
        }
        
        return new Promise((resolve, reject) => {
            try {
                const request = indexedDB.open(this.dbName, this.dbVersion);
                
                request.onerror = () => {
                    const error = request.error || new Error('Erro ao abrir IndexedDB');
                    console.error('❌ Erro ao abrir IndexedDB:', error);
                    reject(error);
                };
                
                request.onblocked = () => {
                    console.warn('⚠️ IndexedDB bloqueado - pode estar aberto em outra aba');
                    // Aguardar e tentar novamente
                    setTimeout(() => {
                        if (request.result) {
                            this.db = request.result;
                            this.initialized = true;
                            resolve(this.db);
                        }
                    }, 1000);
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
                        const productStore = db.createObjectStore('products', { keyPath: 'id', autoIncrement: true });
                        productStore.createIndex('name', 'name', { unique: false });
                        productStore.createIndex('isPending', 'isPending', { unique: false });
                    }
                    
                    // Store para compras
                    if (!db.objectStoreNames.contains('purchases')) {
                        const purchaseStore = db.createObjectStore('purchases', { keyPath: 'id', autoIncrement: true });
                        purchaseStore.createIndex('date', 'date', { unique: false });
                        purchaseStore.createIndex('isPending', 'isPending', { unique: false });
                    }
                    
                    // Store para fila de sincronização
                    if (!db.objectStoreNames.contains('syncQueue')) {
                        db.createObjectStore('syncQueue', { keyPath: 'id', autoIncrement: true });
                    }
                };
            } catch (error) {
                console.error('❌ Erro ao inicializar IndexedDB:', error);
                reject(error);
            }
        });
    }
    
    // Salvar produto offline
    async saveProduct(product) {
        await this.waitForInit();
        
        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(['products'], 'readwrite');
            const store = transaction.objectStore('products');
            
            const productData = {
                ...product,
                isPending: true,
                timestamp: new Date().toISOString()
            };
            
            const request = store.add(productData);
            
            request.onsuccess = () => {
                console.log('✅ Produto salvo offline:', productData);
                resolve(request.result);
            };
            
            request.onerror = () => {
                console.error('❌ Erro ao salvar produto offline:', request.error);
                reject(request.error);
            };
        });
    }
    
    // Salvar compra offline
    async savePurchase(purchase) {
        await this.waitForInit();
        
        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(['purchases'], 'readwrite');
            const store = transaction.objectStore('purchases');
            
            const purchaseData = {
                ...purchase,
                isPending: true,
                timestamp: new Date().toISOString()
            };
            
            const request = store.add(purchaseData);
            
            request.onsuccess = () => {
                console.log('✅ Compra salva offline:', purchaseData);
                resolve(request.result);
            };
            
            request.onerror = () => {
                console.error('❌ Erro ao salvar compra offline:', request.error);
                reject(request.error);
            };
        });
    }
    
    // Obter compras pendentes
    async getPendingPurchases() {
        await this.waitForInit();
        
        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(['purchases'], 'readonly');
            const store = transaction.objectStore('purchases');
            
            // Buscar todas e filtrar pendentes (caso o índice não exista)
            const request = store.getAll();
            
            request.onsuccess = () => {
                const all = request.result || [];
                const pending = all.filter(p => p.isPending === true);
                resolve(pending);
            };
            
            request.onerror = () => {
                reject(request.error);
            };
        });
    }
    
    // Remover compra do cache
    async removePurchase(purchaseId) {
        await this.waitForInit();
        
        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(['purchases'], 'readwrite');
            const store = transaction.objectStore('purchases');
            
            const request = store.delete(purchaseId);
            
            request.onsuccess = () => {
                resolve(true);
            };
            
            request.onerror = () => {
                reject(request.error);
            };
        });
    }
    
    // Verificar status online
    isOnlineStatus() {
        return navigator.onLine;
    }
}

// Instanciar globalmente
window.offlineStorage = new OfflineStorage();

// Exportar se for módulo
if (typeof module !== 'undefined' && module.exports) {
    module.exports = OfflineStorage;
}

