<!-- Product Modal - Nova Versão -->
<div class="product-modal-overlay" id="productModalOverlay" style="display: none;">
    <div class="product-modal-container">
        <div class="product-modal-content">
            <!-- Header -->
            <div class="product-modal-header">
                <h3 class="product-modal-title">Adicionar ao Carrinho</h3>
                <button type="button" class="product-modal-close" id="closeProductModal" aria-label="Fechar">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            
            <!-- Product Info -->
            <div class="product-modal-product-info">
                <div class="product-modal-image-wrapper">
                    <img id="modalProductImage" src="{{ asset('images/no-image.png') }}" alt="Produto" class="product-modal-image">
                    </div>
                <div class="product-modal-product-details">
                    <h4 id="modalProductName" class="product-modal-product-name">Nome do Produto</h4>
                    <span id="modalProductCategory" class="product-modal-product-category">Categoria</span>
                </div>
                </div>
                
                <!-- Form Fields -->
            <div class="product-modal-form">
                <!-- Variant (Optional) -->
                <div class="product-modal-field">
                    <label class="product-modal-label">
                        <i class="bi bi-tag"></i>
                        Tipo/Variante <span class="text-muted">(opcional)</span>
                        </label>
                    <select class="product-modal-select" id="variantSelect">
                            <option value="">Padrão</option>
                        </select>
                    </div>
                    
                <!-- Unit (Required) -->
                <div class="product-modal-field">
                    <label class="product-modal-label">
                            <i class="bi bi-rulers"></i>
                        Unidade de Medida <span class="text-danger">*</span>
                        </label>
                    <select class="product-modal-select" id="unitSelect" required>
                            <option value="">Selecione a unidade...</option>
                        </select>
                    </div>
                    
                <!-- Quantity and Price -->
                <div class="product-modal-row">
                    <div class="product-modal-col">
                        <label class="product-modal-label">
                                <i class="bi bi-123"></i>
                                Quantidade
                            </label>
                        <div class="product-modal-quantity">
                            <button type="button" class="product-modal-qty-btn" id="decreaseQty">
                                <i class="bi bi-dash"></i>
                                </button>
                            <input type="number" class="product-modal-qty-input" id="modalQuantity" value="1" min="1">
                            <button type="button" class="product-modal-qty-btn" id="increaseQty">
                                <i class="bi bi-plus"></i>
                                </button>
                            </div>
                        </div>
                    
                    <div class="product-modal-col">
                        <label class="product-modal-label">
                                <i class="bi bi-currency-dollar"></i>
                            Preço Unitário <span class="text-danger">*</span>
                            </label>
                        <div class="product-modal-price">
                            <span class="product-modal-currency">R$</span>
                            <input type="number" class="product-modal-price-input" id="modalPrice" placeholder="0,00" step="0.01" min="0" required>
                        </div>
                        </div>
                    </div>
                    
                <!-- Total -->
                <div class="product-modal-total">
                    <div class="product-modal-total-content">
                        <span class="product-modal-total-label">
                                <i class="bi bi-calculator"></i>
                                Total
                            </span>
                        <span class="product-modal-total-value" id="modalTotal">R$ 0,00</span>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="product-modal-footer">
                <button type="button" class="product-modal-btn product-modal-btn-cancel" id="cancelProductModal">
                    <i class="bi bi-x-lg"></i>
                    Cancelar
                </button>
                <button type="button" class="product-modal-btn product-modal-btn-add" id="addToCartBtn">
                    <i class="bi bi-cart-plus-fill"></i>
                    Adicionar
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* Product Modal - Otimizado para Performance */
.product-modal-overlay {
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
    padding: 1rem;
    opacity: 1;
    will-change: opacity;
}

.product-modal-overlay.hidden {
    opacity: 0;
}

.product-modal-container {
    width: 100%;
    max-width: 480px;
    max-height: 95vh;
    overflow: hidden;
    will-change: transform;
}

.product-modal-content {
    background: #1a1a2e;
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
    overflow: hidden;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

/* Header */
.product-modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.75rem 1rem;
    background: rgba(255, 255, 255, 0.05);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.product-modal-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #fff;
    margin: 0;
}

.product-modal-close {
    background: transparent;
    border: none;
    color: rgba(255, 255, 255, 0.7);
    font-size: 1.25rem;
    cursor: pointer;
    padding: 0.25rem;
    line-height: 1;
    display: flex;
    align-items: center;
    justify-content: center;
}

.product-modal-close:hover {
    color: #fff;
}

/* Product Info */
.product-modal-product-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1rem;
    background: rgba(255, 255, 255, 0.03);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.product-modal-image-wrapper {
    width: 60px;
    height: 60px;
    border-radius: 8px;
    overflow: hidden;
    background: rgba(255, 255, 255, 0.05);
    flex-shrink: 0;
}

.product-modal-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.product-modal-product-details {
    flex: 1;
    min-width: 0;
}

