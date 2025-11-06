@extends('layouts.app')

@section('content')
<div class="premium-content">
    <div class="max-w-6xl mx-auto px-4">
        <!-- Header Premium -->
        <div class="premium-header mb-8">
            <div class="header-content">
                <div class="header-left">
                    <a href="{{ route('admin.products.index') }}" class="back-btn">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <div class="header-title">
                        <h1>Editar Produto</h1>
                        <p class="header-subtitle">Modifique as informações do produto</p>
                    </div>
                </div>
                <div class="header-actions">
                    <a href="{{ route('admin.products.show', $product) }}" class="action-btn" title="Ver Produto">
                        <i class="bi bi-eye"></i>
                    </a>
                    <a href="{{ route('dashboard') }}" class="action-btn" title="Painel Principal">
                        <i class="bi bi-speedometer2"></i>
                    </a>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf
            @method('PUT')

            <!-- Informações Básicas -->
            <div class="chart-section">
                <h3 class="section-title">
                    <i class="bi bi-info-circle"></i>
                    Informações Básicas
                </h3>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Nome do Produto -->
                    <div class="space-y-2">
                        <label for="name" class="block text-sm font-medium text-white/90">
                            Nome do Produto *
                        </label>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               value="{{ old('name', $product->name) }}"
                               class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-3 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300 @error('name') border-red-500/50 focus:ring-red-500 @enderror"
                               placeholder="Nome do produto"
                               required>
                        @error('name')
                            <p class="text-red-300 text-sm flex items-center gap-1">
                                <i class="bi bi-exclamation-circle"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Categoria -->
                    <div class="space-y-2">
                        <label for="category" class="block text-sm font-medium text-white/90">
                            Categoria
                        </label>
                        <div class="flex gap-2">
                            <select name="category" 
                                    id="category" 
                                    class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300 @error('category') border-red-500/50 focus:ring-red-500 @enderror">
                                <option value="">Selecione uma categoria...</option>
                            </select>
                            <button type="button" 
                                    onclick="window.openCategoryModal()"
                                    class="px-4 py-3 bg-emerald-500/20 border border-emerald-500/30 rounded-lg text-emerald-400 hover:bg-emerald-500/30 transition-all duration-300 flex items-center justify-center"
                                    title="Adicionar nova categoria">
                                <i class="bi bi-plus-circle text-lg"></i>
                            </button>
                        </div>
                        <small class="text-white/60 text-xs flex items-center gap-1">
                            <i class="bi bi-info-circle"></i>
                            Selecione uma categoria existente ou crie uma nova
                        </small>
                        @error('category')
                            <p class="text-red-300 text-sm flex items-center gap-1">
                                <i class="bi bi-exclamation-circle"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Departamento do Objetivo -->
                    <div class="space-y-2">
                        <label for="goal_category" class="block text-sm font-medium text-white/90">
                            Departamento *
                        </label>
                        <select name="goal_category" 
                                id="goal_category" 
                                class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300 @error('goal_category') border-red-500/50 focus:ring-red-500 @enderror">
                            <option value="">Selecione o departamento...</option>
                            <option value="fixed_expenses" {{ old('goal_category', $product->goal_category) == 'fixed_expenses' ? 'selected' : '' }}>🏠 Despesa Fixa</option>
                            <option value="professional_resources" {{ old('goal_category', $product->goal_category) == 'professional_resources' ? 'selected' : '' }}>💼 Recursos Profissionais</option>
                            <option value="emergency_reserves" {{ old('goal_category', $product->goal_category) == 'emergency_reserves' ? 'selected' : '' }}>🛡️ Reserva de Emergência</option>
                            <option value="leisure" {{ old('goal_category', $product->goal_category) == 'leisure' ? 'selected' : '' }}>😊 Lazer</option>
                            <option value="debt_installments" {{ old('goal_category', $product->goal_category) == 'debt_installments' ? 'selected' : '' }}>💳 Parcelas de Dívidas</option>
                        </select>
                        <small class="text-white/60 text-xs">Este produto será automaticamente classificado neste departamento nas compras</small>
                        @error('goal_category')
                            <p class="text-red-300 text-sm flex items-center gap-1">
                                <i class="bi bi-exclamation-circle"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Unidade -->
                    <div class="space-y-2">
                        <label for="unit" class="block text-sm font-medium text-white/90">
                            Unidade *
                        </label>
                        <select name="unit" 
                                id="unit" 
                                class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300 @error('unit') border-red-500/50 focus:ring-red-500 @enderror">
                            <option value="">Selecione a unidade...</option>
                            <option value="kg" {{ old('unit', $product->unit) == 'kg' ? 'selected' : '' }}>kg - Quilograma</option>
                            <option value="g" {{ old('unit', $product->unit) == 'g' ? 'selected' : '' }}>g - Grama</option>
                            <option value="L" {{ old('unit', $product->unit) == 'L' ? 'selected' : '' }}>L - Litro</option>
                            <option value="ml" {{ old('unit', $product->unit) == 'ml' ? 'selected' : '' }}>ml - Mililitro</option>
                            <option value="un" {{ old('unit', $product->unit) == 'un' ? 'selected' : '' }}>un - Unidade</option>
                            <option value="pct" {{ old('unit', $product->unit) == 'pct' ? 'selected' : '' }}>pct - Pacote</option>
                            <option value="dz" {{ old('unit', $product->unit) == 'dz' ? 'selected' : '' }}>dz - Dúzia</option>
                            <option value="cx" {{ old('unit', $product->unit) == 'cx' ? 'selected' : '' }}>cx - Caixa</option>
                        </select>
                        @error('unit')
                            <p class="text-red-300 text-sm flex items-center gap-1">
                                <i class="bi bi-exclamation-circle"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                           <!-- Imagem Atual -->
                           @if($product->hasImage())
                           <div class="space-y-2">
                               <label class="block text-sm font-medium text-white/90">
                                   Imagem Atual
                               </label>
                               <div class="current-image-container">
                                   <img src="{{ $product->current_image }}" 
                                        alt="{{ $product->name }}" 
                                        class="current-image"
                                        onerror="this.onerror=null; this.src='{{ asset('images/no-image.png') }}'; this.style.display='block';">
                               </div>
                           </div>
                           @endif

                           <!-- Upload de Nova Imagem -->
                           <div class="space-y-2">
                               <label for="image_file" class="block text-sm font-medium text-white/90">
                                   Nova Imagem do Produto
                               </label>
                               <div class="image-upload-container">
                                   <input type="file" 
                                          name="image_file" 
                                          id="image_file" 
                                          accept="image/*"
                                          class="hidden"
                                          onchange="previewImage(this)">
                                   <label for="image_file" class="image-upload-label">
                                       <div class="image-upload-content">
                                           <i class="bi bi-camera text-2xl text-white/60"></i>
                                           <span class="text-white/80">Clique para selecionar uma nova imagem</span>
                                           <small class="text-white/60">JPG, PNG, GIF ou WebP (máx. 2MB)</small>
                                       </div>
                                   </label>
                                   <div id="image-preview" class="image-preview hidden">
                                       <img id="preview-img" src="" alt="Preview" class="preview-image">
                                       <button type="button" onclick="removeImage()" class="remove-image-btn">
                                           <i class="bi bi-x"></i>
                                       </button>
                                   </div>
                               </div>
                               @error('image_file')
                                   <p class="text-red-300 text-sm flex items-center gap-1">
                                       <i class="bi bi-exclamation-circle"></i> {{ $message }}
                                   </p>
                               @enderror
                           </div>

                           <!-- URL de Imagem (Alternativa) -->
                           <div class="space-y-2">
                               <label for="image" class="block text-sm font-medium text-white/90">
                                   Ou cole uma URL de imagem
                               </label>
                               <input type="url" 
                                      name="image" 
                                      id="image" 
                                      value="{{ old('image', $product->image) }}"
                                      class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-3 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300 @error('image') border-red-500/50 focus:ring-red-500 @enderror"
                                      placeholder="https://exemplo.com/imagem.jpg">
                               @error('image')
                                   <p class="text-red-300 text-sm flex items-center gap-1">
                                       <i class="bi bi-exclamation-circle"></i> {{ $message }}
                                   </p>
                               @enderror
                           </div>
                </div>

                <!-- Descrição -->
                <div class="mt-6 space-y-2">
                    <label for="description" class="block text-sm font-medium text-white/90">
                        Descrição
                    </label>
                    <textarea name="description" 
                              id="description" 
                              rows="4"
                              class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-3 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300 resize-none @error('description') border-red-500/50 focus:ring-red-500 @enderror"
                              placeholder="Descreva o produto...">{{ old('description', $product->description) }}</textarea>
                    @error('description')
                        <p class="text-red-300 text-sm flex items-center gap-1">
                            <i class="bi bi-exclamation-circle"></i> {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            <!-- Seção de Variantes -->
            <div class="chart-section">
                <h3 class="section-title">
                    <i class="bi bi-layers"></i>
                    Variantes do Produto
                </h3>
                
                <div id="variants-container" class="space-y-4">
                    @if($product->variants && count($product->variants) > 0)
                        @foreach($product->variants as $index => $variant)
                            <div class="variant-item bg-white/5 border border-white/10 rounded-lg p-4">
                                <div class="flex justify-between items-center mb-4">
                                    <h4 class="text-sm font-medium text-white/90">
                                        Variante {{ $index + 1 }}
                                    </h4>
                                    <button type="button" 
                                            onclick="removeVariant(this)" 
                                            class="text-red-400 hover:text-red-300 text-sm">
                                        Remover
                                    </button>
                                </div>
                                
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-white/80">Nome da Variante</label>
                                <input type="text" 
                                       name="variants[{{ $index }}][name]" 
                                       value="{{ old('variants.'.$index.'.name', $variant['name']) }}"
                                       class="w-full bg-white/10 border border-white/20 rounded-lg px-3 py-2 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300"
                                       placeholder="Ex: Tamanho P, Sabor Chocolate">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-white/80">Preço <span class="text-white/60 text-xs">(opcional)</span></label>
                                <input type="number" 
                                       name="variants[{{ $index }}][price]" 
                                       value="{{ old('variants.'.$index.'.price', $variant['price']) }}"
                                       step="0.01" 
                                       min="0"
                                       class="w-full bg-white/10 border border-white/20 rounded-lg px-3 py-2 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300"
                                                  placeholder="Deixe vazio se variar por mercado">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-white/80">Unidade</label>
                                <select name="variants[{{ $index }}][unit]" 
                                        class="w-full bg-white/10 border border-white/20 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300">
                                    <option value="kg" {{ old('variants.'.$index.'.unit', $variant['unit']) == 'kg' ? 'selected' : '' }}>kg</option>
                                    <option value="g" {{ old('variants.'.$index.'.unit', $variant['unit']) == 'g' ? 'selected' : '' }}>g</option>
                                    <option value="L" {{ old('variants.'.$index.'.unit', $variant['unit']) == 'L' ? 'selected' : '' }}>L</option>
                                    <option value="ml" {{ old('variants.'.$index.'.unit', $variant['unit']) == 'ml' ? 'selected' : '' }}>ml</option>
                                    <option value="un" {{ old('variants.'.$index.'.unit', $variant['unit']) == 'un' ? 'selected' : '' }}>un</option>
                                    <option value="pct" {{ old('variants.'.$index.'.unit', $variant['unit']) == 'pct' ? 'selected' : '' }}>pct</option>
                                    <option value="dz" {{ old('variants.'.$index.'.unit', $variant['unit']) == 'dz' ? 'selected' : '' }}>dz</option>
                                    <option value="cx" {{ old('variants.'.$index.'.unit', $variant['unit']) == 'cx' ? 'selected' : '' }}>cx</option>
                                </select>
                            </div>
                        </div>
                            </div>
                        @endforeach
                    @else
                        <div class="variant-item bg-white/5 border border-white/10 rounded-lg p-4">
                            <div class="flex justify-between items-center mb-4">
                                <h4 class="text-sm font-medium text-white/90">
                                    Variante 1
                                </h4>
                                <button type="button" 
                                        onclick="removeVariant(this)" 
                                        class="text-red-400 hover:text-red-300 text-sm">
                                    Remover
                                </button>
                            </div>
                            
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-white/80">Nome da Variante</label>
                                <input type="text" 
                                       name="variants[0][name]" 
                                       class="w-full bg-white/10 border border-white/20 rounded-lg px-3 py-2 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300"
                                       placeholder="Ex: Tamanho P, Sabor Chocolate">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-white/80">Preço <span class="text-white/60 text-xs">(opcional)</span></label>
                                <input type="number" 
                                       name="variants[0][price]" 
                                       step="0.01" 
                                       min="0"
                                       class="w-full bg-white/10 border border-white/20 rounded-lg px-3 py-2 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300"
                                                  placeholder="Deixe vazio se variar por mercado">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-white/80">Unidade</label>
                                <select name="variants[0][unit]" 
                                        class="w-full bg-white/10 border border-white/20 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300">
                                    <option value="kg">kg</option>
                                    <option value="g">g</option>
                                    <option value="L">L</option>
                                    <option value="ml">ml</option>
                                    <option value="un">un</option>
                                    <option value="pct">pct</option>
                                    <option value="dz">dz</option>
                                    <option value="cx">cx</option>
                                </select>
                            </div>
                        </div>
                        </div>
                    @endif
                </div>

                <button type="button" 
                        onclick="addVariant()" 
                        class="mt-4 premium-btn outline">
                    <i class="bi bi-plus-circle"></i>
                    Adicionar Variante
                </button>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row justify-end gap-3">
                <a href="{{ route('admin.products.index') }}" 
                   class="premium-btn outline">
                    <i class="bi bi-arrow-left"></i>
                    Cancelar
                </a>
                <button type="submit" 
                        class="premium-btn primary">
                    <i class="bi bi-check-circle"></i>
                    Atualizar Produto
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let variantCount = {{ $product->variants ? count($product->variants) : 1 }};

function addVariant() {
    const container = document.getElementById('variants-container');
    const newVariant = document.createElement('div');
    newVariant.className = 'variant-item bg-white/5 border border-white/10 rounded-lg p-4';
    newVariant.innerHTML = `
        <div class="flex justify-between items-center mb-4">
            <h4 class="text-sm font-medium text-white/90">
                Variante ${variantCount + 1}
            </h4>
            <button type="button" 
                    onclick="removeVariant(this)" 
                    class="text-red-400 hover:text-red-300 text-sm">
                Remover
            </button>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div class="space-y-2">
                <label class="block text-sm font-medium text-white/80">Nome da Variante</label>
                <input type="text" 
                       name="variants[${variantCount}][name]" 
                       class="w-full bg-white/10 border border-white/20 rounded-lg px-3 py-2 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300"
                       placeholder="Ex: Tamanho P, Sabor Chocolate">
            </div>
            <div class="space-y-2">
                <label class="block text-sm font-medium text-white/80">Preço <span class="text-white/60 text-xs">(opcional)</span></label>
                <input type="number" 
                       name="variants[${variantCount}][price]" 
                       step="0.01" 
                       min="0"
                       class="w-full bg-white/10 border border-white/20 rounded-lg px-3 py-2 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300"
                                                  placeholder="Deixe vazio se variar por mercado">
            </div>
            <div class="space-y-2">
                <label class="block text-sm font-medium text-white/80">Unidade</label>
                <select name="variants[${variantCount}][unit]" 
                        class="w-full bg-white/10 border border-white/20 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300">
                    <option value="kg">kg</option>
                    <option value="g">g</option>
                    <option value="L">L</option>
                    <option value="ml">ml</option>
                    <option value="un">un</option>
                    <option value="pct">pct</option>
                    <option value="dz">dz</option>
                    <option value="cx">cx</option>
                </select>
            </div>
        </div>
    `;
    container.appendChild(newVariant);
    variantCount++;
}

       function removeVariant(button) {
           button.closest('.variant-item').remove();
       }

       // Image upload functions
       function previewImage(input) {
           if (input.files && input.files[0]) {
               const reader = new FileReader();
               
               reader.onload = function(e) {
                   const preview = document.getElementById('image-preview');
                   const previewImg = document.getElementById('preview-img');
                   
                   previewImg.src = e.target.result;
                   preview.classList.remove('hidden');
                   
                   // Hide upload label
                   document.querySelector('.image-upload-label').style.display = 'none';
               }
               
               reader.readAsDataURL(input.files[0]);
           }
       }

       function removeImage() {
           const input = document.getElementById('image_file');
           const preview = document.getElementById('image-preview');
           const label = document.querySelector('.image-upload-label');
           
           input.value = '';
           preview.classList.add('hidden');
           label.style.display = 'block';
       }

       // Atualizar imagem atual quando URL for alterada
       document.addEventListener('DOMContentLoaded', function() {
           const urlInput = document.getElementById('image');
           const currentImage = document.querySelector('.current-image');
           
           if (urlInput && currentImage) {
               urlInput.addEventListener('input', function() {
                   if (this.value.trim() !== '') {
                       currentImage.src = this.value;
                       currentImage.style.display = 'block';
                   }
               });
           }
       });

// Input focus effects
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('input, textarea, select');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.style.background = 'rgba(255, 255, 255, 0.12)';
            this.style.borderColor = 'rgba(16, 185, 129, 0.6)';
            this.style.transform = 'translateY(-2px)';
            this.style.boxShadow = '0 0 0 4px rgba(16, 185, 129, 0.15), 0 4px 20px rgba(16, 185, 129, 0.1)';
        });
        
        input.addEventListener('blur', function() {
            this.style.background = 'rgba(255, 255, 255, 0.08)';
            this.style.borderColor = 'rgba(255, 255, 255, 0.15)';
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = 'none';
        });
        
        // Add hover effects
        input.addEventListener('mouseenter', function() {
            if (document.activeElement !== this) {
                this.style.background = 'rgba(255, 255, 255, 0.1)';
                this.style.borderColor = 'rgba(255, 255, 255, 0.2)';
            }
        });
        
        input.addEventListener('mouseleave', function() {
            if (document.activeElement !== this) {
                this.style.background = 'rgba(255, 255, 255, 0.08)';
                this.style.borderColor = 'rgba(255, 255, 255, 0.15)';
            }
        });
    });
});
</script>

