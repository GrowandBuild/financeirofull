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
            <button class="action-btn" onclick="clearCart()" title="Limpar carrinho">
                <i class="bi bi-trash"></i>
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
        <div class="store-info-card">
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
                    <div class="premium-product-card cart-product-card product-clickable" 
                         data-product-id="{{ $product->id }}"
                         data-product-name="{{ htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8') }}"
                         data-product-category="{{ htmlspecialchars($product->category ?? '', ENT_QUOTES, 'UTF-8') }}"
                         data-product-image="{{ htmlspecialchars($product->image_url ?? '', ENT_QUOTES, 'UTF-8') }}"
                         style="cursor: pointer;">
                        <div class="premium-product-image">
                            <img src="{{ $product->image_url ?? asset('images/no-image.png') }}" 
                                 alt="{{ $product->name }}" 
                                 class="img-fluid"
                                 onerror="this.onerror=null; this.src='{{ asset('images/no-image.png') }}';">
                            <div class="product-overlay">
                                <i class="bi bi-plus-circle"></i>
                            </div>
                        </div>
                        <div class="premium-product-info">
                            <h5 class="premium-product-name">{{ $product->name }}</h5>
                            <div class="premium-product-category">{{ $product->category ?? 'Sem categoria' }}</div>
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

{{-- Modal agora renderizado no layout principal (app.blade.php), FORA do mobile-container --}}
@endsection

@section('scripts')
<script>
let cart = {};
let cartTotal = 0;
let cartItemsCount = 0;
let currentProduct = null;

const CART_STORAGE_KEY = 'purchaseCartData';
let cachedStorageProvider = null;

function resolveStorageProvider() {
    if (cachedStorageProvider !== null) {
        return cachedStorageProvider;
    }
    
    if (typeof window === 'undefined') {
        cachedStorageProvider = null;
        return cachedStorageProvider;
    }
    
    const storageCandidates = ['localStorage', 'sessionStorage'];
    
    for (const candidate of storageCandidates) {
        try {
            if (!(candidate in window)) {
                continue;
            }
            
            const storage = window[candidate];
            const testKey = '__cart_storage_test__';
            storage.setItem(testKey, '1');
            storage.removeItem(testKey);
            cachedStorageProvider = storage;
            break;
        } catch (error) {
            console.warn(`Storage "${candidate}" indisponível:`, error);
        }
    }
    
    if (!cachedStorageProvider) {
        console.warn('Nenhum armazenamento persistente disponível (localStorage/sessionStorage).');
    }
    
    return cachedStorageProvider;
}

function saveCartToStorage() {
    try {
        const storage = resolveStorageProvider();
        if (!storage) {
            return;
        }
        
        const sanitizedCart = {};
        Object.entries(cart).forEach(([key, item]) => {
            if (item && typeof item === 'object') {
                sanitizedCart[key] = {
                    ...item,
                    quantity: Number(item.quantity) || 0,
                    price: Number(item.price) || 0,
                    total: Number(item.total) || 0
                };
            }
        });
        
        const payload = {
            items: sanitizedCart,
            updatedAt: Date.now()
        };
        
        storage.setItem(CART_STORAGE_KEY, JSON.stringify(payload));
    } catch (error) {
        console.error('Erro ao salvar carrinho no storage:', error);
    }
}

function loadCartFromStorage() {
    try {
        const storage = resolveStorageProvider();
        if (!storage) {
            return null;
        }
        
        const raw = storage.getItem(CART_STORAGE_KEY);
        if (!raw) {
            return null;
        }
        
        const parsed = JSON.parse(raw);
        if (!parsed || typeof parsed.items !== 'object' || parsed.items === null) {
            return null;
        }
        
        const loadedCart = {};
        Object.entries(parsed.items).forEach(([key, item]) => {
            if (item && typeof item === 'object') {
                loadedCart[key] = {
                    ...item,
                    quantity: Number(item.quantity) || 0,
                    price: Number(item.price) || 0,
                    total: Number(item.total) || 0
                };
            }
        });
        
        return loadedCart;
    } catch (error) {
        console.error('Erro ao carregar carrinho do storage:', error);
        return null;
    }
}

function clearCartStorage() {
    try {
        const storage = resolveStorageProvider();
        if (!storage) {
            return;
        }
        
        storage.removeItem(CART_STORAGE_KEY);
    } catch (error) {
        console.error('Erro ao limpar carrinho do storage:', error);
    }
}

// Caminho da imagem padrão para fallback
const DEFAULT_IMAGE = '{{ asset('images/no-image.png') }}';

// Product variants and units database - será carregado dinamicamente
let productVariants = {};

// Carregar produtos com variantes do servidor
async function loadProductsWithVariants() {
    try {
        const response = await fetch('/api/products');
        const products = await response.json();
        
        // Converter para o formato esperado pelo JavaScript
        // IMPORTANTE: Incluir TODOS os produtos, não apenas os com variantes
        productVariants = {};
        products.forEach(product => {
            // Processar variantes: podem ser objetos com 'name' ou strings simples
            let processedVariants = [];
            if (product.variants && Array.isArray(product.variants) && product.variants.length > 0) {
                processedVariants = product.variants.map(variant => {
                    // Se for objeto, pegar o 'name'; se for string, usar diretamente
                    if (typeof variant === 'object' && variant !== null) {
                        return variant.name || variant.unit || JSON.stringify(variant);
                    }
                    return variant;
                }).filter(v => v); // Remover valores vazios/null
            }
            
            // Sempre adicionar o produto, mesmo se não tiver variantes
            productVariants[product.name] = {
                id: product.id,
                category: product.category,
                variants: processedVariants,
                // Sempre incluir a unidade do produto
                units: product.unit ? [product.unit] : ['unidade'],
                image_url: product.image_url
            };
        });
        
        console.log('Produtos carregados:', productVariants);
    } catch (error) {
        console.error('Erro ao carregar produtos:', error);
    }
}

// Função para aguardar Bootstrap estar pronto
function waitForBootstrap(callback, maxAttempts = 10, attempt = 0) {
    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        callback();
    } else if (attempt < maxAttempts) {
        setTimeout(() => {
            waitForBootstrap(callback, maxAttempts, attempt + 1);
        }, 100);
    } else {
        console.error('Bootstrap não foi carregado após', maxAttempts * 100, 'ms');
        alert('Erro: Bootstrap não foi carregado. Recarregue a página.');
    }
}

