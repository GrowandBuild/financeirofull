<!-- Expenses Modal - Detalhamento de Despesas do Mês -->
<div class="expenses-modal-overlay" id="expensesModalOverlay" style="display: none;">
    <div class="expenses-modal-container">
        <div class="expenses-modal-content">
            <!-- Header -->
            <div class="expenses-modal-header">
                <h3 class="expenses-modal-title">
                    <i class="bi bi-receipt"></i>
                    Despesas do Mês
                </h3>
                <button type="button" class="expenses-modal-close" id="closeExpensesModal" aria-label="Fechar">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            
            <!-- Content -->
            <div class="expenses-modal-body">
                <div id="expensesLoading" class="expenses-loading">
                    <i class="bi bi-hourglass-split"></i>
                    <span>Carregando despesas...</span>
                </div>
                
                <div id="expensesContent" style="display: none;">
                    <div class="expenses-summary">
                        <div class="expenses-total">
                            <span class="expenses-total-label">Total:</span>
                            <span class="expenses-total-value" id="expensesTotal">R$ 0,00</span>
                        </div>
                        <div class="expenses-count">
                            <span id="expensesCount">0</span> despesas
                        </div>
                    </div>
                    
                    <div class="expenses-list" id="expensesList">
                        <!-- Lista de despesas será inserida aqui via JavaScript -->
                    </div>
                </div>
                
                <div id="expensesError" class="expenses-error" style="display: none;">
                    <i class="bi bi-exclamation-triangle"></i>
                    <span>Erro ao carregar despesas. Tente novamente.</span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Expenses Modal Styles */
.expenses-modal-overlay {
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
    backdrop-filter: blur(4px);
}

.expenses-modal-container {
    width: 100%;
    max-width: 900px;
    max-height: 90vh;
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

.expenses-modal-content {
    background: #1a1a2e;
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
    overflow: hidden;
    border: 1px solid rgba(255, 255, 255, 0.1);
    display: flex;
    flex-direction: column;
    max-height: 90vh;
}

.expenses-modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem 1.5rem;
    background: rgba(255, 255, 255, 0.05);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    flex-shrink: 0;
}

.expenses-modal-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #fff;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.expenses-modal-title i {
    color: #ef4444;
}

.expenses-modal-close {
    background: transparent;
    border: none;
    color: rgba(255, 255, 255, 0.7);
    font-size: 1.5rem;
    cursor: pointer;
    padding: 0.25rem;
    line-height: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: color 0.2s;
}

.expenses-modal-close:hover {
    color: #fff;
}

.expenses-modal-body {
    padding: 1.5rem;
    overflow-y: auto;
    flex: 1;
    min-height: 0;
}

.expenses-loading {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 3rem;
    color: rgba(255, 255, 255, 0.6);
    gap: 1rem;
}

