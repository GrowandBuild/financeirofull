<!-- Quick Search Component -->
<div class="quick-search-container position-relative">
    <div class="input-group">
        <input type="text" 
               class="form-control" 
               id="quickSearchInput" 
               placeholder="Buscar produtos..."
               autocomplete="off">
        <button class="btn btn-outline-secondary" type="button" id="quickSearchBtn">
            <i class="bi bi-search"></i>
        </button>
    </div>
    
    <!-- Search Results Dropdown -->
    <div id="searchResults" class="search-results-dropdown" style="display: none;">
        <div class="search-results-header">
            <small class="text-muted">Resultados da busca</small>
        </div>
        <div id="searchResultsList" class="search-results-list">
            <!-- Results will be populated here -->
        </div>
    </div>
</div>

<style>
.quick-search-container {
    position: relative;
}

.search-results-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    z-index: 1000;
    max-height: 300px;
    overflow-y: auto;
}

.search-results-header {
    padding: 0.75rem;
    border-bottom: 1px solid #e5e7eb;
    background-color: #f9fafb;
}

.search-results-list {
    padding: 0.5rem;
}

.search-result-item {
    padding: 0.5rem;
    border-radius: 0.25rem;
    cursor: pointer;
    transition: background-color 0.2s;
    border-bottom: 1px solid #f3f4f6;
}

.search-result-item:hover {
    background-color: #f9fafb;
}

.search-result-item:last-child {
    border-bottom: none;
}

.search-result-name {
    font-weight: 600;
    color: #374151;
    font-size: 0.875rem;
}

.search-result-category {
    font-size: 0.75rem;
    color: #6b7280;
}

.search-result-price {
    font-size: 0.75rem;
    color: #059669;
    font-weight: 500;
}

.no-results {
    padding: 1rem;
    text-align: center;
    color: #6b7280;
    font-size: 0.875rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('quickSearchInput');
    const searchBtn = document.getElementById('quickSearchBtn');
    const searchResults = document.getElementById('searchResults');
    const searchResultsList = document.getElementById('searchResultsList');
    
    let searchTimeout;
    
    // Debounced search
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();
        
        if (query.length >= 2) {
            searchTimeout = setTimeout(() => {
                performSearch(query);
            }, 300);
        } else {
            hideResults();
        }
    });
    
    // Search button click
    searchBtn.addEventListener('click', function() {
        const query = searchInput.value.trim();
        if (query) {
            window.location.href = `/products/search?q=${encodeURIComponent(query)}`;
        }
    });
    
    // Enter key press
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            const query = this.value.trim();
            if (query) {
                window.location.href = `/products/search?q=${encodeURIComponent(query)}`;
            }
        }
    });
    
    // Hide results when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.quick-search-container')) {
            hideResults();
        }
    });
    
    function performSearch(query) {
        fetch(`/products/search?q=${encodeURIComponent(query)}&ajax=1`)
            .then(response => response.json())
            .then(data => {
                displayResults(data.products, query);
            })
            .catch(error => {
                console.error('Erro na busca:', error);
                hideResults();
            });
    }
    
    function displayResults(products, query) {
        if (products.length === 0) {
            searchResultsList.innerHTML = '<div class="no-results">Nenhum produto encontrado</div>';
        } else {
            searchResultsList.innerHTML = products.map(product => `
                <div class="search-result-item" onclick="viewProduct(${product.id})">
                    <div class="search-result-name">${highlightMatch(product.name, query)}</div>
                    ${product.category ? `<div class="search-result-category">${product.category}</div>` : ''}
                    ${product.last_price > 0 ? `<div class="search-result-price">R$ ${product.last_price.toFixed(2).replace('.', ',')}</div>` : ''}
                </div>
            `).join('');
        }
        showResults();
    }
    
    function highlightMatch(text, query) {
        const regex = new RegExp(`(${query})`, 'gi');
        return text.replace(regex, '<span class="search-highlight">$1</span>');
    }
    
    function showResults() {
        searchResults.style.display = 'block';
    }
    
    function hideResults() {
        searchResults.style.display = 'none';
    }
});

function viewProduct(productId) {
    window.location.href = `/products/${productId}`;
}
</script>
