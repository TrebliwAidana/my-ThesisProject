import './bootstrap';
import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
Alpine.plugin(collapse);

window.Alpine = Alpine;
Alpine.start();

// ============================================
// NOTIFICATION MANAGER
// ============================================
class NotificationManager {
    constructor() {
        this.container = document.getElementById('notification-container');
        this.notifications = [];
        this.init();
    }
    
    init() {
        this.checkFlashData();
        
        // Handle page restore from cache (back/forward)
        window.addEventListener('pageshow', (event) => {
            if (event.persisted) {
                this.clearAll();
                this.checkFlashData();
            }
        });
        
        // Clear on page unload
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
            } catch(e) {
                console.error('Failed to parse flash data:', e);
            }
        }
    }
    
    show(type, message, duration = 5000) {
        // Prevent duplicate notifications
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
        
        const titles = {
            success: 'Success!',
            error: 'Error!',
            warning: 'Warning!',
            info: 'Information'
        };
        
        const color = colors[type] || 'blue';
        const title = titles[type] || 'Notification';
        
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
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">${title}</p>
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

// Add CSS animations
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

// Initialize notification manager
window.notificationManager = new NotificationManager();

// Global notification helper
window.notify = {
    success: (msg) => window.notificationManager?.show('success', msg),
    error: (msg) => window.notificationManager?.show('error', msg),
    warning: (msg) => window.notificationManager?.show('warning', msg),
    info: (msg) => window.notificationManager?.show('info', msg)
};

// ============================================
// GLOBAL ANIMATIONS
// ============================================
window.animations = {
    // Animate elements on scroll
    initScrollAnimations() {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });
        
        document.querySelectorAll('.stagger-item, .timeline-item, .card-enter, .fade-in-up').forEach(el => {
            observer.observe(el);
        });
    },
    
    // Add hover animations to buttons
    initButtons() {
        document.querySelectorAll('button:not(.no-animation), a.btn, .btn-primary, .btn-secondary').forEach(btn => {
            btn.classList.add('transition-all', 'duration-200', 'transform', 'hover:scale-105', 'active:scale-95');
        });
    },
    
    // Animate number counters
    animateNumbers() {
        document.querySelectorAll('.counter').forEach(counter => {
            const target = parseInt(counter.getAttribute('data-target'));
            const current = parseInt(counter.innerText);
            const increment = target / 50;
            
            const updateCount = () => {
                if (current < target) {
                    counter.innerText = Math.ceil(current + increment);
                    setTimeout(updateCount, 20);
                } else {
                    counter.innerText = target;
                }
            };
            
            updateCount();
        });
    },
    
    // Show toast notification
    showToast(message, type = 'success') {
        window.notificationManager.show(type, message);
    },
    
    // Initialize all animations
    init() {
        this.initScrollAnimations();
        this.initButtons();
        this.animateNumbers();
    }
};