// Carregar produtos quando a página carregar
document.addEventListener('DOMContentLoaded', function() {
    console.log('=== DOMContentLoaded ===');
    
    // Aguardar Bootstrap estar pronto
    waitForBootstrap(function() {
        console.log('Bootstrap disponível, inicializando página...');
        initializePage();
    });
    
    function initializePage() {
        console.log('Bootstrap disponível:', typeof bootstrap !== 'undefined');
        loadProductsWithVariants();
        
        // Adicionar event listeners para os cards de produtos
        setupProductCardListeners();
    }
});

// Variável para armazenar a referência do listener e evitar duplicação
let productCardClickListener = null;

// Configurar event listeners para os cards de produtos
function setupProductCardListeners() {
    console.log('=== CONFIGURANDO EVENT LISTENERS ===');
    
    // Usar event delegation no container para melhor performance
    const productGrid = document.getElementById('productGrid');
    
    if (!productGrid) {
        console.error('Grid de produtos não encontrado! #productGrid não existe no DOM.');
        return;
    }
    
    console.log('Grid de produtos encontrado:', productGrid);
    console.log('Número de cards clicáveis:', productGrid.querySelectorAll('.product-clickable').length);
    
    // Remover listener anterior se existir
    if (productCardClickListener) {
        productGrid.removeEventListener('click', productCardClickListener);
        productCardClickListener = null;
    }
    
    // Criar nova referência do listener
    productCardClickListener = handleProductCardClick;
    
    // Garantir que o container possa receber eventos
    productGrid.style.pointerEvents = 'auto';
    
    // Adicionar listener único usando delegation na fase de bubbling
    productGrid.addEventListener('click', productCardClickListener, false);
    
    console.log('Event listeners configurados para cards de produtos');
}

// Handler para clique nos cards de produtos
function handleProductCardClick(e) {
    console.log('=== CLIQUE DETECTADO ===');
    console.log('Event:', e);
    console.log('Target clicado:', e.target);
    console.log('CurrentTarget:', e.currentTarget);
    console.log('Target classes:', e.target.className);
    console.log('Target tagName:', e.target.tagName);
    console.log('Target ID:', e.target.id);
    
    // Encontrar o card clicado (pode ser o card ou um elemento filho)
    let card = e.target.closest('.product-clickable');
    
    // Se não encontrou, tentar procurar pelo currentTarget
    if (!card && e.currentTarget && e.currentTarget.classList.contains('product-clickable')) {
        card = e.currentTarget;
        console.log('Card encontrado via currentTarget:', card);
    }
    
    // Se ainda não encontrou, procurar no path do evento
    if (!card && e.composedPath) {
        const path = e.composedPath();
        card = path.find(el => el && el.classList && el.classList.contains('product-clickable'));
        if (card) {
            console.log('Card encontrado via composedPath:', card);
        }
    }
    
    if (!card) {
        console.log('Não é um card de produto clicável');
        console.log('Path do evento:', e.composedPath ? e.composedPath() : 'N/A');
        return; // Não é um card de produto
    }
    
    console.log('Card encontrado:', card);
    
    e.preventDefault();
    e.stopPropagation();
    
    const productId = card.getAttribute('data-product-id');
    const productName = card.getAttribute('data-product-name');
    const productCategory = card.getAttribute('data-product-category');
    const productImage = card.getAttribute('data-product-image');
    
    console.log('Dados extraídos do card:', {
        productId: productId,
        productName: productName,
        productCategory: productCategory,
        productImage: productImage,
        target: e.target,
        card: card
    });
    
    if (productId && productName) {
        console.log('Chamando openProductModal...');
        openProductModal(
            parseInt(productId),
            productName,
            productCategory || '',
            productImage || ''
        );
    } else {
        console.error('Dados do produto incompletos:', {
            productId: productId,
            productName: productName
        });
        alert('Erro: Dados do produto incompletos. Recarregue a página.');
    }
}

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
    
    const storedCart = loadCartFromStorage();
    if (storedCart && Object.keys(storedCart).length > 0) {
        cart = storedCart;
        console.log('Carrinho restaurado do storage:', cart);
    } else {
        cart = {};
        clearCartStorage();
    }
    
    cartTotal = 0;
    cartItemsCount = 0;
    console.log('Carrinho inicializado:', cart);
    updateCartDisplay();
}

function refreshProductCardsFromCart() {
    const cartCards = document.querySelectorAll('.cart-product-card');
    if (cartCards && cartCards.length > 0) {
        cartCards.forEach(card => {
            card.classList.remove('in-cart');
            const controls = card.querySelector('.cart-controls');
            const quantityElement = card.querySelector('.quantity, .cart-quantity');
            if (controls) {
                controls.style.display = 'none';
            }
            if (quantityElement) {
                quantityElement.textContent = '0';
            }
        });
    }
    
    Object.entries(cart).forEach(([key, item]) => {
        if (!item || item.quantity <= 0) {
            return;
        }
        
        const productId = parseInt(key.split('_')[0], 10);
        if (!productId) {
            return;
        }
        
        const productCard = document.querySelector(`[data-product-id="${productId}"]`);
        if (!productCard) {
            return;
        }
        
        const cartControls = productCard.querySelector('.cart-controls');
        const quantitySpan = productCard.querySelector(`#qty-${productId}`) || productCard.querySelector('.quantity');
        
        productCard.classList.add('in-cart');
        if (cartControls) {
            cartControls.style.display = 'flex';
        }
        if (quantitySpan) {
            quantitySpan.textContent = item.quantity;
        }
    });
}

// Variável global para instância do modal
let modalInstance = null;
let modalEventListeners = {
    shown: null,
    hidden: null,
    focusin: null
};