<style>
/* Override mobile-container for admin pages */
.premium-content {
    max-width: none !important;
    width: 100% !important;
}

.premium-content .max-w-6xl {
    max-width: 1200px !important;
}

/* Ensure proper spacing on larger screens */
@media (min-width: 1024px) {
    .premium-content {
        padding: 2rem 1rem;
    }
    
    .chart-section {
        padding: 1.5rem;
    }
    
    .variant-item {
        padding: 1.5rem;
    }
}

/* Grid improvements for larger screens */
@media (min-width: 768px) {
    .grid.grid-cols-1.lg\\:grid-cols-2 {
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
    }
    
    .grid.grid-cols-1.md\\:grid-cols-3 {
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
    }
}

/* Fix overflow issues */
.premium-content {
    overflow-x: hidden !important;
}

.chart-section {
    overflow: hidden !important;
}

.variant-item {
    overflow: hidden !important;
}

/* Better spacing for variant cards */
.variant-item .grid {
    gap: 0.75rem !important;
}

.variant-item .space-y-2 {
    margin-bottom: 0.5rem !important;
}

/* Dropdown improvements */
select {
    background: rgba(255, 255, 255, 0.1) !important;
    border: 2px solid rgba(255, 255, 255, 0.2) !important;
    color: white !important;
    font-weight: 600 !important;
    appearance: none !important;
    background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='white' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6,9 12,15 18,9'%3e%3c/polyline%3e%3c/svg%3e") !important;
    background-repeat: no-repeat !important;
    background-position: right 12px center !important;
    background-size: 16px !important;
    padding-right: 40px !important;
}

