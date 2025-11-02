@extends('layouts.app')

@section('title', 'Modo Compra')

@section('content')
<!-- Premium Header -->
<div class="premium-header">
    <div class="header-content">
        <div class="header-title">
            <h1>Modo Compra</h1>
            <span class="header-subtitle">Adicione produtos ao carrinho</span>
        </div>
        <div class="header-actions">
            <button class="action-btn" onclick="clearCart()">
                <i class="bi bi-trash"></i>
            </button>
            <button class="action-btn" onclick="savePurchase()">
                <i class="bi bi-check-circle"></i>
            </button>
        </div>
    </div>
</div>

<!-- Premium Content -->
<div class="premium-content">
    <!-- Cart Summary -->
    <div class="cart-summary" id="cartSummary" style="display: none;">
        <div class="spend-hero-card">
            <div class="spend-header">
                <div class="spend-icon">
                    <i class="bi bi-cart3"></i>
                </div>
                <div class="spend-info">
                    <h3 class="spend-title">Carrinho de Compras</h3>
                    <div class="spend-amount" id="cartTotal">R$ 0,00</div>
                    <div class="spend-trend">
                        <i class="bi bi-bag-check"></i>
                        <span id="cartItems">0 itens</span>
                    </div>
                </div>
                <button class="add-product-btn" onclick="toggleProductList()">
                    <i class="bi bi-plus-lg"></i>
                </button>
            </div>
            <!-- Botão de Finalizar Compra -->
            <div class="checkout-section" id="checkoutSection" style="display: none;">
                <button class="checkout-btn" onclick="finalizePurchase()">
                    <i class="bi bi-credit-card"></i>
                    Finalizar Compra
                </button>
            </div>
        </div>
    </div>

    <!-- Store Information -->
    <div class="store-info-section">
        <div class="search-form-container">
            <div class="filter-header">
                <i class="bi bi-shop"></i>
                <span>Informações da Loja</span>
            </div>
            <div class="row">
                <div class="col-8">
                    <input type="text" 
                           class="premium-search-input" 
                           id="storeName" 
                           placeholder="Nome da loja/mercado..."
                           value="">
                </div>
                <div class="col-4">
                    <input type="date" 
                           class="premium-search-input" 
                           id="purchaseDate" 
                           value="{{ date('Y-m-d') }}">
                </div>
            </div>
        </div>
    </div>


    <!-- Product List -->
    <div class="product-list-section" id="productListSection">
        <div class="section-header">
            <h3 class="section-title">
                <i class="bi bi-grid-3x3-gap"></i>
                Produtos Disponíveis
            </h3>
            <div class="filter-section">
                <select class="premium-select" id="categoryFilter">
                    <option value="">Todas as categorias</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}">{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        
        <!-- Premium Product Grid -->
        <div class="premium-product-grid" id="productGrid">
            @if($products && $products->count() > 0)
                @foreach($products as $product)
                    <div class="premium-product-card cart-product-card" 
                         data-product-id="{{ $product->id }}"
                         data-product-name="{{ $product->name }}"
                         data-product-category="{{ $product->category }}"
                         data-product-image="{{ $product->image_url }}"
                         onclick="openProductModal({{ $product->id }}, '{{ $product->name }}', '{{ $product->category }}', '{{ $product->image_url }}')">
                        <div class="premium-product-image">
                            <img src="{{ $product->image_url }}" 
                                 alt="{{ $product->name }}" 
                                 class="img-fluid">
                            <div class="product-overlay">
                                <i class="bi bi-plus-circle"></i>
                            </div>
                        </div>
                        <div class="premium-product-info">
                            <h5 class="premium-product-name">{{ $product->name }}</h5>
                            <div class="premium-product-category">{{ $product->category ?? 'Sem categoria' }}</div>
                            <div class="premium-product-price">
                                @if($product->monthly_spend > 0)
                                    R$ {{ number_format($product->monthly_spend, 2, ',', '.') }}
                                    <small class="text-white/60 text-xs block">Total do mês</small>
                                @else
                                    Sem gastos
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    <!-- Cart Items List -->
    <div class="cart-items-section" id="cartItemsSection" style="display: none;">
        <div class="section-header">
            <h3 class="section-title">
                <i class="bi bi-cart-check"></i>
                Itens no Carrinho
            </h3>
        </div>
        
        <div class="cart-items-list" id="cartItemsList">
            <!-- Cart items will be dynamically added here -->
        </div>
    </div>