// Função para limpar e resetar o modal
function resetModal() {
    const modalElement = document.getElementById('productModal');
    if (!modalElement) return;
    
    // Fechar todos os selects abertos - CRÍTICO para evitar select pendurado
    const unitSelect = document.getElementById('unitSelect');
    const variantSelect = document.getElementById('variantSelect');
    
    if (unitSelect) {
        unitSelect.blur(); // Remover foco
        unitSelect.size = 1; // Garantir que não está aberto como lista
        // Resetar estilos que podem estar causando o select a ficar pendurado
        if (unitSelect.style) {
            unitSelect.style.height = '';
            unitSelect.style.position = '';
            unitSelect.style.zIndex = '';
        }
        // Remover foco forçadamente se ainda estiver ativo
        if (document.activeElement === unitSelect) {
            document.activeElement.blur();
            document.body.focus();
        }
    }
    
    if (variantSelect) {
        variantSelect.blur(); // Remover foco
        variantSelect.size = 1; // Garantir que não está aberto como lista
        // Resetar estilos que podem estar causando o select a ficar pendurado
        if (variantSelect.style) {
            variantSelect.style.height = '';
            variantSelect.style.position = '';
            variantSelect.style.zIndex = '';
        }
        // Remover foco forçadamente se ainda estiver ativo
        if (document.activeElement === variantSelect) {
            document.activeElement.blur();
            document.body.focus();
        }
    }
    
    // Garantir que rolagem seja restaurada se ainda estiver bloqueada
    const body = document.body;
    const html = document.documentElement;
    const scrollY = modalElement.dataset.scrollY || 0;
    
    // Restaurar rolagem se ainda estiver bloqueada
    if (body.style.position === 'fixed') {
        body.style.position = '';
        body.style.top = '';
        body.style.width = '';
        body.style.overflow = '';
        body.style.paddingRight = '';
        html.style.overflow = '';
        
        if (scrollY) {
            window.scrollTo(0, parseInt(scrollY));
        }
        
        delete modalElement.dataset.scrollY;
    }
    
    // Remover event listeners antigos apenas se existirem
    if (modalEventListeners.shown && modalElement) {
        modalElement.removeEventListener('shown.bs.modal', modalEventListeners.shown);
    }
    if (modalEventListeners.hidden && modalElement) {
        modalElement.removeEventListener('hidden.bs.modal', modalEventListeners.hidden);
    }
    if (modalEventListeners.focusin && modalElement) {
        modalElement.removeEventListener('focusin', modalEventListeners.focusin);
    }
}

// Open product selection modal
// Usa o novo ProductModalManager
function openProductModal(productId, productName, productCategory, productImage) {
    // Validar parâmetros
    if (!productId || !productName) {
        console.error('Parâmetros inválidos para openProductModal:', {
            productId: productId,
            productName: productName
        });
        alert('Erro: Dados do produto inválidos. Recarregue a página e tente novamente.');
        return;
    }
    
        // Verificar se ProductModalManager está disponível
        if (typeof window.openProductModal === 'function' && window.ProductModalManager) {
            // Usar o ProductModalManager global (definido no product-modal.js)
            // A função openProductModal já está sobrescrita no product-modal.js
            // Mas vamos garantir que está usando o manager
            if (window.productModalManager) {
                // Aguardar abertura assíncrona
                window.productModalManager.open(productId, productName, productCategory, productImage).catch(err => {
                    console.error('Erro ao abrir modal:', err);
                });
            } else if (window.ProductModalManager) {
                // Se ainda não foi inicializado, inicializar agora
                window.productModalManager = new window.ProductModalManager();
                window.productModalManager.open(productId, productName, productCategory, productImage).catch(err => {
                    console.error('Erro ao abrir modal:', err);
                });
            } else {
                console.error('ProductModalManager não encontrado');
                alert('Erro: Modal não inicializado. Recarregue a página.');
            }
            return;
        }
        
        // Fallback: tentar usar função global
        if (typeof window.openProductModal === 'function') {
            window.openProductModal(productId, productName, productCategory, productImage).catch(err => {
                console.error('Erro ao abrir modal:', err);
            });
            return;
        }
    
    console.error('Nenhum sistema de modal disponível');
    alert('Erro: Sistema de modal não disponível. Recarregue a página.');
}

// Add product to cart from modal
// Aceita objeto com dados ou pega do DOM
function addToCartFromModal(data = null) {
    let variant, unit, quantity, subquantity, price, productId, productName, productCategory;
    
    // Se dados foram passados como objeto (novo modal)
    if (data && typeof data === 'object') {
        productId = data.productId;
        productName = data.productName;
        productCategory = data.category || '';
        variant = data.variant || 'Padrão';
        unit = data.unit;
        quantity = data.quantity || 1;
        subquantity = data.subquantity || null;
        price = data.price;
    } else {
        // Pegar do DOM (compatibilidade com código antigo)
        variant = document.getElementById('variantSelect')?.value || 'Padrão';
        unit = document.getElementById('unitSelect')?.value;
        quantity = parseInt(document.getElementById('modalQuantity')?.value) || 1;
        const subquantityInput = document.getElementById('subquantityInput');
        subquantity = subquantityInput && subquantityInput.value ? parseFloat(subquantityInput.value.replace(',', '.')) : null;
        price = parseFloat(document.getElementById('modalPrice')?.value?.replace(',', '.')) || 0;
        
        if (!currentProduct || !currentProduct.id) {
            alert('Erro: Produto não selecionado corretamente. Tente novamente.');
            return;
        }
        
        productId = currentProduct.id;
        productName = currentProduct.name;
        productCategory = currentProduct.category || '';
    }
    
    // Validação
    if (!unit || !unit.trim()) {
        alert('Por favor, selecione uma unidade de medida!');
        return;
    }
    
    if (!price || price <= 0) {
        alert('Por favor, informe um preço unitário válido!');
        return;
    }
    
    if (quantity <= 0) {
        alert('A quantidade deve ser maior que zero!');
        return;
    }
    
    // Criar chave única para o carrinho (incluindo subquantity se houver)
    const variantForKey = variant || 'Padrão';
    const subquantityKey = subquantity ? `_${subquantity}` : '';
    const cartKey = `${productId}_${variantForKey}_${unit}${subquantityKey}`;
    const displayName = variant && variant !== 'Padrão' && variant !== ''
        ? `${productName} - ${variant}` 
        : productName;
    
    // Adicionar informação de subquantidade ao nome de exibição se houver
    let displayNameWithSubquantity = displayName;
    if (subquantity) {
        const unitLower = unit.toLowerCase();
        if (unitLower === 'g' || unitLower === 'grama') {
            displayNameWithSubquantity += ` (${subquantity}g)`;
        } else if (unitLower === 'ml' || unitLower === 'mililitro') {
            displayNameWithSubquantity += ` (${subquantity}ml)`;
        } else if (unitLower === 'kg' || unitLower === 'quilograma') {
            displayNameWithSubquantity += ` (${subquantity}g)`;
        } else if (unitLower === 'l' || unitLower === 'litro') {
            displayNameWithSubquantity += ` (${subquantity}ml)`;
        }
    }
    
    if (!cart[cartKey]) {
        cart[cartKey] = {
            id: productId,
            name: productName,
            variant: variant,
            unit: unit,
            subquantity: subquantity,
            displayName: displayNameWithSubquantity,
            category: productCategory,
            image: currentProduct?.image || '',
            quantity: 0,
            price: 0,
            total: 0
        };
    }
    
    cart[cartKey].quantity += quantity;
    cart[cartKey].price = price;
    cart[cartKey].total = cart[cartKey].quantity * cart[cartKey].price;
    
    updateCartDisplay();
    
    // Fechar selects antes de fechar o modal
    const unitSelect = document.getElementById('unitSelect');
    const variantSelect = document.getElementById('variantSelect');
    if (unitSelect) unitSelect.blur();
    if (variantSelect) variantSelect.blur();
    if (unitSelect) unitSelect.size = 1;
    if (variantSelect) variantSelect.size = 1;
    
    // Close modal
    if (modalInstance) {
        modalInstance.hide();
    }
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
    
    // Persistir o carrinho antes de atualizar o DOM (evita perda em caso de erro)
    saveCartToStorage();
    
    const cartTotalElement = document.getElementById('cartTotal');
    const cartItemsElement = document.getElementById('cartItems');
    const cartSummarySection = document.getElementById('cartSummary');
    const cartItemsSection = document.getElementById('cartItemsSection');
    const checkoutSection = document.getElementById('checkoutSection');
    
    if (cartTotalElement) {
        cartTotalElement.textContent = `R$ ${cartTotal.toFixed(2).replace('.', ',')}`;
    }
    
    if (cartItemsElement) {
        cartItemsElement.textContent = `${cartItemsCount} itens`;
    }
    
    if (cartItemsCount > 0) {
        if (cartSummarySection) cartSummarySection.style.display = 'block';
        if (cartItemsSection) cartItemsSection.style.display = 'block';
        if (checkoutSection) checkoutSection.style.display = 'block';
        updateCartItemsList();
    } else {
        if (cartSummarySection) cartSummarySection.style.display = 'none';
        if (cartItemsSection) cartItemsSection.style.display = 'none';
        if (checkoutSection) checkoutSection.style.display = 'none';
    }
    
    refreshProductCardsFromCart();
}