// ============================================
// ALPINE COMPONENTS
// ============================================
document.addEventListener('alpine:init', () => {
    // Position Change Tracking Component
    Alpine.data('positionChangeHandler', (memberData = {}) => ({
        memberId: memberData.id || null,
        originalPosition: memberData.position || '',
        selectedPosition: memberData.position || '',
        reason: '',
        confirmChange: false,
        isSubmitting: false,
        historyModalOpen: false,
        historyLoading: false,
        historyData: [],
        
        get isPositionChanged() {
            return this.selectedPosition !== this.originalPosition;
        },
        
        get canSubmit() {
            if (!this.isPositionChanged) return true;
            return this.reason.trim().length > 0 && this.confirmChange;
        },
        
        get changePreview() {
            if (!this.isPositionChanged) return null;
            return { from: this.originalPosition, to: this.selectedPosition };
        },
        
        init() {
            if (this.originalPosition) {
                this.selectedPosition = this.originalPosition;
            }
            this.$nextTick(() => {
                const container = this.$el;
                if (container) container.classList.add('fade-in-up');
            });
        },
        
        resetForm() {
            this.selectedPosition = this.originalPosition;
            this.reason = '';
            this.confirmChange = false;
        },
        
        async submitForm() {
            if (!this.canSubmit) return;
            this.isSubmitting = true;
            try {
                const form = document.getElementById('editMemberForm');
                if (form) form.submit();
            } catch (error) {
                window.animations.showToast('Error updating member: ' + error.message, 'error');
                this.isSubmitting = false;
            }
        },
        
        async openHistoryModal() {
            this.historyModalOpen = true;
            await this.loadHistoryData();
        },
        
        closeHistoryModal() {
            this.historyModalOpen = false;
        },
        
        async loadHistoryData() {
            this.historyLoading = true;
            try {
                const response = await fetch(`/members/${this.memberId}/position-history-data`);
                this.historyData = await response.json();
            } catch (error) {
                console.error('Error loading history:', error);
                window.animations.showToast('Failed to load position history', 'error');
            } finally {
                this.historyLoading = false;
            }
        },
        
        showToast(message, type = 'success') {
            window.animations.showToast(message, type);
        },
        
        getBadgeClass(position) {
            const classes = {
                'member': 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/50 dark:text-indigo-400',
                'officer': 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-400',
                'adviser': 'bg-orange-100 text-orange-800 dark:bg-orange-900/50 dark:text-orange-400',
                'admin': 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-400'
            };
            return classes[position] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400';
        },
        
        getTimelineBadgeClass(oldPosition, newPosition) {
            if (newPosition === 'admin') return 'bg-red-500';
            if (oldPosition === 'member' && newPosition === 'officer') return 'bg-emerald-500';
            if (oldPosition === 'officer' && newPosition === 'member') return 'bg-amber-500';
            if (newPosition === 'adviser') return 'bg-orange-500';
            return 'bg-indigo-500';
        },
        
        formatDate(date) {
            if (!date) return 'N/A';
            const d = new Date(date);
            return d.toLocaleDateString('en-US', { 
                month: 'short', 
                day: 'numeric', 
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        },
        
        timeAgo(date) {
            if (!date) return '';
            const seconds = Math.floor((new Date() - new Date(date)) / 1000);
            const intervals = { year: 31536000, month: 2592000, week: 604800, day: 86400, hour: 3600, minute: 60 };
            for (const [unit, secondsInUnit] of Object.entries(intervals)) {
                const interval = Math.floor(seconds / secondsInUnit);
                if (interval >= 1) return `${interval} ${unit}${interval === 1 ? '' : 's'} ago`;
            }
            return 'just now';
        }
    }));
    
    // Timeline Observer Component
    Alpine.data('timelineObserver', () => ({
        init() {
            this.$nextTick(() => {
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('visible');
                            observer.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.1 });
                document.querySelectorAll('.timeline-item').forEach(item => observer.observe(item));
            });
        }
    }));
    
    // Card Animation Component
    Alpine.data('cardAnimation', () => ({
        visible: false,
        init() {
            this.$nextTick(() => {
                this.visible = true;
            });
        }
    }));
    
    // Stagger Table Component
    Alpine.data('staggerTable', () => ({
        rows: [],
        init() {
            this.rows = Array.from(this.$el.querySelectorAll('tbody tr'));
            this.rows.forEach((row, index) => {
                row.style.opacity = '0';
                row.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    row.style.transition = 'all 0.4s cubic-bezier(0.4, 0, 0.2, 1)';
                    row.style.opacity = '1';
                    row.style.transform = 'translateY(0)';
                }, index * 50);
            });
        }
    }));
});

// ============================================
// THEME STORE
// ============================================
document.addEventListener('alpine:init', () => {
    Alpine.store('theme', {
        dark: localStorage.getItem('dark') === 'true',
        toggle() {
            this.dark = !this.dark;
            localStorage.setItem('dark', this.dark);
            document.documentElement.classList.toggle('dark', this.dark);
        },
        init() {
            if (this.dark) document.documentElement.classList.add('dark');
        }
    });
    
    Alpine.store('theme').init();
});

// ============================================
// INITIALIZE ALPINE AND ANIMATIONS
// ============================================
Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    window.animations.init();
    
    const mainContent = document.querySelector('main');
    if (mainContent) mainContent.classList.add('page-transition');
    
    document.querySelectorAll('.card').forEach((card, index) => {
        setTimeout(() => card.classList.add('card-enter'), index * 100);
    });
});

export default window.animations;