</div>

<!-- Product Selection Modal -->
<div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content premium-modal">
            <div class="modal-header">
                <h5 class="modal-title" id="productModalLabel">Selecionar Produto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <div class="product-modal-info">
                    <div class="product-modal-image">
                        <img id="modalProductImage" src="" alt="" class="img-fluid">
                    </div>
                    <div class="product-modal-details">
                        <h6 id="modalProductName"></h6>
                        <span id="modalProductCategory" class="text-muted"></span>
                    </div>
                </div>
                
                <!-- Variant Selection -->
                <div class="variant-section">
                    <label class="form-label">Tipo/Variante:</label>
                    <select class="form-select" id="variantSelect">
                        <option value="">Selecione o tipo...</option>
                    </select>
                </div>
                
                <!-- Unit Selection -->
                <div class="unit-section">
                    <label class="form-label">Unidade de Medida:</label>
                    <select class="form-select" id="unitSelect">
                        <option value="">Selecione a unidade...</option>
                    </select>
                </div>
                
                <!-- Quantity and Price -->
                <div class="row">
                    <div class="col-6">
                        <label class="form-label">Quantidade:</label>
                        <div class="input-group">
                            <button class="btn btn-outline-secondary" type="button" onclick="decreaseModalQuantity()">
                                <i class="bi bi-dash"></i>
                            </button>
                            <input type="number" class="form-control text-center" id="modalQuantity" value="1" min="1">
                            <button class="btn btn-outline-secondary" type="button" onclick="increaseModalQuantity()">
                                <i class="bi bi-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-6">
                        <label class="form-label">Preço Unitário:</label>
                        <div class="input-group">
                            <span class="input-group-text">R$</span>
                            <input type="number" class="form-control" id="modalPrice" placeholder="0,00" step="0.01" min="0">
                        </div>
                    </div>
                </div>
                
                <!-- Total Preview -->
                <div class="total-preview">
                    <div class="total-label">Total:</div>
                    <div class="total-value" id="modalTotal">R$ 0,00</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="addToCartFromModal()">Adicionar ao Carrinho</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let cart = {};
let cartTotal = 0;
let cartItemsCount = 0;
let currentProduct = null;

// Product variants and units database - será carregado dinamicamente
let productVariants = {};

// Carregar produtos com variantes do servidor
async function loadProductsWithVariants() {
    try {
        const response = await fetch('/api/products');
        const products = await response.json();
        
        // Converter para o formato esperado pelo JavaScript
        productVariants = {};
        products.forEach(product => {
            if (product.variants && product.variants.length > 0) {
                productVariants[product.name] = {
                    id: product.id,
                    category: product.category,
                    variants: product.variants.map(variant => variant.name),
                    units: [product.unit],
                    image_url: product.image_url
                };
            }
        });
        
        console.log('Produtos com variantes carregados:', productVariants);
    } catch (error) {
        console.error('Erro ao carregar produtos:', error);
    }
}

// Carregar produtos quando a página carregar
document.addEventListener('DOMContentLoaded', function() {
    loadProductsWithVariants();
});

// Dados hardcoded removidos - usando apenas API do banco de dados

// Get variants by category
function getVariantsByCategory(category) {
    // Buscar variantes dinamicamente do banco de dados
    for (const [productName, productData] of Object.entries(productVariants)) {
        if (productData.category === category) {
            return productData.variants || [];
        }
    }
    
    // Se não encontrar no banco, retornar array vazio
    return [];
}

// Get units by category - usando apenas dados do banco
function getUnitsByCategory(category) {
    // Buscar unidades dinamicamente do banco de dados
    for (const [productName, productData] of Object.entries(productVariants)) {
        if (productData.category === category) {
            return productData.units || [];
        }
    }
    
    // Se não encontrar no banco, retornar unidades básicas
    return ['unidade', 'kg', 'L', 'g', 'ml'];
}

// Initialize cart
function initializeCart() {
    console.log('Inicializando carrinho');
    cart = {};
    cartTotal = 0;
    cartItemsCount = 0;
    console.log('Carrinho inicializado:', cart);
    updateCartDisplay();
}