.expenses-loading i {
    font-size: 2rem;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.expenses-error {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 3rem;
    color: #ef4444;
    gap: 1rem;
    text-align: center;
}

.expenses-summary {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    background: rgba(239, 68, 68, 0.1);
    border: 1px solid rgba(239, 68, 68, 0.3);
    border-radius: 8px;
    margin-bottom: 1.5rem;
}

.expenses-total {
    display: flex;
    align-items: baseline;
    gap: 0.5rem;
}

.expenses-total-label {
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.9rem;
}

.expenses-total-value {
    color: #ef4444;
    font-size: 1.5rem;
    font-weight: 700;
}

.expenses-count {
    color: rgba(255, 255, 255, 0.6);
    font-size: 0.875rem;
}

.expenses-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.expense-item {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    padding: 1rem;
    transition: all 0.2s;
}

.expense-item:hover {
    background: rgba(255, 255, 255, 0.08);
    border-color: rgba(255, 255, 255, 0.2);
}

.expense-item-header {
    display: flex;
    justify-content: space-between;
    align-items: start;
    margin-bottom: 0.5rem;
}

.expense-item-title {
    color: #fff;
    font-weight: 600;
    font-size: 1rem;
    flex: 1;
}

.expense-item-amount {
    color: #ef4444;
    font-weight: 700;
    font-size: 1.1rem;
}

.expense-item-details {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 0.75rem;
    font-size: 0.875rem;
    color: rgba(255, 255, 255, 0.6);
}

.expense-item-detail {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.expense-item-detail i {
    font-size: 0.875rem;
}

.expense-item-department {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.expense-department-select {
    padding: 0.4rem 0.6rem;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 6px;
    color: #fff;
    font-size: 0.875rem;
    cursor: pointer;
    min-width: 200px;
    transition: all 0.2s;
}

.expense-department-select:focus {
    outline: none;
    border-color: #10b981;
    background: rgba(255, 255, 255, 0.15);
}

.expense-department-select option {
    background: #1a1a2e;
    color: #fff;
}

.expense-department-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    background: rgba(16, 185, 129, 0.2);
    color: #10b981;
    border: 1px solid rgba(16, 185, 129, 0.3);
}

.expense-department-badge.missing {
    background: rgba(239, 68, 68, 0.2);
    color: #ef4444;
    border-color: rgba(239, 68, 68, 0.3);
}

.expense-save-btn {
    padding: 0.4rem 0.8rem;
    background: #10b981;
    color: #fff;
    border: none;
    border-radius: 6px;
    font-size: 0.875rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.expense-save-btn:hover {
    background: #059669;
}

.expense-save-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

@media (max-width: 768px) {
    .expenses-modal-container {
        max-width: 100%;
        max-height: 95vh;
    }
    
    .expenses-modal-body {
        padding: 1rem;
    }
    
    .expense-item-header {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .expenses-summary {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
}
</style>

<script>
// Expenses Modal Functions
document.addEventListener('DOMContentLoaded', function() {
    const expensesModalOverlay = document.getElementById('expensesModalOverlay');
    const closeExpensesModal = document.getElementById('closeExpensesModal');
    let currentMonth = {{ $selectedDate->month }};
    let currentYear = {{ $selectedDate->year }};
    
    // Abrir modal
    window.openExpensesModal = function() {
        if (expensesModalOverlay) {
            expensesModalOverlay.style.display = 'flex';
            loadExpenses(currentMonth, currentYear);
        }
    };
    
    // Fechar modal
    function closeModal() {
        if (expensesModalOverlay) {
            expensesModalOverlay.style.display = 'none';
        }
    }
    
    // Event listeners
    if (closeExpensesModal) {
        closeExpensesModal.addEventListener('click', closeModal);
    }
    
    // Fechar ao clicar no overlay
    if (expensesModalOverlay) {
        expensesModalOverlay.addEventListener('click', function(e) {
            if (e.target === expensesModalOverlay) {
                closeModal();
            }
        });
    }
    
    // Fechar com ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && expensesModalOverlay && expensesModalOverlay.style.display !== 'none') {
            closeModal();
        }
    });
    
    // Carregar despesas
    async function loadExpenses(month, year) {
        const loading = document.getElementById('expensesLoading');
        const content = document.getElementById('expensesContent');
        const error = document.getElementById('expensesError');
        
        loading.style.display = 'flex';
        content.style.display = 'none';
        error.style.display = 'none';
        
        try {
            const response = await fetch(`/api/goals/monthly-expenses?month=${month}&year=${year}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                credentials: 'same-origin'
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            
            if (data.success) {
                displayExpenses(data.expenses, data.total);
                loading.style.display = 'none';
                content.style.display = 'block';
            } else {
                throw new Error('Erro ao carregar despesas');
            }
        } catch (err) {
            console.error('Erro ao carregar despesas:', err);
            loading.style.display = 'none';
            error.style.display = 'flex';
        }
    }
    
    // Exibir despesas
    function displayExpenses(expenses, total) {
        const expensesList = document.getElementById('expensesList');
        const expensesTotal = document.getElementById('expensesTotal');
        const expensesCount = document.getElementById('expensesCount');
        
        expensesTotal.textContent = `R$ ${parseFloat(total).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
        expensesCount.textContent = expenses.length;
        
        expensesList.innerHTML = '';
        
        const departments = {
            'fixed_expenses': '🏠 Despesa Fixa',
            'professional_resources': '💼 Recursos Profissionais',
            'emergency_reserves': '🛡️ Reserva de Emergência',
            'leisure': '😊 Lazer',
            'debt_installments': '💳 Parcelas de Dívidas',
            'investments': '📈 Investimentos',
            'long_term_savings': '💰 Poupanças de Longo Prazo',
            'education': '📚 Educação',
            'health': '🏥 Saúde'
        };
        
        expenses.forEach(expense => {
            const item = document.createElement('div');
            item.className = 'expense-item';
            item.dataset.expenseId = expense.id;
            
            const departmentSelect = `
                <select class="expense-department-select" 
                        data-expense-id="${expense.id}"
                        onchange="updateExpenseDepartment(${expense.id}, this.value)">
                    <option value="">Selecione o departamento...</option>
                    ${Object.entries(departments).map(([key, label]) => 
                        `<option value="${key}" ${expense.goal_category === key ? 'selected' : ''}>${label}</option>`
                    ).join('')}
                </select>
            `;
            
            const departmentDisplay = expense.goal_category 
                ? `<span class="expense-department-badge">${expense.goal_category_label}</span>`
                : `<span class="expense-department-badge missing">Sem departamento</span>`;
            
            item.innerHTML = `
                <div class="expense-item-header">
                    <div class="expense-item-title">${expense.title || 'Sem título'}</div>
                    <div class="expense-item-amount">R$ ${parseFloat(expense.amount).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</div>
                </div>
                <div class="expense-item-details">
                    <div class="expense-item-detail">
                        <i class="bi bi-calendar"></i>
                        <span>${expense.transaction_date}</span>
                    </div>
                    ${expense.category_name ? `
                        <div class="expense-item-detail">
                            <i class="bi bi-tag"></i>
                            <span>${expense.category_name}</span>
                        </div>
                    ` : ''}
                    ${expense.payment_method ? `
                        <div class="expense-item-detail">
                            <i class="bi bi-credit-card"></i>
                            <span>${expense.payment_method}</span>
                        </div>
                    ` : ''}
                </div>
                ${expense.description ? `
                    <div style="color: rgba(255,255,255,0.7); font-size: 0.875rem; margin-bottom: 0.75rem;">
                        ${expense.description}
                    </div>
                ` : ''}
                <div class="expense-item-department">
                    ${departmentDisplay}
                    ${departmentSelect}
                </div>
            `;
            
            expensesList.appendChild(item);
        });
    }
    
    // Atualizar departamento
    window.updateExpenseDepartment = async function(expenseId, goalCategory) {
        try {
            const response = await fetch(`/api/expenses/${expenseId}/department`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                                   document.querySelector('input[name="_token"]')?.value || ''
                },
                credentials: 'same-origin',
                body: JSON.stringify({ goal_category: goalCategory || null })
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            
            if (data.success) {
                // Atualizar badge
                const expenseItem = document.querySelector(`[data-expense-id="${expenseId}"]`);
                if (expenseItem) {
                    const badge = expenseItem.querySelector('.expense-department-badge');
                    if (badge) {
                        if (data.expense.goal_category_label) {
                            badge.textContent = data.expense.goal_category_label;
                            badge.classList.remove('missing');
                        } else {
                            badge.textContent = 'Sem departamento';
                            badge.classList.add('missing');
                        }
                    }
                }
                
                // Mostrar feedback visual
                const select = document.querySelector(`select[data-expense-id="${expenseId}"]`);
                if (select) {
                    select.style.borderColor = '#10b981';
                    setTimeout(() => {
                        select.style.borderColor = '';
                    }, 1000);
                }
                
                // Recarregar página após 1 segundo para atualizar os dados
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            }
        } catch (error) {
            console.error('Erro ao atualizar departamento:', error);
            alert('Erro ao atualizar departamento. Tente novamente.');
        }
    };
});
</script>

