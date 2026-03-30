// resources/js/notifications.js

class NotificationManager {
    constructor() {
        this.container = document.getElementById('notification-container');
        this.notifications = [];
        this.init();
    }
    
    init() {
        this.checkFlashData();
        
        window.addEventListener('pageshow', (event) => {
            if (event.persisted) {
                this.clearAll();
                this.checkFlashData();
            }
        });
        
        window.addEventListener('beforeunload', () => {
            this.clearAll();
        });
    }
    
    checkFlashData() {
        const flashData = document.querySelector('meta[name="flash-data"]');
        if (flashData && flashData.content) {
            try {
                const data = JSON.parse(flashData.content);
                if (data.success) this.show('success', data.success);
                if (data.error) this.show('error', data.error);
                if (data.warning) this.show('warning', data.warning);
                if (data.info) this.show('info', data.info);
                flashData.remove();
            } catch(e) {}
        }
    }
    
    show(type, message, duration = 5000) {
        const existing = this.notifications.find(n => n.message === message && n.type === type);
        if (existing) return;
        
        const id = Date.now() + Math.random();
        const notification = this.createNotification(type, message, id);
        
        this.container.appendChild(notification);
        this.notifications.push({ id, type, message });
        
        const timeoutId = setTimeout(() => {
            this.remove(id);
        }, duration);
        
        this.notifications = this.notifications.map(n => 
            n.id === id ? { ...n, timeoutId } : n
        );
    }
    
    createNotification(type, message, id) {
        const colors = {
            success: 'green',
            error: 'red',
            warning: 'amber',
            info: 'blue'
        };
        
        const icons = {
            success: 'M5 13l4 4L19 7',
            error: 'M6 18L18 6M6 6l12 12',
            warning: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
            info: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
        };
        
        const color = colors[type] || 'blue';
        
        const div = document.createElement('div');
        div.className = `mb-2 rounded-xl shadow-lg overflow-hidden border-l-4 border-${color}-500 bg-white dark:bg-gray-800 transform transition-all duration-300`;
        div.setAttribute('data-notification-id', id);
        div.style.animation = 'slideInRight 0.3s cubic-bezier(0.4, 0, 0.2, 1) forwards';
        
        div.innerHTML = `
            <div class="p-4">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 rounded-full bg-${color}-100 dark:bg-${color}-900/50 flex items-center justify-center">
                            <svg class="w-4 h-4 text-${color}-600 dark:text-${color}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${icons[type]}"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">${type === 'success' ? 'Success!' : type === 'error' ? 'Error!' : type === 'warning' ? 'Warning!' : 'Information'}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-0.5">${this.escapeHtml(message)}</p>
                    </div>
                    <div class="flex-shrink-0">
                        <button onclick="window.notificationManager.remove(${id})" class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            <div class="h-1 bg-${color}-500 animate-progress" style="width: 100%; animation: progressShrink 5s linear forwards;"></div>
        `;
        
        return div;
    }
    
    remove(id) {
        const notification = this.container.querySelector(`[data-notification-id="${id}"]`);
        if (notification && notification.parentElement) {
            notification.style.animation = 'slideOutRight 0.3s cubic-bezier(0.4, 0, 0.2, 1) forwards';
            setTimeout(() => {
                if (notification && notification.parentElement) {
                    notification.remove();
                    this.notifications = this.notifications.filter(n => n.id !== id);
                }
            }, 300);
        }
    }
    
    clearAll() {
        this.notifications.forEach(n => {
            if (n.timeoutId) clearTimeout(n.timeoutId);
        });
        this.notifications = [];
        const notifications = this.container.querySelectorAll('[data-notification-id]');
        notifications.forEach(notification => notification.remove());
    }
    
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

const style = document.createElement('style');
style.textContent = `
    @keyframes progressShrink {
        from { width: 100%; }
        to { width: 0%; }
    }
    .animate-progress {
        animation: progressShrink 5s linear forwards;
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

// Initialize and expose globally
window.notificationManager = new NotificationManager();
window.notify = {
    success: (msg) => window.notificationManager?.show('success', msg),
    error: (msg) => window.notificationManager?.show('error', msg),
    warning: (msg) => window.notificationManager?.show('warning', msg),
    info: (msg) => window.notificationManager?.show('info', msg)
};

export default window.notificationManager;