// Open product selection modal
function openProductModal(productId, productName, productCategory, productImage) {
    console.log('Abrindo modal para produto:', {
        productId: productId,
        productIdType: typeof productId,
        productName: productName,
        productCategory: productCategory,
        productImage: productImage
    });
    
    currentProduct = {
        id: parseInt(productId), // Garantir que seja um número
        name: productName,
        category: productCategory,
        image: productImage
    };
    
    console.log('currentProduct definido:', currentProduct);
    
    // Set modal content
    document.getElementById('modalProductImage').src = productImage;
    document.getElementById('modalProductName').textContent = productName;
    document.getElementById('modalProductCategory').textContent = productCategory;
    
    // Populate variants
    const variantSelect = document.getElementById('variantSelect');
    variantSelect.innerHTML = '<option value="">Selecione o tipo...</option>';
    
    if (productVariants[productName]) {
        productVariants[productName].variants.forEach(variant => {
            const option = document.createElement('option');
            option.value = variant;
            option.textContent = variant;
            variantSelect.appendChild(option);
        });
    } else {
        // Try to match by category for better variants
        const categoryVariants = getVariantsByCategory(productCategory);
        categoryVariants.forEach(variant => {
            const option = document.createElement('option');
            option.value = variant;
            option.textContent = variant;
            variantSelect.appendChild(option);
        });
    }
    
    // Populate units
    const unitSelect = document.getElementById('unitSelect');
    unitSelect.innerHTML = '<option value="">Selecione a unidade...</option>';
    
    if (productVariants[productName]) {
        productVariants[productName].units.forEach(unit => {
            const option = document.createElement('option');
            option.value = unit;
            option.textContent = unit;
            unitSelect.appendChild(option);
        });
    } else {
        // Try to match by category for better units
        const categoryUnits = getUnitsByCategory(productCategory);
        categoryUnits.forEach(unit => {
            const option = document.createElement('option');
            option.value = unit;
            option.textContent = unit;
            unitSelect.appendChild(option);
        });
    }
    
    // Reset modal values
    document.getElementById('modalQuantity').value = 1;
    document.getElementById('modalPrice').value = '';
    updateModalTotal();
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('productModal'));
    modal.show();
}

// Add product to cart from modal
function addToCartFromModal() {
    console.log('addToCartFromModal chamada, currentProduct:', currentProduct);
    
    const variant = document.getElementById('variantSelect').value;
    const unit = document.getElementById('unitSelect').value;
    const quantity = parseInt(document.getElementById('modalQuantity').value);
    const price = parseFloat(document.getElementById('modalPrice').value);
    
    console.log('Dados do modal:', { variant, unit, quantity, price });
    
    if (!variant || !unit || !price) {
        alert('Preencha todos os campos obrigatórios!');
        return;
    }
    
    if (!currentProduct || !currentProduct.id) {
        console.error('currentProduct não está definido ou não tem id:', currentProduct);
        alert('Erro: Produto não selecionado corretamente. Tente novamente.');
        return;
    }
    
    const cartKey = `${currentProduct.id}_${variant}_${unit}`;
    const displayName = `${currentProduct.name} - ${variant}`;
    
    console.log('cartKey gerado:', cartKey);
    
    if (!cart[cartKey]) {
        console.log('Criando novo item no carrinho:', {
            cartKey: cartKey,
            currentProduct: currentProduct,
            productId: currentProduct.id,
            productIdType: typeof currentProduct.id
        });
        
        cart[cartKey] = {
            id: currentProduct.id,
            name: currentProduct.name,
            variant: variant,
            unit: unit,
            displayName: displayName,
            category: currentProduct.category,
            image: currentProduct.image,
            quantity: 0,
            price: 0,
            total: 0
        };
        
        console.log('Item criado no carrinho:', cart[cartKey]);
    }
    
    cart[cartKey].quantity += quantity;
    cart[cartKey].price = price;
    cart[cartKey].total = cart[cartKey].quantity * cart[cartKey].price;
    
    console.log('Carrinho após adicionar item:', cart);
    console.log('Item específico no carrinho:', cart[cartKey]);
    
    updateCartDisplay();
    
    // Close modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('productModal'));
    modal.hide();
}

