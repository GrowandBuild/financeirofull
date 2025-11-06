<div class="premium-product-card schedule-card @if($schedule->type === 'expense' && $schedule->is_confirmed) schedule-card-paid @endif" style="margin-bottom: 15px;">
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
                    @if($schedule->category)
                    <div class="schedule-category">
                        <i class="bi bi-tag"></i> {{ $schedule->category->name }}
                    </div>
                    @endif
                    <div class="schedule-amount">
                        {{ $schedule->formatted_amount }}
                    </div>
                    <div class="schedule-meta">
                        <i class="bi bi-calendar-event"></i> 
                        {{ $schedule->scheduled_date->format('d/m/Y') }}
                    </div>
                </div>
                
                <div class="schedule-actions">
                    <!-- Botão de Editar -->
                    <a href="{{ route('financial-schedule.edit', $schedule->id) }}" class="btn btn-primary btn-sm schedule-action-btn" title="Editar">
                        <i class="bi bi-pencil"></i> <span class="schedule-btn-text">Editar</span>
                    </a>
                    
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
                        <button type="submit" class="btn btn-danger btn-sm schedule-action-btn schedule-delete-btn" title="Excluir">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
