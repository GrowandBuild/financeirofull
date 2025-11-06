@extends('layouts.app')

@section('content')
<div class="premium-content">
    <div class="max-w-4xl mx-auto px-4">
        <!-- Header Premium -->
        <div class="premium-header mb-8">
            <div class="header-content">
                <div class="header-left">
                    <a href="{{ route('admin.product-categories.index') }}" class="back-btn">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <div class="header-title">
                        <h1>Nova Categoria</h1>
                        <p class="header-subtitle">Crie uma nova categoria de produtos</p>
                    </div>
                </div>
                <div class="header-actions">
                    <a href="{{ route('admin.product-categories.index') }}" class="action-btn" title="Lista de Categorias">
                        <i class="bi bi-list-ul"></i>
                    </a>
                    <a href="{{ route('dashboard') }}" class="action-btn" title="Painel Principal">
                        <i class="bi bi-speedometer2"></i>
                    </a>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.product-categories.store') }}" method="POST" class="space-y-8">
            @csrf

            <!-- Informações Básicas -->
            <div class="chart-section">
                <h3 class="section-title">
                    <i class="bi bi-info-circle"></i>
                    Informações da Categoria
                </h3>

                <div class="grid grid-cols-1 gap-6">
                    <!-- Nome da Categoria -->
                    <div class="space-y-2">
                        <label for="name" class="block text-sm font-medium text-white/90">
                            Nome da Categoria *
                        </label>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               value="{{ old('name') }}"
                               class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-3 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300 @error('name') border-red-500/50 focus:ring-red-500 @enderror"
                               placeholder="Ex: Laticínios, Carnes, Verduras, Proteínas, etc"
                               required>
                        <small class="text-white/60 text-xs flex items-center gap-1">
                            <i class="bi bi-info-circle"></i>
                            O sistema normaliza automaticamente variações (ex: "vício" = "vícios")
                        </small>
                        @error('name')
                            <p class="text-red-300 text-sm flex items-center gap-1">
                                <i class="bi bi-exclamation-circle"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row justify-end gap-3">
                <a href="{{ route('admin.product-categories.index') }}" 
                   class="premium-btn outline">
                    <i class="bi bi-arrow-left"></i>
                    Cancelar
                </a>
                <button type="submit" 
                        class="premium-btn primary">
                    <i class="bi bi-check-circle"></i>
                    Criar Categoria
                </button>
            </div>
        </form>
    </div>
</div>

<style>
/* Reusar estilos de produtos */
.chart-section {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.08) 0%, rgba(255, 255, 255, 0.04) 100%);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 16px;
    padding: 2rem;
    backdrop-filter: blur(20px);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
}

.section-title {
    color: white;
    font-size: 1.25rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.section-title i {
    color: #10b981;
    font-size: 1.5rem;
}
</style>
@endsection

