import './bootstrap';
import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';

Alpine.plugin(collapse);
window.Alpine = Alpine;

// ── Register ALL stores and components in ONE alpine:init ──────────────
document.addEventListener('alpine:init', () => {

    // ── Theme store ──────────────────────────────────────────────────────
    Alpine.store('theme', {
        dark: localStorage.getItem('dark') === 'true',
        toggle() {
            this.dark = !this.dark;
            localStorage.setItem('dark', this.dark);
            document.documentElement.classList.toggle('dark', this.dark);
        },
        init() {
            document.documentElement.classList.toggle('dark', this.dark);
        }
    });

    Alpine.store('theme').init();

    // ── positionChangeHandler component ──────────────────────────────────
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
            if (this.originalPosition) this.selectedPosition = this.originalPosition;
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
                'member':  'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/50 dark:text-indigo-400',
                'officer': 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-400',
                'adviser': 'bg-orange-100 text-orange-800 dark:bg-orange-900/50 dark:text-orange-400',
                'admin':   'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-400',
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
            return new Date(date).toLocaleDateString('en-US', {
                month: 'short', day: 'numeric', year: 'numeric',
                hour: '2-digit', minute: '2-digit',
            });
        },
        timeAgo(date) {
            if (!date) return '';
            const seconds = Math.floor((new Date() - new Date(date)) / 1000);
            const intervals = {
                year: 31536000, month: 2592000, week: 604800,
                day: 86400, hour: 3600, minute: 60,
            };
            for (const [unit, s] of Object.entries(intervals)) {
                const n = Math.floor(seconds / s);
                if (n >= 1) return `${n} ${unit}${n === 1 ? '' : 's'} ago`;
            }
            return 'just now';
        },
    }));

    // ── timelineObserver component ───────────────────────────────────────
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
        },
    }));

    // ── cardAnimation component ──────────────────────────────────────────
    Alpine.data('cardAnimation', () => ({
        visible: false,
        init() {
            this.$nextTick(() => { this.visible = true; });
        },
    }));

    // ── staggerTable component ───────────────────────────────────────────
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
        },
    }));

});

// ── Start Alpine AFTER all registrations ───────────────────────────────
Alpine.start();