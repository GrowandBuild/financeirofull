@extends('layouts.app')

@section('title', 'Reset do Banco de Dados')

@section('content')
<div class="premium-content">
    <!-- Header -->
    <div class="premium-header">
        <div class="header-content">
            <div class="header-left">
                <a href="{{ route('admin.products.index') }}" class="back-btn">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div class="header-title">
                    <h1>⚠️ RESET PERIGOSO DO BANCO ⚠️</h1>
                    <p class="header-subtitle">🚨 APAGA TUDO - APENAS USUÁRIOS SOBREVIVEM 🚨</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Aviso de Segurança -->
    <div class="warning-section">
        <div class="warning-card">
            <div class="warning-icon">
                <i class="bi bi-exclamation-triangle"></i>
            </div>
            <div class="warning-content">
                <h3>⚠️ ATENÇÃO - OPERAÇÃO IRREVERSÍVEL</h3>
                <p>Esta operação irá <strong>apagar permanentemente</strong> todos os dados do sistema, exceto os usuários cadastrados.</p>
                <ul>
                    <li>🗑️ Todos os produtos serão removidos</li>
                    <li>🗑️ Todo o histórico de compras será apagado</li>
                    <li>🗑️ Todos os alertas de preço serão deletados</li>
                    <li>👥 Apenas os usuários serão mantidos</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Botão de Limpeza de Cache -->
    <div class="cache-section mb-4">
        <div class="card-custom">
            <div class="card-body">
                <h5 class="text-dark-custom mb-3">
                    <i class="bi bi-arrow-clockwise"></i> Limpeza de Cache
                </h5>
                <p class="text-gray-custom mb-3">
                    Se os produtos ainda aparecerem após o reset, clique no botão abaixo para limpar completamente o cache do navegador.
                </p>
                <button onclick="clearAllCaches()" class="premium-btn">
                    <i class="bi bi-trash3"></i> Limpar Cache do Navegador
                </button>
            </div>
        </div>
    </div>

    <!-- Estatísticas Atuais -->
    <div class="stats-section">
        <h3 class="section-title">
            <i class="bi bi-graph-up"></i>
            Dados Atuais no Sistema
        </h3>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon products">
                    <i class="bi bi-box"></i>
                </div>
                <div class="stat-content">
                    <h4>Produtos</h4>
                    <span class="stat-value">{{ $stats['products'] }}</span>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon purchases">
                    <i class="bi bi-cart-check"></i>
                </div>
                <div class="stat-content">
                    <h4>Compras</h4>
                    <span class="stat-value">{{ $stats['purchases'] }}</span>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon alerts">
                    <i class="bi bi-bell"></i>
                </div>
                <div class="stat-content">
                    <h4>Alertas</h4>
                    <span class="stat-value">{{ $stats['alerts'] }}</span>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon users">
                    <i class="bi bi-people"></i>
                </div>
                <div class="stat-content">
                    <h4>Usuários</h4>
                    <span class="stat-value">{{ $stats['users'] }}</span>
                    <small class="stat-note">Serão mantidos</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulário de Confirmação -->
    <div class="confirmation-section">
        <form action="{{ route('admin.reset.execute') }}" method="POST" id="resetForm">
            @csrf
            
            <div class="confirmation-card">
                <h3 class="card-title">
                    <i class="bi bi-shield-exclamation"></i>
                    Confirmação de Segurança
                </h3>
                
                <div class="confirmation-content">
                    <p>Para confirmar o reset, digite exatamente <strong>"RESETAR"</strong> no campo abaixo:</p>
                    
                    <div class="input-group">
                        <label for="confirmation" class="input-label">Digite "RESETAR" para confirmar:</label>
                        <input type="text" 
                               name="confirmation" 
                               id="confirmation" 
                               class="confirmation-input"
                               placeholder="Digite RESETAR aqui..."
                               required>
                    </div>
                    
                    <div class="checkbox-group">
                        <label class="checkbox-container">
                            <input type="checkbox" id="understand" required>
                            <span class="checkmark"></span>
                            <span class="checkbox-text">Entendo que esta operação é irreversível e apagará todos os dados</span>
                        </label>
                    </div>
                </div>
                
                <div class="form-actions">
                    <a href="{{ route('admin.products.index') }}" class="premium-btn outline">
                        <i class="bi bi-arrow-left"></i>
                        Cancelar
                    </a>
                    <button type="submit" class="premium-btn danger" id="resetButton" disabled>
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        🚨 DESTRUIR TUDO 🚨
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
/* Estilos para a página de reset */
.warning-section {
    margin-bottom: 2rem;
}

