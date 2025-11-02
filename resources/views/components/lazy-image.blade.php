@props([
    'src' => '',
    'alt' => '',
    'class' => '',
    'width' => '100%',
    'height' => 'auto',
    'placeholder' => '/images/no-image.png'
])

@php
    // Detectar se é WebP suportado
    $webpSrc = str_replace(['.jpg', '.jpeg', '.png'], '.webp', $src);
    $isWebP = file_exists(public_path(str_replace('/storage/', 'storage/', $webpSrc)));
@endphp

<div class="lazy-image-container {{ $class }}" style="width: {{ $width }}; height: {{ $height }};">
    <img 
        class="lazy-image" 
        data-src="{{ $isWebP ? $webpSrc : $src }}"
        src="{{ $placeholder }}"
        alt="{{ $alt }}"
        loading="lazy"
        style="width: 100%; height: 100%; object-fit: cover; transition: opacity 0.3s ease;"
        onload="this.style.opacity='1'"
        onerror="this.src='{{ $placeholder }}'"
    />
    <div class="lazy-loading" style="
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: #9ca3af;
        font-size: 0.75rem;
        display: none;
    ">
        <i class="bi bi-hourglass-split"></i> Carregando...
    </div>
</div>

<style>
.lazy-image-container {
    position: relative;
    background: #f3f4f6;
    border-radius: 0.5rem;
    overflow: hidden;
}

.lazy-image {
    opacity: 0;
    transition: opacity 0.3s ease;
}

.lazy-image.loaded {
    opacity: 1;
}

.lazy-image-container:hover .lazy-loading {
    display: block;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const lazyImages = document.querySelectorAll('.lazy-image');
    
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    const container = img.closest('.lazy-image-container');
                    const loading = container.querySelector('.lazy-loading');
                    
                    if (loading) loading.style.display = 'block';
                    
                    img.addEventListener('load', function() {
                        this.classList.add('loaded');
                        if (loading) loading.style.display = 'none';
                    });
                    
                    img.src = img.dataset.src;
                    observer.unobserve(img);
                }
            });
        }, {
            rootMargin: '50px 0px',
            threshold: 0.1
        });
        
        lazyImages.forEach(img => imageObserver.observe(img));
    } else {
        // Fallback para navegadores sem IntersectionObserver
        lazyImages.forEach(img => {
            img.src = img.dataset.src;
        });
    }
});
</script>
