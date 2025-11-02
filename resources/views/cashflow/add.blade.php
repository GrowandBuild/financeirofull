@extends('layouts.cashflow')

@section('title', 'Adicionar Transação')

@section('content')
<div class="container-fluid p-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card-cashflow p-4">
                <h1 class="h3 mb-1 text-white">
                    <i class="bi bi-plus-circle me-2"></i>
                    Adicionar Transação
                </h1>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card-cashflow p-4">
                <form action="{{ route('cashflow.store') }}" method="POST">
                    @csrf
                    
                    <!-- Tipo -->
                    <div class="mb-3">
                        <label for="type" class="form-label text-white">Tipo *</label>
                        <select name="type" id="type" class="form-control" required style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white;">
                            <option value="">Selecione...</option>
                            <option value="income" {{ old('type') === 'income' ? 'selected' : '' }}>Receita</option>
                            <option value="expense" {{ old('type') === 'expense' ? 'selected' : '' }}>Despesa</option>
                        </select>
                        @error('type')
                            <p class="text-danger text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Título -->
                    <div class="mb-3">
                        <label for="title" class="form-label text-white">Título *</label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" class="form-control" required style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white;">
                        @error('title')
                            <p class="text-danger text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Valor -->
                    <div class="mb-3">
                        <label for="amount" class="form-label text-white">Valor *</label>
                        <input type="number" name="amount" id="amount" step="0.01" min="0.01" value="{{ old('amount') }}" class="form-control" required style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white;">
                        @error('amount')
                            <p class="text-danger text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Descrição -->
                    <div class="mb-3">
                        <label for="description" class="form-label text-white">Descrição</label>
                        <textarea name="description" id="description" rows="3" class="form-control" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white;">{{ old('description') }}</textarea>
                    </div>

                    <div class="row">
                        <!-- Categoria -->
                        <div class="col-md-6 mb-3">
                            <label for="category_id" class="form-label text-white">Categoria</label>
                            <select name="category_id" id="category_id" class="form-control" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white;">
                                <option value="">Sem categoria</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Data -->
                        <div class="col-md-6 mb-3">
                            <label for="transaction_date" class="form-label text-white">Data *</label>
                            <input type="date" name="transaction_date" id="transaction_date" value="{{ old('transaction_date', date('Y-m-d')) }}" class="form-control" required style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white;">
                            @error('transaction_date')
                                <p class="text-danger text-sm">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Departamentos do Objetivo (apenas para despesas) -->
                    <div class="mb-3" id="goal_category_wrapper" style="display: none;">
                        <label for="goal_category" class="form-label text-white">Para qual departamento é essa saída? *</label>
                        <select name="goal_category" id="goal_category" class="form-control" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white;">
                            <option value="">Selecione...</option>
                            <option value="fixed_expenses">🏠 Despesa Fixa</option>
                            <option value="professional_resources">💼 Recursos Profissionais</option>
                            <option value="emergency_reserves">🛡️ Reserva de Emergência</option>
                            <option value="leisure">😊 Lazer</option>
                            <option value="debt_installments">💳 Parcelas de Dívidas</option>
                        </select>
                        <small class="text-white-50">Essa informação ajuda a monitorar seus objetivos financeiros</small>
                        @error('goal_category')
                            <p class="text-danger text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="row">
                        <!-- Método de Pagamento -->
                        <div class="col-md-6 mb-3">
                            <label for="payment_method" class="form-label text-white">Método de Pagamento</label>
                            <select name="payment_method" id="payment_method" class="form-control" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white;">
                                <option value="">Não informado</option>
                                <option value="cash" {{ old('payment_method') === 'cash' ? 'selected' : '' }}>Dinheiro</option>
                                <option value="card" {{ old('payment_method') === 'card' ? 'selected' : '' }}>Cartão</option>
                                <option value="pix" {{ old('payment_method') === 'pix' ? 'selected' : '' }}>PIX</option>
                                <option value="transfer" {{ old('payment_method') === 'transfer' ? 'selected' : '' }}>Transferência</option>
                            </select>
                        </div>

                        <!-- Referência -->
                        <div class="col-md-6 mb-3">
                            <label for="reference" class="form-label text-white">Referência</label>
                            <input type="text" name="reference" id="reference" value="{{ old('reference') }}" class="form-control" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white;">
                        </div>
                    </div>

                    <!-- Botões -->
                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('cashflow.dashboard') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle me-2"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle me-2"></i> Salvar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Mostrar campo de goal_category apenas para despesas
    document.getElementById('type').addEventListener('change', function() {
        const goalCategoryWrapper = document.getElementById('goal_category_wrapper');
        const goalCategory = document.getElementById('goal_category');
        
        if (this.value === 'expense') {
            goalCategoryWrapper.style.display = 'block';
            goalCategory.required = true;
        } else {
            goalCategoryWrapper.style.display = 'none';
            goalCategory.required = false;
            goalCategory.value = '';
        }
    });
    
    // Trigger inicial se já tiver valor
    document.getElementById('type').dispatchEvent(new Event('change'));
</script>
@endpush
@endsection

