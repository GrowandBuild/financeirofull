@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Paginação" class="pagination-container">
        {{-- Informação de resultados (mostrar primeiro em desktop) --}}
        <div class="pagination-results-info">
            @if ($paginator->firstItem())
                <span class="pagination-results-text">
                    Mostrando <strong class="pagination-results-number">{{ $paginator->firstItem() }}</strong>
                    até <strong class="pagination-results-number">{{ $paginator->lastItem() }}</strong>
                    de <strong class="pagination-results-number">{{ $paginator->total() }}</strong> resultados
                </span>
            @else
                <span class="pagination-results-text">
                    <strong class="pagination-results-number">{{ $paginator->count() }}</strong> resultado(s)
                </span>
            @endif
        </div>

        {{-- Controles de navegação --}}
        <div class="pagination-wrapper">
            {{-- Botão Anterior --}}
            @if ($paginator->onFirstPage())
                <button disabled class="pagination-btn pagination-btn-disabled" aria-label="Anterior">
                    <i class="bi bi-chevron-left"></i>
                </button>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="pagination-btn pagination-btn-prev" aria-label="Anterior">
                    <i class="bi bi-chevron-left"></i>
                </a>
            @endif

            {{-- Informação da página atual --}}
            <div class="pagination-info">
                <span class="pagination-current">{{ $paginator->currentPage() }}</span>
                <span class="pagination-separator">/</span>
                <span class="pagination-total">{{ $paginator->lastPage() }}</span>
            </div>

            {{-- Botão Próximo --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="pagination-btn pagination-btn-next" aria-label="Próximo">
                    <i class="bi bi-chevron-right"></i>
                </a>
            @else
                <button disabled class="pagination-btn pagination-btn-disabled" aria-label="Próximo">
                    <i class="bi bi-chevron-right"></i>
                </button>
            @endif
        </div>
    </nav>
@endif

<style>
.pagination-container {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    justify-content: center;
    align-items: center;
    padding: 1.5rem 0;
    width: 100%;
}

.pagination-results-info {
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
}

.pagination-results-text {
    color: rgba(255, 255, 255, 0.95);
    font-size: 0.95rem;
    font-weight: 400;
    text-align: center;
    padding: 0.5rem 1rem;
    background: rgba(255, 255, 255, 0.03);
    border-radius: 12px;
    border: 1px solid rgba(255, 255, 255, 0.08);
    letter-spacing: 0.01em;
    line-height: 1.6;
}

.pagination-results-number {
    color: #10b981;
    font-weight: 600;
    font-size: 1rem;
}

.pagination-wrapper {
    display: flex;
    align-items: center;
    gap: 1rem;
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 16px;
    padding: 0.5rem 1rem;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.pagination-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.15);
    color: rgba(255, 255, 255, 0.9);
    font-size: 1.25rem;
    text-decoration: none;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
    flex-shrink: 0;
}

.pagination-btn:hover:not(.pagination-btn-disabled) {
    background: rgba(16, 185, 129, 0.2);
    border-color: rgba(16, 185, 129, 0.4);
    color: #10b981;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.pagination-btn:active:not(.pagination-btn-disabled) {
    transform: translateY(0);
    box-shadow: 0 2px 6px rgba(16, 185, 129, 0.2);
}

.pagination-btn-disabled {
    opacity: 0.4;
    cursor: not-allowed;
    background: rgba(255, 255, 255, 0.05);
    border-color: rgba(255, 255, 255, 0.1);
}

.pagination-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: rgba(255, 255, 255, 0.9);
    font-size: 0.95rem;
    font-weight: 500;
    min-width: 60px;
    justify-content: center;
}

.pagination-current {
    color: #10b981;
    font-weight: 600;
    font-size: 1.1rem;
}

.pagination-separator {
    color: rgba(255, 255, 255, 0.5);
    margin: 0 0.25rem;
}

.pagination-total {
    color: rgba(255, 255, 255, 0.7);
}

/* Responsivo */
@media (max-width: 640px) {
    .pagination-container {
        gap: 0.75rem;
    }
    
    .pagination-results-info {
        order: 2;
    }
    
    .pagination-results-text {
        font-size: 0.85rem;
        padding: 0.4rem 0.75rem;
    }
    
    .pagination-results-number {
        font-size: 0.9rem;
    }
    
    .pagination-wrapper {
        padding: 0.5rem;
        gap: 0.75rem;
        order: 1;
    }
    
    .pagination-btn {
        width: 36px;
        height: 36px;
        font-size: 1.1rem;
    }
    
    .pagination-info {
        font-size: 0.85rem;
        min-width: 50px;
        gap: 0.25rem;
    }
    
    .pagination-current {
        font-size: 1rem;
    }
}
</style>

