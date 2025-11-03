@extends('layouts.app')

@section('title', 'Agenda Financeira')

@section('content')
<!-- Premium Header -->
<div class="premium-header">
    <div class="header-content">
        <div class="header-title">
            <h1>Agenda Financeira</h1>
            <span class="header-subtitle">{{ $schedules->count() }} itens neste mês</span>
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

    <!-- Lista de Itens Agendados -->
    @if($schedules->count() > 0)
        @foreach($schedules as $schedule)
        <div class="premium-product-card schedule-card" style="margin-bottom: 15px;">
            <div class="schedule-card-content">
                <!-- Imagem se houver -->
                @if($schedule->image_path)
                <div class="schedule-image-wrapper">
                    <img src="{{ asset('storage/' . $schedule->image_path) }}" 
                         alt="{{ $schedule->title }}" 
                         class="schedule-image">
                </div>
                @endif
                
                <div class="schedule-main-content">
                    <div class="schedule-header">
                        <div class="schedule-title-section">
                            <h4 class="schedule-title">
                                {{ $schedule->title }}
                                @if($schedule->is_cancelled)
                                    <span class="badge bg-secondary schedule-badge-cancelled">
                                        <i class="bi bi-x-circle"></i> Cancelado
                                    </span>
                                @endif
                            </h4>
                            @if($schedule->description)
                            <p class="schedule-description">{{ $schedule->description }}</p>
                            @endif
                        </div>
                        <div class="schedule-badges-top">
                            <span class="badge {{ $schedule->type === 'income' ? 'bg-success' : 'bg-danger' }} schedule-type-badge">
                                {{ $schedule->type_label }}
                            </span>
                            @if($schedule->is_recurring)
                            <span class="badge bg-info schedule-recurring-badge" title="Recorrente: {{ $schedule->recurring_label }}">
                                <i class="bi bi-arrow-repeat"></i> {{ $schedule->recurring_label }}
                            </span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="schedule-footer">
                        <div class="schedule-info">
                            <div class="schedule-amount">
                                {{ $schedule->formatted_amount }}
                            </div>
                            <div class="schedule-meta">
                                <i class="bi bi-calendar-event"></i> 
                                {{ $schedule->scheduled_date->format('d/m/Y') }}
                                @if($schedule->category)
                                    <span class="schedule-category-separator">•</span>
                                    <i class="bi bi-tag"></i> {{ $schedule->category->name }}
                                @endif
                            </div>
                        </div>
                        
                        <div class="schedule-actions">
                            @if($schedule->is_cancelled)
                            <span class="badge bg-secondary schedule-status-badge">
                                <i class="bi bi-x-circle"></i> Cancelado
                            </span>
                            @elseif(!$schedule->is_confirmed)
                            <form action="{{ route('financial-schedule.confirm', $schedule->id) }}" method="POST" class="schedule-action-form">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm schedule-action-btn">
                                    <i class="bi bi-check-circle"></i> <span class="schedule-btn-text">Confirmar</span>
                                </button>
                            </form>
                            @else
                            <form action="{{ route('financial-schedule.unconfirm', $schedule->id) }}" method="POST" class="schedule-action-form">
                                @csrf
                                <button type="submit" class="btn btn-secondary btn-sm schedule-action-btn" onclick="return confirm('Tem certeza que deseja desfazer a confirmação? A transação será removida do Fluxo de Caixa.')">
                                    <i class="bi bi-arrow-counterclockwise"></i> <span class="schedule-btn-text">Desfazer</span>
                                </button>
                            </form>
                            @endif
                            
                            @if(!$schedule->is_cancelled && !$schedule->is_confirmed)
                            <button type="button" class="btn btn-warning btn-sm schedule-action-btn schedule-cancel-btn" onclick="openCancelModal({{ $schedule->id }}, '{{ $schedule->title }}')">
                                <i class="bi bi-x-circle"></i> <span class="schedule-btn-text">Cancelar</span>
                            </button>
                            @endif
                            
                            <form action="{{ route('financial-schedule.destroy', $schedule->id) }}" method="POST" class="schedule-action-form" onsubmit="return confirm('Tem certeza que deseja excluir este item?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm schedule-action-btn schedule-delete-btn">
                                    <i class="bi bi-trash"></i> <span class="schedule-btn-text-mobile">Excluir</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
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