// Modal quantity controls
function increaseModalQuantity() {
    const quantityInput = document.getElementById('modalQuantity');
    quantityInput.value = parseInt(quantityInput.value) + 1;
    updateModalTotal();
}

function decreaseModalQuantity() {
    const quantityInput = document.getElementById('modalQuantity');
    if (parseInt(quantityInput.value) > 1) {
        quantityInput.value = parseInt(quantityInput.value) - 1;
        updateModalTotal();
    }
}

// Update modal total
function updateModalTotal() {
    const quantity = parseInt(document.getElementById('modalQuantity').value) || 0;
    const price = parseFloat(document.getElementById('modalPrice').value) || 0;
    const total = quantity * price;
    
    document.getElementById('modalTotal').textContent = `R$ ${total.toFixed(2).replace('.', ',')}`;
}

// Add product to cart (legacy function for compatibility)
function addToCart(productId, productName, productCategory, productImage, defaultPrice) {
    // This function is now handled by the modal
    console.log('Legacy addToCart called - use modal instead');
}

// Remove product from cart
function removeFromCart(productId) {
    if (cart[productId]) {
        cart[productId].quantity = 0;
        cart[productId].total = 0;
        updateCartDisplay();
        updateProductCard(productId);
    }
}

// Update quantity
function updateQuantity(productId, newQuantity) {
    if (cart[productId]) {
        cart[productId].quantity = Math.max(0, newQuantity);
        cart[productId].price = parseFloat(document.getElementById(`price-${productId}`).value) || 0;
        cart[productId].total = cart[productId].quantity * cart[productId].price;
        
        if (cart[productId].quantity === 0) {
            delete cart[productId];
        }
        
        updateCartDisplay();
        updateProductCard(productId);
    }
}

// Increase quantity
function increaseQuantity(productId) {
    const currentQty = cart[productId] ? cart[productId].quantity : 0;
    updateQuantity(productId, currentQty + 1);
}

// Decrease quantity
function decreaseQuantity(productId) {
    const currentQty = cart[productId] ? cart[productId].quantity : 0;
    updateQuantity(productId, currentQty - 1);
}

// Update cart display
function updateCartDisplay() {
    console.log('updateCartDisplay chamada, carrinho atual:', cart);
    
    cartTotal = Object.values(cart).reduce((sum, item) => sum + item.total, 0);
    cartItemsCount = Object.values(cart).reduce((sum, item) => sum + item.quantity, 0);
    
    console.log('Totais calculados:', { cartTotal, cartItemsCount });
    
    document.getElementById('cartTotal').textContent = `R$ ${cartTotal.toFixed(2).replace('.', ',')}`;
    document.getElementById('cartItems').textContent = `${cartItemsCount} itens`;
    
    if (cartItemsCount > 0) {
        document.getElementById('cartSummary').style.display = 'block';
        document.getElementById('cartItemsSection').style.display = 'block';
        document.getElementById('checkoutSection').style.display = 'block';
        updateCartItemsList();
    } else {
        document.getElementById('cartSummary').style.display = 'none';
        document.getElementById('cartItemsSection').style.display = 'none';
        document.getElementById('checkoutSection').style.display = 'none';
    }
}

// Update product card display
function updateProductCard(productId) {
    const productCard = document.querySelector(`[data-product-id="${productId}"]`);
    const cartControls = productCard.querySelector('.cart-controls');
    const quantitySpan = productCard.querySelector(`#qty-${productId}`);
    
    if (cart[productId] && cart[productId].quantity > 0) {
        cartControls.style.display = 'flex';
        quantitySpan.textContent = cart[productId].quantity;
        productCard.classList.add('in-cart');
    } else {
        cartControls.style.display = 'none';
        quantitySpan.textContent = '0';
        productCard.classList.remove('in-cart');
    }
}

