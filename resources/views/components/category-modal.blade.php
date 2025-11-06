<!-- Category Modal - Criar Nova Categoria -->
<div class="category-modal-overlay" id="categoryModalOverlay" style="display: none;">
    <div class="category-modal-container">
        <div class="category-modal-content">
            <!-- Header -->
            <div class="category-modal-header">
                <h3 class="category-modal-title">Nova Categoria</h3>
                <button type="button" class="category-modal-close" id="closeCategoryModal" aria-label="Fechar">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            
            <!-- Form -->
            <div class="category-modal-form">
                <form id="createCategoryForm">
                    @csrf
                    <div class="category-modal-field">
                        <label class="category-modal-label">
                            <i class="bi bi-tag"></i>
                            Nome da Categoria <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               name="name" 
                               id="categoryNameInput" 
                               class="category-modal-input" 
                               placeholder="Ex: Laticínios, Verduras, Proteínas, etc"
                               required>
                        <small class="category-modal-help-text">
                            O sistema normaliza automaticamente variações (ex: "vício" = "vícios")
                        </small>
                        <div id="categoryError" class="category-modal-error hidden"></div>
                    </div>
                </form>
            </div>
            
            <!-- Footer -->
            <div class="category-modal-footer">
                <button type="button" class="category-modal-btn category-modal-btn-cancel" id="cancelCategoryModal">
                    <i class="bi bi-x-lg"></i>
                    Cancelar
                </button>
                <button type="button" class="category-modal-btn category-modal-btn-add" id="createCategoryBtn">
                    <i class="bi bi-plus-circle"></i>
                    Criar Categoria
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* Category Modal Styles */
.category-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.8);
    z-index: 10001;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
    opacity: 1;
    backdrop-filter: blur(4px);
}

.category-modal-container {
    width: 100%;
    max-width: 480px;
    max-height: 90vh;
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

.category-modal-content {
    background: #1a1a2e;
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
    overflow: hidden;
    border: 1px solid rgba(255, 255, 255, 0.1);
    display: flex;
    flex-direction: column;
    max-height: 90vh;
}

.category-modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.75rem 1rem;
    background: rgba(255, 255, 255, 0.05);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    flex-shrink: 0;
}

.category-modal-title {
    font-size: 1rem;
    font-weight: 600;
    color: #fff;
    margin: 0;
}

.category-modal-close {
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
    transition: color 0.2s;
}

.category-modal-close:hover {
    color: #fff;
}

.category-modal-form {
    padding: 1rem;
    overflow-y: auto;
    flex: 1;
    min-height: 0;
}

.category-modal-field {
    margin-bottom: 1rem;
}

.category-modal-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    font-weight: 500;
    color: rgba(255, 255, 255, 0.9);
    margin-bottom: 0.5rem;
}

.category-modal-label i {
    color: #10b981;
    font-size: 1rem;
}

.category-modal-input {
    width: 100%;
    padding: 0.75rem;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 6px;
    color: #fff;
    font-size: 0.875rem;
    transition: all 0.2s;
}