select:focus {
    background: rgba(255, 255, 255, 0.15) !important;
    border-color: rgba(16, 185, 129, 0.7) !important;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2) !important;
}

select option {
    background: #1f2937 !important;
    color: white !important;
    padding: 8px 12px !important;
}

/* Variant dropdowns */
.variant-item select {
    background: rgba(255, 255, 255, 0.08) !important;
    border: 2px solid rgba(255, 255, 255, 0.15) !important;
}

.variant-item select:focus {
    background: rgba(255, 255, 255, 0.12) !important;
    border-color: rgba(16, 185, 129, 0.6) !important;
}

/* Better responsive design */
@media (max-width: 768px) {
    .grid.grid-cols-1.md\\:grid-cols-3 {
        grid-template-columns: 1fr !important;
        gap: 1rem !important;
    }
    
    .variant-item {
        padding: 1rem !important;
    }
    
    .chart-section {
        padding: 1.5rem !important;
    }
}

/* Input field width fixes */
input, textarea, select {
    width: 100% !important;
    max-width: 100% !important;
    box-sizing: border-box !important;
}

/* Fix buttons getting stuck behind bottom nav */
.premium-content {
    padding-bottom: 120px !important;
    margin: -1rem !important;
    padding-left: 1rem !important;
    padding-right: 1rem !important;
    padding-top: 1rem !important;
}

