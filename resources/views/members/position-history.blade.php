@extends('layouts.app')

@section('title', 'Position History - ' . ($member->user->name ?? $member->full_name ?? 'Member') . ' — VSULHS SSLG')
@section('page-title', 'Position History')

@push('styles')
<style>
/* ════════════════════════════════════════════════
   POSITION HISTORY — Emerald & Gold Luxury Theme
   Matching all management views
════════════════════════════════════════════════ */

[x-cloak] { display: none !important; }

/* ── Back Button ── */
.back-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.4rem 1rem;
    font-size: 0.75rem;
    font-weight: 500;
    background: transparent;
    color: var(--text-3);
    text-decoration: none;
    border-radius: 0.5rem;
    transition: all 0.18s ease;
}
.back-btn:hover {
    color: var(--gold-dark);
    background: rgba(212,175,55,0.08);
    transform: translateX(-2px);
}
html.dark .back-btn:hover {
    color: var(--gold-light);
}
.back-btn svg {
    width: 1rem;
    height: 1rem;
    stroke: currentColor;
    fill: none;
}

/* ── Header Section ── */
.history-header {
    margin-top: 0.5rem;
}
.history-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text);
    letter-spacing: -0.02em;
    font-family: 'DM Serif Display', serif;
}
.history-subtitle {
    font-size: 0.8rem;
    color: var(--text-3);
    margin-top: 0.25rem;
}
.history-subtitle span {
    color: var(--emerald-dark);
    font-weight: 600;
}
html.dark .history-subtitle span {
    color: var(--emerald-light);
}

/* ── Main Card ── */
.history-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 1.25rem;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
}
html.dark .history-card {
    box-shadow: 0 4px 20px rgba(0,0,0,0.22);
}
.history-card-body {
    padding: 1.5rem;
}

/* ── Loading State ── */
.loading-container {
    text-align: center;
    padding: 3rem;
}
.loading-spinner {
    width: 3rem;
    height: 3rem;
    margin: 0 auto;
    border: 3px solid var(--border);
    border-top-color: var(--emerald);
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}
@keyframes spin {
    to { transform: rotate(360deg); }
}
.loading-text {
    margin-top: 1rem;
    font-size: 0.8rem;
    color: var(--text-3);
}

