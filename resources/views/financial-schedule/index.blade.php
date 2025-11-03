@extends('layouts.app')

@section('title', 'Agenda Financeira')

@section('content')
<!-- Premium Header -->
<div class="premium-header" style="padding: 1rem 1.25rem !important;">
    <div class="header-content">
        <div class="header-title">
            <h1>Agenda Financeira</h1>
            <span class="header-subtitle">{{ ($incomes->count() + $expenses->count()) }} itens neste mês</span>
        </div>
        <div class="header-actions">
            <a href="{{ route('financial-schedule.create') }}" class="action-btn" style="background: #10b981; border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                <i class="bi bi-plus-lg"></i>
            </a>
        </div>
    </div>
</div>

<!-- Balanço Compacto -->
<div class="balance-summary" style="margin-top: 1rem; margin-bottom: 1.5rem; padding: 1rem 1.25rem; background: rgba(255,255,255,0.05); border-radius: 10px; display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap;">
    <div class="balance-item" style="display: flex; align-items: center; gap: 0.5rem; flex: 1; min-width: 0;">
        <i class="bi bi-arrow-down-circle text-success" style="font-size: 1rem;"></i>
        <div style="display: flex; flex-direction: column; gap: 0;">
            <span style="color: rgba(255,255,255,0.6); font-size: 0.75rem; line-height: 1;">Entradas</span>
            <span style="color: #10b981; font-size: 1rem; font-weight: 600; line-height: 1.2;">R$ {{ number_format($totalIncomes, 2, ',', '.') }}</span>
        </div>
    </div>
    <div class="balance-item" style="display: flex; align-items: center; gap: 0.5rem; flex: 1; min-width: 0;">
        <i class="bi bi-arrow-up-circle text-danger" style="font-size: 1rem;"></i>
        <div style="display: flex; flex-direction: column; gap: 0;">
            <span style="color: rgba(255,255,255,0.6); font-size: 0.75rem; line-height: 1;">Saídas</span>
            <span style="color: #ef4444; font-size: 1rem; font-weight: 600; line-height: 1.2;">R$ {{ number_format($totalExpenses, 2, ',', '.') }}</span>
        </div>
    </div>
    <div class="balance-item" style="display: flex; align-items: center; gap: 0.5rem; flex: 1; min-width: 0;">
        <i class="bi bi-calculator" style="font-size: 1rem; color: {{ $balance >= 0 ? '#10b981' : '#ef4444' }};"></i>
        <div style="display: flex; flex-direction: column; gap: 0;">
            <span style="color: rgba(255,255,255,0.6); font-size: 0.75rem; line-height: 1;">Balanço</span>
            <span style="color: {{ $balance >= 0 ? '#10b981' : '#ef4444' }}; font-size: 1.1rem; font-weight: 700; line-height: 1.2;">R$ {{ number_format($balance, 2, ',', '.') }}</span>
        </div>
    </div>
</div>