// Update cart items list
function updateCartItemsList() {
    console.log('updateCartItemsList chamada, carrinho:', cart);
    const cartItemsList = document.getElementById('cartItemsList');
    cartItemsList.innerHTML = '';
    
    Object.values(cart).forEach((item, index) => {
        console.log(`Processando item ${index} para lista:`, item);
        if (item.quantity > 0) {
            const cartItem = document.createElement('div');
            cartItem.className = 'cart-item';
            cartItem.innerHTML = `
                <div class="cart-item-image">
                    <img src="${item.image}" alt="${item.displayName || item.name}">
                </div>
                <div class="cart-item-info">
                    <h6 class="cart-item-name">${item.displayName || item.name}</h6>
                    <div class="cart-item-category">${item.category}</div>
                    <div class="cart-item-variant">${item.variant} - ${item.unit}</div>
                </div>
                <div class="cart-item-controls">
                    <div class="quantity-controls">
                        <button class="quantity-btn" onclick="decreaseCartItem('${Object.keys(cart).find(key => cart[key] === item)}')">
                            <i class="bi bi-dash"></i>
                        </button>
                        <span class="quantity">${item.quantity}</span>
                        <button class="quantity-btn" onclick="increaseCartItem('${Object.keys(cart).find(key => cart[key] === item)}')">
                            <i class="bi bi-plus"></i>
                        </button>
                    </div>
                    <div class="cart-item-price">
                        <div class="unit-price">R$ ${item.price.toFixed(2).replace('.', ',')} / ${item.unit}</div>
                        <div class="total-price">R$ ${item.total.toFixed(2).replace('.', ',')}</div>
                    </div>
                </div>
            `;
            cartItemsList.appendChild(cartItem);
        }
    });
}

// Cart item controls
function increaseCartItem(cartKey) {
    if (cart[cartKey]) {
        cart[cartKey].quantity++;
        cart[cartKey].total = cart[cartKey].quantity * cart[cartKey].price;
        updateCartDisplay();
    }
}

function decreaseCartItem(cartKey) {
    if (cart[cartKey]) {
        cart[cartKey].quantity--;
        cart[cartKey].total = cart[cartKey].quantity * cart[cartKey].price;
        
        if (cart[cartKey].quantity <= 0) {
            delete cart[cartKey];
        }
        
        updateCartDisplay();
    }
}

// Toggle product list visibility
function toggleProductList() {
    const productListSection = document.getElementById('productListSection');
    if (productListSection.style.display === 'none') {
        productListSection.style.display = 'block';
    } else {
        productListSection.style.display = 'none';
    }
}

// Clear cart
function clearCart() {
    if (confirm('Tem certeza que deseja limpar o carrinho?')) {
        initializeCart();
        // Reset all product cards
        document.querySelectorAll('.cart-product-card').forEach(card => {
            card.classList.remove('in-cart');
            card.querySelector('.cart-controls').style.display = 'none';
            card.querySelector('.quantity').textContent = '0';
        });
    }
}

// Save purchase
function savePurchase() {
    if (cartItemsCount === 0) {
        alert('Adicione pelo menos um produto ao carrinho!');
        return;
    }
    
    const storeName = document.getElementById('storeName').value;
    const purchaseDate = document.getElementById('purchaseDate').value;
    
    if (!storeName.trim()) {
        alert('Informe o nome da loja!');
        return;
    }
    
    // Here you would send the data to the server
    console.log('Saving purchase:', {
        store: storeName,
        date: purchaseDate,
        items: cart,
        total: cartTotal
    });
    
    alert('Compra salva com sucesso!');
    clearCart();
}

