@extends('layouts.app')

@section('title', 'Editar Livro')

@section('content')
<div class="premium-content">
    <div class="max-w-4xl mx-auto px-4">
        <!-- Header Premium -->
        <div class="premium-header mb-8">
            <div class="header-content">
                <div class="header-left">
                    <a href="{{ route('books.index') }}" class="back-btn">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <div class="header-title">
                        <h1>Editar Livro</h1>
                        <p class="header-subtitle">{{ $book->title }}</p>
                    </div>
                </div>
                <div class="header-actions">
                    <a href="{{ route('books.show', $book->id) }}" class="action-btn" title="Ver Livro">
                        <i class="bi bi-eye"></i>
                    </a>
                </div>
            </div>
        </div>

        <form action="{{ route('books.update', $book->id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Informações Básicas -->
            <div class="chart-section mb-4">
                <h3 class="section-title">
                    <i class="bi bi-info-circle"></i>
                    Informações do Livro
                </h3>

                <div class="mb-3">
                    <label for="title" class="form-label" style="color: rgba(255,255,255,0.9); margin-bottom: 8px;">
                        Título do Livro *
                    </label>
                    <input type="text" 
                           name="title" 
                           id="title" 
                           value="{{ old('title', $book->title) }}"
                           class="form-control"
                           placeholder="Ex: A Mente de Quem Veio de Baixo"
                           required
                           style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; border-radius: 8px; padding: 12px;">
                    @error('title')
                        <p class="text-danger text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="content" class="form-label" style="color: rgba(255,255,255,0.9); margin-bottom: 8px;">
                        Conteúdo *
                    </label>
                    <textarea name="content" 
                              id="content" 
                              rows="20"
                              class="form-control"
                              placeholder="Digite o conteúdo do livro aqui..."
                              required
                              style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; border-radius: 8px; padding: 12px; font-family: monospace; white-space: pre-wrap;">{{ old('content', $book->content) }}</textarea>
                    @error('content')
                        <p class="text-danger text-sm">{{ $message }}</p>
                    @enderror
                    <small style="color: rgba(255,255,255,0.6);">
                        <i class="bi bi-info-circle me-1"></i>
                        Você pode continuar adicionando conteúdo ou editar o que já existe.
                    </small>
                </div>
            </div>

            <!-- Botões de Ação -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                <a href="{{ route('books.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle me-2"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-circle me-2"></i> Atualizar Livro
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