.warning-card {
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(220, 38, 38, 0.05));
    border: 2px solid rgba(239, 68, 68, 0.3);
    border-radius: 16px;
    padding: 1.5rem;
    display: flex;
    gap: 1rem;
    align-items: flex-start;
}

.warning-icon {
    color: #ef4444;
    font-size: 2rem;
    flex-shrink: 0;
}

.warning-content h3 {
    color: #ef4444;
    font-size: 1.25rem;
    font-weight: 700;
    margin: 0 0 0.75rem 0;
}

.warning-content p {
    color: rgba(255, 255, 255, 0.9);
    margin: 0 0 1rem 0;
    line-height: 1.6;
}

.warning-content ul {
    color: rgba(255, 255, 255, 0.8);
    margin: 0;
    padding-left: 1.5rem;
}

.warning-content li {
    margin-bottom: 0.5rem;
}

.stats-section {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 16px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    backdrop-filter: blur(10px);
}

.section-title {
    color: #10b981;
    font-size: 1.25rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin: 0 0 1.5rem 0;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.stat-card {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: all 0.3s ease;
}

.stat-card:hover {
    background: rgba(255, 255, 255, 0.08);
    transform: translateY(-2px);
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.stat-icon.products {
    background: rgba(16, 185, 129, 0.2);
    color: #10b981;
}

.stat-icon.purchases {
    background: rgba(59, 130, 246, 0.2);
    color: #3b82f6;
}

.stat-icon.alerts {
    background: rgba(245, 158, 11, 0.2);
    color: #f59e0b;
}

.stat-icon.users {
    background: rgba(139, 92, 246, 0.2);
    color: #8b5cf6;
}

.stat-content h4 {
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.875rem;
    font-weight: 500;
    margin: 0 0 0.25rem 0;
}

.stat-value {
    color: white;
    font-size: 1.5rem;
    font-weight: 700;
}

.stat-note {
    color: rgba(16, 185, 129, 0.8);
    font-size: 0.75rem;
    font-weight: 500;
}

.confirmation-section {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 16px;
    padding: 1.5rem;
    backdrop-filter: blur(10px);
}

.confirmation-card {
    max-width: 600px;
    margin: 0 auto;
}

.card-title {
    color: #ef4444;
    font-size: 1.25rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin: 0 0 1.5rem 0;
}

.confirmation-content p {
    color: rgba(255, 255, 255, 0.9);
    margin: 0 0 1.5rem 0;
    line-height: 1.6;
}

.input-group {
    margin-bottom: 1.5rem;
}

.input-label {
    display: block;
    color: rgba(255, 255, 255, 0.9);
    font-size: 0.875rem;
    font-weight: 500;
    margin-bottom: 0.5rem;
}

.confirmation-input {
    width: 100%;
    background: rgba(255, 255, 255, 0.1);
    border: 2px solid rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    padding: 1rem;
    color: white;
    font-size: 1rem;
    font-weight: 600;
    text-transform: uppercase;
    transition: all 0.3s ease;
}

.confirmation-input:focus {
    outline: none;
    border-color: #ef4444;
    background: rgba(255, 255, 255, 0.15);
}

.confirmation-input::placeholder {
    color: rgba(255, 255, 255, 0.5);
    text-transform: none;
}

.checkbox-group {
    margin-bottom: 2rem;
}

.checkbox-container {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    cursor: pointer;
    color: rgba(255, 255, 255, 0.9);
    line-height: 1.5;
}

.checkbox-container input[type="checkbox"] {
    display: none;
}

.checkmark {
    width: 20px;
    height: 20px;
    background: rgba(255, 255, 255, 0.1);
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    flex-shrink: 0;
    margin-top: 2px;
}

.checkbox-container input[type="checkbox"]:checked + .checkmark {
    background: #ef4444;
    border-color: #ef4444;
}

.checkbox-container input[type="checkbox"]:checked + .checkmark::after {
    content: '✓';
    color: white;
    font-weight: bold;
    font-size: 12px;
}

.form-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
}

.premium-btn.danger {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    border: 1px solid #dc2626;
    color: white;
    font-weight: 600;
    transition: all 0.3s ease;
}

.premium-btn.danger:hover:not(:disabled) {
    background: linear-gradient(135deg, #dc2626, #b91c1c);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(239, 68, 68, 0.3);
}

.premium-btn.danger:disabled {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.2);
    color: rgba(255, 255, 255, 0.5);
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

/* BOTÃO DE DESTRUIÇÃO AGRESSIVO */
.premium-btn.danger:not(:disabled) {
    background: linear-gradient(135deg, #ef4444, #dc2626, #b91c1c) !important;
    border: 3px solid #dc2626 !important;
    color: white !important;
    font-weight: 900 !important;
    font-size: 1.1rem !important;
    text-transform: uppercase !important;
    letter-spacing: 1px !important;
    box-shadow: 0 6px 20px rgba(239, 68, 68, 0.5) !important;
    position: relative !important;
    overflow: hidden !important;
}

.premium-btn.danger:not(:disabled)::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
}

.premium-btn.danger:not(:disabled):hover {
    background: linear-gradient(135deg, #dc2626, #b91c1c, #991b1b) !important;
    transform: translateY(-4px) scale(1.08) !important;
    box-shadow: 0 10px 30px rgba(239, 68, 68, 0.7) !important;
    animation: dangerShake 0.6s ease-in-out !important;
}


@keyframes dangerShake {
    0%, 100% { transform: translateY(-4px) scale(1.08); }
    25% { transform: translateY(-4px) scale(1.08) translateX(-3px); }
    75% { transform: translateY(-4px) scale(1.08) translateX(3px); }
}

/* Responsividade */
@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .warning-card {
        flex-direction: column;
        text-align: center;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const confirmationInput = document.getElementById('confirmation');
    const understandCheckbox = document.getElementById('understand');
    const resetButton = document.getElementById('resetButton');
    const resetForm = document.getElementById('resetForm');
    
    function updateResetButton() {
        const isConfirmationValid = confirmationInput.value.toUpperCase() === 'RESETAR';
        const isCheckboxChecked = understandCheckbox.checked;
        
        resetButton.disabled = !(isConfirmationValid && isCheckboxChecked);
    }
    
    confirmationInput.addEventListener('input', updateResetButton);
    understandCheckbox.addEventListener('change', updateResetButton);
    
    resetForm.addEventListener('submit', function(e) {
        if (!resetButton.disabled) {
            if (!confirm('⚠️ ÚLTIMA CONFIRMAÇÃO: Tem certeza que deseja resetar o banco de dados? Esta ação é irreversível!')) {
                e.preventDefault();
            }
        }
    });
});

// Função para limpar cache do navegador
function clearAllCaches() {
    if (!confirm('🧹 Limpar TODOS os caches do navegador? Isso irá recarregar a página.')) {
        return;
    }
    
    console.log('🧹 Limpando cache do navegador...');
    
    // 1. Limpar localStorage
    try {
        localStorage.clear();
        console.log('✅ localStorage limpo');
    } catch (e) {
        console.warn('⚠️ Erro ao limpar localStorage:', e);
    }
    
    // 2. Limpar sessionStorage
    try {
        sessionStorage.clear();
        console.log('✅ sessionStorage limpo');
    } catch (e) {
        console.warn('⚠️ Erro ao limpar sessionStorage:', e);
    }
    
    // 3. Limpar IndexedDB
    if ('indexedDB' in window) {
        try {
            indexedDB.databases().then(databases => {
                databases.forEach(db => {
                    indexedDB.deleteDatabase(db.name);
                });
                console.log('✅ IndexedDB limpo');
            });
        } catch (e) {
            console.warn('⚠️ Erro ao limpar IndexedDB:', e);
        }
    }
    
    // 4. Limpar Service Worker cache
    if ('caches' in window) {
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cacheName => {
                    console.log('🗑️ Removendo cache:', cacheName);
                    return caches.delete(cacheName);
                })
            );
        }).then(() => {
            console.log('✅ Service Worker cache limpo');
        }).catch(e => {
            console.warn('⚠️ Erro ao limpar Service Worker cache:', e);
        });
    }
    
    // 5. Forçar reload sem cache
    setTimeout(() => {
        console.log('🔄 Recarregando página sem cache...');
        window.location.reload(true);
    }, 1000);
}
</script>
@endsection