// Finalizar compra
function finalizePurchase() {
    console.log('finalizePurchase chamada');
    console.log('cartItemsCount:', cartItemsCount);
    console.log('cart atual:', cart);
    
    if (cartItemsCount === 0) {
        alert('Adicione pelo menos um produto ao carrinho!');
        return;
    }
    
    const storeName = document.getElementById('storeName').value;
    const purchaseDate = document.getElementById('purchaseDate').value;
    
    if (!storeName.trim()) {
        alert('Informe o nome da loja!');
        return;
    }
    
    // Confirmar finalização
    const confirmMessage = `Finalizar compra?\n\nLoja: ${storeName}\nData: ${purchaseDate}\nTotal: R$ ${cartTotal.toFixed(2).replace('.', ',')}\nItens: ${cartItemsCount}`;
    
    if (confirm(confirmMessage)) {
        // Preparar dados para envio
        console.log('Carrinho atual:', cart);
        
        const items = Object.values(cart).map((item, index) => {
            console.log(`Item ${index} do carrinho:`, item);
            console.log(`Item ${index} - id:`, item.id, 'tipo:', typeof item.id);
            
            // Validar se o item tem id válido
            if (!item.id || item.id === 0 || isNaN(item.id)) {
                console.error(`Item ${index} tem id inválido:`, item.id);
                alert(`Erro: Item "${item.name}" não possui ID válido. Recarregue a página e tente novamente.`);
                throw new Error(`Item ${index} tem id inválido`);
            }
            
            const mappedItem = {
                product_id: parseInt(item.id), // Garantir que seja um número inteiro
                quantity: parseFloat(item.quantity),
                price: parseFloat(item.price),
                variant: item.variant || null
            };
            
            console.log(`Item ${index} mapeado:`, mappedItem);
            return mappedItem;
        });
        
        console.log('Items preparados:', items);
        
        const purchaseData = {
            store: storeName,
            date: purchaseDate,
            items: items,
            total: cartTotal,
            _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        };
        
        console.log('Dados da compra:', purchaseData);
        
        // Mostrar loading
        const checkoutBtn = document.querySelector('.checkout-btn');
        const originalText = checkoutBtn.innerHTML;
        checkoutBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Processando...';
        checkoutBtn.disabled = true;
        
        // Verificar se offlineStorage está disponível
        if (!window.offlineStorage) {
            console.error('OfflineStorage não está disponível! Verifique se o arquivo offline-storage.js está carregado.');
            showNotification('Sistema offline não disponível. Recarregue a página.', 'error');
            checkoutBtn.innerHTML = originalText;
            checkoutBtn.disabled = false;
            return;
        }
        
        // Aguardar inicialização do IndexedDB se necessário
        try {
            await window.offlineStorage.waitForInit();
            console.log('✅ IndexedDB inicializado e pronto');
        } catch (error) {
            console.error('❌ Erro ao inicializar IndexedDB:', error);
            showNotification('Erro ao inicializar sistema offline. Recarregue a página.', 'error');
            checkoutBtn.innerHTML = originalText;
            checkoutBtn.disabled = false;
            return;
        }
        
        if (!window.offlineStorage.db) {
            console.error('❌ IndexedDB ainda não está disponível após aguardar');
            showNotification('Erro ao acessar armazenamento offline. Recarregue a página.', 'error');
            checkoutBtn.innerHTML = originalText;
            checkoutBtn.disabled = false;
            return;
        }
        
        // Verificar se está online e usar offlineStorage se necessário
        const isOnline = navigator.onLine && window.offlineStorage.isOnlineStatus();
        
        if (isOnline) {
            // Tentar enviar para o servidor
            fetch('/compra/save', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(purchaseData)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Salvar também no cache offline
                    if (window.offlineStorage) {
                        const purchaseItem = {
                            id: data.purchase_id || null,
                            user_id: purchaseData.user_id || null,
                            items: purchaseData.items,
                            store: purchaseData.store,
                            purchase_date: purchaseData.date,
                            total: purchaseData.total,
                            isPending: false
                        };
                        window.offlineStorage.savePurchase(purchaseItem).catch(err => {
                            console.log('Erro ao salvar no cache offline:', err);
                        });
                    }
                    
                    alert('Compra finalizada com sucesso! 🎉\n\nVocê será redirecionado para o fluxo de caixa.');
                    
                    // Limpar carrinho sem confirmação
                    initializeCart();
                    document.querySelectorAll('.cart-product-card').forEach(card => {
                        card.classList.remove('in-cart');
                        card.querySelector('.cart-controls').style.display = 'none';
                        card.querySelector('.quantity').textContent = '0';
                    });
                    
                    // Redirecionar para fluxo de caixa
                    window.location.href = '/cashflow/dashboard';
                } else {
                    showNotification('Erro ao salvar compra: ' + data.message, 'error');
                    checkoutBtn.innerHTML = originalText;
                    checkoutBtn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Erro ao enviar para servidor, tentando salvar offline:', error);
                
                // Se falhar, tentar salvar offline
                if (window.offlineStorage) {
                    salvarCompraOffline(purchaseData, checkoutBtn, originalText);
                } else {
                    showNotification('Erro ao salvar compra. Verifique sua conexão.', 'error');
                    checkoutBtn.innerHTML = originalText;
                    checkoutBtn.disabled = false;
                }
            });
        } else {
            // Está offline, salvar diretamente
            if (window.offlineStorage) {
                salvarCompraOffline(purchaseData, checkoutBtn, originalText);
            } else {
                showNotification('Você está offline e o sistema de armazenamento offline não está disponível.', 'error');
                checkoutBtn.innerHTML = originalText;
                checkoutBtn.disabled = false;
            }
        }
    }
    
    // Função para salvar compra offline
    async function salvarCompraOffline(purchaseData, checkoutBtn, originalText) {
        if (!window.offlineStorage) {
            showNotification('Sistema offline não disponível.', 'error');
            checkoutBtn.innerHTML = originalText;
            checkoutBtn.disabled = false;
            return;
        }
        
        try {
            // Aguardar inicialização do IndexedDB
            await window.offlineStorage.waitForInit();
            
            if (!window.offlineStorage.db) {
                throw new Error('IndexedDB não está disponível');
            }
            
            // Preparar item de compra para IndexedDB
            // Formato esperado pelo savePurchase
            const purchaseItem = {
                items: purchaseData.items || [],
                store: purchaseData.store || null,
                purchase_date: purchaseData.date || new Date().toISOString(),
                date: purchaseData.date || new Date().toISOString(),
                total: purchaseData.total || 0,
                timestamp: new Date().toISOString(),
                user_id: null // Será preenchido pelo servidor na sincronização
            };
            
            console.log('💾 Salvando compra offline:', purchaseItem);
            
            // Salvar usando offlineStorage
            const saved = await window.offlineStorage.savePurchase(purchaseItem);
            
            console.log('✅ Compra salva offline com sucesso:', saved);
            
            alert('Compra salva offline! ✅\n\nA compra será sincronizada automaticamente quando você voltar online.');
            
            // Limpar carrinho
            initializeCart();
            document.querySelectorAll('.cart-product-card').forEach(card => {
                card.classList.remove('in-cart');
                card.querySelector('.cart-controls').style.display = 'none';
                card.querySelector('.quantity').textContent = '0';
            });
            
            // Não redirecionar quando offline, apenas limpar
            checkoutBtn.innerHTML = originalText;
            checkoutBtn.disabled = false;
            
        } catch (error) {
            console.error('❌ Erro ao salvar offline:', error);
            console.error('Stack:', error.stack);
            showNotification('Erro ao salvar compra offline: ' + error.message, 'error');
            checkoutBtn.innerHTML = originalText;
            checkoutBtn.disabled = false;
        }
    }
    
}