// Update product card display
function updateProductCard(productId) {
    const productCard = document.querySelector(`[data-product-id="${productId}"]`);
    if (!productCard) {
        return;
    }
    
    const cartControls = productCard.querySelector('.cart-controls');
    const quantitySpan = productCard.querySelector(`#qty-${productId}`) || productCard.querySelector('.quantity');
    const cartKey = Object.keys(cart).find(key => key.split('_')[0] === String(productId));
    const cartItem = cartKey ? cart[cartKey] : null;
    
    if (cartItem && cartItem.quantity > 0) {
        if (cartControls) cartControls.style.display = 'flex';
        if (quantitySpan) quantitySpan.textContent = cartItem.quantity;
        productCard.classList.add('in-cart');
    } else {
        if (cartControls) cartControls.style.display = 'none';
        if (quantitySpan) quantitySpan.textContent = '0';
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
        cart = {};
        cartTotal = 0;
        cartItemsCount = 0;
        clearCartStorage();
        updateCartDisplay();
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
                variant: item.variant || null,
                subquantity: item.subquantity ? parseFloat(item.subquantity) : null
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
        
        // Enviar para o servidor
        fetch('/compra/save', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify(purchaseData)
        })
        .then(async response => {
            console.log('Response status:', response.status);
            console.log('Response ok:', response.ok);
            
            // Tentar obter o texto da resposta primeiro para debug
            const responseText = await response.text();
            console.log('Response text:', responseText);
            
            let data;
            try {
                data = JSON.parse(responseText);
            } catch (e) {
                console.error('Erro ao fazer parse do JSON:', e);
                console.error('Response text:', responseText);
                // Se não for JSON válido mas o status for 200, considerar sucesso
                if (response.ok) {
                    return { success: true, message: 'Compra salva com sucesso!' };
                }
                throw new Error('Resposta do servidor não é um JSON válido');
            }
            
            // Se a resposta não foi OK, mas temos dados válidos, retornar os dados
            // para que o código de sucesso possa verificar
            if (!response.ok && data.success === false) {
                return data; // Deixar o código de verificação tratar
            }
            
            // Se não for OK e não tiver success definido, lançar erro
            if (!response.ok) {
                throw new Error(data.message || `HTTP ${response.status}`);
            }
            
            return data;
        })
        .then(data => {
            console.log('Dados recebidos:', data);
            console.log('data.success:', data.success);
            console.log('typeof data.success:', typeof data.success);
            
            // Verificar se é sucesso (pode ser true, 'true', 1, etc)
            const isSuccess = data.success === true || 
                              data.success === 'true' || 
                              data.success === 1 || 
                              data.success === '1';
            
            if (isSuccess) {
                alert('Compra finalizada com sucesso! 🎉\n\nVocê será redirecionado para o fluxo de caixa.');
                
                // Limpar carrinho sem confirmação
                initializeCart();
                document.querySelectorAll('.cart-product-card').forEach(card => {
                    card.classList.remove('in-cart');
                    const controls = card.querySelector('.cart-controls');
                    if (controls) controls.style.display = 'none';
                    const quantity = card.querySelector('.quantity');
                    if (quantity) quantity.textContent = '0';
                });
                
                // Redirecionar para fluxo de caixa
                window.location.href = '/cashflow/dashboard';
            } else {
                const errorMessage = data.message || 'Erro desconhecido ao salvar compra';
                console.error('Erro do servidor:', errorMessage);
                console.error('Dados completos:', data);
                showNotification('Erro ao salvar compra: ' + errorMessage, 'error');
                checkoutBtn.innerHTML = originalText;
                checkoutBtn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Erro ao enviar para servidor:', error);
            console.error('Stack trace:', error.stack);
            const errorMessage = error.message || 'Erro desconhecido';
            showNotification('Erro ao salvar compra: ' + errorMessage + '. Verifique sua conexão.', 'error');
            checkoutBtn.innerHTML = originalText;
            checkoutBtn.disabled = false;
        });
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
    margin-bottom: 1.5rem;
}

.store-info-section {
    margin-bottom: 1.5rem;
}

/* Card de informações da loja */
.store-info-card {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.05) 0%, rgba(255, 255, 255, 0.02) 100%);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    padding: 1rem;
    backdrop-filter: blur(10px);
}

.store-info-card .filter-header {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.75rem;
    color: white;
    font-weight: 600;
    font-size: 0.875rem;
}

.store-info-card .filter-header i {
    color: #10b981;
    font-size: 1rem;
}

.store-info-card .row {
    display: flex;
    gap: 0.75rem;
}

.store-info-card .col-8 {
    flex: 0 0 66.666667%;
    max-width: 66.666667%;
}

.store-info-card .col-4 {
    flex: 0 0 30%;
    max-width: 30%;
}

