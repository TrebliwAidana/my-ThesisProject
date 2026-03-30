// Animation Helper Functions for VSULHS_SSLG
export const animations = {
    // Track if animations have been initialized
    _initialized: false,
    _observers: [],
    
    // Clean up all observers
    cleanup() {
        // Disconnect all IntersectionObservers
        this._observers.forEach(observer => {
            if (observer && observer.disconnect) {
                observer.disconnect();
            }
        });
        this._observers = [];
        
        // Remove any pending timeouts
        if (this._timeouts) {
            this._timeouts.forEach(timeout => clearTimeout(timeout));
            this._timeouts = [];
        }
    },
    
    // Animate elements on scroll with cleanup
    initScrollAnimations() {
        // Clean up existing observers first
        this.cleanup();
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });
        
        this._observers.push(observer);
        
        document.querySelectorAll('.stagger-item, .timeline-item, .card-enter, .fade-in-up').forEach(el => {
            observer.observe(el);
        });
    },
    
    // Animate number counters with cleanup
    animateNumbers() {
        document.querySelectorAll('.counter').forEach(counter => {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const updateCount = () => {
                            const target = parseInt(counter.getAttribute('data-target'));
                            const current = parseInt(counter.innerText);
                            const increment = target / 50;
                            
                            if (current < target) {
                                counter.innerText = Math.ceil(current + increment);
                                setTimeout(updateCount, 20);
                            } else {
                                counter.innerText = target.toLocaleString();
                            }
                        };
                        updateCount();
                        observer.unobserve(entry.target);
                    }
                });
            });
            this._observers.push(observer);
            observer.observe(counter);
        });
    },
    
    // Show toast notification with cleanup tracking
    showToast(message, type = 'success', duration = 5000) {
        // Remove any existing toasts first
        const existingToasts = document.querySelectorAll('.toast-notification');
        existingToasts.forEach(toast => toast.remove());
        
        const toast = document.createElement('div');
        toast.className = 'fixed bottom-6 right-6 z-50 animate-slide-in-right toast-notification';
        
        const icons = {
            success: 'M5 13l4 4L19 7',
            error: 'M6 18L18 6M6 6l12 12',
            warning: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
            info: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
        };
        
        const colors = {
            success: 'bg-green-500',
            error: 'bg-red-500',
            warning: 'bg-amber-500',
            info: 'bg-blue-500'
        };
        
        toast.innerHTML = `
            <div class="${colors[type] || colors.success} text-white rounded-xl shadow-lg p-4 flex items-center justify-between gap-3 min-w-[320px] max-w-md">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${icons[type] || icons.success}"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold">${type === 'success' ? 'Success!' : type === 'error' ? 'Error!' : type === 'warning' ? 'Warning!' : 'Info'}</p>
                        <p class="text-xs opacity-90">${message}</p>
                    </div>
                </div>
                <button onclick="this.closest('.toast-notification')?.remove()" class="text-white hover:text-gray-200 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        `;
        
        document.body.appendChild(toast);
        
        const progressBar = document.createElement('div');
        progressBar.className = 'h-1 bg-white/30 rounded-b-xl';
        progressBar.style.width = '100%';
        progressBar.style.transition = `width ${duration}ms linear`;
        toast.querySelector('div').appendChild(progressBar);
        
        setTimeout(() => {
            progressBar.style.width = '0%';
        }, 10);
        
        const timeoutId = setTimeout(() => {
            if (toast && toast.parentElement) {
                toast.style.animation = 'slideOutRight 0.3s ease-in forwards';
                setTimeout(() => toast.remove(), 300);
            }
        }, duration);
        
        // Track timeout for cleanup
        if (!this._timeouts) this._timeouts = [];
        this._timeouts.push(timeoutId);
    },
    
    // Add click ripple effect
    addRippleEffect(event) {
        const button = event.currentTarget;
        const ripple = document.createElement('span');
        const rect = button.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = event.clientX - rect.left - size / 2;
        const y = event.clientY - rect.top - size / 2;
        
        ripple.style.width = ripple.style.height = `${size}px`;
        ripple.style.left = `${x}px`;
        ripple.style.top = `${y}px`;
        ripple.className = 'ripple-effect';
        
        button.style.position = 'relative';
        button.style.overflow = 'hidden';
        button.appendChild(ripple);
        
        setTimeout(() => ripple.remove(), 600);
    },
    
    // Add hover animation to buttons
    initButtons() {
        document.querySelectorAll('button:not([disabled]), a.btn, .btn-hover').forEach(btn => {
            btn.classList.add('transition-all', 'duration-200', 'hover:scale-105', 'active:scale-95');
            btn.removeEventListener('click', this.addRippleEffect);
            btn.addEventListener('click', this.addRippleEffect.bind(this));
        });
    },
    
    // Animate table rows
    initTableRows() {
        document.querySelectorAll('tbody tr').forEach((row, index) => {
            row.style.animationDelay = `${index * 0.05}s`;
            row.classList.add('stagger-item');
        });
    },
    
    // Add loading skeleton
    showSkeleton(container, count = 3) {
        const skeleton = document.createElement('div');
        skeleton.className = 'space-y-3';
        for (let i = 0; i < count; i++) {
            const item = document.createElement('div');
            item.className = 'skeleton h-12 rounded-lg';
            skeleton.appendChild(item);
        }
        container.innerHTML = '';
        container.appendChild(skeleton);
    },
    
    // Hide skeleton and show content
    hideSkeleton(container, content) {
        container.innerHTML = '';
        container.appendChild(content);
        container.classList.add('fade-in-up');
    },
    
    // Confirmation dialog
    confirm(message, options = {}) {
        return new Promise((resolve) => {
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm';
            modal.innerHTML = `
                <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-md w-full p-6 modal-enter">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-full ${options.type === 'danger' ? 'bg-red-100 dark:bg-red-900/30' : 'bg-amber-100 dark:bg-amber-900/30'} flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 ${options.type === 'danger' ? 'text-red-600' : 'text-amber-600'}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                ${options.type === 'danger' ? 
                                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>' :
                                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>'
                                }
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">${options.title || 'Confirm Action'}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">${message}</p>
                        </div>
                    </div>
                    <div class="flex gap-3 mt-6">
                        <button class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                            Cancel
                        </button>
                        <button class="flex-1 px-4 py-2 text-sm font-medium text-white ${options.type === 'danger' ? 'bg-red-600 hover:bg-red-700' : 'bg-indigo-600 hover:bg-indigo-700'} rounded-lg transition">
                            ${options.confirmText || 'Confirm'}
                        </button>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
            
            modal.querySelector('button:first-child').onclick = () => {
                modal.remove();
                resolve(false);
            };
            modal.querySelector('button:last-child').onclick = () => {
                modal.remove();
                resolve(true);
            };
            
            modal.onclick = (e) => {
                if (e.target === modal) {
                    modal.remove();
                    resolve(false);
                }
            };
        });
    },
    
    // Handle page cache/restore
    handlePageRestore() {
        // Clean up all animations
        this.cleanup();
        
        // Remove all toast notifications
        document.querySelectorAll('.toast-notification').forEach(toast => toast.remove());
        
        // Remove any leftover modals
        document.querySelectorAll('.fixed.inset-0.bg-black\\/50').forEach(modal => modal.remove());
        
        // Re-initialize animations
        setTimeout(() => {
            this.init();
        }, 100);
    },
    
    // Initialize all animations
    init() {
        // Don't re-initialize if already done
        if (this._initialized) return;
        this._initialized = true;
        
        this.initScrollAnimations();
        this.initButtons();
        this.initTableRows();
        this.animateNumbers();
        
        // Handle page cache (back/forward)
        window.addEventListener('pageshow', (event) => {
            if (event.persisted) {
                this.handlePageRestore();
            }
        });
    }
};

// Add CSS for animations
const style = document.createElement('style');
style.textContent = `
    .toast-enter {
        animation: slideInRight 0.3s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }
    
    .toast-leave {
        animation: slideOutRight 0.3s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }
    
    .ripple-effect {
        position: absolute;
        border-radius: 50%;
        background-color: rgba(255, 255, 255, 0.7);
        transform: scale(0);
        animation: ripple 0.6s linear;
        pointer-events: none;
    }
    
    @keyframes ripple {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
    
    .btn-click {
        transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .btn-click:active {
        transform: scale(0.95);
    }
    
    .stagger-item {
        opacity: 0;
        animation: fadeInUp 0.5s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }
    
    .timeline-item {
        opacity: 0;
        transform: translateY(20px);
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .timeline-item.visible {
        opacity: 1;
        transform: translateY(0);
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
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .modal-enter {
        animation: modalSlideIn 0.3s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }
    
    @keyframes modalSlideIn {
        from {
            opacity: 0;
            transform: scale(0.95) translateY(-20px);
        }
        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }
    
    .fade-in-up {
        animation: fadeInUp 0.5s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }
`;
document.head.appendChild(style);

// Auto-initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    animations.init();
});

export default animations;