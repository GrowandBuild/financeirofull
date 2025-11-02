@extends('layouts.app')

@section('title', 'Sabedoria')

@section('content')
<!-- Premium Header -->
<div class="premium-header">
    <div class="header-content">
        <div class="header-title">
            <h1>Sabedoria</h1>
            <span class="header-subtitle">Livros e textos</span>
        </div>
        <div class="header-actions">
            <a href="{{ route('books.create') }}" class="action-btn" title="Novo Livro">
                <i class="bi bi-plus-circle"></i>
            </a>
        </div>
    </div>
</div>

<!-- Premium Content -->
<div class="premium-content">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($books->count() > 0)
        <div class="row" style="margin: 0; max-width: 100%;">
            @foreach($books as $book)
            <div class="col-12 col-md-6 col-lg-4 mb-4" style="padding-left: 0.5rem; padding-right: 0.5rem; max-width: 100%;">
                <div class="premium-product-card" style="cursor: pointer; word-wrap: break-word; overflow-wrap: break-word; max-width: 100%;" onclick="window.location='{{ route('books.show', $book->id) }}'">
                    <div class="card-header mb-3">
                        <h3 style="color: white; font-size: 1.2rem; margin: 0; word-wrap: break-word; overflow-wrap: break-word;">
                            <i class="bi bi-book me-2"></i>{{ Str::limit($book->title, 40) }}
                        </h3>
                    </div>
                    <div class="card-body" style="word-wrap: break-word; overflow-wrap: break-word;">
                        <p style="color: rgba(255,255,255,0.7); font-size: 0.9rem; margin-bottom: 1rem; word-wrap: break-word; overflow-wrap: break-word;">
                            {{ Str::limit(strip_tags($book->content), 150) }}
                        </p>
                        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.8rem; color: rgba(255,255,255,0.5); flex-wrap: wrap; gap: 0.5rem;">
                            <span style="white-space: nowrap;">
                                <i class="bi bi-calendar me-1"></i>
                                {{ $book->created_at->format('d/m/Y') }}
                            </span>
                            <span style="white-space: nowrap;">
                                <i class="bi bi-clock me-1"></i>
                                {{ $book->updated_at->diffForHumans() }}
                            </span>
                        </div>
                    </div>
                    <div class="card-actions" style="display: flex; gap: 10px; margin-top: 15px; padding-top: 15px; border-top: 1px solid rgba(255,255,255,0.1); flex-wrap: wrap;">
                        <a href="{{ route('books.show', $book->id) }}" class="btn btn-sm btn-primary" style="flex: 1; min-width: 0;" onclick="event.stopPropagation();">
                            <i class="bi bi-eye me-1"></i> <span class="d-none d-sm-inline">Ver</span>
                        </a>
                        <a href="{{ route('books.edit', $book->id) }}" class="btn btn-sm btn-warning" style="flex: 1; min-width: 0;" onclick="event.stopPropagation();">
                            <i class="bi bi-pencil me-1"></i> <span class="d-none d-sm-inline">Editar</span>
                        </a>
                        <form action="{{ route('books.destroy', $book->id) }}" method="POST" style="flex: 1; min-width: 0;" onclick="event.stopPropagation();" onsubmit="return confirm('Tem certeza que deseja excluir este livro?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger w-100">
                                <i class="bi bi-trash me-1"></i> <span class="d-none d-sm-inline">Excluir</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="premium-product-card text-center" style="padding: 2rem 1rem; max-width: 100%; word-wrap: break-word; overflow-wrap: break-word;">
            <i class="bi bi-book" style="font-size: 4rem; color: rgba(255,255,255,0.3); margin-bottom: 1rem;"></i>
            <h3 style="color: white; margin-bottom: 1rem; word-wrap: break-word;">Nenhum livro cadastrado</h3>
            <p style="color: rgba(255,255,255,0.7); margin-bottom: 2rem; word-wrap: break-word; overflow-wrap: break-word; padding: 0 1rem;">
                Comece adicionando seu primeiro livro ou texto de sabedoria.
            </p>
            <a href="{{ route('books.create') }}" class="btn btn-success" style="word-wrap: break-word;">
                <i class="bi bi-plus-circle me-2"></i> Criar Primeiro Livro
            </a>
        </div>
    @endif
</div>
@endsection