/* Melhorar contraste e espaçamento dos inputs */
.store-info-card input[type="text"],
.store-info-card input[type="date"] {
    background: rgba(255, 255, 255, 0.1) !important;
    border: 2px solid rgba(255, 255, 255, 0.2) !important;
    color: white !important;
    font-weight: 500 !important;
    padding: 0.75rem 1rem !important;
    border-radius: 12px !important;
    transition: all 0.3s ease !important;
    width: 100%;
    box-sizing: border-box;
}

.store-info-card input[type="text"]:focus,
.store-info-card input[type="date"]:focus {
    background: rgba(255, 255, 255, 0.15) !important;
    border-color: #10b981 !important;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2) !important;
    outline: none !important;
}

.store-info-card input[type="text"]::placeholder {
    color: rgba(255, 255, 255, 0.5) !important;
}

/* Melhorar contraste do filtro de categoria */
.premium-select {
    background: rgba(255, 255, 255, 0.1) !important;
    border: 2px solid rgba(255, 255, 255, 0.2) !important;
    color: white !important;
    font-weight: 500 !important;
    padding: 0.75rem 1rem !important;
    border-radius: 12px !important;
    transition: all 0.3s ease !important;
}

.premium-select:focus {
    background: rgba(255, 255, 255, 0.15) !important;
    border-color: #10b981 !important;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2) !important;
    outline: none !important;
}

.premium-select option {
    background: #1f2937 !important;
    color: white !important;
    padding: 0.75rem;
}

.cart-product-card {
    position: relative;
    transition: all 0.3s ease;
    cursor: pointer !important;
    user-select: none;
    border: 2px solid transparent !important;
}

.cart-product-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    border-color: rgba(16, 185, 129, 0.3) !important;
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(5, 150, 105, 0.05) 100%) !important;
}

/* Garantir que o grid possa receber eventos */
#productGrid {
    pointer-events: auto !important;
}

.product-clickable {
    pointer-events: auto !important;
    cursor: pointer !important;
    position: relative;
    z-index: 1;
}

.product-clickable * {
    pointer-events: none;
}

/* Garantir que o overlay não bloqueie cliques */
.product-clickable .product-overlay {
    pointer-events: none !important;
}

/* Garantir que elementos internos não bloqueiem o clique no card */
.product-clickable img,
.product-clickable h5,
.product-clickable div,
.product-clickable span {
    pointer-events: none !important;
}

.cart-product-card.in-cart {
    border: 2px solid #10b981 !important;
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.2) 0%, rgba(5, 150, 105, 0.1) 100%) !important;
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

/* Modal Styles - Visual Premium e Moderno */
/* CRÍTICO: Modal renderizado FORA do mobile-container no body, então não é afetado por contain */
/* O modal é renderizado pelo Bootstrap diretamente no body através do layout */
#productModal.modal {
    z-index: 9999 !important; /* z-index muito alto para garantir que fique acima de TUDO */
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    bottom: 0 !important;
    display: none !important; /* Bootstrap controla */
    align-items: center !important;
    justify-content: center !important;
    overflow-y: auto !important;
    padding: 1rem !important;
    animation: fadeIn 0.2s ease-out !important;
    pointer-events: none !important; /* Não bloquear cliques - permitir que passem para dialog/content */
    contain: none !important; /* CRÍTICO: Não ter contain */
    isolation: isolate !important; /* Criar novo stacking context isolado */
}

/* Quando o modal está aberto */
#productModal.modal.show {
    display: flex !important;
    z-index: 9999 !important; /* z-index muito alto */
    pointer-events: none !important; /* CRÍTICO: Permitir que cliques passem para o conteúdo */
    contain: none !important; /* CRÍTICO: Não ter contain */
    isolation: isolate !important; /* Criar novo stacking context isolado */
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

/* Removido - já está acima */

/* Backdrop do Bootstrap - abaixo do modal mas acima de outros elementos */
.modal-backdrop {
    z-index: 9998 !important; /* Um nível abaixo do modal (9999) */
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    bottom: 0 !important;
    background: linear-gradient(135deg, rgba(0, 0, 0, 0.75) 0%, rgba(0, 0, 0, 0.85) 100%) !important;
    backdrop-filter: blur(8px) !important;
    -webkit-backdrop-filter: blur(8px) !important;
    animation: backdropFadeIn 0.3s ease-out !important;
    pointer-events: auto !important; /* Permitir cliques para fechar */
    contain: none !important; /* Não ter contain */
}

/* CRÍTICO: Garantir que o backdrop não bloqueie o modal quando estiver atrás */
.modal.show ~ .modal-backdrop,
.modal.show + .modal-backdrop {
    pointer-events: auto !important;
}

@keyframes backdropFadeIn {
    from {
        opacity: 0;
        backdrop-filter: blur(0px);
    }
    to {
        opacity: 1;
        backdrop-filter: blur(8px);
    }
}

.modal-backdrop.show {
    z-index: 9998 !important; /* Um nível abaixo do modal */
}

/* Modal dialog - centralizado corretamente com animação */
/* CRÍTICO: z-index deve ser maior que backdrop (9998) */
#productModal .modal-dialog {
    z-index: 10000 !important; /* Acima do modal (9999) e backdrop (9998) */
    position: relative !important;
    margin: auto !important;
    max-width: 90vw !important;
    width: 100% !important;
    max-height: 90vh !important;
    pointer-events: none !important; /* Não bloquear cliques - permitir que passem para o content */
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    /* Garantir centralização vertical e horizontal */
    flex-shrink: 0 !important;
    animation: modalSlideIn 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) !important;
    contain: none !important; /* Não ter contain */
    isolation: auto !important; /* Não criar novo stacking context aqui */
}

@keyframes modalSlideIn {
    from {
        opacity: 0;
        transform: translateY(-30px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

/* Responsividade do modal */
@media (min-width: 576px) {
    #productModal .modal-dialog {
        max-width: 550px !important;
    }
    
    #productModal .modal-content {
        max-width: 100% !important;
    }
}

@media (max-width: 575px) {
    #productModal .modal-dialog {
        max-width: 95vw !important;
        margin: 0.5rem auto !important;
    }
    
    #productModal .modal-content {
        max-width: 100% !important;
        margin: 0 !important;
        border-radius: 20px !important;
    }
    
    #productModal .modal-body {
        padding: 1rem !important;
    }
    
    .product-modal-info {
        padding: 1rem !important;
    }
    
    .variant-section,
    .unit-section {
        margin-bottom: 1rem !important;
    }
    
    .total-preview {
        padding: 1rem !important;
    }
}

