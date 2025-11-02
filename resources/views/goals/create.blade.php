@extends('layouts.app')

@section('title', 'Novo Objetivo')

@section('content')
<div class="premium-content">
    <div class="max-w-4xl mx-auto px-4">
        <!-- Header Premium -->
        <div class="premium-header mb-8">
            <div class="header-content">
                <div class="header-left">
                    <a href="{{ route('goals.index') }}" class="back-btn">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <div class="header-title">
                        <h1>Novo Objetivo Financeiro</h1>
                        <p class="header-subtitle">Defina sua pizza financeira ideal</p>
                    </div>
                </div>
                <div class="header-actions">
                    <a href="{{ route('goals.index') }}" class="action-btn" title="Lista de Objetivos">
                        <i class="bi bi-list-ul"></i>
                    </a>
                </div>
            </div>
        </div>

        <form action="{{ route('goals.store') }}" method="POST">
            @csrf

            <!-- Informações Básicas -->
            <div class="chart-section mb-4">
                <h3 class="section-title">
                    <i class="bi bi-info-circle"></i>
                    Informações Básicas
                </h3>

                <div class="mb-3">
                    <label for="name" class="form-label" style="color: rgba(255,255,255,0.9); margin-bottom: 8px;">
                        Nome do Objetivo *
                    </label>
                    <input type="text" 
                           name="name" 
                           id="name" 
                           value="{{ old('name') }}"
                           class="form-control"
                           placeholder="Ex: Pizza Financeira Ideale"
                           required
                           style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; border-radius: 8px; padding: 12px;">
                    @error('name')
                        <p class="text-danger text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label" style="color: rgba(255,255,255,0.9); margin-bottom: 8px;">
                        Descrição
                    </label>
                    <textarea name="description" 
                              id="description" 
                              rows="2"
                              class="form-control"
                              placeholder="Descreva seu objetivo..."
                              style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; border-radius: 8px; padding: 12px;">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-danger text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="total_income" class="form-label" style="color: rgba(255,255,255,0.9); margin-bottom: 8px;">
                            Total de Entradas (Meta) *
                        </label>
                        <input type="number" 
                               name="total_income" 
                               id="total_income" 
                               step="0.01"
                               min="0.01"
                               value="{{ old('total_income') }}"
                               class="form-control"
                               placeholder="0.00"
                               required
                               style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; border-radius: 8px; padding: 12px;">
                        @error('total_income')
                            <p class="text-danger text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="start_date" class="form-label" style="color: rgba(255,255,255,0.9); margin-bottom: 8px;">
                            Data de Início *
                        </label>
                        <input type="date" 
                               name="start_date" 
                               id="start_date" 
                               value="{{ old('start_date', date('Y-m-d')) }}"
                               class="form-control"
                               required
                               style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; border-radius: 8px; padding: 12px;">
                        @error('start_date')
                            <p class="text-danger text-sm">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="end_date" class="form-label" style="color: rgba(255,255,255,0.9); margin-bottom: 8px;">
                        Data de Término (Opcional - deixe em branco para contínuo)
                    </label>
                    <input type="date" 
                           name="end_date" 
                           id="end_date" 
                           value="{{ old('end_date') }}"
                           class="form-control"
                           style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; border-radius: 8px; padding: 12px;">
                    @error('end_date')
                        <p class="text-danger text-sm">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Distribuição da Pizza -->
            <div class="chart-section mb-4">
                <h3 class="section-title">
                    <i class="bi bi-pie-chart"></i>
                    Distribuição da Pizza (%)
                </h3>
                
                <div class="alert alert-info" style="background: rgba(59, 130, 246, 0.2); border: 1px solid rgba(59, 130, 246, 0.3); color: rgba(255,255,255,0.9); margin-bottom: 20px; border-radius: 10px;">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>A soma deve ser exatamente 100%</strong>
                </div>

                <div class="row" id="distribution-container">
                    <!-- Despesas Fixas -->
                    <div class="col-md-6 mb-3">
                        <label for="distribution_fixed_expenses" class="form-label" style="color: rgba(255,255,255,0.9); margin-bottom: 8px;">
                            <i class="bi bi-house-door text-primary"></i> Despesas Fixas (%)
                        </label>
                        <input type="number" 
                               name="distribution[fixed_expenses]" 
                               id="distribution_fixed_expenses" 
                               step="0.01"
                               min="0"
                               max="100"
                               value="{{ old('distribution.fixed_expenses', 40) }}"
                               class="form-control distribution-input"
                               required
                               style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; border-radius: 8px; padding: 12px;">
                    </div>

                    <!-- Recursos Profissionais -->
                    <div class="col-md-6 mb-3">
                        <label for="distribution_professional_resources" class="form-label" style="color: rgba(255,255,255,0.9); margin-bottom: 8px;">
                            <i class="bi bi-briefcase text-success"></i> Recursos Profissionais (%)
                        </label>
                        <input type="number" 
                               name="distribution[professional_resources]" 
                               id="distribution_professional_resources" 
                               step="0.01"
                               min="0"
                               max="100"
                               value="{{ old('distribution.professional_resources', 10) }}"
                               class="form-control distribution-input"
                               required
                               style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; border-radius: 8px; padding: 12px;">
                    </div>

                    <!-- Reservas de Emergência -->
                    <div class="col-md-6 mb-3">
                        <label for="distribution_emergency_reserves" class="form-label" style="color: rgba(255,255,255,0.9); margin-bottom: 8px;">
                            <i class="bi bi-shield-check text-warning"></i> Reservas de Emergência (%)
                        </label>
                        <input type="number" 
                               name="distribution[emergency_reserves]" 
                               id="distribution_emergency_reserves" 
                               step="0.01"
                               min="0"
                               max="100"
                               value="{{ old('distribution.emergency_reserves', 30) }}"
                               class="form-control distribution-input"
                               required
                               style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; border-radius: 8px; padding: 12px;">
                    </div>

                    <!-- Lazer -->
                    <div class="col-md-6 mb-3">
                        <label for="distribution_leisure" class="form-label" style="color: rgba(255,255,255,0.9); margin-bottom: 8px;">
                            <i class="bi bi-emoji-laughing text-danger"></i> Lazer (%)
                        </label>
                        <input type="number" 
                               name="distribution[leisure]" 
                               id="distribution_leisure" 
                               step="0.01"
                               min="0"
                               max="100"
                               value="{{ old('distribution.leisure', 10) }}"
                               class="form-control distribution-input"
                               required
                               style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; border-radius: 8px; padding: 12px;">
                    </div>

                    <!-- Parcelas de Dívidas -->
                    <div class="col-md-6 mb-3">
                        <label for="distribution_debt_installments" class="form-label" style="color: rgba(255,255,255,0.9); margin-bottom: 8px;">
                            <i class="bi bi-credit-card text-info"></i> Parcelas de Dívidas (%)
                        </label>
                        <input type="number" 
                               name="distribution[debt_installments]" 
                               id="distribution_debt_installments" 
                               step="0.01"
                               min="0"
                               max="100"
                               value="{{ old('distribution.debt_installments', 10) }}"
                               class="form-control distribution-input"
                               required
                               style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; border-radius: 8px; padding: 12px;">
                    </div>
                </div>

                <!-- Total da Distribuição -->
                <div class="alert mt-3" id="total-alert" style="background: rgba(16, 185, 129, 0.2); border: 1px solid rgba(16, 185, 129, 0.3); border-radius: 10px;">
                    <div class="d-flex justify-content-between align-items-center">
                        <span style="color: rgba(255,255,255,0.9); font-weight: 600;">
                            <i class="bi bi-calculator me-2"></i>Total:
                        </span>
                        <span id="total-percentage" style="color: #10b981; font-weight: 700; font-size: 1.2rem;">0%</span>
                    </div>
                </div>
                @error('distribution')
                    <p class="text-danger text-sm">{{ $message }}</p>
                @enderror
            </div>

            <!-- Mapeamento de Categorias (Opcional) -->
            @if($categories->count() > 0)
            <div class="chart-section mb-4">
                <h3 class="section-title">
                    <i class="bi bi-diagram-3"></i>
                    Mapear Categorias (Opcional)
                </h3>
                
                <div class="alert alert-info" style="background: rgba(59, 130, 246, 0.2); border: 1px solid rgba(59, 130, 246, 0.3); color: rgba(255,255,255,0.9); margin-bottom: 20px; border-radius: 10px;">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Relacione as categorias do sistema com cada fatia da pizza</strong><br>
                    <small>Isso permitirá calcular automaticamente quanto você está gastando em cada categoria.</small>
                </div>

                <div class="row">
                    <!-- Mapear Despesas Fixas -->
                    <div class="col-md-6 mb-3">
                        <label for="mapping_fixed_expenses" class="form-label" style="color: rgba(255,255,255,0.9); margin-bottom: 8px;">
                            <i class="bi bi-house-door text-primary"></i> Despesas Fixas
                        </label>
                        <select name="category_mapping[fixed_expenses][]" 
                                id="mapping_fixed_expenses" 
                                class="form-control category-select" 
                                multiple
                                style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; border-radius: 8px; padding: 12px;">
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" style="background: #374151; color: white;">
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Mapear Recursos Profissionais -->
                    <div class="col-md-6 mb-3">
                        <label for="mapping_professional_resources" class="form-label" style="color: rgba(255,255,255,0.9); margin-bottom: 8px;">
                            <i class="bi bi-briefcase text-success"></i> Recursos Profissionais
                        </label>
                        <select name="category_mapping[professional_resources][]" 
                                id="mapping_professional_resources" 
                                class="form-control category-select" 
                                multiple
                                style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; border-radius: 8px; padding: 12px;">
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" style="background: #374151; color: white;">
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Mapear Reservas de Emergência -->
                    <div class="col-md-6 mb-3">
                        <label for="mapping_emergency_reserves" class="form-label" style="color: rgba(255,255,255,0.9); margin-bottom: 8px;">
                            <i class="bi bi-shield-check text-warning"></i> Reservas de Emergência
                        </label>
                        <select name="category_mapping[emergency_reserves][]" 
                                id="mapping_emergency_reserves" 
                                class="form-control category-select" 
                                multiple
                                style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; border-radius: 8px; padding: 12px;">
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" style="background: #374151; color: white;">
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Mapear Lazer -->
                    <div class="col-md-6 mb-3">
                        <label for="mapping_leisure" class="form-label" style="color: rgba(255,255,255,0.9); margin-bottom: 8px;">
                            <i class="bi bi-emoji-laughing text-danger"></i> Lazer
                        </label>
                        <select name="category_mapping[leisure][]" 
                                id="mapping_leisure" 
                                class="form-control category-select" 
                                multiple
                                style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; border-radius: 8px; padding: 12px;">
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" style="background: #374151; color: white;">
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Mapear Parcelas de Dívidas -->
                    <div class="col-md-6 mb-3">
                        <label for="mapping_debt_installments" class="form-label" style="color: rgba(255,255,255,0.9); margin-bottom: 8px;">
                            <i class="bi bi-credit-card text-info"></i> Parcelas de Dívidas
                        </label>
                        <select name="category_mapping[debt_installments][]" 
                                id="mapping_debt_installments" 
                                class="form-control category-select" 
                                multiple
                                style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; border-radius: 8px; padding: 12px;">
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" style="background: #374151; color: white;">
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            @else
            <div class="alert alert-warning" style="background: rgba(245, 158, 11, 0.2); border: 1px solid rgba(245, 158, 11, 0.3); color: rgba(255,255,255,0.9); margin-bottom: 20px; border-radius: 10px;">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <strong>Nenhuma categoria de despesa cadastrada ainda.</strong><br>
                <small>Crie categorias no sistema antes de mapear os objetivos.</small>
            </div>
            @endif

            <!-- Botões de Ação -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                <a href="{{ route('goals.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle me-2"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-circle me-2"></i> Criar Objetivo
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Calcular total dinâmico
    document.addEventListener('DOMContentLoaded', function() {
        const inputs = document.querySelectorAll('.distribution-input');
        const totalSpan = document.getElementById('total-percentage');
        const alertDiv = document.getElementById('total-alert');
        
        function updateTotal() {
            let total = 0;
            inputs.forEach(input => {
                const value = parseFloat(input.value) || 0;
                total += value;
            });
            
            totalSpan.textContent = total.toFixed(2) + '%';
            
            // Mudar cor baseado no total
            if (total === 100) {
                alertDiv.style.background = 'rgba(16, 185, 129, 0.2)';
                alertDiv.style.borderColor = 'rgba(16, 185, 129, 0.3)';
                totalSpan.style.color = '#10b981';
            } else if (total > 100) {
                alertDiv.style.background = 'rgba(239, 68, 68, 0.2)';
                alertDiv.style.borderColor = 'rgba(239, 68, 68, 0.3)';
                totalSpan.style.color = '#ef4444';
            } else {
                alertDiv.style.background = 'rgba(245, 158, 11, 0.2)';
                alertDiv.style.borderColor = 'rgba(245, 158, 11, 0.3)';
                totalSpan.style.color = '#f59e0b';
            }
        }
        
        inputs.forEach(input => {
            input.addEventListener('input', updateTotal);
        });
        
        // Calcular inicial
        updateTotal();
    });
</script>
@endpush
@endsection

