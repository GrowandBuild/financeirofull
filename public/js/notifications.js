/**
 * Sistema de Notificações
 * Substitui alert() e confirm() por notificações elegantes
 */

class NotificationSystem {
    constructor() {
        this.notifications = [];
        this.container = null;
        this.init();
    }

    init() {
        // Criar container de notificações se não existir
        if (!document.getElementById('notification-container')) {
            this.container = document.createElement('div');
            this.container.id = 'notification-container';
            this.container.style.cssText = `
                position: fixed;
                top: 1rem;
                right: 1rem;
                z-index: 10000;
                display: flex;
                flex-direction: column;
                gap: 0.75rem;
                max-width: 400px;
            `;
            document.body.appendChild(this.container);
        } else {
            this.container = document.getElementById('notification-container');
        }
    }

    show(message, type = 'info', duration = 4000) {
        const notification = this.createNotification(message, type);
        this.container.appendChild(notification);
        this.notifications.push(notification);

        // Animar entrada
        requestAnimationFrame(() => {
            notification.style.animation = 'slideInRight 0.3s ease';
            notification.style.opacity = '1';
            notification.style.transform = 'translateX(0)';
        });

        // Auto remover
        if (duration > 0) {
            setTimeout(() => {
                this.remove(notification);
            }, duration);
        }

        return notification;
    }

    success(message, duration = 4000) {
        return this.show(message, 'success', duration);
    }

    error(message, duration = 5000) {
        return this.show(message, 'error', duration);
    }

    info(message, duration = 4000) {
        return this.show(message, 'info', duration);
    }

    warning(message, duration = 4500) {
        return this.show(message, 'warning', duration);
    }

    createNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        
        const icons = {
            success: 'check-circle',
            error: 'exclamation-triangle',
            info: 'info-circle',
            warning: 'alert-triangle'
        };

        const colors = {
            success: '#10b981',
            error: '#ef4444',
            info: '#3b82f6',
            warning: '#f59e0b'
        };

        notification.innerHTML = `
            <div class="notification-content">
                <i class="bi bi-${icons[type]}"></i>
                <span class="notification-message">${this.escapeHtml(message)}</span>
                <button class="notification-close" onclick="window.notificationSystem.remove(this.parentElement.parentElement)">
                    <i class="bi bi-x"></i>
                </button>
            </div>
        `;

        notification.style.cssText = `
            background: ${colors[type]};
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            opacity: 0;
            transform: translateX(100%);
            transition: all 0.3s ease;
        `;

        // Estilos inline do conteúdo
        const content = notification.querySelector('.notification-content');
        content.style.cssText = `
            display: flex;
            align-items: center;
            gap: 0.75rem;
        `;

        const messageEl = notification.querySelector('.notification-message');
        messageEl.style.cssText = `
            flex: 1;
            line-height: 1.5;
        `;

        const closeBtn = notification.querySelector('.notification-close');
        closeBtn.style.cssText = `
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
            font-size: 1.2rem;
        `;
        closeBtn.onmouseover = () => closeBtn.style.opacity = '1';
        closeBtn.onmouseout = () => closeBtn.style.opacity = '0.8';

        return notification;
    }

    remove(notification) {
        if (!notification || !notification.parentElement) return;
        
        notification.style.animation = 'slideOutRight 0.3s ease';
        notification.style.opacity = '0';
        notification.style.transform = 'translateX(100%)';
        
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
            this.notifications = this.notifications.filter(n => n !== notification);
        }, 300);
    }

    confirm(message, title = 'Confirmação') {
        return new Promise((resolve) => {
            const modal = this.createConfirmModal(message, title, resolve);
            document.body.appendChild(modal);
            
            requestAnimationFrame(() => {
                modal.style.opacity = '1';
                const content = modal.querySelector('.confirm-modal-content');
                content.style.transform = 'scale(1)';
            });
        });
    }

    createConfirmModal(message, title, callback) {
        const modal = document.createElement('div');
        modal.className = 'confirm-modal';
        modal.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 10001;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        `;

        const content = document.createElement('div');
        content.className = 'confirm-modal-content';
        content.style.cssText = `
            background: #1f2937;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 2rem;
            max-width: 400px;
            width: 90%;
            transform: scale(0.9);
            transition: transform 0.3s ease;
        `;

        content.innerHTML = `
            <h3 style="color: white; margin: 0 0 1rem 0; font-size: 1.25rem; font-weight: 600;">${this.escapeHtml(title)}</h3>
            <p style="color: rgba(255, 255, 255, 0.8); margin: 0 0 1.5rem 0; line-height: 1.6;">${this.escapeHtml(message)}</p>
            <div style="display: flex; gap: 0.75rem; justify-content: flex-end;">
                <button class="confirm-btn cancel" style="
                    background: rgba(255, 255, 255, 0.1);
                    border: 1px solid rgba(255, 255, 255, 0.2);
                    color: white;
                    padding: 0.75rem 1.5rem;
                    border-radius: 8px;
                    cursor: pointer;
                    font-weight: 500;
                    transition: all 0.2s;
                ">Cancelar</button>
                <button class="confirm-btn confirm" style="
                    background: #10b981;
                    border: none;
                    color: white;
                    padding: 0.75rem 1.5rem;
                    border-radius: 8px;
                    cursor: pointer;
                    font-weight: 500;
                    transition: all 0.2s;
                ">Confirmar</button>
            </div>
        `;

        const buttons = content.querySelectorAll('.confirm-btn');
        buttons[0].addEventListener('click', () => {
            modal.remove();
            callback(false);
        });
        buttons[1].addEventListener('click', () => {
            modal.remove();
            callback(true);
        });

        modal.appendChild(content);
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.remove();
                callback(false);
            }
        });

        // Adicionar hover effects
        buttons.forEach(btn => {
            btn.onmouseover = () => {
                if (btn.classList.contains('cancel')) {
                    btn.style.background = 'rgba(255, 255, 255, 0.15)';
                } else {
                    btn.style.background = '#059669';
                }
            };
            btn.onmouseout = () => {
                if (btn.classList.contains('cancel')) {
                    btn.style.background = 'rgba(255, 255, 255, 0.1)';
                } else {
                    btn.style.background = '#10b981';
                }
            };
        });

        return modal;
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Adicionar animações CSS
if (!document.getElementById('notification-styles')) {
    const style = document.createElement('style');
    style.id = 'notification-styles';
    style.textContent = `
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
        
        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(style);
}

// Inicializar sistema de notificações
window.notificationSystem = new NotificationSystem();

// Funções globais para compatibilidade
window.showNotification = (message, type, duration) => {
    return window.notificationSystem.show(message, type, duration);
};

window.showSuccess = (message, duration) => {
    return window.notificationSystem.success(message, duration);
};

window.showError = (message, duration) => {
    return window.notificationSystem.error(message, duration);
};

window.showInfo = (message, duration) => {
    return window.notificationSystem.info(message, duration);
};

window.showWarning = (message, duration) => {
    return window.notificationSystem.warning(message, duration);
};

window.confirmAction = async (message, title) => {
    return await window.notificationSystem.confirm(message, title);
};