/* ── History Item ── */
.history-item {
    margin-bottom: 1.5rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid var(--border);
}
.history-item:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.history-timeline {
    display: flex;
    gap: 1rem;
}
.timeline-icon {
    flex-shrink: 0;
}
.timeline-badge {
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 9999px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.timeline-badge svg {
    width: 1.25rem;
    height: 1.25rem;
    stroke: #fff;
}
.timeline-badge-promotion { background: linear-gradient(135deg, #059669, #10b981); }
.timeline-badge-demotion { background: linear-gradient(135deg, #d97706, #f59e0b); }
.timeline-badge-adviser { background: linear-gradient(135deg, #ea580c, #f97316); }
.timeline-badge-default { background: linear-gradient(135deg, var(--emerald), var(--emerald-dark)); }

/* ── Position Change Visual ── */
.position-change {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-bottom: 0.75rem;
}
.position-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.7rem;
    font-weight: 700;
    font-family: 'DM Mono', monospace;
}
.position-badge-from {
    background: var(--surface-3);
    color: var(--text-3);
    border: 1px solid var(--border);
}
.position-badge-to {
    background: rgba(5,150,105,0.1);
    color: var(--emerald-dark);
    border: 1px solid rgba(5,150,105,0.2);
}
html.dark .position-badge-to {
    background: rgba(16,185,129,0.15);
    color: #6ee7b7;
}
.position-arrow {
    color: var(--gold-dark);
    font-size: 0.75rem;
}
.position-arrow svg {
    width: 1rem;
    height: 1rem;
}

.latest-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.2rem 0.65rem;
    border-radius: 9999px;
    font-size: 0.65rem;
    font-weight: 700;
    background: linear-gradient(135deg, var(--emerald), var(--emerald-dark));
    color: #fff;
    font-family: 'DM Mono', monospace;
}

/* ── History Meta ── */
.history-meta {
    font-size: 0.7rem;
    color: var(--text-3);
    margin-bottom: 0.75rem;
    font-family: 'DM Mono', monospace;
}
.history-meta span {
    margin-right: 0.5rem;
}

/* ── Reason Box ── */
.reason-box {
    margin-top: 0.75rem;
    padding: 0.75rem 1rem;
    background: rgba(212,175,55,0.05);
    border-left: 3px solid var(--gold);
    border-radius: 0.5rem;
}
.reason-box p {
    font-size: 0.7rem;
    color: var(--gold-dark);
    line-height: 1.5;
}
.reason-box .reason-label {
    font-weight: 700;
    margin-right: 0.5rem;
}
html.dark .reason-box {
    background: rgba(212,175,55,0.08);
}
html.dark .reason-box p {
    color: var(--gold-light);
}

/* ── Empty State ── */
.empty-state {
    text-align: center;
    padding: 3rem 2rem;
}
.empty-icon {
    width: 5rem;
    height: 5rem;
    margin: 0 auto 1rem;
    border-radius: 50%;
    background: linear-gradient(135deg, rgba(5,150,105,0.1), rgba(212,175,55,0.08));
    border: 1.5px dashed rgba(212,175,55,0.3);
    display: flex;
    align-items: center;
    justify-content: center;
}
.empty-icon svg {
    width: 2.5rem;
    height: 2.5rem;
    stroke: var(--text-3);
}
.empty-title {
    font-size: 1rem;
    font-weight: 600;
    color: var(--text-2);
    margin-bottom: 0.25rem;
}
.empty-desc {
    font-size: 0.75rem;
    color: var(--text-3);
    margin-bottom: 1rem;
}
.btn-empty {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1.25rem;
    font-size: 0.75rem;
    font-weight: 700;
    background: linear-gradient(135deg, var(--emerald), var(--emerald-dark));
    color: #fff;
    border: none;
    border-radius: 0.65rem;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
}
.btn-empty:hover {
    background: linear-gradient(135deg, var(--gold), var(--gold-dark));
    color: #0f172a;
    transform: translateY(-1px);
}

/* ── Summary Statistics ── */
.summary-stats {
    margin-top: 1.5rem;
    padding-top: 1.5rem;
    border-top: 1px solid var(--border);
}
.summary-title {
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: var(--text-2);
    margin-bottom: 1rem;
    font-family: 'DM Mono', monospace;
}
.stats-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
}
@media (min-width: 768px) {
    .stats-grid {
        grid-template-columns: repeat(4, 1fr);
    }
}
.stat-item {
    text-align: center;
    padding: 0.75rem;
    background: var(--surface-2);
    border-radius: 0.75rem;
    border: 1px solid var(--border);
}
.stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    font-family: 'DM Mono', monospace;
    margin-bottom: 0.25rem;
}
.stat-value.total { color: var(--text); }
.stat-value.promotion { color: #059669; }
.stat-value.demotion { color: #d97706; }
.stat-value.adviser { color: #ea580c; }
.stat-label {
    font-size: 0.65rem;
    color: var(--text-3);
    font-family: 'DM Mono', monospace;
}
html.dark .stat-value.promotion { color: #6ee7b7; }
html.dark .stat-value.demotion { color: #fcd34d; }
html.dark .stat-value.adviser { color: #fdba74; }

/* ── Animations ── */
@keyframes slideInRight {
    from { transform: translateX(100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}
@keyframes slideOutRight {
    from { transform: translateX(0); opacity: 1; }
    to { transform: translateX(100%); opacity: 0; }
}
.animate-slide-in-right {
    animation: slideInRight 0.3s ease-out;
}
@keyframes fadeUp {
    from { opacity: 0; transform: translateY(14px); }
    to   { opacity: 1; transform: translateY(0); }
}
.anim-1 { animation: fadeUp 0.38s ease 0.04s both; }
.anim-2 { animation: fadeUp 0.38s ease 0.10s both; }
</style>
@endpush

@section('content')

<div x-data="positionHistoryComponent({{ $member->id ?? $member->user_id ?? 0 }})" x-init="loadHistory()" class="space-y-5">
    
    {{-- Back Button --}}
    <div class="anim-1">
        <a href="{{ route('members.edit', $member->id ?? $member->user_id) }}" class="back-btn">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Edit Member
        </a>
        
        <div class="history-header">
            <h1 class="history-title">Position Change History</h1>
            <p class="history-subtitle">
                {{ $member->user->full_name ?? $member->full_name ?? 'Member' }} 
                (Current: <span>{{ $member->position ?? $member->user->position ?? 'N/A' }}</span>)
            </p>
        </div>
    </div>

    {{-- Main Card --}}
    <div class="history-card anim-2">
        <div class="history-card-body">
            
            {{-- Loading State --}}
            <div x-show="loading" x-cloak>
                <div class="loading-container">
                    <div class="loading-spinner"></div>
                    <p class="loading-text">Loading position history...</p>
                </div>
            </div>
            
            {{-- History Content --}}
            <div x-show="!loading" x-cloak>
                
                {{-- History Items --}}
                <template x-for="(log, index) in historyData" :key="index">
                    <div class="history-item">
                        <div class="history-timeline">
                            <div class="timeline-icon">
                                <div class="timeline-badge" :class="getTimelineBadgeClass(log.old_position, log.new_position)">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                    </svg>
                                </div>
                            </div>
                            
                            <div class="flex-1">
                                <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
                                    <div class="position-change">
                                        <span class="position-badge position-badge-from" x-text="log.old_position || 'Not set'"></span>
                                        <span class="position-arrow">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                            </svg>
                                        </span>
                                        <span class="position-badge position-badge-to" x-text="log.new_position || 'Not set'"></span>
                                    </div>
                                    <span x-show="index === 0 && historyData.length > 0" class="latest-badge">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                        </svg>
                                        Latest Change
                                    </span>
                                </div>
                                
                                <div class="history-meta">
                                    <span>👤 Changed by: <strong x-text="log.changer?.full_name || log.changer?.name || 'System'"></strong></span>
                                    <span>📅 <span x-text="formatDate(log.created_at)"></span> (<span x-text="timeAgo(log.created_at)"></span>)</span>
                                    <span x-show="log.ip_address">🌐 IP: <span x-text="log.ip_address"></span></span>
                                </div>
                                
                                <div x-show="log.reason" class="reason-box">
                                    <p>
                                        <span class="reason-label">📝 Reason:</span>
                                        <span x-text="log.reason"></span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
                
                {{-- Empty State --}}
                <div x-show="historyData.length === 0" x-cloak>
                    <div class="empty-state">
                        <div class="empty-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="empty-title">No Position Changes Yet</h3>
                        <p class="empty-desc">This member hasn't had any position changes recorded.</p>
                        <a href="{{ route('members.edit', $member->id ?? $member->user_id) }}" class="btn-empty">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Edit Member
                        </a>
                    </div>
                </div>
                
                {{-- Summary Statistics --}}
                <div x-show="historyData.length > 0" x-cloak class="summary-stats">
                    <h4 class="summary-title">📊 Summary Statistics</h4>
                    <div class="stats-grid">
                        <div class="stat-item">
                            <div class="stat-value total" x-text="historyData.length"></div>
                            <div class="stat-label">Total Changes</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value promotion" x-text="historyData.filter(l => l.old_position && l.old_position !== '—' && l.new_position && l.new_position !== '—').length"></div>
                            <div class="stat-label">Position Changes</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value demotion" x-text="historyData.filter(l => l.old_position && l.old_position !== '—' && !l.new_position || l.new_position === '—').length"></div>
                            <div class="stat-label">Position Removed</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value adviser" x-text="historyData.filter(l => l.old_position && l.old_position !== l.new_position).length"></div>
                            <div class="stat-label">Distinct Changes</div>
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
            
            setTimeout(() => {
                if (toast && toast.parentElement) {
                    toast.style.animation = 'slideOutRight 0.3s ease-out';
                    setTimeout(() => toast.remove(), 300);
                }
            }, 5000);
        },
        
        getBadgeClass(position) {
            if (!position || position === '—') return 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400';
            
            const pos = position.toLowerCase();
            
            // Match based on your role structure
            if (pos === 'system administrator') return 'bg-purple-100 text-purple-800 dark:bg-purple-900/50 dark:text-purple-400';
            if (pos === 'club adviser') return 'bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-400';
            if (pos === 'treasurer') return 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-400';
            if (pos === 'auditor') return 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-400';
            if (pos === 'guest') return 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400';
            
            return 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400';
        },
        
        getTimelineBadgeClass(oldPosition, newPosition) {
            if (!oldPosition || !newPosition) return 'timeline-badge-default';
            if (oldPosition === '—' || newPosition === '—') return 'timeline-badge-default';
            
            const oldPos = oldPosition.toLowerCase();
            const newPos = newPosition.toLowerCase();
            
            // Promotion: member/officer to higher position
            if ((oldPos.includes('member') || oldPos.includes('officer')) && 
                (newPos.includes('treasurer') || newPos.includes('auditor') || newPos.includes('adviser') || newPos.includes('administrator'))) {
                return 'timeline-badge-promotion';
            }
            // Demotion: higher position to member/officer
            if ((oldPos.includes('treasurer') || oldPos.includes('auditor') || oldPos.includes('adviser')) && 
                (newPos.includes('member') || newPos.includes('officer'))) {
                return 'timeline-badge-demotion';
            }
            // To Adviser
            if (newPos.includes('adviser')) return 'timeline-badge-adviser';
            
            return 'timeline-badge-default';
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

@endsection