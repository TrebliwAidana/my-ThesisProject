// Animation Helper Functions
export const animations = {
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
        
        document.querySelectorAll('.stagger-item, .timeline-item').forEach(el => {
            observer.observe(el);
        });
    },
    
    // Animate number counters
    animateNumbers() {
        document.querySelectorAll('.counter').forEach(counter => {
            const updateCount = () => {
                const target = parseInt(counter.getAttribute('data-target'));
                const current = parseInt(counter.innerText);
                const increment = target / 50;
                
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
        const toast = document.createElement('div');
        toast.className = 'fixed bottom-6 right-6 z-50 toast-enter';
        toast.innerHTML = `
            <div class="${type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500'} 
                        text-white rounded-lg shadow-lg p-4 flex items-center justify-between gap-3 min-w-[300px]">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        ${type === 'success' ? 
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>' :
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>'
                        }
                    </svg>
                    <p class="text-sm font-medium">${message}</p>
                </div>
                <button onclick="this.closest('.toast-notification')?.remove()" class="text-white hover:text-gray-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        `;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            if (toast && toast.parentElement) {
                toast.classList.remove('toast-enter');
                toast.classList.add('toast-leave');
                setTimeout(() => toast.remove(), 300);
            }
        }, 5000);
    },
    
    // Add hover animation to buttons
    initButtons() {
        document.querySelectorAll('button, a.btn').forEach(btn => {
            btn.classList.add('btn-click');
        });
    },
    
    // Initialize all animations
    init() {
        this.initScrollAnimations();
        this.initButtons();
    }
};

// Auto-initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    animations.init();
});

export default animations;