// Filter products by category
document.getElementById('categoryFilter').addEventListener('change', function() {
    const selectedCategory = this.value;
    const productCards = document.querySelectorAll('.cart-product-card');
    
    productCards.forEach(card => {
        const category = card.querySelector('.premium-product-category').textContent;
        if (selectedCategory === '' || category === selectedCategory) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
});

</script>

<style>
.cart-summary {
    margin-bottom: 1rem;
}

.store-info-section {
    margin-bottom: 1rem;
}

.cart-product-card {
    position: relative;
    transition: all 0.3s ease;
}

.cart-product-card.in-cart {
    border: 2px solid #10b981;
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(5, 150, 105, 0.05) 100%);
}

.cart-controls {
    position: absolute;
    bottom: 0.5rem;
    right: 0.5rem;
    left: 0.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: rgba(0, 0, 0, 0.8);
    padding: 0.5rem;
    border-radius: 0.5rem;
    backdrop-filter: blur(10px);
}

.quantity-controls {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.quantity-btn {
    background: #10b981;
    border: none;
    color: white;
    width: 24px;
    height: 24px;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.quantity-btn:hover {
    background: #059669;
    transform: scale(1.1);
}

.quantity {
    color: white;
    font-weight: 600;
    min-width: 20px;
    text-align: center;
}

.price-input {
    flex: 1;
    margin-left: 0.5rem;
}

.price-input-field {
    width: 100%;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 4px;
    padding: 0.25rem 0.5rem;
    color: white;
    font-size: 0.75rem;
    text-align: right;
}

.price-input-field::placeholder {
    color: rgba(255, 255, 255, 0.5);
}

.cart-item {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%);
    border-radius: 10px;
    padding: 0.75rem;
    margin-bottom: 0.75rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    border: 1px solid rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
}

.cart-item-image {
    width: 50px;
    height: 50px;
    border-radius: 8px;
    overflow: hidden;
    flex-shrink: 0;
}

.cart-item-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.cart-item-info {
    flex: 1;
    min-width: 0;
}

.cart-item-name {
    color: white;
    font-size: 0.875rem;
    font-weight: 600;
    margin-bottom: 0.25rem;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.cart-item-category {
    color: #9ca3af;
    font-size: 0.75rem;
}

.cart-item-controls {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 0.5rem;
}

.cart-item-price {
    text-align: right;
}

.unit-price {
    color: #9ca3af;
    font-size: 0.75rem;
}

.total-price {
    color: #10b981;
    font-size: 0.875rem;
    font-weight: 700;
}

/* Modal Styles */
.premium-modal {
    background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    color: white;
}

.premium-modal .modal-header {
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%);
}

.premium-modal .modal-title {
    color: white;
    font-weight: 600;
}

.premium-modal .btn-close {
    filter: invert(1);
}

.premium-modal .form-label {
    color: #d1d5db;
    font-weight: 500;
    margin-bottom: 0.5rem;
}

.premium-modal .form-select,
.premium-modal .form-control {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: white;
    border-radius: 8px;
}

.premium-modal .form-select:focus,
.premium-modal .form-control:focus {
    background: rgba(255, 255, 255, 0.15);
    border-color: #10b981;
    box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.1);
    color: white;
}

.premium-modal .form-select option {
    background: #1f2937;
    color: white;
}

.premium-modal .input-group-text {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #d1d5db;
}

.product-modal-info {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.5rem;
    padding: 1rem;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 8px;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.product-modal-image {
    width: 60px;
    height: 60px;
    border-radius: 8px;
    overflow: hidden;
    flex-shrink: 0;
}

.product-modal-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.product-modal-details h6 {
    color: white;
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.variant-section,
.unit-section {
    margin-bottom: 1rem;
}

.total-preview {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(5, 150, 105, 0.05) 100%);
    border-radius: 8px;
    border: 1px solid rgba(16, 185, 129, 0.2);
    margin-top: 1rem;
}

.total-label {
    color: #d1d5db;
    font-weight: 500;
}

.total-value {
    color: #10b981;
    font-size: 1.25rem;
    font-weight: 700;
}

/* Botão de Finalizar Compra */
.checkout-section {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.checkout-btn {
    width: 100%;
    background: linear-gradient(135deg, #10b981, #059669);
    border: none;
    color: white;
    padding: 1rem 1.5rem;
    border-radius: 12px;
    font-weight: 700;
    font-size: 1.1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.checkout-btn:hover {
    background: linear-gradient(135deg, #059669, #047857);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
    color: white;
}

.checkout-btn:active {
    transform: translateY(0);
    box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
}

.checkout-btn i {
    font-size: 1.2rem;
}

.cart-item-variant {
    color: #9ca3af;
    font-size: 0.75rem;
    margin-top: 0.125rem;
}

/* Animações para notificações */
@keyframes slideDown {
    from { transform: translateX(-50%) translateY(-100%); opacity: 0; }
    to { transform: translateX(-50%) translateY(0); opacity: 1; }
}

@keyframes slideUp {
    from { transform: translateX(-50%) translateY(0); opacity: 1; }
    to { transform: translateX(-50%) translateY(-100%); opacity: 0; }
}

// Função para mostrar notificações elegantes
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.innerHTML = `
        <i class="bi bi-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-triangle' : 'info-circle'}"></i>
        <span>${message}</span>
    `;
    
    // Add styles
    notification.style.cssText = `
        position: fixed;
        top: 1rem;
        left: 50%;
        transform: translateX(-50%);
        background: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6'};
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 500;
        z-index: 10000;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        animation: slideDown 0.3s ease;
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 4 seconds
    setTimeout(() => {
        notification.style.animation = 'slideUp 0.3s ease';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 300);
    }, 4000);
}

/* Event listeners for modal */
document.addEventListener('DOMContentLoaded', function() {
    // Modal quantity and price change listeners
    document.getElementById('modalQuantity').addEventListener('input', updateModalTotal);
    document.getElementById('modalPrice').addEventListener('input', updateModalTotal);
    
    // Initialize cart
    initializeCart();
});
</style>
@endsection