/* Action buttons container */
.flex.flex-col.sm\\:flex-row.justify-end.gap-3 {
    margin-top: 2rem !important;
    padding: 1.5rem 0 !important;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.05) 0%, rgba(255, 255, 255, 0.02) 100%) !important;
    border-radius: 12px !important;
    backdrop-filter: blur(10px) !important;
    border: 1px solid rgba(255, 255, 255, 0.1) !important;
    position: sticky !important;
    bottom: 80px !important;
    z-index: 10 !important;
}

/* Mobile adjustments */
@media (max-width: 768px) {
    .premium-content {
        padding-bottom: 140px !important;
    }
    
    .flex.flex-col.sm\\:flex-row.justify-end.gap-3 {
        bottom: 90px !important;
        margin: 1rem 0 !important;
        padding: 1rem !important;
    }
}

/* Input field improvements */
input, textarea, select {
    background: rgba(255, 255, 255, 0.08) !important;
    border: 1px solid rgba(255, 255, 255, 0.15) !important;
    color: white !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    backdrop-filter: blur(10px) !important;
    border-radius: 12px !important;
    font-weight: 500 !important;
}

input:focus, textarea:focus, select:focus {
    background: rgba(255, 255, 255, 0.12) !important;
    border-color: rgba(16, 185, 129, 0.6) !important;
    box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.15), 0 4px 20px rgba(16, 185, 129, 0.1) !important;
    outline: none !important;
    transform: translateY(-2px) !important;
}