/* Modal content - Visual Premium com Glassmorphism */
/* CRÍTICO: Este deve ter pointer-events: auto para receber cliques */
#productModal .modal-content,
#productModal.premium-modal {
    background: linear-gradient(135deg, rgba(31, 41, 55, 0.95) 0%, rgba(17, 24, 39, 0.98) 100%) !important;
    backdrop-filter: blur(20px) saturate(180%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(180%) !important;
    border: 2px solid rgba(255, 255, 255, 0.15) !important;
    border-radius: 24px !important;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5),
                0 0 0 1px rgba(255, 255, 255, 0.05),
                inset 0 1px 0 rgba(255, 255, 255, 0.1) !important;
    color: white !important;
    position: relative !important;
    z-index: 10001 !important; /* MAIS ALTO que tudo - acima do dialog (10000) */
    pointer-events: auto !important; /* CRÍTICO: Permitir cliques no conteúdo do modal */
    margin: 1rem auto !important; /* Centralizado */
    max-height: 90vh !important;
    max-width: 90vw !important;
    overflow-y: auto !important;
    overflow-x: hidden !important;
    padding: 0 !important;
    contain: none !important; /* CRÍTICO: Não ter contain */
    isolation: auto !important; /* Não criar novo stacking context */
    display: flex !important;
    flex-direction: column !important;
}

/* Removido - já está definido acima */

/* Efeito de brilho sutil no topo */
#productModal .modal-content::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(90deg, 
        transparent 0%, 
        rgba(16, 185, 129, 0.5) 50%, 
        transparent 100%);
    pointer-events: none;
    z-index: 1;
}

/* Modal body - garantir que contenha os selects */
#productModal .modal-body {
    pointer-events: auto !important;
    position: relative;
    z-index: 10002 !important;
    padding: 2rem !important;
    overflow: visible !important;
    max-height: calc(90vh - 200px);
    overflow-y: auto;
    overflow-x: hidden;
    flex: 1;
    min-height: 0;
}

/* Responsividade do modal-body */
@media (max-width: 576px) {
    #productModal .modal-body {
        padding: 1.5rem !important;
    }
}

/* Container do modal content - garantir que contenha o dropdown mas permita scroll */
#productModal .modal-content {
    overflow-y: auto !important; /* Permitir scroll interno do content */
    overflow-x: hidden !important;
    position: relative;
    max-height: 90vh; /* Limitar altura total */
}

/* Permitir que dropdowns dos selects apareçam mesmo com overflow */
#productModal .modal-content .variant-section,
#productModal .modal-content .unit-section {
    overflow: visible !important;
}

/* Variant e unit sections - garantir contenção */
#productModal .variant-section,
#productModal .unit-section {
    position: relative;
    z-index: 10003 !important; /* Acima do body */
    overflow: visible;
    margin-bottom: 1.5rem;
}

/* Todos os elementos interativos do modal */
#productModal select,
#productModal input,
#productModal button,
#productModal .form-select,
#productModal .form-control,
#productModal .btn,
#productModal .input-group,
#productModal .input-group-text,
#productModal .variant-section,
#productModal .unit-section,
.premium-modal select,
.premium-modal input,
.premium-modal button,
.premium-modal .form-select,
.premium-modal .form-control,
.premium-modal .btn,
.premium-modal .input-group,
.premium-modal .input-group-text {
    pointer-events: auto !important;
    position: relative !important;
    z-index: 10003 !important; /* Consistente com sections */
    cursor: pointer;
}

/* Inputs específicos não devem ter cursor pointer */
#productModal input[type="number"],
#productModal input[type="text"],
.premium-modal input[type="number"],
.premium-modal input[type="text"] {
    cursor: text !important;
}

/* Select específico - CRÍTICO: garantir que não saia do modal */
#productModal .form-select {
    pointer-events: auto !important;
    z-index: 10003 !important; /* Consistente */
    position: relative !important;
    cursor: pointer !important;
    max-width: 100% !important;
    overflow: hidden !important;
    min-height: 44px; /* Melhor área de toque para mobile */
}

#productModal .form-select:focus {
    pointer-events: auto !important;
    z-index: 10003 !important; /* Consistente */
    outline: none !important;
}

#productModal .form-select:focus-visible {
    outline: 2px solid #10b981 !important;
    outline-offset: 2px !important;
}

/* Garantir que o select não abra fora do modal */
#productModal .unit-section .form-select,
#productModal .variant-section .form-select {
    width: 100% !important;
    position: relative !important;
}

/* Options do select - garantir que apareçam dentro do modal */
#productModal .form-select option {
    pointer-events: auto !important;
    cursor: pointer !important;
    background: #1f2937 !important;
    color: white !important;
}

/* Forçar fechamento do select quando o modal fechar */
#productModal.hide .form-select,
#productModal:not(.show) .form-select {
    size: 1 !important;
}

.premium-modal .modal-header {
    border-bottom: 2px solid rgba(255, 255, 255, 0.1) !important;
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(5, 150, 105, 0.05) 100%) !important;
    backdrop-filter: blur(10px) !important;
    padding: 1.75rem 1.75rem 1.25rem 1.75rem !important;
    border-radius: 24px 24px 0 0 !important;
    flex-shrink: 0;
    z-index: 10004;
    position: relative;
}

.premium-modal .modal-title {
    color: white !important;
    font-weight: 700 !important;
    font-size: 1.5rem !important;
    letter-spacing: -0.5px !important;
    margin: 0 !important;
}

.premium-modal .btn-close {
    filter: invert(1) !important;
    opacity: 0.8 !important;
    transition: all 0.2s ease !important;
    background: rgba(255, 255, 255, 0.1) !important;
    border-radius: 8px !important;
    padding: 0.5rem !important;
    width: 2rem !important;
    height: 2rem !important;
}

.premium-modal .btn-close:hover {
    opacity: 1 !important;
    background: rgba(255, 255, 255, 0.2) !important;
    transform: rotate(90deg) !important;
}

.premium-modal .form-label {
    color: rgba(255, 255, 255, 0.9) !important;
    font-weight: 600 !important;
    margin-bottom: 0.75rem !important;
    font-size: 0.95rem;
    letter-spacing: -0.2px;
}

/* Form Selects e Controls - Design Moderno */
.premium-modal .form-select,
.premium-modal .form-control {
    background: rgba(255, 255, 255, 0.08) !important;
    backdrop-filter: blur(10px) !important;
    border: 2px solid rgba(255, 255, 255, 0.15) !important;
    color: white !important;
    border-radius: 14px !important;
    padding: 0.875rem 1.25rem !important;
    font-size: 1rem !important;
    font-weight: 500 !important;
    transition: all 0.3s ease !important;
    pointer-events: auto !important;
    cursor: pointer;
    z-index: 10003 !important;
    position: relative;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
    width: 100% !important;
    max-width: 100% !important;
    overflow: hidden !important;
    min-height: 52px;
}