<!-- Premium Content -->
<div class="premium-content" style="padding: 1.25rem 1rem !important; padding-bottom: 100px !important;">
    <!-- Notificação de Lembretes -->
    @if($notificationCount > 0)
    <div class="alert alert-warning schedule-notification d-flex align-items-center" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border: none; margin-bottom: 1.5rem; border-radius: 15px; padding: 1rem 1.25rem;">
        <i class="bi bi-bell-fill schedule-notification-icon"></i>
        <div class="flex-grow-1 schedule-notification-text">
            <strong>Você tem {{ $notificationCount }} lembrete(s) próximo(s) ao vencimento!</strong>
        </div>
    </div>
    @endif

    <!-- Grid de 2 Colunas: Entradas e Saídas -->
    @if($incomes->count() > 0 || $expenses->count() > 0)
    <div class="schedule-grid">
        <!-- Coluna Esquerda: Entradas -->
        <div class="schedule-column">
            <div class="schedule-column-header">
                <h3 class="schedule-column-title">
                    <i class="bi bi-arrow-down-circle text-success"></i> Entradas
                </h3>
                <span class="schedule-column-count">{{ $incomes->count() }}</span>
            </div>
            
            @if($incomes->count() > 0)
                @foreach($incomes as $schedule)
                @include('financial-schedule.partials.schedule-card', ['schedule' => $schedule])
                @endforeach
            @else
                <div class="no-schedules-message">
                    <p class="text-muted">Nenhuma entrada agendada</p>
                </div>
            @endif
        </div>
        
        <!-- Coluna Direita: Saídas -->
        <div class="schedule-column">
            <div class="schedule-column-header">
                <h3 class="schedule-column-title">
                    <i class="bi bi-arrow-up-circle text-danger"></i> Saídas
                </h3>
                <span class="schedule-column-count">{{ $expenses->count() }}</span>
            </div>
            
            @if($expenses->count() > 0)
                @foreach($expenses as $schedule)
                @include('financial-schedule.partials.schedule-card', ['schedule' => $schedule])
                @endforeach
            @else
                <div class="no-schedules-message">
                    <p class="text-muted">Nenhuma saída agendada</p>
                </div>
            @endif
        </div>
    </div>
    @else
        <div class="no-data-card">
            <div class="no-data-icon">
                <i class="bi bi-calendar-x"></i>
            </div>
            <div class="no-data-content">
                <h4>Nenhum item agendado</h4>
                <p>Adicione receitas e despesas futuras para organizar suas finanças</p>
                <a href="{{ route('financial-schedule.create') }}" class="btn btn-success mt-3">
                    <i class="bi bi-plus-lg me-2"></i> Adicionar Agendamento
                </a>
            </div>
        </div>
    @endif
</div>