input::placeholder, textarea::placeholder {
    color: rgba(255, 255, 255, 0.5) !important;
    font-weight: 400 !important;
}

/* Variant inputs specific styling */
.variant-item input, .variant-item textarea, .variant-item select {
    background: rgba(255, 255, 255, 0.06) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 10px !important;
}

.variant-item input:focus, .variant-item textarea:focus, .variant-item select:focus {
    background: rgba(255, 255, 255, 0.1) !important;
    border-color: rgba(16, 185, 129, 0.5) !important;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1), 0 2px 12px rgba(16, 185, 129, 0.08) !important;
}

/* Labels styling */
label {
    color: rgba(255, 255, 255, 0.9) !important;
    font-weight: 600 !important;
    font-size: 0.875rem !important;
    margin-bottom: 0.5rem !important;
    display: block !important;
}

/* Section titles */
.section-title {
    color: white !important;
    font-size: 1.25rem !important;
    font-weight: 700 !important;
    margin-bottom: 1.5rem !important;
    display: flex !important;
    align-items: center !important;
    gap: 0.75rem !important;
}

.section-title i {
    color: #10b981 !important;
    font-size: 1.5rem !important;
}

/* Chart section improvements */
.chart-section {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.08) 0%, rgba(255, 255, 255, 0.04) 100%) !important;
    border: 1px solid rgba(255, 255, 255, 0.1) !important;
    border-radius: 16px !important;
    padding: 2rem !important;
    backdrop-filter: blur(20px) !important;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1) !important;
}

