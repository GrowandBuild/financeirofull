/**
 * Script de teste para verificar se o sistema offline está funcionando
 * Execute este no console do navegador para testar
 */

// Função de teste
async function testarOffline() {
    console.log('🧪 Testando sistema offline...\n');
    
    // 1. Verificar se offlineStorage existe
    if (!window.offlineStorage) {
        console.error('❌ window.offlineStorage não está disponível!');
        console.log('Verifique se o arquivo offline-storage.js está sendo carregado.');
        return;
    }
    console.log('✅ window.offlineStorage encontrado');
    
    // 2. Verificar IndexedDB
    if (!window.offlineStorage.db) {
        console.warn('⚠️ IndexedDB ainda não inicializado, aguardando...');
        await new Promise(resolve => setTimeout(resolve, 1000));
    }
    
    if (!window.offlineStorage.db) {
        console.error('❌ IndexedDB não foi inicializado!');
        console.log('Tente recarregar a página.');
        return;
    }
    console.log('✅ IndexedDB inicializado');
    
    // 3. Verificar status online
    const isOnline = window.offlineStorage.isOnlineStatus();
    console.log(`📡 Status online: ${isOnline ? 'Online' : 'Offline'}`);
    
    // 4. Testar salvamento de produto
    try {
        const testProduct = {
            name: 'Produto Teste',
            price: 10.50,
            category: 'Teste'
        };
        
        console.log('\n📦 Testando salvamento de produto...');
        const savedProduct = await window.offlineStorage.saveProduct(testProduct);
        console.log('✅ Produto salvo:', savedProduct);
        
        // Buscar produtos
        const products = await window.offlineStorage.getProducts();
        console.log(`✅ Total de produtos no cache: ${products.length}`);
        
    } catch (error) {
        console.error('❌ Erro ao testar salvamento de produto:', error);
    }
    
    // 5. Testar salvamento de compra
    try {
        const testPurchase = {
            product_id: 1,
            quantity: 2,
            price: 10.50,
            store: 'Loja Teste',
            purchase_date: new Date().toISOString()
        };
        
        console.log('\n🛒 Testando salvamento de compra...');
        const savedPurchase = await window.offlineStorage.savePurchase(testPurchase);
        console.log('✅ Compra salva:', savedPurchase);
        
        // Buscar compras
        const purchases = await window.offlineStorage.getPurchases();
        console.log(`✅ Total de compras no cache: ${purchases.length}`);
        
    } catch (error) {
        console.error('❌ Erro ao testar salvamento de compra:', error);
    }
    
    // 6. Verificar fila de sincronização
    try {
        console.log('\n🔄 Verificando fila de sincronização...');
        const db = window.offlineStorage.db;
        const transaction = db.transaction(['syncQueue'], 'readonly');
        const store = transaction.objectStore('syncQueue');
        const request = store.getAll();
        
        request.onsuccess = () => {
            const queueItems = request.result;
            console.log(`✅ Itens na fila de sincronização: ${queueItems.length}`);
            if (queueItems.length > 0) {
                console.log('Itens pendentes:', queueItems);
            }
        };
        
        request.onerror = () => {
            console.error('❌ Erro ao verificar fila de sincronização');
        };
        
    } catch (error) {
        console.error('❌ Erro ao verificar fila:', error);
    }
    
    console.log('\n✨ Teste concluído!');
    console.log('\n💡 Dicas:');
    console.log('  - Desative a internet e tente fazer uma compra');
    console.log('  - Ative a internet novamente e veja a sincronização automática');
    console.log('  - Verifique o console para logs de sincronização');
}

// Auto-executar quando carregado
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        setTimeout(testarOffline, 2000); // Aguardar 2 segundos para inicializar
    });
} else {
    setTimeout(testarOffline, 2000);
}

