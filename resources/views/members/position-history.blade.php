@extends('layouts.app')

@section('title', 'Position History - ' . ($member->user->name ?? $member->full_name ?? 'Member'))

@section('content')
<div x-data="positionHistoryComponent({{ $member->id ?? $member->user_id ?? 0 }})" x-init="loadHistory()">
    
    <div class="mb-6">
        <a href="{{ route('members.edit', $member->id ?? $member->user_id) }}" 
           class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Edit
        </a>
        
        <div class="mt-3">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Position Change History</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                {{ $member->user->full_name ?? $member->full_name ?? 'Member' }} (Current: {{ $member->position ?? $member->user->position ?? 'N/A' }})
            </p>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gold-200 dark:border-gold-800">
        <div class="p-6">
            <!-- Loading State -->
            <div x-show="loading" class="text-center py-12">
                <svg class="animate-spin h-12 w-12 mx-auto text-primary-600" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="mt-3 text-gray-500 dark:text-gray-400">Loading position history...</p>
            </div>
            
            <!-- History Content -->
            <div x-show="!loading">
                <!-- History Items -->
                <template x-for="(log, index) in historyData" :key="index">
                    <div class="mb-6 pb-6 border-b border-gold-200 dark:border-gold-800 last:border-0">
                        <div class="flex items-start gap-4">
                            <!-- Timeline Icon -->
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center shadow-lg" :class="getTimelineBadgeClass(log.old_position, log.new_position)">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                    </svg>
                                </div>
                            </div>
                            
                            <!-- Content -->
                            <div class="flex-1">
                                <div class="flex items-center justify-between flex-wrap gap-2 mb-2">
                                    <div class="flex items-center gap-2">
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold" :class="getBadgeClass(log.old_position)" x-text="log.old_position || 'Not set'"></span>
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                        </svg>
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold" :class="getBadgeClass(log.new_position)" x-text="log.new_position"></span>
                                    </div>
                                    <span x-show="index === 0 && historyData.length > 0" class="px-2 py-1 bg-emerald-500 text-white text-xs font-semibold rounded-full">Latest Change</span>
                                </div>
                                
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">
                                    <span class="font-medium">Changed by:</span> 
                                    <span x-text="log.changer?.name || 'System'"></span>
                                </p>
                                
                                <p class="text-xs text-gray-500 dark:text-gray-500 mb-2">
                                    <span x-text="formatDate(log.created_at)"></span>
                                    (<span x-text="timeAgo(log.created_at)"></span>)
                                    <span x-show="log.ip_address"> • IP: <span x-text="log.ip_address"></span></span>
                                </p>
                                
                                <div x-show="log.reason" class="mt-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg border-l-4 border-primary-500">
                                    <p class="text-sm text-gray-700 dark:text-gray-300">
                                        <span class="font-medium">Reason:</span> 
                                        <span x-text="log.reason"></span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
                
                <!-- Empty State -->
                <div x-show="historyData.length === 0" class="text-center py-12">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-1">No Position Changes Yet</h3>
                    <p class="text-gray-500 dark:text-gray-400">This member hasn't had any position changes recorded.</p>
                    <a href="{{ route('members.edit', $member->id ?? $member->user_id) }}" class="inline-flex items-center gap-2 mt-4 px-4 py-2 bg-primary-600 hover:bg-gold-500 text-white text-sm font-medium rounded-lg transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit Member
                    </a>
                </div>
                
                <!-- Summary Statistics -->
                <div x-show="historyData.length > 0" class="mt-8 pt-6 border-t border-gold-200 dark:border-gold-800">
                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Summary Statistics</h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="text-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <p class="text-2xl font-bold text-primary-600 dark:text-primary-400" x-text="historyData.length"></p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Total Changes</p>
                        </div>
                        <div class="text-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400" x-text="historyData.filter(l => l.old_position && l.old_position.toLowerCase().includes('member') && l.new_position && l.new_position.toLowerCase().includes('officer')).length"></p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Promotions</p>
                        </div>
                        <div class="text-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <p class="text-2xl font-bold text-amber-600 dark:text-amber-400" x-text="historyData.filter(l => l.old_position && l.old_position.toLowerCase().includes('officer') && l.new_position && l.new_position.toLowerCase().includes('member')).length"></p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Demotions</p>
                        </div>
                        <div class="text-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <p class="text-2xl font-bold text-orange-600 dark:text-orange-400" x-text="historyData.filter(l => l.new_position && l.new_position.toLowerCase().includes('adviser')).length"></p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">To Adviser</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function positionHistoryComponent(memberId) {
    return {
        memberId: memberId,
        historyData: [],
        loading: false,
        
        async loadHistory() {
            this.loading = true;
            try {
                const response = await fetch(`/members/${this.memberId}/position-history-data`);
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                const data = await response.json();
                this.historyData = data;
            } catch (error) {
                console.error('Error loading history:', error);
                this.showToast('Failed to load position history', 'error');
            } finally {
                this.loading = false;
            }
        },
        
        showToast(message, type) {
            // Create a nice toast notification
            const toast = document.createElement('div');
            toast.className = 'fixed bottom-6 right-6 z-50 animate-slide-in-right';
            toast.innerHTML = `
                <div class="${type === 'success' ? 'bg-emerald-500' : 'bg-red-500'} text-white rounded-lg shadow-lg p-4 flex items-center justify-between gap-3 min-w-[300px]">
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
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                if (toast && toast.parentElement) {
                    toast.style.animation = 'slideOutRight 0.3s ease-out';
                    setTimeout(() => toast.remove(), 300);
                }
            }, 5000);
        },
        
        getBadgeClass(position) {
            if (!position) return 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400';
            
            const pos = position.toLowerCase();
            const classes = {
                'system administrator': 'bg-gold-100 text-gold-800 dark:bg-gold-900/50 dark:text-gold-400',
                'supreme admin': 'bg-primary-100 text-primary-800 dark:bg-primary-900/50 dark:text-primary-400',
                'supreme officer': 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-400',
                'org admin': 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-400',
                'org officer': 'bg-sky-100 text-sky-800 dark:bg-sky-900/50 dark:text-sky-400',
                'club adviser': 'bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-400',
                'adviser': 'bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-400',
                'member': 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400'
            };
            
            for (const [key, value] of Object.entries(classes)) {
                if (pos.includes(key)) return value;
            }
            return classes.member;
        },
        
        getTimelineBadgeClass(oldPosition, newPosition) {
            if (!oldPosition || !newPosition) return 'bg-primary-500';
            
            const oldPos = oldPosition.toLowerCase();
            const newPos = newPosition.toLowerCase();
            
            if (newPos.includes('admin') || newPos.includes('system') || newPos.includes('supreme')) return 'bg-gold-500';
            if (oldPos.includes('member') && (newPos.includes('officer') || newPos.includes('admin'))) return 'bg-emerald-500';
            if ((oldPos.includes('officer') || oldPos.includes('admin')) && newPos.includes('member')) return 'bg-amber-500';
            if (newPos.includes('adviser')) return 'bg-orange-500';
            return 'bg-primary-500';
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
    }
}
</script>

<style>
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

.animate-slide-in-right {
    animation: slideInRight 0.3s ease-out;
}
</style>

@endsection