<!-- Modal de Cancelamento -->
<div class="modal fade" id="cancelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="background: #1f2937; color: white; border: 1px solid rgba(255,255,255,0.1);">
            <div class="modal-header" style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                <h5 class="modal-title">
                    <i class="bi bi-x-circle text-warning me-2"></i> Cancelar Agendamento
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter: invert(1);"></button>
            </div>
            <form id="cancelForm" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Tem certeza que deseja cancelar o agendamento:</p>
                    <p class="fw-bold" id="cancelItemTitle"></p>
                    
                    <div class="mb-3 mt-3">
                        <label for="cancellation_reason" class="form-label">Motivo do Cancelamento (opcional)</label>
                        <textarea name="cancellation_reason" id="cancellation_reason" class="form-control" rows="3" 
                                  placeholder="Ex: Cliente encerrou contrato..." 
                                  style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white;"></textarea>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid rgba(255,255,255,0.1);">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Não, manter</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-x-circle me-2"></i> Sim, Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Grid de 2 Colunas */
    .schedule-grid {
        margin-top: 1.5rem;
        margin-left: 0;
        margin-right: 0;
        display: flex;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .schedule-column {
        flex: 1;
        padding: 0;
        display: flex;
        flex-direction: column;
        align-items: stretch;
    }
    
    /* Primeira coluna - padding apenas à direita */
    .schedule-column:first-child {
        padding-right: 0.5rem;
    }
    
    /* Última coluna - padding apenas à esquerda */
    .schedule-column:last-child {
        padding-left: 0.5rem;
    }
    
    .schedule-column-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        padding-bottom: 0.75rem;
        padding-left: 0.5rem;
        padding-right: 0.5rem;
        border-bottom: 2px solid rgba(255, 255, 255, 0.2);
        width: 100%;
    }
    
    .schedule-column-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: white;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .schedule-column-count {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 600;
    }
    
    .no-schedules-message {
        text-align: center;
        padding: 20px;
        color: rgba(255, 255, 255, 0.6);
    }
    
    /* Responsivo: Em mobile, ajustar espaçamentos mas manter lado a lado */
    @media (max-width: 768px) {
        .schedule-grid {
            gap: 0.75rem;
        }
        
        .schedule-column:first-child {
            padding-right: 0.375rem;
        }
        
        .schedule-column:last-child {
            padding-left: 0.375rem;
        }
        
        .schedule-column-header {
            padding-left: 0.375rem;
            padding-right: 0.375rem;
        }
        
        .schedule-column-header {
            margin-bottom: 10px;
            padding-bottom: 8px;
        }
        
        .schedule-column-title {
            font-size: 0.95rem;
            gap: 0.25rem;
        }
        
        .schedule-column-title i {
            font-size: 1rem;
        }
        
        .schedule-column-count {
            padding: 2px 8px;
            font-size: 0.75rem;
        }
    }
    
    @media (max-width: 480px) {
        .schedule-grid {
            gap: 0.5rem;
        }
        
        .schedule-column:first-child {
            padding-right: 0.25rem;
        }
        
        .schedule-column:last-child {
            padding-left: 0.25rem;
        }
        
        .schedule-column-header {
            padding-left: 0.25rem;
            padding-right: 0.25rem;
        }
        
        .schedule-column-header {
            margin-bottom: 8px;
            padding-bottom: 6px;
        }
        
        .schedule-column-title {
            font-size: 0.85rem;
            gap: 0.2rem;
        }
        
        .schedule-column-title i {
            font-size: 0.9rem;
        }
        
        .schedule-column-count {
            padding: 2px 6px;
            font-size: 0.7rem;
        }
    }
    
    @media (max-width: 375px) {
        .schedule-grid {
            gap: 0.375rem;
        }
        
        .schedule-column:first-child {
            padding-right: 0.125rem;
        }
        
        .schedule-column:last-child {
            padding-left: 0.125rem;
        }
        
        .schedule-column-header {
            padding-left: 0.125rem;
            padding-right: 0.125rem;
        }
        
        .schedule-column-title {
            font-size: 0.8rem;
        }
        
        .schedule-column-title i {
            font-size: 0.85rem;
        }
        
        .schedule-column-count {
            padding: 1px 4px;
            font-size: 0.65rem;
        }
    }
    
    /* ============ OTIMIZAÇÕES DE DESIGN - REDUZIR POLUIÇÃO VISUAL ============ */
    
    /* Card mais compacto - usando especificidade maior */
    .premium-content .schedule-card {
        margin-bottom: 0.875rem !important;
        padding: 1rem !important;
        border-radius: 12px !important;
        display: flex !important;
        flex-direction: column !important;
        max-width: 100% !important;
        overflow: hidden !important;
        box-sizing: border-box !important;
    }
    
    /* Reduzir espaçamento interno do card */
    .premium-content .schedule-card-content {
        gap: 0.75rem !important;
        display: flex !important;
        align-items: flex-start !important;
        flex: 1 !important;
        min-height: 0 !important;
        max-width: 100% !important;
        overflow: hidden !important;
        box-sizing: border-box !important;
    }
    
    /* Melhorar alinhamento do conteúdo principal */
    .premium-content .schedule-main-content {
        flex: 1 !important;
        min-width: 0 !important;
        display: flex !important;
        flex-direction: column !important;
        gap: 0.5rem !important;
        min-height: 0 !important;
        max-width: 100% !important;
        overflow: hidden !important;
        box-sizing: border-box !important;
    }
    
    /* Garantir que título e descrição não ultrapassem */
    .premium-content .schedule-card .schedule-title {
        max-width: 100% !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        white-space: nowrap !important;
        word-break: break-word !important;
    }
    
    .premium-content .schedule-card .schedule-description {
        max-width: 100% !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        white-space: nowrap !important;
        word-break: break-word !important;
    }
    
    /* Garantir que todos os elementos respeitem limites da tela */
    .premium-content,
    .premium-header,
    .balance-summary,
    .schedule-grid,
    .schedule-column,
    .schedule-card {
        box-sizing: border-box !important;
        max-width: 100% !important;
        overflow-x: hidden !important;
    }
    
    /* Evitar quebra de linha em valores */
    .premium-content .schedule-card .schedule-amount,
    .premium-content .schedule-card .schedule-meta,
    .premium-content .schedule-card .schedule-category {
        word-break: keep-all !important;
        overflow-wrap: normal !important;
    }
    
    /* Imagem menor em mobile */
    .premium-content .schedule-image {
        width: 50px;
        height: 50px;
        border-radius: 8px;
    }
    
    .premium-content .schedule-header {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        margin-bottom: 0;
        align-items: flex-start;
    }
    
    .premium-content .schedule-title-section {
        width: 100%;
    }
    
    .premium-content .schedule-badges-top {
        display: flex;
        flex-wrap: wrap;
        gap: 0.35rem;
        align-items: center;
        width: 100%;
    }
    
    /* Título menor */
    .premium-content .schedule-title {
        font-size: 1rem;
        margin-bottom: 0.125rem;
    }
    
    /* Descrição menor */
    .premium-content .schedule-description {
        font-size: 0.8rem;
    }
    
    /* Badges mais compactos */
    .premium-content .schedule-type-badge,
    .premium-content .schedule-recurring-badge,
    .premium-content .schedule-badge-cancelled {
        font-size: 0.65rem;
        padding: 2px 6px;
    }
    
    /* Footer mais compacto - horizontal em desktop, vertical em mobile */
    .premium-content .schedule-card .schedule-footer {
        display: flex !important;
        flex-direction: row !important;
        justify-content: space-between !important;
        align-items: center !important;
        gap: 0.75rem !important;
        width: 100% !important;
        margin-top: 0.5rem !important;
        flex-wrap: wrap !important;
        overflow: hidden !important;
        max-width: 100% !important;
    }
    
    /* Em telas pequenas, botões abaixo do valor/data */
    @media (max-width: 576px) {
        .premium-content .schedule-card .schedule-footer {
            flex-direction: column !important;
            align-items: flex-start !important;
            gap: 0.5rem !important;
        }
        
        .premium-content .schedule-card .schedule-info {
            width: 100% !important;
        }
        
        .premium-content .schedule-card .schedule-actions {
            width: 100% !important;
            justify-content: flex-start !important;
        }
    }
    
    .premium-content .schedule-card .schedule-info {
        display: flex !important;
        flex-direction: column !important;
        gap: 0.125rem !important;
        align-items: flex-start !important;
        flex: 1 !important;
        min-width: 0 !important;
        max-width: 100% !important;
        overflow: hidden !important;
    }
    
    /* Categoria acima do valor */
    .premium-content .schedule-card .schedule-category {
        font-size: 0.7rem !important;
        color: rgba(255, 255, 255, 0.6) !important;
        line-height: 1.2 !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        max-width: 100% !important;
        margin-bottom: 0.125rem !important;
    }
    
    .premium-content .schedule-card .schedule-category i {
        font-size: 0.65rem !important;
        margin-right: 0.25rem !important;
    }
    
    /* Valor menor e compacto */
    .premium-content .schedule-card .schedule-amount {
        font-size: 1.1rem !important;
        margin-bottom: 0.125rem !important;
        line-height: 1.2 !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        max-width: 100% !important;
    }
    
    /* Meta menor e compacto */
    .premium-content .schedule-card .schedule-meta {
        font-size: 0.75rem !important;
        line-height: 1.2 !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        max-width: 100% !important;
    }
    
    /* Ações mais compactas - horizontal - FORÇAR SOBRESCRITA */
    .premium-content .schedule-card .schedule-actions {
        gap: 0.35rem !important;
        display: flex !important;
        flex-direction: row !important;
        flex-wrap: wrap !important;
        align-items: center !important;
        justify-content: flex-end !important;
        flex-shrink: 0 !important;
        width: auto !important;
        min-width: 0 !important;
        max-width: 100% !important;
        overflow: hidden !important;
    }
    
    /* Garantir que todos os elementos filhos sejam inline */
    .premium-content .schedule-card .schedule-actions > * {
        display: inline-flex !important;
        flex: 0 0 auto !important;
        width: auto !important;
        min-width: auto !important;
        margin: 0 !important;
        vertical-align: middle !important;
    }
    
    .premium-content .schedule-card .schedule-action-btn {
        padding: 0.3rem 0.6rem !important;
        font-size: 0.7rem !important;
        min-height: 28px !important;
        height: 28px !important;
        max-height: 28px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 0.25rem !important;
        flex: 0 0 auto !important;
        min-width: auto !important;
        width: auto !important;
        white-space: nowrap !important;
        border-radius: 6px !important;
        margin: 0 !important;
        line-height: 1 !important;
    }
    
    .premium-content .schedule-action-btn i {
        font-size: 0.875rem;
        line-height: 1;
    }
    
    .premium-content .schedule-card .schedule-action-form {
        display: inline-flex !important;
        margin: 0 !important;
        flex: 0 0 auto !important;
        width: auto !important;
        padding: 0 !important;
        border: none !important;
        height: auto !important;
        vertical-align: middle !important;
    }
    
    .premium-content .schedule-card .schedule-action-form button,
    .premium-content .schedule-card .schedule-action-form .btn {
        display: inline-flex !important;
        flex: 0 0 auto !important;
        width: auto !important;
        min-width: auto !important;
        margin: 0 !important;
        height: 28px !important;
        max-height: 28px !important;
        line-height: 1 !important;
    }
    
    /* Badge dentro de actions também inline */
    .premium-content .schedule-card .schedule-actions .badge {
        display: inline-flex !important;
        flex: 0 0 auto !important;
        height: 28px !important;
        max-height: 28px !important;
        line-height: 1 !important;
        align-items: center !important;
    }
    
    /* Esconder texto nos botões pequenos, manter só ícones */
    @media (max-width: 480px) {
        .premium-content .schedule-btn-text {
            display: none;
        }
        
        .premium-content .schedule-action-btn {
            min-width: 32px;
            padding: 0.3rem;
            border-radius: 6px;
        }
        
        .premium-content .schedule-actions {
            gap: 0.3rem;
        }
    }
    
    /* Mobile específico */
    @media (max-width: 768px) {
        .premium-content .schedule-card {
            margin-bottom: 0.75rem !important;
            padding: 0.875rem !important;
        }
        
        .premium-content .schedule-image {
            width: 45px;
            height: 45px;
        }
        
        .premium-content .schedule-title {
            font-size: 0.9rem;
        }
        
        .premium-content .schedule-description {
            font-size: 0.75rem;
        }
        
        .premium-content .schedule-card .schedule-amount {
            font-size: 1rem !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }
        
        .premium-content .schedule-card .schedule-meta {
            font-size: 0.7rem !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }
        
        .premium-content .schedule-card .schedule-footer {
            flex-direction: column !important;
            align-items: flex-start !important;
            gap: 0.5rem !important;
        }
        
        .premium-content .schedule-card .schedule-info {
            width: 100% !important;
        }
        
        .premium-content .schedule-card .schedule-actions {
            width: 100% !important;
            justify-content: flex-start !important;
        }
        
        .premium-content .schedule-card .schedule-action-btn {
            padding: 0.25rem 0.45rem !important;
            font-size: 0.65rem !important;
            min-height: 26px !important;
            height: 26px !important;
            max-height: 26px !important;
            flex: 0 0 auto !important;
            min-width: auto !important;
            width: auto !important;
        }
        
        .premium-content .schedule-card .schedule-action-btn i {
            font-size: 0.75rem !important;
        }
        
        .premium-content .schedule-card .schedule-actions {
            gap: 0.25rem !important;
            width: auto !important;
            flex-direction: row !important;
            flex-wrap: wrap !important;
        }
        
        .premium-content .schedule-card .schedule-action-form button,
        .premium-content .schedule-card .schedule-action-form .btn {
            height: 26px !important;
            max-height: 26px !important;
        }
    }
    
    @media (max-width: 480px) {
        .premium-content .schedule-card {
            margin-bottom: 0.625rem !important;
            padding: 0.75rem !important;
        }
        
        .premium-content {
            padding: 1rem 0.75rem !important;
        }
        
        .premium-header {
            padding: 0.875rem 1rem !important;
        }
        
        .balance-summary {
            padding: 0.875rem 1rem !important;
            margin-top: 0.875rem !important;
            margin-bottom: 1.25rem !important;
        }
        
        .premium-content .schedule-card-content {
            gap: 0.375rem;
        }
        
        .premium-content .schedule-image {
            width: 40px;
            height: 40px;
        }
        
        .premium-content .schedule-main-content {
            gap: 0.375rem;
        }
        
        .premium-content .schedule-title {
            font-size: 0.85rem;
        }
        
        .premium-content .schedule-description {
            font-size: 0.7rem;
        }
        
        .premium-content .schedule-card .schedule-amount {
            font-size: 0.95rem !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }
        
        .premium-content .schedule-card .schedule-meta {
            font-size: 0.65rem !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }
        
        .premium-content .schedule-type-badge,
        .premium-content .schedule-recurring-badge,
        .premium-content .schedule-badge-cancelled {
            font-size: 0.6rem;
            padding: 1px 4px;
        }
        
        .premium-content .schedule-card .schedule-footer {
            flex-direction: column !important;
            align-items: flex-start !important;
            gap: 0.4rem !important;
        }
        
        .premium-content .schedule-card .schedule-info {
            width: 100% !important;
            flex: none !important;
        }
        
        .premium-content .schedule-card .schedule-actions {
            width: 100% !important;
            justify-content: flex-start !important;
        }
        
        .premium-content .schedule-card .schedule-amount {
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }
        
        .premium-content .schedule-card .schedule-meta {
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }
        
        .premium-content .schedule-card .schedule-action-btn {
            padding: 0.2rem 0.35rem !important;
            font-size: 0.6rem !important;
            min-height: 24px !important;
            height: 24px !important;
            max-height: 24px !important;
            min-width: 24px !important;
            flex: 0 0 auto !important;
            width: auto !important;
        }
        
        .premium-content .schedule-card .schedule-action-btn i {
            font-size: 0.7rem !important;
        }
        
        .premium-content .schedule-card .schedule-actions {
            gap: 0.2rem !important;
            flex-shrink: 0 !important;
            width: auto !important;
            flex-direction: row !important;
            flex-wrap: wrap !important;
        }
        
        .premium-content .schedule-card .schedule-action-form button,
        .premium-content .schedule-card .schedule-action-form .btn {
            height: 24px !important;
            max-height: 24px !important;
        }
    }
    
    @media (max-width: 375px) {
        .premium-content .schedule-card {
            margin-bottom: 0.5rem !important;
            padding: 0.625rem !important;
        }
        
        .premium-content {
            padding: 0.875rem 0.625rem !important;
        }
        
        .premium-header {
            padding: 0.75rem 0.875rem !important;
        }
        
        .balance-summary {
            padding: 0.75rem 0.875rem !important;
            margin-top: 0.75rem !important;
            margin-bottom: 1rem !important;
        }
        
        .premium-content .schedule-image {
            width: 35px;
            height: 35px;
        }
        
        .premium-content .schedule-title {
            font-size: 0.8rem;
        }
        
        .premium-content .schedule-amount {
            font-size: 0.9rem;
        }
        
        .premium-content .schedule-card .schedule-footer {
            flex-direction: column !important;
            align-items: flex-start !important;
            gap: 0.3rem !important;
        }
        
        .premium-content .schedule-card .schedule-info {
            width: 100% !important;
        }
        
        .premium-content .schedule-card .schedule-actions {
            width: 100% !important;
            justify-content: flex-start !important;
        }
        
        .premium-content .schedule-action-btn {
            padding: 0.18rem 0.3rem !important;
            font-size: 0.55rem;
            min-height: 22px;
            min-width: 22px !important;
            flex: 0 0 auto !important;
            width: auto !important;
        }
        
        .premium-content .schedule-action-btn i {
            font-size: 0.65rem;
        }
        
        .premium-content .schedule-actions {
            gap: 0.15rem !important;
            width: auto !important;
        }
    }
    
    /* Reduzir header das colunas */
    .premium-content .schedule-column-header {
        margin-bottom: 10px;
        padding-bottom: 8px;
    }
    
    @media (max-width: 768px) {
        .premium-content .schedule-column-header {
            margin-bottom: 8px;
            padding-bottom: 6px;
        }
    }
    
    @media (max-width: 480px) {
        .premium-content .schedule-column-header {
            margin-bottom: 6px;
            padding-bottom: 4px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Função para abrir modal de cancelamento
    function openCancelModal(id, title) {
        document.getElementById('cancelItemTitle').textContent = title;
        document.getElementById('cancelForm').action = '{{ url("financial-schedule") }}/' + id + '/cancel';
        const modal = new bootstrap.Modal(document.getElementById('cancelModal'));
        modal.show();
    }

    // Atualizar contador de notificações a cada 5 minutos
    setInterval(function() {
        fetch('{{ route("financial-schedule.notifications") }}')
            .then(response => response.json())
            .then(data => {
                // Atualizar badge se necessário
                console.log('Notificações:', data.count);
            });
    }, 300000);
</script>
@endpush
@endsection

