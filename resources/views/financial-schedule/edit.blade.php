@extends('layouts.app')

@section('title', 'Editar Agendamento')

@section('content')
<div class="premium-content">
    <div class="max-w-4xl mx-auto px-4">
        <!-- Header Premium -->
        <div class="premium-header mb-8">
            <div class="header-content">
                <div class="header-left">
                    <a href="{{ route('financial-schedule.index') }}" class="back-btn">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <div class="header-title">
                        <h1>Editar Agendamento</h1>
                        <p class="header-subtitle">Edite os dados do agendamento</p>
                    </div>
                </div>
                <div class="header-actions">
                    <a href="{{ route('financial-schedule.index') }}" class="action-btn" title="Lista de Agendamentos">
                        <i class="bi bi-list-ul"></i>
                    </a>
                </div>
            </div>
        </div>

        <form action="{{ route('financial-schedule.update', $schedule->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Tipo e Valor -->
            <div class="chart-section mb-4">
                <h3 class="section-title">
                    <i class="bi bi-info-circle"></i>
                    Informações Básicas
                </h3>

                <div class="row">
                    <!-- Tipo -->
                    <div class="col-md-6 mb-3">
                        <label for="type" class="form-label" style="color: rgba(255,255,255,0.9); margin-bottom: 8px;">
                            Tipo *
                        </label>
                        <select name="type" id="type" class="form-control" required style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; border-radius: 8px; padding: 12px;">
                            <option value="" style="background: #374151; color: white;">Selecione...</option>
                            <option value="income" style="background: #374151; color: white;" {{ old('type', $schedule->type) === 'income' ? 'selected' : '' }}>Receita</option>
                            <option value="expense" style="background: #374151; color: white;" {{ old('type', $schedule->type) === 'expense' ? 'selected' : '' }}>Despesa</option>
                        </select>
                        @error('type')
                            <p class="text-danger text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Valor -->
                    <div class="col-md-6 mb-3">
                        <label for="amount" class="form-label" style="color: rgba(255,255,255,0.9); margin-bottom: 8px;">
                            Valor *
                        </label>
                        <input type="number" 
                               name="amount" 
                               id="amount" 
                               step="0.01"
                               min="0.01"
                               value="{{ old('amount', $schedule->amount) }}"
                               class="form-control"
                               placeholder="0.00"
                               required
                               style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; border-radius: 8px; padding: 12px;">
                        @error('amount')
                            <p class="text-danger text-sm">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Descrição -->
            <div class="chart-section mb-4">
                <h3 class="section-title">
                    <i class="bi bi-file-text"></i>
                    Detalhes
                </h3>

                <div class="mb-3">
                    <label for="title" class="form-label" style="color: rgba(255,255,255,0.9); margin-bottom: 8px;">
                        Título *
                    </label>
                    <input type="text" 
                           name="title" 
                           id="title" 
                           value="{{ old('title', $schedule->title) }}"
                           class="form-control"
                           placeholder="Ex: Pagamento Cliente X"
                           required
                           style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; border-radius: 8px; padding: 12px;">
                    @error('title')
                        <p class="text-danger text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label" style="color: rgba(255,255,255,0.9); margin-bottom: 8px;">
                        Descrição
                    </label>
                    <textarea name="description" 
                              id="description" 
                              rows="3"
                              class="form-control"
                              placeholder="Observações adicionais..."
                              style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; border-radius: 8px; padding: 12px;">{{ old('description', $schedule->description) }}</textarea>
                    @error('description')
                        <p class="text-danger text-sm">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Tipo de Agendamento -->
            <div class="chart-section mb-4">
                <h3 class="section-title">
                    <i class="bi bi-calendar-check"></i>
                    Tipo de Agendamento
                </h3>

                <!-- Frequência (apenas para recorrente) -->
                <div class="mb-3" id="recurring_frequency_wrapper" style="{{ $schedule->is_recurring ? '' : 'display: none;' }}">
                    <label for="recurring_frequency" class="form-label" style="color: rgba(255,255,255,0.9); margin-bottom: 8px;">
                        Frequência de Recorrência
                    </label>
                    <select name="recurring_frequency" id="recurring_frequency" class="form-control" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; border-radius: 8px; padding: 12px;">
                        <option value="monthly" style="background: #374151; color: white;" {{ (old('recurring_frequency', $schedule->recurring_config['frequency'] ?? 'monthly')) === 'monthly' ? 'selected' : '' }}>Mensal</option>
                        <option value="weekly" style="background: #374151; color: white;" {{ (old('recurring_frequency', $schedule->recurring_config['frequency'] ?? '')) === 'weekly' ? 'selected' : '' }}>Semanal</option>
                        <option value="biweekly" style="background: #374151; color: white;" {{ (old('recurring_frequency', $schedule->recurring_config['frequency'] ?? '')) === 'biweekly' ? 'selected' : '' }}>Quinzenal</option>
                        <option value="daily" style="background: #374151; color: white;" {{ (old('recurring_frequency', $schedule->recurring_config['frequency'] ?? '')) === 'daily' ? 'selected' : '' }}>Diário</option>
                        <option value="quarterly" style="background: #374151; color: white;" {{ (old('recurring_frequency', $schedule->recurring_config['frequency'] ?? '')) === 'quarterly' ? 'selected' : '' }}>Trimestral</option>
                        <option value="semiannual" style="background: #374151; color: white;" {{ (old('recurring_frequency', $schedule->recurring_config['frequency'] ?? '')) === 'semiannual' ? 'selected' : '' }}>Semestral</option>
                        <option value="yearly" style="background: #374151; color: white;" {{ (old('recurring_frequency', $schedule->recurring_config['frequency'] ?? '')) === 'yearly' ? 'selected' : '' }}>Anual</option>
                    </select>
                </div>

                <div class="row">
                    <!-- Data Fixa (Pagamento Único) -->
                    <div class="col-md-6 mb-3">
                        <label for="scheduled_date" class="form-label" style="color: rgba(255,255,255,0.9); margin-bottom: 8px;">
                            Data Fixa (Pagamento Único)
                        </label>
                        <input type="date" 
                               name="scheduled_date" 
                               id="scheduled_date" 
                               value="{{ old('scheduled_date', $schedule->is_recurring ? '' : $schedule->scheduled_date->format('Y-m-d')) }}"
                               class="form-control"
                               style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; border-radius: 8px; padding: 12px;">
                        @error('scheduled_date')
                            <p class="text-danger text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Data Variável (Pagamento Recorrente) -->
                    <div class="col-md-6 mb-3">
                        <label for="scheduled_day" class="form-label" style="color: rgba(255,255,255,0.9); margin-bottom: 8px;">
                            Data Variável (Dia do Mês - Recorrente)
                        </label>
                        <input type="number" 
                               name="scheduled_day" 
                               id="scheduled_day" 
                               min="1" 
                               max="31"
                               value="{{ old('scheduled_day', $schedule->is_recurring ? $schedule->scheduled_date->day : '') }}"
                               placeholder="Dia do mês (1-31)"
                               class="form-control"
                               style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; border-radius: 8px; padding: 12px;">
                        @error('scheduled_day')
                            <p class="text-danger text-sm">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Categoria -->
                <div class="mb-3">
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                        <label for="category_id" class="form-label" style="color: rgba(255,255,255,0.9); margin-bottom: 0;">
                            Categoria
                        </label>
                        <button type="button" class="btn btn-sm btn-success" onclick="openCategoryModal()" style="padding: 4px 12px; font-size: 12px; display: flex; align-items: center; gap: 5px;">
                            <i class="bi bi-plus-circle"></i> Nova
                        </button>
                    </div>
                    <select name="category_id" id="category_id" class="form-control" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; border-radius: 8px; padding: 12px;">
                        <option value="" style="background: #374151; color: white;">Sem categoria</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" style="background: #374151; color: white;" {{ old('category_id', $schedule->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="text-danger text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Departamento do Objetivo (apenas para despesas) -->
                <div class="mb-3" id="goal_category_wrapper" style="{{ old('type', $schedule->type) === 'expense' ? '' : 'display: none;' }}">
                    <label for="goal_category" class="form-label" style="color: rgba(255,255,255,0.9); margin-bottom: 8px;">
                        Para qual departamento é essa saída? *
                    </label>
                    <select name="goal_category" id="goal_category" class="form-control" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; border-radius: 8px; padding: 12px;">
                        <option value="" style="background: #374151; color: white;">Selecione...</option>
                        <option value="fixed_expenses" style="background: #374151; color: white;" {{ old('goal_category', $schedule->goal_category) === 'fixed_expenses' ? 'selected' : '' }}>🏠 Despesa Fixa</option>
                        <option value="professional_resources" style="background: #374151; color: white;" {{ old('goal_category', $schedule->goal_category) === 'professional_resources' ? 'selected' : '' }}>💼 Recursos Profissionais</option>
                        <option value="emergency_reserves" style="background: #374151; color: white;" {{ old('goal_category', $schedule->goal_category) === 'emergency_reserves' ? 'selected' : '' }}>🛡️ Reserva de Emergência</option>
                        <option value="leisure" style="background: #374151; color: white;" {{ old('goal_category', $schedule->goal_category) === 'leisure' ? 'selected' : '' }}>😊 Lazer</option>
                        <option value="debt_installments" style="background: #374151; color: white;" {{ old('goal_category', $schedule->goal_category) === 'debt_installments' ? 'selected' : '' }}>💳 Parcelas de Dívidas</option>
                    </select>
                    <small class="text-white-50">Essa informação ajuda a monitorar seus objetivos financeiros</small>
                    @error('goal_category')
                        <p class="text-danger text-sm">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Imagem Representativa -->
            <div class="chart-section mb-4">
                <h3 class="section-title">
                    <i class="bi bi-image"></i>
                    Imagem Representativa
                </h3>

                @if($schedule->image_path)
                <div class="mb-3">
                    <label class="form-label" style="color: rgba(255,255,255,0.9); margin-bottom: 8px;">
                        Imagem Atual
                    </label>
                    <div>
                        <img src="{{ asset('storage/' . $schedule->image_path) }}" 
                             alt="{{ $schedule->title }}" 
                             style="max-width: 200px; border-radius: 12px; margin-top: 10px;">
                    </div>
                </div>
                @endif

                <div class="mb-3">
                    <label for="image" class="form-label" style="color: rgba(255,255,255,0.9); margin-bottom: 8px;">
                        {{ $schedule->image_path ? 'Substituir Imagem' : 'Upload de Imagem' }}
                    </label>
                    <input type="file" 
                           name="image" 
                           id="image" 
                           accept="image/*"
                           class="form-control"
                           style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; border-radius: 8px; padding: 12px;">
                    @error('image')
                        <p class="text-danger text-sm">{{ $message }}</p>
                    @enderror
                    <small class="text-white-50">Tamanho máximo: 2MB. Formatos: JPG, PNG, GIF</small>
                </div>

                <div id="imagePreview" class="mt-3" style="display: none;">
                    <img id="preview" src="" alt="Preview" style="max-width: 200px; border-radius: 12px; margin-top: 10px;">
                </div>
            </div>

            <!-- Botões de Ação -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                <a href="{{ route('financial-schedule.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle me-2"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-circle me-2"></i> Atualizar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal de Nova Categoria -->
