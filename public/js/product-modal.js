/**
 * Product Modal Manager - Nova Versão
 * Gestão simplificada e robusta do modal de produtos
 */

class ProductModalManager {
    constructor() {
        this.overlay = null;
        this.isOpen = false;
        this.currentProduct = null;
        this.productVariants = {};
        this.loadProductsPromise = null;
        
        this.init();
    }
    
    init() {
        // Aguardar DOM estar pronto
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.setup());
        } else {
            this.setup();
        }
    }
    
    setup() {
        this.overlay = document.getElementById('productModalOverlay');
        if (!this.overlay) {
            console.error('Product modal overlay não encontrado!');
            return;
        }
        
        // Event listeners
        this.setupEventListeners();
        
        // Carregar produtos
        this.loadProducts();
    }
    
    setupEventListeners() {
        // Fechar modal
        const closeBtn = document.getElementById('closeProductModal');
        const cancelBtn = document.getElementById('cancelProductModal');
        
        if (closeBtn) {
            closeBtn.addEventListener('click', () => this.close());
        }
        
        if (cancelBtn) {
            cancelBtn.addEventListener('click', () => this.close());
        }
        
        // Fechar ao clicar no overlay
        if (this.overlay) {
            this.overlay.addEventListener('click', (e) => {
                if (e.target === this.overlay) {
                    this.close();
                }
            });
        }
        
        // Fechar com ESC
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.isOpen) {
                this.close();
            }
        });
        
        // Quantidade
        const decreaseBtn = document.getElementById('decreaseQty');
        const increaseBtn = document.getElementById('increaseQty');
        const quantityInput = document.getElementById('modalQuantity');
        
        if (decreaseBtn) {
            decreaseBtn.addEventListener('click', () => this.decreaseQuantity());
        }
        
        if (increaseBtn) {
            increaseBtn.addEventListener('click', () => this.increaseQuantity());
        }
        
        if (quantityInput) {
            quantityInput.addEventListener('input', () => this.updateTotal());
            quantityInput.addEventListener('change', () => {
                const value = parseInt(quantityInput.value) || 1;
                if (value < 1) {
                    quantityInput.value = 1;
                }
                this.updateTotal();
            });
        }
        
        // Preço
        const priceInput = document.getElementById('modalPrice');
        if (priceInput) {
            priceInput.addEventListener('input', () => this.updateTotal());
        }
        
        // Unidade - mostrar/esconder campo de subquantidade
        const unitSelect = document.getElementById('unitSelect');
        if (unitSelect) {
            unitSelect.addEventListener('change', () => this.updateSubquantityVisibility());
        }
        
        // Subquantidade
        const subquantityInput = document.getElementById('subquantityInput');
        if (subquantityInput) {
            subquantityInput.addEventListener('input', () => this.updateTotal());
        }
        
        // Adicionar ao carrinho
        const addBtn = document.getElementById('addToCartBtn');
        if (addBtn) {
            addBtn.addEventListener('click', () => this.addToCart());
        }
        
        // Prevenir fechamento ao clicar dentro do modal
        const modalContent = this.overlay?.querySelector('.product-modal-container');
        if (modalContent) {
            modalContent.addEventListener('click', (e) => {
                e.stopPropagation();
            });
        }
    }
    
    async loadProducts() {
        // Se já está carregando, retornar a promise existente
        if (this.loadProductsPromise) {
            return this.loadProductsPromise;
        }
        
        this.loadProductsPromise = (async () => {
            try {
                const response = await fetch('/api/products');
                const products = await response.json();
                
                this.productVariants = {};
                products.forEach(product => {
                    let processedVariants = [];
                    if (product.variants && Array.isArray(product.variants) && product.variants.length > 0) {
                        processedVariants = product.variants.map(variant => {
                            if (typeof variant === 'object' && variant !== null) {
                                return variant.name || variant.unit || JSON.stringify(variant);
                            }
                            return variant;
                        }).filter(v => v);
                    }
                    
                    this.productVariants[product.name] = {
                        id: product.id,
                        category: product.category,
                        variants: processedVariants,
                        units: product.unit ? [product.unit] : ['unidade'],
                        image_url: product.image_url
                    };
                });
                
                console.log('Produtos carregados:', this.productVariants);
                return this.productVariants;
            } catch (error) {
                console.error('Erro ao carregar produtos:', error);
                this.loadProductsPromise = null; // Resetar para tentar novamente
                throw error;
            }
        })();
        
        return this.loadProductsPromise;
    }
    
    async open(productId, productName, productCategory, productImage) {
        if (!productId || !productName) {
            console.error('Parâmetros inválidos para open:', { productId, productName });
            return;
        }
        
        this.currentProduct = {
            id: parseInt(productId),
            name: String(productName).trim(),
            category: String(productCategory || '').trim(),
            image: String(productImage || '').trim()
        };
        
        // Garantir que os produtos foram carregados antes de popular variantes
        try {
            await this.loadProducts();
        } catch (error) {
            console.error('Erro ao carregar produtos antes de abrir modal:', error);
        }
        
        // Preencher informações do produto
        this.populateProductInfo();
        
        // Popular variantes (agora os produtos já estão carregados)
        this.populateVariants();
        
        // Popular unidades
        this.populateUnits();
        
        // Resetar valores
        this.resetValues();
        
        // Atualizar visibilidade do campo de subquantidade
        this.updateSubquantityVisibility();
        
        // Mostrar modal
        this.show();
    }
    
    populateProductInfo() {
        const img = document.getElementById('modalProductImage');
        const name = document.getElementById('modalProductName');
        const category = document.getElementById('modalProductCategory');
        
        if (img) {
            const defaultImage = '/images/no-image.png';
            if (this.currentProduct.image && this.currentProduct.image.trim() !== '') {
                img.src = this.currentProduct.image;
                img.onerror = function() {
                    this.onerror = null;
                    if (this.src !== defaultImage) {
                        this.src = defaultImage;
                    }
                };
            } else {
                img.src = defaultImage;
            }
        }
        
        if (name) {
            name.textContent = this.currentProduct.name;
        }
        
        if (category) {
            category.textContent = this.currentProduct.category || 'Sem categoria';
        }
    }
    
    populateVariants() {
        const variantSelect = document.getElementById('variantSelect');
        if (!variantSelect) return;
        
        // Limpar select
        variantSelect.innerHTML = '<option value="">Padrão</option>';
        
        let variantsToShow = [];
        const productName = this.currentProduct.name;
        
        // Primeiro tentar buscar pelo nome exato do produto
        if (this.productVariants[productName]?.variants?.length > 0) {
            variantsToShow = [...this.productVariants[productName].variants];
        } else {
            // Tentar buscar por categoria
            variantsToShow = this.getVariantsByCategory(this.currentProduct.category);
            
            // Se ainda não encontrou, buscar por produtos com nome similar
            if (variantsToShow.length === 0) {
                // Procurar produtos que começam com o mesmo nome
                for (const [name, data] of Object.entries(this.productVariants)) {
                    if (name.toLowerCase().includes(productName.toLowerCase()) || 
                        productName.toLowerCase().includes(name.toLowerCase())) {
                        if (data.variants && data.variants.length > 0) {
                            variantsToShow = [...data.variants];
                            break;
                        }
                    }
                }
            }
        }
        
        // Adicionar variantes ao select
        if (variantsToShow.length > 0) {
            variantsToShow.forEach(variant => {
                if (variant && variant.trim() !== '') {
                    const option = document.createElement('option');
                    option.value = variant;
                    option.textContent = variant;
                    variantSelect.appendChild(option);
                }
            });
        }
        
        console.log('Variantes carregadas para', productName, ':', variantsToShow);
    }
    
    populateUnits() {
        const unitSelect = document.getElementById('unitSelect');
        if (!unitSelect) return;
        
        // Limpar select
        unitSelect.innerHTML = '<option value="">Selecione a unidade...</option>';
        
        let unitsToShow = [];
        const productName = this.currentProduct.name;
        
        // Tentar obter unidades do produto
        if (this.productVariants[productName]?.units?.length > 0) {
            unitsToShow = this.productVariants[productName].units;
        } else {
            // Tentar obter unidades da categoria
            const categoryUnits = this.getUnitsByCategory(this.currentProduct.category);
            if (categoryUnits && categoryUnits.length > 0) {
                unitsToShow = categoryUnits;
            } else {
                // Unidades padrão
                unitsToShow = ['unidade', 'kg', 'L', 'g', 'ml'];
            }
        }
        
        // Adicionar unidades ao select
        unitsToShow.forEach(unit => {
            if (unit && unit.trim() !== '') {
                const option = document.createElement('option');
                option.value = unit.trim();
                option.textContent = unit.trim();
                unitSelect.appendChild(option);
            }
        });
    }
    
    getVariantsByCategory(category) {
        for (const [productName, productData] of Object.entries(this.productVariants)) {
            if (productData.category === category) {
                return productData.variants || [];
            }
        }
        return [];
    }
    
    getUnitsByCategory(category) {
        for (const [productName, productData] of Object.entries(this.productVariants)) {
            if (productData.category === category) {
                return productData.units || [];
            }
        }
        return ['unidade', 'kg', 'L', 'g', 'ml'];
    }
    
    resetValues() {
        const quantityInput = document.getElementById('modalQuantity');
        const priceInput = document.getElementById('modalPrice');
        const unitSelect = document.getElementById('unitSelect');
        const subquantityInput = document.getElementById('subquantityInput');
        
        if (quantityInput) {
            quantityInput.value = 1;
        }
        
        if (priceInput) {
            priceInput.value = '';
        }
        
        if (unitSelect) {
            unitSelect.selectedIndex = 0;
        }
        
        if (subquantityInput) {
            subquantityInput.value = '';
        }
        
        this.updateSubquantityVisibility();
        this.updateTotal();
    }
    
    updateSubquantityVisibility() {
        const unitSelect = document.getElementById('unitSelect');
        const subquantityField = document.getElementById('subquantityField');
        const subquantityLabel = document.getElementById('subquantityLabel');
        const subquantityHelp = document.getElementById('subquantityHelp');
        const subquantityInput = document.getElementById('subquantityInput');
        
        if (!unitSelect || !subquantityField) return;
        
        const selectedUnit = unitSelect.value.toLowerCase().trim();
        
        // Unidades que precisam de subquantidade
        const unitsWithSubquantity = {
            'g': { label: 'Quantidade em gramas', help: 'Digite a quantidade em gramas (ex: 500 para 500g)', placeholder: 'Ex: 500' },
            'grama': { label: 'Quantidade em gramas', help: 'Digite a quantidade em gramas (ex: 500 para 500g)', placeholder: 'Ex: 500' },
            'ml': { label: 'Quantidade em mililitros', help: 'Digite a quantidade em mililitros (ex: 250 para 250ml)', placeholder: 'Ex: 250' },
            'mililitro': { label: 'Quantidade em mililitros', help: 'Digite a quantidade em mililitros (ex: 250 para 250ml)', placeholder: 'Ex: 250' },
            'kg': { label: 'Quantidade em gramas', help: 'Digite a quantidade em gramas para converter (ex: 1500 para 1,5kg)', placeholder: 'Ex: 1500' },
            'quilograma': { label: 'Quantidade em gramas', help: 'Digite a quantidade em gramas para converter (ex: 1500 para 1,5kg)', placeholder: 'Ex: 1500' },
            'l': { label: 'Quantidade em mililitros', help: 'Digite a quantidade em mililitros para converter (ex: 1000 para 1L)', placeholder: 'Ex: 1000' },
            'litro': { label: 'Quantidade em mililitros', help: 'Digite a quantidade em mililitros para converter (ex: 1000 para 1L)', placeholder: 'Ex: 1000' }
        };
        
        const unitConfig = unitsWithSubquantity[selectedUnit];
        
        if (unitConfig) {
            subquantityField.style.display = 'block';
            if (subquantityLabel) {
                subquantityLabel.textContent = unitConfig.label;
            }
            if (subquantityHelp) {
                subquantityHelp.textContent = unitConfig.help;
            }
            if (subquantityInput) {
                subquantityInput.placeholder = unitConfig.placeholder;
                subquantityInput.required = true;
            }
        } else {
            subquantityField.style.display = 'none';
            if (subquantityInput) {
                subquantityInput.value = '';
                subquantityInput.required = false;
            }
        }
    }
    
    decreaseQuantity() {
        const quantityInput = document.getElementById('modalQuantity');
        if (!quantityInput) return;
        
        const current = parseInt(quantityInput.value) || 1;
        if (current > 1) {
            quantityInput.value = current - 1;
            this.updateTotal();
        }
    }
    
    increaseQuantity() {
        const quantityInput = document.getElementById('modalQuantity');
        if (!quantityInput) return;
        
        const current = parseInt(quantityInput.value) || 1;
        quantityInput.value = current + 1;
        this.updateTotal();
    }
    
    updateTotal() {
        const quantityInput = document.getElementById('modalQuantity');
        const priceInput = document.getElementById('modalPrice');
        const totalElement = document.getElementById('modalTotal');
        
        if (!quantityInput || !priceInput || !totalElement) return;
        
        const quantity = parseFloat(quantityInput.value) || 0;
        const price = parseFloat(priceInput.value.replace(',', '.')) || 0;
        const total = quantity * price;
        
        totalElement.textContent = this.formatCurrency(total);
    }
    
    formatCurrency(value) {
        return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        }).format(value);
    }
    
    addToCart() {
        const unitSelect = document.getElementById('unitSelect');
        const quantityInput = document.getElementById('modalQuantity');
        const priceInput = document.getElementById('modalPrice');
        const variantSelect = document.getElementById('variantSelect');
        const subquantityInput = document.getElementById('subquantityInput');
        const subquantityField = document.getElementById('subquantityField');
        
        // Validação
        if (!unitSelect || !unitSelect.value || unitSelect.value === '') {
            alert('Por favor, selecione uma unidade de medida.');
            unitSelect.focus();
            return;
        }
        
        if (!priceInput || !priceInput.value || parseFloat(priceInput.value.replace(',', '.')) <= 0) {
            alert('Por favor, informe um preço unitário válido.');
            priceInput.focus();
            return;
        }
        
        // Verificar se precisa de subquantidade e se foi preenchida
        const needsSubquantity = subquantityField && subquantityField.style.display !== 'none';
        if (needsSubquantity) {
            if (!subquantityInput || !subquantityInput.value || parseFloat(subquantityInput.value.replace(',', '.')) <= 0) {
                alert('Por favor, informe a quantidade em ' + (unitSelect.value.toLowerCase() === 'g' || unitSelect.value.toLowerCase() === 'grama' ? 'gramas' : 'mililitros') + '.');
                subquantityInput.focus();
                return;
            }
        }
        
        const quantity = parseFloat(quantityInput.value) || 1;
        const price = parseFloat(priceInput.value.replace(',', '.')) || 0;
        const unit = unitSelect.value;
        const variant = variantSelect?.value || '';
        const subquantity = needsSubquantity ? parseFloat(subquantityInput.value.replace(',', '.')) : null;
        
        // Chamar função global addToCart se existir
        if (typeof window.addToCartFromModal === 'function') {
            window.addToCartFromModal({
                productId: this.currentProduct.id,
                productName: this.currentProduct.name,
                category: this.currentProduct.category,
                variant: variant,
                unit: unit,
                quantity: quantity,
                subquantity: subquantity,
                price: price
            });
        } else {
            console.warn('Função addToCartFromModal não encontrada');
        }
        
        this.close();
    }
    
    show() {
        if (!this.overlay) return;
        
        this.overlay.style.display = 'flex';
        this.isOpen = true;
        
        // Bloquear scroll do body
        document.body.style.overflow = 'hidden';
        
        // Foco no primeiro campo
        setTimeout(() => {
            const unitSelect = document.getElementById('unitSelect');
            if (unitSelect) {
                unitSelect.focus();
            }
        }, 100);
    }
    
    close() {
        if (!this.overlay) return;
        
        this.overlay.classList.add('hidden');
        
        setTimeout(() => {
            this.overlay.style.display = 'none';
            this.overlay.classList.remove('hidden');
            this.isOpen = false;
            this.currentProduct = null;
            
            // Restaurar scroll do body
            document.body.style.overflow = '';
        }, 200);
    }
}

// Instância global
let productModalManager = null;

// Inicializar quando DOM estiver pronto
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        productModalManager = new ProductModalManager();
    });
} else {
    productModalManager = new ProductModalManager();
}

// Função global para abrir o modal (compatibilidade)
window.openProductModal = async function(productId, productName, productCategory, productImage) {
    if (productModalManager) {
        await productModalManager.open(productId, productName, productCategory, productImage);
    } else {
        console.error('ProductModalManager não inicializado');
    }
};

// Exportar para uso global
window.ProductModalManager = ProductModalManager;