/* Variant items */
.variant-item {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.06) 0%, rgba(255, 255, 255, 0.02) 100%) !important;
    border: 1px solid rgba(255, 255, 255, 0.08) !important;
    border-radius: 12px !important;
    backdrop-filter: blur(10px) !important;
    transition: all 0.3s ease !important;
}

.variant-item:hover {
    border-color: rgba(16, 185, 129, 0.2) !important;
    box-shadow: 0 4px 20px rgba(16, 185, 129, 0.05) !important;
}

/* Buttons improvements */
.premium-btn {
    border-radius: 12px !important;
    font-weight: 600 !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    backdrop-filter: blur(10px) !important;
}

.premium-btn.primary {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
    border: 1px solid rgba(16, 185, 129, 0.3) !important;
    box-shadow: 0 4px 20px rgba(16, 185, 129, 0.2) !important;
}

.premium-btn.primary:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 8px 25px rgba(16, 185, 129, 0.3) !important;
}

.premium-btn.outline {
    background: rgba(255, 255, 255, 0.08) !important;
    border: 1px solid rgba(255, 255, 255, 0.2) !important;
    color: white !important;
}

       .premium-btn.outline:hover {
           background: rgba(255, 255, 255, 0.12) !important;
           border-color: rgba(255, 255, 255, 0.3) !important;
           transform: translateY(-1px) !important;
       }

       /* Image Upload Styles */
       .current-image-container {
           margin-bottom: 1rem;
       }

       .current-image {
           width: 100%;
           max-height: 200px;
           object-fit: cover;
           border-radius: 12px;
           border: 2px solid rgba(16, 185, 129, 0.3);
       }

       .image-upload-container {
           position: relative;
           width: 100%;
       }

       .image-upload-label {
           display: block;
           width: 100%;
           min-height: 120px;
           border: 2px dashed rgba(255, 255, 255, 0.3);
           border-radius: 12px;
           cursor: pointer;
           transition: all 0.3s ease;
           background: rgba(255, 255, 255, 0.05);
       }

       .image-upload-label:hover {
           border-color: rgba(16, 185, 129, 0.5);
           background: rgba(16, 185, 129, 0.1);
           transform: translateY(-2px);
       }

       .image-upload-content {
           display: flex;
           flex-direction: column;
           align-items: center;
           justify-content: center;
           height: 100%;
           padding: 1rem;
           text-align: center;
           gap: 0.5rem;
       }

       .image-preview {
           position: relative;
           width: 100%;
           margin-top: 1rem;
       }

       .preview-image {
           width: 100%;
           max-height: 200px;
           object-fit: cover;
           border-radius: 12px;
           border: 2px solid rgba(16, 185, 129, 0.3);
       }

       .remove-image-btn {
           position: absolute;
           top: 8px;
           right: 8px;
           background: rgba(239, 68, 68, 0.9);
           color: white;
           border: none;
           border-radius: 50%;
           width: 32px;
           height: 32px;
           display: flex;
           align-items: center;
           justify-content: center;
           cursor: pointer;
           transition: all 0.3s ease;
       }

       .remove-image-btn:hover {
           background: rgba(239, 68, 68, 1);
           transform: scale(1.1);
       }

       .hidden {
           display: none !important;
       }
       
       /* Autocomplete Styles */
       #category-autocomplete-wrapper {
           position: relative;
           z-index: 10;
       }
       
       .category-suggestions {
           position: absolute;
           top: 100%;
           left: 0;
           right: 0;
           margin-top: 0.25rem;
           background: rgba(26, 26, 46, 0.98);
           border: 1px solid rgba(255, 255, 255, 0.2);
           border-radius: 8px;
           max-height: 300px;
           overflow-y: auto;
           z-index: 10000 !important;
           box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
           backdrop-filter: blur(20px);
           display: block !important;
           opacity: 1 !important;
           visibility: visible !important;
       }
       
       .category-suggestions.hidden {
           display: none !important;
           opacity: 0 !important;
           visibility: hidden !important;
       }
       
       .suggestion-item {
           padding: 0.75rem 1rem;
           cursor: pointer;
           transition: all 0.2s ease;
           border-bottom: 1px solid rgba(255, 255, 255, 0.05);
       }
       
       .suggestion-item:last-child {
           border-bottom: none;
       }
       
       .suggestion-item:hover,
       .suggestion-item.selected {
           background: rgba(16, 185, 129, 0.2);
           border-left: 3px solid #10b981;
       }
       
       .suggestion-name {
           color: rgba(255, 255, 255, 0.9);
           font-weight: 500;
       }
       
       .suggestion-name strong {
           color: #10b981;
           font-weight: 700;
       }
       
       .suggestion-count {
           color: rgba(255, 255, 255, 0.5);
           font-size: 0.75rem;
       }
       
       .suggestion-create {
           background: rgba(16, 185, 129, 0.1);
           border-top: 2px solid rgba(16, 185, 129, 0.3);
       }
       
       .suggestion-create:hover {
           background: rgba(16, 185, 129, 0.3);
       }
       
       .suggestion-create i {
           color: #10b981;
       }
       </style>

