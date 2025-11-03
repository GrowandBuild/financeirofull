@extends('layouts.app')

@section('title', 'Agenda Financeira')

@section('content')
<!-- Premium Header -->
<div class="premium-header">
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

<!-- Premium Content -->
<div class="premium-content">
    <!-- Notificação de Lembretes -->
    @if($notificationCount > 0)
    <div class="alert alert-warning schedule-notification d-flex align-items-center" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border: none; margin-bottom: 20px; border-radius: 15px; padding: 1rem;">
        <i class="bi bi-bell-fill schedule-notification-icon"></i>
        <div class="flex-grow-1 schedule-notification-text">
            <strong>Você tem {{ $notificationCount }} lembrete(s) próximo(s) ao vencimento!</strong>
        </div>
    </div>
    @endif

    <!-- Grid de 2 Colunas: Entradas e Saídas -->
    @if($incomes->count() > 0 || $expenses->count() > 0)
    <div class="row g-3 schedule-grid">
        <!-- Coluna Esquerda: Entradas -->
        <div class="col-md-6 schedule-column">
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
        <div class="col-md-6 schedule-column">
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
        margin-top: 20px;
    }
    
    .schedule-column {
        padding: 0 10px;
    }
    
    .schedule-column-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid rgba(255, 255, 255, 0.2);
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
    
    /* Responsivo: Em mobile, as colunas ficam empilhadas */
    @media (max-width: 768px) {
        .schedule-column {
            padding: 0;
            margin-bottom: 20px;
        }
        
        .schedule-column-header {
            margin-bottom: 10px;
        }
        
        .schedule-column-title {
            font-size: 1.1rem;
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