.premium-modal .form-control {
    cursor: text !important;
}

.premium-modal .form-select:focus,
.premium-modal .form-control:focus {
    background: rgba(255, 255, 255, 0.12) !important;
    border-color: #10b981 !important;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2), 
                0 6px 16px rgba(16, 185, 129, 0.15) !important;
    color: white !important;
    outline: none !important;
    transform: translateY(-2px) !important;
}

.premium-modal .form-select:hover,
.premium-modal .form-control:hover {
    border-color: rgba(255, 255, 255, 0.25) !important;
    background: rgba(255, 255, 255, 0.1) !important;
}

.premium-modal .form-select option {
    background: #1f2937;
    color: white;
    padding: 0.75rem;
}

.premium-modal .input-group-text {
    background: rgba(255, 255, 255, 0.1) !important;
    backdrop-filter: blur(10px);
    border: 1.5px solid rgba(255, 255, 255, 0.15) !important;
    color: rgba(255, 255, 255, 0.9) !important;
    font-weight: 600 !important;
    border-radius: 12px 0 0 12px !important;
}

.premium-modal .input-group .form-control {
    border-radius: 0 12px 12px 0 !important;
    border-left: none !important;
}

.premium-modal .input-group .form-control:focus {
    border-left: 1.5px solid rgba(255, 255, 255, 0.15) !important;
}

/* Product Info Card - Design Moderno */
.product-modal-info {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    margin-bottom: 2rem;
    padding: 1.5rem;
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(5, 150, 105, 0.05) 100%);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    border: 2px solid rgba(16, 185, 129, 0.2);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
    transition: all 0.3s ease;
    flex-shrink: 0;
}

.product-modal-info:hover {
    border-color: rgba(16, 185, 129, 0.4);
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.2),
                inset 0 1px 0 rgba(255, 255, 255, 0.15);
    transform: translateY(-2px);
}

.product-modal-image {
    width: 100px;
    height: 100px;
    border-radius: 20px;
    overflow: hidden;
    flex-shrink: 0;
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.25),
                0 0 0 3px rgba(16, 185, 129, 0.2);
    border: 3px solid rgba(16, 185, 129, 0.3);
    transition: all 0.3s ease;
    background: rgba(255, 255, 255, 0.05);
}

.product-modal-image:hover {
    transform: scale(1.05);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.3),
                0 0 0 3px rgba(16, 185, 129, 0.4);
}

.product-modal-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.product-modal-details {
    flex: 1;
    min-width: 0;
}

.product-modal-details .product-name {
    color: white !important;
    font-weight: 700 !important;
    font-size: 1.25rem !important;
    margin-bottom: 0.5rem !important;
    letter-spacing: -0.5px;
    line-height: 1.3;
}

.product-modal-details .product-category {
    display: inline-block;
    padding: 0.375rem 0.875rem;
    background: rgba(16, 185, 129, 0.2);
    border: 1px solid rgba(16, 185, 129, 0.3);
    border-radius: 12px;
    color: rgba(255, 255, 255, 0.9) !important;
    font-size: 0.875rem;
    font-weight: 500;
}

