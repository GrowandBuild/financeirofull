@extends('layouts.app')

@section('content')
<div class="premium-content">
    <div class="max-w-md mx-auto">
        <!-- Header Premium -->
        <div class="premium-header text-center mb-8">
            <div class="header-content">
                <div class="header-left">
                    <a href="{{ route('products.index') }}" class="back-btn">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <div class="header-title">
                        <h1>Cadastrar</h1>
                        <p class="header-subtitle">Crie sua conta para gerenciar produtos</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Register Card Premium -->
        <div class="product-hero">
            @if ($errors->any())
                <div class="bg-red-500/20 border border-red-500/30 text-red-300 px-4 py-3 rounded-lg mb-6 backdrop-blur-sm">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="bi bi-exclamation-triangle"></i>
                        <span class="font-medium">Erro de validação</span>
                    </div>
                    <ul class="text-sm space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-6">
                @csrf

                <!-- Name Field -->
                <div class="space-y-2">
                    <label for="name" class="block text-sm font-medium text-white/90">
                        <i class="bi bi-person mr-2"></i>Nome Completo
                    </label>
                    <div class="relative">
                        <input type="text" 
                               name="name" 
                               id="name" 
                               value="{{ old('name') }}"
                               class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-3 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300 @error('name') border-red-500/50 focus:ring-red-500 @enderror"
                               placeholder="Seu nome completo"
                               required 
                               autofocus>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="bi bi-person text-white/50"></i>
                        </div>
                    </div>
                    @error('name')
                        <p class="text-red-300 text-sm flex items-center gap-1">
                            <i class="bi bi-exclamation-circle"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Email Field -->
                <div class="space-y-2">
                    <label for="email" class="block text-sm font-medium text-white/90">
                        <i class="bi bi-envelope mr-2"></i>Email
                    </label>
                    <div class="relative">
                        <input type="email" 
                               name="email" 
                               id="email" 
                               value="{{ old('email') }}"
                               class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-3 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300 @error('email') border-red-500/50 focus:ring-red-500 @enderror"
                               placeholder="seu@email.com"
                               required>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="bi bi-envelope text-white/50"></i>
                        </div>
                    </div>
                    @error('email')
                        <p class="text-red-300 text-sm flex items-center gap-1">
                            <i class="bi bi-exclamation-circle"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Password Field -->
                <div class="space-y-2">
                    <label for="password" class="block text-sm font-medium text-white/90">
                        <i class="bi bi-lock mr-2"></i>Senha
                    </label>
                    <div class="relative">
                        <input type="password" 
                               name="password" 
                               id="password"
                               class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-3 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300 @error('password') border-red-500/50 focus:ring-red-500 @enderror"
                               placeholder="••••••••"
                               required>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="bi bi-lock text-white/50"></i>
                        </div>
                    </div>
                    @error('password')
                        <p class="text-red-300 text-sm flex items-center gap-1">
                            <i class="bi bi-exclamation-circle"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Confirm Password Field -->
                <div class="space-y-2">
                    <label for="password_confirmation" class="block text-sm font-medium text-white/90">
                        <i class="bi bi-lock-fill mr-2"></i>Confirmar Senha
                    </label>
                    <div class="relative">
                        <input type="password" 
                               name="password_confirmation" 
                               id="password_confirmation"
                               class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-3 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300"
                               placeholder="••••••••"
                               required>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="bi bi-lock-fill text-white/50"></i>
                        </div>
                    </div>
                </div>

                <!-- Register Button -->
                <button type="submit" 
                        class="w-full premium-btn primary group">
                    <i class="bi bi-person-plus group-hover:scale-110 transition-transform duration-200"></i>
                    Criar Conta
                </button>

                <!-- Login Link -->
                <div class="text-center pt-4 border-t border-white/10">
                    <span class="text-white/60 text-sm">Já tem uma conta?</span>
                    <a href="{{ route('login') }}" 
                       class="text-emerald-400 hover:text-emerald-300 font-medium text-sm ml-1 transition-colors duration-200">
                        Faça login aqui
                    </a>
                </div>
            </form>
        </div>

        <!-- Benefits Card -->
        <div class="mt-6 bg-blue-500/10 border border-blue-500/20 rounded-lg p-4 backdrop-blur-sm">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 bg-blue-500/20 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                    <i class="bi bi-star text-blue-400 text-sm"></i>
                </div>
                <div>
                    <h4 class="text-blue-300 font-medium text-sm mb-2">Benefícios da Conta</h4>
                    <div class="text-white/70 text-xs space-y-1">
                        <p>• Gerenciar produtos com variantes</p>
                        <p>• Acompanhar histórico de compras</p>
                        <p>• Configurar alertas de preço</p>
                        <p>• Acesso completo ao painel admin</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Input focus effects */
input:focus {
    background: rgba(255, 255, 255, 0.15) !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
}

/* Button hover effects */
.premium-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(16, 185, 129, 0.3);
}

/* Form animations */
.product-hero {
    animation: slideUp 0.6s ease-out;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
@endsection