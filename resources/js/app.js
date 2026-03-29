import './bootstrap';
import Alpine from 'alpinejs'

window.Alpine = Alpine

// Position Change Tracking Alpine Component
document.addEventListener('alpine:init', () => {
    Alpine.data('positionChangeHandler', (memberData = {}) => ({
        // Data
        memberId: memberData.id || null,
        originalPosition: memberData.position || '',
        selectedPosition: memberData.position || '',
        reason: '',
        confirmChange: false,
        isSubmitting: false,
        
        // History Modal Data
        historyModalOpen: false,
        historyLoading: false,
        historyData: [],
        
        // Computed properties
        get isPositionChanged() {
            return this.selectedPosition !== this.originalPosition;
        },
        
        get canSubmit() {
            if (!this.isPositionChanged) return true;
            return this.reason.trim().length > 0 && this.confirmChange;
        },
        
        get changePreview() {
            if (!this.isPositionChanged) return null;
            return {
                from: this.originalPosition,
                to: this.selectedPosition
            };
        },
        
        // Methods
        init() {
            if (this.originalPosition) {
                this.selectedPosition = this.originalPosition;
            }
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
                if (form) {
                    form.submit();
                }
            } catch (error) {
                this.showToast('Error updating member: ' + error.message, 'error');
                this.isSubmitting = false;
            }
        },
        
        // History Modal Methods
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
                this.showToast('Failed to load position history', 'error');
            } finally {
                this.historyLoading = false;
            }
        },
        
        // Utility Methods
        showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = 'fixed bottom-6 right-6 z-50 animate-slide-in-right';
            toast.innerHTML = `
                <div class="${type === 'success' ? 'bg-emerald-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500'} text-white rounded-lg shadow-lg p-4 flex items-center justify-between gap-3 min-w-[300px]">
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
            toast.classList.add('toast-notification');
            document.body.appendChild(toast);
            
            setTimeout(() => {
                if (toast && toast.parentElement) {
                    toast.style.animation = 'slideOutRight 0.3s ease-out';
                    setTimeout(() => toast.remove(), 300);
                }
            }, 5000);
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
            
            const intervals = {
                year: 31536000,
                month: 2592000,
                week: 604800,
                day: 86400,
                hour: 3600,
                minute: 60
            };
            
            for (const [unit, secondsInUnit] of Object.entries(intervals)) {
                const interval = Math.floor(seconds / secondsInUnit);
                if (interval >= 1) {
                    return `${interval} ${unit}${interval === 1 ? '' : 's'} ago`;
                }
            }
            
            return 'just now';
        }
    }));
    
    // Timeline Observer for scroll animations
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
                
                document.querySelectorAll('.timeline-item').forEach(item => {
                    observer.observe(item);
                });
            });
        }
    }));
});

Alpine.start();