.category-modal-input:focus {
    outline: none;
    border-color: #10b981;
    background-color: rgba(255, 255, 255, 0.08);
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

.category-modal-input::placeholder {
    color: rgba(255, 255, 255, 0.5);
}

.category-modal-help-text {
    display: block;
    margin-top: 0.5rem;
    font-size: 0.75rem;
    color: rgba(255, 255, 255, 0.6);
    line-height: 1.4;
}

.category-modal-error {
    margin-top: 0.5rem;
    padding: 0.5rem;
    background: rgba(239, 68, 68, 0.1);
    border: 1px solid rgba(239, 68, 68, 0.3);
    border-radius: 6px;
    color: #fca5a5;
    font-size: 0.875rem;
}

.category-modal-footer {
    display: flex;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    background: rgba(255, 255, 255, 0.05);
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    flex-shrink: 0;
}

.category-modal-btn {
    flex: 1;
    padding: 0.75rem 1rem;
    border: none;
    border-radius: 6px;
    font-size: 0.875rem;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    transition: all 0.2s;
}

.category-modal-btn-cancel {
    background: rgba(255, 255, 255, 0.1);
    color: rgba(255, 255, 255, 0.9);
}

.category-modal-btn-cancel:hover {
    background: rgba(255, 255, 255, 0.15);
    color: #fff;
}

.category-modal-btn-add {
    background: #10b981;
    color: #fff;
}

.category-modal-btn-add:hover {
    background: #059669;
}

.category-modal-btn-add:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.hidden {
    display: none !important;
}
</style>

<script>
// Category Modal Functions
document.addEventListener('DOMContentLoaded', function() {
    const categoryModalOverlay = document.getElementById('categoryModalOverlay');
    const categoryModal = document.getElementById('categoryModalOverlay');
    const closeCategoryModal = document.getElementById('closeCategoryModal');
    const cancelCategoryModal = document.getElementById('cancelCategoryModal');
    const createCategoryBtn = document.getElementById('createCategoryBtn');
    const categoryNameInput = document.getElementById('categoryNameInput');
    const categoryError = document.getElementById('categoryError');
    const createCategoryForm = document.getElementById('createCategoryForm');
    
    // Abrir modal
    window.openCategoryModal = function() {
        if (categoryModalOverlay) {
            categoryModalOverlay.style.display = 'flex';
            categoryNameInput.focus();
        }
    };
    
    // Fechar modal
    function closeModal() {
        if (categoryModalOverlay) {
            categoryModalOverlay.style.display = 'none';
            createCategoryForm.reset();
            categoryError.classList.add('hidden');
            categoryError.textContent = '';
        }
    }
    
    // Event listeners
    if (closeCategoryModal) {
        closeCategoryModal.addEventListener('click', closeModal);
    }
    
    if (cancelCategoryModal) {
        cancelCategoryModal.addEventListener('click', closeModal);
    }
    
    // Fechar ao clicar no overlay
    if (categoryModalOverlay) {
        categoryModalOverlay.addEventListener('click', function(e) {
            if (e.target === categoryModalOverlay) {
                closeModal();
            }
        });
    }
    
    // Fechar com ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && categoryModalOverlay && categoryModalOverlay.style.display !== 'none') {
            closeModal();
        }
    });
    
    // Criar categoria
    if (createCategoryBtn) {
        createCategoryBtn.addEventListener('click', async function() {
            const categoryName = categoryNameInput.value.trim();
            
            if (!categoryName) {
                categoryError.textContent = 'Por favor, digite o nome da categoria';
                categoryError.classList.remove('hidden');
                return;
            }
            
            createCategoryBtn.disabled = true;
            createCategoryBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Criando...';
            
            try {
                const response = await fetch('/api/product-categories/find-or-create', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                                       document.querySelector('input[name="_token"]')?.value || ''
                    },
                    credentials: 'same-origin', // Importante: envia cookies de sessão
                    body: JSON.stringify({ name: categoryName })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Atualizar campo de categoria
                    const categorySelect = document.getElementById('category');
                    if (categorySelect) {
                        // Se for um select, recarregar categorias e selecionar a nova
                        if (categorySelect.tagName === 'SELECT') {
                            if (window.reloadCategories) {
                                window.reloadCategories();
                                // Aguardar um pouco para garantir que as categorias foram carregadas
                                setTimeout(() => {
                                    categorySelect.value = data.category.name;
                                }, 100);
                            }
                        } else {
                            // Se for input (compatibilidade com outros lugares)
                            categorySelect.value = data.category.name;
                        }
                    }
                    
                    closeModal();
                } else {
                    categoryError.textContent = data.message || 'Erro ao criar categoria';
                    categoryError.classList.remove('hidden');
                }
            } catch (error) {
                console.error('Erro ao criar categoria:', error);
                categoryError.textContent = 'Erro ao criar categoria. Tente novamente.';
                categoryError.classList.remove('hidden');
            } finally {
                createCategoryBtn.disabled = false;
                createCategoryBtn.innerHTML = '<i class="bi bi-plus-circle"></i> Criar Categoria';
            }
        });
    }
    
    // Submit form com Enter
    if (createCategoryForm) {
        createCategoryForm.addEventListener('submit', function(e) {
            e.preventDefault();
            if (createCategoryBtn) {
                createCategoryBtn.click();
            }
        });
    }
});
</script>