/* Form Fields Container */
.modal-form-fields {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

/* Form Field Group */
.form-field-group {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.form-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: rgba(255, 255, 255, 0.95) !important;
    font-weight: 600 !important;
    font-size: 0.95rem !important;
    margin-bottom: 0 !important;
    letter-spacing: -0.2px;
}

.form-label i {
    color: #10b981;
    font-size: 1rem;
}

.form-label .required {
    color: #ef4444;
    font-weight: 700;
    margin-left: 0.25rem;
}

.form-label .optional {
    color: rgba(255, 255, 255, 0.6);
    font-weight: 400;
    font-size: 0.85rem;
    margin-left: 0.25rem;
}

/* Responsividade do product-modal-info */
@media (max-width: 576px) {
    .product-modal-info {
        flex-direction: column;
        text-align: center;
        gap: 1rem;
        padding: 1.25rem;
    }
    
    .product-modal-image {
        width: 80px;
        height: 80px;
    }
    
    .product-modal-details .product-name {
        font-size: 1.1rem !important;
    }
}

/* Quantity Input Group - Design Moderno */
.quantity-input-group {
    display: flex;
    align-items: center;
    background: rgba(255, 255, 255, 0.08);
    border: 2px solid rgba(255, 255, 255, 0.15);
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.quantity-input-group:focus-within {
    border-color: #10b981;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2),
                0 4px 12px rgba(16, 185, 129, 0.15);
}

.quantity-btn {
    background: rgba(255, 255, 255, 0.1);
    border: none;
    color: white;
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
    flex-shrink: 0;
}

.quantity-btn:hover {
    background: rgba(16, 185, 129, 0.3);
    color: white;
}

.quantity-btn:active {
    transform: scale(0.95);
    background: rgba(16, 185, 129, 0.4);
}

.quantity-btn.decrease {
    border-right: 1px solid rgba(255, 255, 255, 0.1);
}

.quantity-btn.increase {
    border-left: 1px solid rgba(255, 255, 255, 0.1);
}

.quantity-input {
    flex: 1;
    border: none;
    background: transparent;
    color: white;
    text-align: center;
    font-weight: 700;
    font-size: 1.1rem;
    padding: 0.75rem;
    outline: none;
}

.quantity-input:focus {
    outline: none;
}

/* Price Input Group - Design Moderno */
.price-input-group {
    display: flex;
    align-items: center;
    background: rgba(255, 255, 255, 0.08);
    border: 2px solid rgba(255, 255, 255, 0.15);
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.price-input-group:focus-within {
    border-color: #10b981;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2),
                0 4px 12px rgba(16, 185, 129, 0.15);
}

.currency-symbol {
    padding: 0.75rem 1rem;
    background: rgba(16, 185, 129, 0.2);
    color: #10b981;
    font-weight: 700;
    font-size: 1rem;
    border-right: 1px solid rgba(255, 255, 255, 0.1);
    flex-shrink: 0;
}

.price-input {
    flex: 1;
    border: none;
    background: transparent;
    color: white;
    padding: 0.75rem 1rem;
    font-weight: 600;
    font-size: 1rem;
    outline: none;
}

.price-input::placeholder {
    color: rgba(255, 255, 255, 0.4);
}

.price-input:focus {
    outline: none;
}

/* Modal Footer - Design Moderno */
.modal-footer-premium {
    border-top: 2px solid rgba(255, 255, 255, 0.1) !important;
    padding: 1.5rem 1.75rem !important;
    background: linear-gradient(180deg, rgba(16, 185, 129, 0.05) 0%, rgba(5, 150, 105, 0.02) 100%) !important;
    backdrop-filter: blur(10px) !important;
    border-radius: 0 0 24px 24px !important;
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    flex-shrink: 0;
    margin-top: auto;
}

/* Responsividade do footer */
@media (max-width: 576px) {
    .modal-footer-premium {
        flex-direction: column-reverse;
        gap: 0.75rem;
        padding: 1.25rem 1.5rem !important;
    }
    
    .modal-btn-cancel,
    .modal-btn-add {
        width: 100%;
    }
}

/* Modal Buttons - Design Moderno */
.modal-btn-cancel {
    background: rgba(255, 255, 255, 0.08) !important;
    backdrop-filter: blur(10px) !important;
    border: 2px solid rgba(255, 255, 255, 0.15) !important;
    color: rgba(255, 255, 255, 0.9) !important;
    padding: 0.875rem 1.75rem !important;
    border-radius: 14px !important;
    font-weight: 600 !important;
    font-size: 1rem !important;
    transition: all 0.3s ease !important;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    min-height: 52px;
    pointer-events: auto !important;
    cursor: pointer !important;
}

.modal-btn-cancel:hover {
    background: rgba(255, 255, 255, 0.12) !important;
    border-color: rgba(255, 255, 255, 0.25) !important;
    color: white !important;
    transform: translateY(-2px) !important;
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2) !important;
}

.modal-btn-cancel:active {
    transform: translateY(0) !important;
}

.modal-btn-cancel i {
    font-size: 1.1rem;
}

.modal-btn-add {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
    border: none !important;
    color: white !important;
    padding: 0.875rem 2rem !important;
    border-radius: 14px !important;
    font-weight: 700 !important;
    font-size: 1rem !important;
    box-shadow: 0 6px 20px rgba(16, 185, 129, 0.35),
                0 2px 8px rgba(16, 185, 129, 0.25) !important;
    transition: all 0.3s ease !important;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    min-height: 52px;
    pointer-events: auto !important;
    cursor: pointer !important;
    position: relative;
    overflow: hidden;
}

.modal-btn-add::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s ease;
}

.modal-btn-add:hover::before {
    left: 100%;
}

.modal-btn-add:hover {
    background: linear-gradient(135deg, #059669 0%, #047857 100%) !important;
    transform: translateY(-2px) !important;
    box-shadow: 0 8px 24px rgba(16, 185, 129, 0.45),
                0 4px 12px rgba(16, 185, 129, 0.35) !important;
    color: white !important;
}

.modal-btn-add:active {
    transform: translateY(0) !important;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3) !important;
}

.modal-btn-add i {
    font-size: 1.2rem;
}

/* Total Preview - Design Moderno */
.total-preview {
    margin-top: 1.5rem;
    padding: 1.5rem;
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.15) 0%, rgba(5, 150, 105, 0.1) 100%);
    backdrop-filter: blur(10px);
    border-radius: 18px;
    border: 2px solid rgba(16, 185, 129, 0.3);
    box-shadow: 0 8px 24px rgba(16, 185, 129, 0.15),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
    flex-shrink: 0;
    transition: all 0.3s ease;
}

.total-preview:hover {
    border-color: rgba(16, 185, 129, 0.5);
    box-shadow: 0 12px 32px rgba(16, 185, 129, 0.2),
                inset 0 1px 0 rgba(255, 255, 255, 0.15);
}

.total-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
}

.total-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: rgba(255, 255, 255, 0.9) !important;
    font-weight: 600 !important;
    font-size: 1.1rem !important;
    letter-spacing: -0.3px;
}

.total-label i {
    color: #10b981;
    font-size: 1.2rem;
}

.total-value {
    color: #10b981 !important;
    font-size: 2rem !important;
    font-weight: 800 !important;
    text-shadow: 0 2px 12px rgba(16, 185, 129, 0.4);
    letter-spacing: -0.5px;
    line-height: 1;
}

/* Responsividade do total-preview */
@media (max-width: 576px) {
    .total-preview {
        padding: 1.25rem;
    }
    
    .total-content {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.75rem;
    }
    
    .total-value {
        font-size: 1.75rem !important;
        width: 100%;
        text-align: right;
    }
}

/* Botão de Finalizar Compra */
.checkout-section {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.checkout-btn {
    width: 100%;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
    border: none !important;
    color: white !important;
    padding: 1rem 1.5rem !important;
    border-radius: 12px !important;
    font-weight: 700 !important;
    font-size: 1rem !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 0.5rem !important;
    transition: all 0.3s ease !important;
    box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3) !important;
    letter-spacing: 0.5px !important;
    cursor: pointer !important;
}

.checkout-btn:hover {
    background: linear-gradient(135deg, #059669 0%, #047857 100%) !important;
    transform: translateY(-2px) !important;
    box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4) !important;
    color: white !important;
}

.checkout-btn:active {
    transform: translateY(0) !important;
    box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3) !important;
}

.checkout-btn i {
    font-size: 1.2rem;
}

.action-btn {
    background: rgba(255, 255, 255, 0.1) !important;
    border: 2px solid rgba(255, 255, 255, 0.2) !important;
    color: white !important;
    padding: 0.5rem !important;
    border-radius: 10px !important;
    font-size: 1rem !important;
    cursor: pointer !important;
    transition: all 0.3s ease !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    min-width: 2.5rem !important;
    height: 2.5rem !important;
}

.action-btn:hover {
    background: rgba(255, 255, 255, 0.15) !important;
    border-color: rgba(255, 255, 255, 0.3) !important;
    transform: translateY(-2px) !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3) !important;
}

.action-btn i {
    font-size: 1.1rem;
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
    const modalQuantity = document.getElementById('modalQuantity');
    const modalPrice = document.getElementById('modalPrice');
    
    if (modalQuantity) {
        modalQuantity.addEventListener('input', updateModalTotal);
    }
    
    if (modalPrice) {
        modalPrice.addEventListener('input', updateModalTotal);
    }
    
    // Initialize cart
    initializeCart();
    
    // Verificar se Bootstrap está carregado
    if (typeof bootstrap === 'undefined') {
        console.error('Bootstrap não está carregado!');
        alert('Erro: Bootstrap não está disponível. Verifique sua conexão e recarregue a página.');
    } else {
        console.log('Bootstrap carregado com sucesso');
    }
});
</style>
@endsection