.product-modal-product-name {
    font-size: 0.95rem;
    font-weight: 600;
    color: #fff;
    margin: 0 0 0.125rem 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.product-modal-product-category {
    font-size: 0.75rem;
    color: rgba(255, 255, 255, 0.6);
}

/* Form */
.product-modal-form {
    padding: 1rem;
}

.product-modal-field {
    margin-bottom: 0.875rem;
}

.product-modal-label {
    display: flex;
    align-items: center;
    gap: 0.375rem;
    font-size: 0.8rem;
    font-weight: 500;
    color: rgba(255, 255, 255, 0.9);
    margin-bottom: 0.375rem;
}

.product-modal-label i {
    color: #10b981;
    font-size: 0.875rem;
}

.product-modal-select {
    width: 100%;
    padding: 0.5rem 0.75rem;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 6px;
    color: #fff;
    font-size: 0.875rem;
    cursor: pointer;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23ffffff' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 0.75rem center;
    background-size: 10px;
    padding-right: 2rem;
}

.product-modal-select:focus {
    outline: none;
    border-color: #10b981;
    background-color: rgba(255, 255, 255, 0.08);
}

.product-modal-select option {
    background: #1a1a2e;
    color: #fff;
}

.product-modal-row {
    display: grid;
    grid-template-columns: auto 1fr;
    gap: 0.75rem;
    margin-bottom: 0.875rem;
    align-items: end;
}

.product-modal-col {
    display: flex;
    flex-direction: column;
}

/* Quantity */
.product-modal-col:first-child {
    width: auto;
    min-width: 120px;
}

.product-modal-quantity {
    display: flex;
    align-items: center;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 6px;
    overflow: hidden;
    width: fit-content;
    min-width: 100px;
}

.product-modal-qty-btn {
    background: transparent;
    border: none;
    color: rgba(255, 255, 255, 0.7);
    padding: 0.5rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 32px;
}

.product-modal-qty-btn:hover {
    background: rgba(255, 255, 255, 0.1);
    color: #fff;
}

.product-modal-qty-input {
    flex: 1;
    background: transparent;
    border: none;
    color: #fff;
    text-align: center;
    padding: 0.5rem;
    font-size: 0.875rem;
    font-weight: 500;
    min-width: 0;
}

.product-modal-qty-input:focus {
    outline: none;
}

/* Price */
.product-modal-price {
    display: flex;
    align-items: center;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 6px;
    overflow: hidden;
}

.product-modal-currency {
    padding: 0.5rem 0.75rem;
    background: rgba(16, 185, 129, 0.2);
    color: #10b981;
    font-weight: 600;
    font-size: 0.875rem;
    border-right: 1px solid rgba(255, 255, 255, 0.1);
}

.product-modal-price-input {
    flex: 1;
    background: transparent;
    border: none;
    color: #fff;
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
    min-width: 0;
}

.product-modal-price-input:focus {
    outline: none;
}

.product-modal-price:focus-within {
    border-color: #10b981;
}

/* Total */
.product-modal-total {
    margin-top: 0.875rem;
    padding-top: 0.875rem;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.product-modal-total-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: rgba(16, 185, 129, 0.15);
    border: 1px solid rgba(16, 185, 129, 0.3);
    border-radius: 6px;
    padding: 0.75rem 1rem;
}

.product-modal-total-label {
    display: flex;
    align-items: center;
    gap: 0.375rem;
    color: rgba(255, 255, 255, 0.9);
    font-weight: 500;
    font-size: 0.875rem;
}

.product-modal-total-label i {
    color: #10b981;
    font-size: 0.875rem;
}

.product-modal-total-value {
    font-size: 1.25rem;
    font-weight: 700;
    color: #10b981;
}

/* Footer */
.product-modal-footer {
    display: flex;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    background: rgba(255, 255, 255, 0.05);
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.product-modal-btn {
    flex: 1;
    padding: 0.625rem 1rem;
    border: none;
    border-radius: 6px;
    font-size: 0.875rem;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.375rem;
}

.product-modal-btn-cancel {
    background: rgba(255, 255, 255, 0.1);
    color: rgba(255, 255, 255, 0.9);
}

.product-modal-btn-cancel:hover {
    background: rgba(255, 255, 255, 0.15);
    color: #fff;
}

.product-modal-btn-add {
    background: #10b981;
    color: #fff;
}

.product-modal-btn-add:hover {
    background: #059669;
}

.product-modal-btn-add:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none;
}

/* Responsive */
@media (max-width: 576px) {
    .product-modal-container {
        max-width: 100%;
        padding: 0;
    }
    
    .product-modal-row {
        grid-template-columns: 1fr;
    }
    
    .product-modal-footer {
        flex-direction: column;
    }
    
    .product-modal-btn {
        width: 100%;
    }
}

/* Scrollbar removido - não precisa mais */
</style>