<script>
// Carregar categorias no select
document.addEventListener('DOMContentLoaded', function() {
    const categorySelect = document.getElementById('category');
    const oldCategoryValue = '{{ old("category", $product->category) }}';
    
    if (!categorySelect) {
        console.warn('Select de categoria não encontrado');
        return;
    }
    
    // Função para carregar categorias
    async function loadCategories() {
        try {
            const response = await fetch('/api/product-categories/search?q=', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                credentials: 'same-origin' // Importante: envia cookies de sessão
            });
            
            // Verificar se a resposta é JSON (não HTML)
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error(`Resposta não é JSON. Tipo: ${contentType}. Status: ${response.status}`);
            }
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            console.log('Dados recebidos da API:', data); // Debug
            
            // Limpar opções existentes
            categorySelect.innerHTML = '<option value="">Selecione uma categoria...</option>';
            
            if (data.success && Array.isArray(data.categories) && data.categories.length > 0) {
                // Adicionar categorias
                data.categories.forEach(category => {
                    const option = document.createElement('option');
                    option.value = category.name;
                    option.textContent = category.name;
                    if (category.usage_count > 0) {
                        option.textContent += ` (${category.usage_count} produtos)`;
                    }
                    
                    // Selecionar se for o valor atual do produto ou old
                    if (oldCategoryValue && category.name === oldCategoryValue) {
                        option.selected = true;
                    }
                    
                    categorySelect.appendChild(option);
                });
            } else {
                // Se não houver categorias, mostrar mensagem e tentar migrar
                const noCategoryOption = document.createElement('option');
                noCategoryOption.value = '';
                noCategoryOption.textContent = 'Nenhuma categoria cadastrada - Clique em + para criar';
                noCategoryOption.disabled = true;
                categorySelect.appendChild(noCategoryOption);
                
                // Tentar migrar categorias existentes dos produtos
                try {
                    const migrateResponse = await fetch('/api/product-categories/migrate', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                                           document.querySelector('input[name="_token"]')?.value || ''
                        }
                    });
                    
                    if (migrateResponse.ok) {
                        const migrateData = await migrateResponse.json();
                        if (migrateData.success && migrateData.migrated_count > 0) {
                            // Recarregar categorias após migração
                            setTimeout(() => loadCategories(), 500);
                            return;
                        }
                    }
                } catch (migrateError) {
                    console.warn('Não foi possível migrar categorias automaticamente:', migrateError);
                }
            }
        } catch (error) {
            console.error('Erro ao carregar categorias:', error);
            const errorOption = document.createElement('option');
            errorOption.value = '';
            errorOption.textContent = 'Erro ao carregar categorias';
            errorOption.disabled = true;
            categorySelect.innerHTML = '';
            categorySelect.appendChild(errorOption);
        }
    }
    
    // Carregar categorias ao carregar a página
    loadCategories();
    
    // Expor função para recarregar após criar categoria
    window.reloadCategories = loadCategories;
});
</script>

@include('components.category-modal')
@endsection
