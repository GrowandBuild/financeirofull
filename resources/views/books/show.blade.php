@extends('layouts.app')

@section('title', $book->title)

@section('content')
<!-- Premium Header -->
<div class="premium-header">
    <div class="header-content">
        <div class="header-left">
            <a href="{{ route('books.index') }}" class="back-btn">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div class="header-title">
                <h1>{{ $book->title }}</h1>
                <span class="header-subtitle">Criado em {{ $book->created_at->format('d/m/Y H:i') }}</span>
            </div>
        </div>
        <div class="header-actions">
            <a href="{{ route('books.edit', $book->id) }}" class="action-btn" title="Editar">
                <i class="bi bi-pencil"></i>
            </a>
            <form action="{{ route('books.destroy', $book->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Tem certeza que deseja excluir este livro?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="action-btn text-danger" title="Excluir">
                    <i class="bi bi-trash"></i>
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Premium Content -->
<div class="premium-content">
    <div class="premium-product-card">
        <div class="book-content" style="color: rgba(255,255,255,0.9); line-height: 1.8; font-size: 1rem; white-space: pre-wrap; font-family: 'Georgia', 'Times New Roman', serif;">
            {!! nl2br(e($book->content)) !!}
        </div>
    </div>
</div>

<style>
.book-content {
    padding: 2rem;
}

.book-content p {
    margin-bottom: 1.5rem;
}

.book-content h1, .book-content h2, .book-content h3 {
    color: #10b981;
    margin-top: 2rem;
    margin-bottom: 1rem;
}

.book-content h1 {
    font-size: 2rem;
}

.book-content h2 {
    font-size: 1.5rem;
}

.book-content h3 {
    font-size: 1.25rem;
}

.book-content ul, .book-content ol {
    margin-left: 2rem;
    margin-bottom: 1.5rem;
}

.book-content li {
    margin-bottom: 0.5rem;
}

.book-content strong {
    color: #10b981;
    font-weight: 600;
}

.book-content em {
    font-style: italic;
    color: rgba(255,255,255,0.8);
}
</style>
@endsection