<div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="background: #1f2937; border: 1px solid rgba(255,255,255,0.2);">
            <div class="modal-header" style="border-bottom: 1px solid rgba(255,255,255,0.2);">
                <h5 class="modal-title" id="categoryModalLabel" style="color: white;">
                    <i class="bi bi-plus-circle me-2"></i>Nova Categoria
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="newCategoryForm">
                    @csrf
                    <div class="mb-3">
                        <label for="category_name" class="form-label" style="color: rgba(255,255,255,0.9);">Nome da Categoria *</label>
                        <input type="text" class="form-control" id="category_name" name="name" required style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white;">
                    </div>
                    <div class="mb-3">
                        <label for="category_type" class="form-label" style="color: rgba(255,255,255,0.9);">Tipo *</label>
                        <select class="form-control" id="category_type" name="type" required style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white;">
                            <option value="income" style="background: #374151; color: white;">Receita</option>
                            <option value="expense" style="background: #374151; color: white;">Despesa</option>
                        </select>
                    </div>
                    <div id="categoryMessage" class="alert" style="display: none;"></div>
                </form>
            </div>
            <div class="modal-footer" style="border-top: 1px solid rgba(255,255,255,0.2);">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" onclick="createCategory()">
                    <i class="bi bi-check-circle me-2"></i>Criar
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Preview de imagem
    document.getElementById('image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('preview').src = e.target.result;
                document.getElementById('imagePreview').style.display = 'block';
            }
            reader.readAsDataURL(file);
        } else {
            document.getElementById('imagePreview').style.display = 'none';
        }
    });

    // Controla campos de data: ao preencher um, esconde o outro
    document.addEventListener('DOMContentLoaded', function() {
        const dayInput = document.getElementById('scheduled_day');
        const dateInput = document.getElementById('scheduled_date');
        const wrapper = document.getElementById('recurring_frequency_wrapper');
        
        // Controle do campo goal_category baseado no tipo
        const typeSelect = document.getElementById('type');
        const goalCategoryWrapper = document.getElementById('goal_category_wrapper');
        const goalCategory = document.getElementById('goal_category');
        
        typeSelect.addEventListener('change', function() {
            if (this.value === 'expense') {
                goalCategoryWrapper.style.display = 'block';
                goalCategory.required = true;
            } else {
                goalCategoryWrapper.style.display = 'none';
                goalCategory.required = false;
                goalCategory.value = '';
            }
        });
        
        // Trigger inicial
        typeSelect.dispatchEvent(new Event('change'));
        
        // Função para atualizar visibilidade
        function updateVisibility() {
            const hasDate = dateInput.value !== '';
            const hasDay = dayInput.value !== '';
            
            if (hasDate) {
                // Data fixa (única) - esconder dia e frequência
                dayInput.style.display = 'none';
                dayInput.disabled = true; // Desabilitar para não enviar
                dateInput.disabled = false;
                wrapper.style.display = 'none';
            } else if (hasDay) {
                // Dia recorrente - esconder data fixa e mostrar frequência
                dateInput.style.display = 'none';
                dateInput.disabled = true; // Desabilitar para não enviar
                dayInput.disabled = false;
                wrapper.style.display = 'block';
            } else {
                // Ambos vazios - mostrar ambos campos, esconder frequência
                dayInput.style.display = 'block';
                dateInput.style.display = 'block';
                dayInput.disabled = false;
                dateInput.disabled = false;
                wrapper.style.display = 'none';
            }
        }
        
        // Quando preencher data fixa (única)
        dateInput.addEventListener('change', function() {
            if (this.value) {
                dayInput.value = '';
            }
            updateVisibility();
        });
        
        // Quando preencher dia recorrente
        dayInput.addEventListener('input', function() {
            if (this.value) {
                dateInput.value = '';
            }
            updateVisibility();
        });
        
        // Inicializar visibilidade baseado nos valores existentes
        @if($schedule->is_recurring)
            dateInput.value = '';
        @else
            dayInput.value = '';
        @endif
        
        updateVisibility();
    });

    // Função para abrir modal de nova categoria
    function openCategoryModal() {
        const modal = new bootstrap.Modal(document.getElementById('categoryModal'));
        modal.show();
        
        // Definir o tipo baseado no tipo selecionado no formulário
        const selectedType = document.getElementById('type').value;
        if (selectedType) {
            document.getElementById('category_type').value = selectedType;
        }
    }

    // Função para criar categoria
    function createCategory() {
        const name = document.getElementById('category_name').value;
        const type = document.getElementById('category_type').value;
        const messageDiv = document.getElementById('categoryMessage');
        
        if (!name || !type) {
            messageDiv.className = 'alert alert-danger';
            messageDiv.textContent = 'Preencha todos os campos!';
            messageDiv.style.display = 'block';
            return;
        }
        
        // Desabilitar botão
        const btn = event.target;
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Criando...';
        
        fetch('{{ route("categories.quick-create") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ name, type })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Adicionar ao select
                const select = document.getElementById('category_id');
                const option = document.createElement('option');
                option.value = data.category.id;
                option.textContent = data.category.name;
                option.setAttribute('style', 'background: #374151; color: white;');
                select.appendChild(option);
                
                // Selecionar a nova categoria
                select.value = data.category.id;
                
                // Fechar modal e limpar formulário
                bootstrap.Modal.getInstance(document.getElementById('categoryModal')).hide();
                document.getElementById('category_name').value = '';
                messageDiv.style.display = 'none';
                
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Criar';
            } else {
                messageDiv.className = 'alert alert-danger';
                messageDiv.textContent = data.message || 'Erro ao criar categoria';
                messageDiv.style.display = 'block';
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Criar';
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            messageDiv.className = 'alert alert-danger';
            messageDiv.textContent = 'Erro ao criar categoria';
            messageDiv.style.display = 'block';
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Criar';
        });
    }
</script>
@endpush
@endsection

