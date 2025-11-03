<div class="notification notification-{{ $type }}" id="notification-{{ uniqid() }}">
    <div class="notification-content">
        <i class="bi bi-{{ $type === 'success' ? 'check-circle' : ($type === 'error' ? 'exclamation-triangle' : 'info-circle') }}"></i>
        <span class="notification-message">{{ $message }}</span>
        <button class="notification-close" onclick="this.parentElement.parentElement.remove()">
            <i class="bi bi-x"></i>
        </button>
    </div>
</div>

<style>
.notification {
    position: fixed;
    top: 1rem;
    right: 1rem;
    background: var(--notification-bg, #1f2937);
    color: white;
    padding: 1rem 1.5rem;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    z-index: 10000;
    max-width: 400px;
    animation: slideInRight 0.3s ease;
}

.notification-success {
    --notification-bg: #10b981;
}

.notification-error {
    --notification-bg: #ef4444;
}

.notification-info {
    --notification-bg: #3b82f6;
}

.notification-warning {
    --notification-bg: #f59e0b;
}

.notification-content {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.notification-message {
    flex: 1;
}

.notification-close {
    background: transparent;
    border: none;
    color: white;
    cursor: pointer;
    padding: 0.25rem;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0.8;
    transition: opacity 0.2s;
}

.notification-close:hover {
    opacity: 1;
}

@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}
</style>

