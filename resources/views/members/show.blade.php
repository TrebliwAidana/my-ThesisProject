@extends('layouts.app')

@section('title', $user->full_name . ' — Member Profile')
@section('page-title', 'Member Profile')

@push('styles')
<style>
/* ════════════════════════════════════════════════
   MEMBER PROFILE — Emerald & Gold Luxury Theme
   Matching all management views
════════════════════════════════════════════════ */

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

/* ── Profile Card ── */
.profile-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 1.25rem;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
}
html.dark .profile-card {
    box-shadow: 0 4px 20px rgba(0,0,0,0.22);
}

.profile-header {
    background: linear-gradient(135deg, #064E3B 0%, #047857 60%, #065F46 100%);
    padding: 1.5rem;
    text-align: center;
}
.profile-avatar {
    width: 6rem;
    height: 6rem;
    border-radius: 9999px;
    margin: 0 auto 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    box-shadow: 0 4px 16px rgba(0,0,0,0.15);
}
.profile-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.profile-avatar-placeholder {
    width: 100%;
    height: 100%;
    background: rgba(255,255,255,0.15);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    font-weight: 700;
    color: #fff;
    font-family: 'DM Mono', monospace;
}
.profile-name {
    font-size: 1.25rem;
    font-weight: 700;
    color: #fff;
    margin-bottom: 0.25rem;
}
.profile-position {
    font-size: 0.8rem;
    color: rgba(255,255,255,0.7);
    margin-bottom: 0.75rem;
}
.profile-badges {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    justify-content: center;
}
.badge-role {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.7rem;
    font-weight: 700;
    font-family: 'DM Mono', monospace;
}
.badge-role-admin { background: rgba(139,92,246,0.2); color: #c4b5fd; border: 1px solid rgba(139,92,246,0.3); }
.badge-role-adviser { background: rgba(245,158,11,0.2); color: #fcd34d; border: 1px solid rgba(245,158,11,0.3); }
.badge-role-treasurer { background: rgba(5,150,105,0.2); color: #6ee7b7; border: 1px solid rgba(5,150,105,0.3); }
.badge-role-auditor { background: rgba(59,130,246,0.2); color: #93c5fd; border: 1px solid rgba(59,130,246,0.3); }
.badge-role-guest { background: rgba(107,114,128,0.2); color: #cbd5e1; border: 1px solid rgba(107,114,128,0.3); }
.badge-abbr {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.7rem;
    font-family: 'DM Mono', monospace;
    background: rgba(255,255,255,0.1);
    color: rgba(255,255,255,0.7);
    border: 1px solid rgba(255,255,255,0.15);
}

/* ── Profile Info Section ── */
.profile-info {
    padding: 1.5rem;
}
.profile-info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid var(--border);
}
.profile-info-row:last-child {
    border-bottom: none;
}
.profile-info-label {
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--text-3);
    font-family: 'DM Mono', monospace;
}
.profile-info-value {
    font-size: 0.8rem;
    font-weight: 500;
    color: var(--text);
    text-align: right;
}

/* ── Status Badges ── */
.status-active {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.7rem;
    font-weight: 700;
    background: rgba(5,150,105,0.1);
    color: #047857;
    border: 1px solid rgba(5,150,105,0.2);
}
.status-inactive {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.7rem;
    font-weight: 700;
    background: rgba(220,38,38,0.1);
    color: #dc2626;
    border: 1px solid rgba(220,38,38,0.2);
}
html.dark .status-active {
    background: rgba(16,185,129,0.15);
    color: #6ee7b7;
}
html.dark .status-inactive {
    background: rgba(248,113,113,0.15);
    color: #fca5a5;
}

/* ── Action Buttons Section ── */
.profile-actions {
    padding: 1rem 1.5rem 1.5rem;
    border-top: 1px solid var(--border);
    background: var(--surface-2);
}
.btn-emerald {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.6rem 1rem;
    font-size: 0.8rem;
    font-weight: 700;
    background: linear-gradient(135deg, var(--emerald), var(--emerald-dark));
    color: #fff;
    border: none;
    border-radius: 0.75rem;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    font-family: 'Outfit', sans-serif;
    width: 100%;
}
.btn-emerald:hover {
    background: linear-gradient(135deg, var(--gold), var(--gold-dark));
    color: #0f172a;
    transform: translateY(-1px);
}
.btn-blue {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.6rem 1rem;
    font-size: 0.8rem;
    font-weight: 700;
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: #fff;
    border: none;
    border-radius: 0.75rem;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    font-family: 'Outfit', sans-serif;
    width: 100%;
}
.btn-blue:hover {
    background: linear-gradient(135deg, var(--gold), var(--gold-dark));
    color: #0f172a;
    transform: translateY(-1px);
}
.btn-red {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.6rem 1rem;
    font-size: 0.8rem;
    font-weight: 700;
    background: linear-gradient(135deg, #dc2626, #b91c1c);
    color: #fff;
    border: none;
    border-radius: 0.75rem;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    font-family: 'Outfit', sans-serif;
    width: 100%;
}
.btn-red:hover {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    transform: translateY(-1px);
}
.btn-green {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.6rem 1rem;
    font-size: 0.8rem;
    font-weight: 700;
    background: linear-gradient(135deg, #10b981, #059669);
    color: #fff;
    border: none;
    border-radius: 0.75rem;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    font-family: 'Outfit', sans-serif;
    width: 100%;
}
.btn-green:hover {
    background: linear-gradient(135deg, #34d399, #10b981);
    transform: translateY(-1px);
}
.btn-outline {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.6rem 1rem;
    font-size: 0.8rem;
    font-weight: 600;
    background: transparent;
    color: var(--text-2);
    border: 1.5px solid var(--border);
    border-radius: 0.75rem;
    cursor: pointer;
    transition: all 0.18s ease;
    text-decoration: none;
    font-family: 'Outfit', sans-serif;
    width: 100%;
}
.btn-outline:hover {
    border-color: rgba(212,175,55,0.4);
    color: var(--gold-dark);
    background: rgba(212,175,55,0.06);
}
.action-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0.75rem;
    margin-bottom: 0.75rem;
}
.action-full {
    margin-top: 0.75rem;
}

/* ── Stats Cards ── */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
}
.stat-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 1rem;
    padding: 1.25rem;
    transition: all 0.25s ease;
}
.stat-card:hover {
    border-color: rgba(212,175,55,0.35);
    transform: translateY(-2px);
}
.stat-icon {
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 0.75rem;
}
.stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text);
    font-family: 'DM Mono', monospace;
}
.stat-label {
    font-size: 0.7rem;
    font-weight: 600;
    color: var(--text-3);
    margin-top: 0.25rem;
}

/* ── Activity Section ── */
.activity-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 1.25rem;
    overflow: hidden;
}
.activity-header {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid var(--border);
    background: var(--surface-2);
}
.activity-header h3 {
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: var(--text-2);
    font-family: 'DM Mono', monospace;
}
.activity-item {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid var(--border);
    transition: background 0.15s ease;
}
.activity-item:last-child {
    border-bottom: none;
}
.activity-item:hover {
    background: rgba(212,175,55,0.025);
}
.activity-icon {
    width: 2rem;
    height: 2rem;
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.activity-description {
    font-size: 0.8rem;
    font-weight: 500;
    color: var(--text);
}
.activity-time {
    font-size: 0.65rem;
    color: var(--text-3);
    font-family: 'DM Mono', monospace;
}
.activity-empty {
    padding: 2rem;
    text-align: center;
    color: var(--text-3);
    font-size: 0.8rem;
}

/* ── Animations ── */
@keyframes fadeUp {
    from { opacity: 0; transform: translateY(14px); }
    to   { opacity: 1; transform: translateY(0); }
}
.anim-1 { animation: fadeUp 0.38s ease 0.04s both; }
.anim-2 { animation: fadeUp 0.38s ease 0.10s both; }
.anim-3 { animation: fadeUp 0.38s ease 0.16s both; }
</style>
@endpush

@section('content')

@php
    $currentUser = auth()->user();
    $isSystemAdmin = $currentUser->role_id == 1;
    $canManageAccounts = ($isSystemAdmin || $currentUser->role->name === 'Supreme Admin' || $currentUser->role->name === 'Club Adviser');
    
    $roleBadgeClass = match($user->role->name) {
        'System Administrator' => 'badge-role-admin',
        'Club Adviser' => 'badge-role-adviser',
        'Treasurer' => 'badge-role-treasurer',
        'Auditor' => 'badge-role-auditor',
        'Guest' => 'badge-role-guest',
        default => 'badge-role-guest',
    };
    
    // Determine avatar URL (supports both Cloudinary and local storage)
    $avatarUrl = null;
    if ($user->avatar) {
        $avatarUrl = str_starts_with($user->avatar, 'http') 
            ? $user->avatar 
            : asset('storage/' . $user->avatar);
    }
@endphp

<div class="space-y-5">
    
    {{-- Back Button --}}
    <div class="anim-1">
        <a href="{{ route('members.index') }}" class="back-btn">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Members
        </a>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Profile Card --}}
        <div class="lg:col-span-1 anim-2">
            <div class="profile-card">
                <div class="profile-header">
                    <div class="profile-avatar">
                        @if($avatarUrl)
                            <img src="{{ $avatarUrl }}" alt="{{ $user->full_name }}">
                        @else
                            <div class="profile-avatar-placeholder">
                                {{ strtoupper(substr($user->full_name, 0, 2)) }}
                            </div>
                        @endif
                    </div>
                    <h2 class="profile-name">{{ $user->full_name }}</h2>
                    <p class="profile-position">{{ $user->position ?? 'No position' }}</p>
                    <div class="profile-badges">
                        <span class="badge-role {{ $roleBadgeClass }}">
                            {{ $user->role->name }}
                        </span>
                        @if($user->role->abbreviation)
                        <span class="badge-abbr">
                            {{ $user->role->abbreviation }}
                        </span>
                        @endif
                    </div>
                </div>

                <div class="profile-info">
                    <div class="profile-info-row">
                        <span class="profile-info-label">Email</span>
                        <span class="profile-info-value">{{ $user->email }}</span>
                    </div>
                    <div class="profile-info-row">
                        <span class="profile-info-label">Member Since</span>
                        <span class="profile-info-value">{{ $user->created_at->format('F d, Y') }}</span>
                    </div>
                    <div class="profile-info-row">
                        <span class="profile-info-label">Status</span>
                        <span class="profile-info-value">
                            @if($user->is_active)
                                <span class="status-active">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse inline-block mr-1"></span>
                                    Active
                                </span>
                            @else
                                <span class="status-inactive">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500 inline-block mr-1"></span>
                                    Inactive
                                </span>
                            @endif
                        </span>
                    </div>
                    @if($user->student_id)
                    <div class="profile-info-row">
                        <span class="profile-info-label">Student ID</span>
                        <span class="profile-info-value">{{ $user->student_id }}</span>
                    </div>
                    @endif
                    @if($user->year_level)
                    <div class="profile-info-row">
                        <span class="profile-info-label">Year Level</span>
                        <span class="profile-info-value">{{ $user->year_level }}</span>
                    </div>
                    @endif
                    @if($user->gender)
                    <div class="profile-info-row">
                        <span class="profile-info-label">Gender</span>
                        <span class="profile-info-value">{{ $user->gender }}</span>
                    </div>
                    @endif
                    @if($user->phone)
                    <div class="profile-info-row">
                        <span class="profile-info-label">Phone</span>
                        <span class="profile-info-value">{{ $user->phone }}</span>
                    </div>
                    @endif
                    @if($user->birthday)
                    <div class="profile-info-row">
                        <span class="profile-info-label">Birthday</span>
                        <span class="profile-info-value">{{ $user->birthday->format('F d, Y') }}</span>
                    </div>
                    @endif
                    <div class="profile-info-row">
                        <span class="profile-info-label">Role Level</span>
                        <span class="profile-info-value">Level {{ $user->role->level }}</span>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="profile-actions">
                    <div class="action-grid">
                        <a href="{{ route('members.edit', $user->id) }}" class="btn-emerald">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Edit Profile
                        </a>
                        <a href="{{ route('members.edit-history', $user->id) }}" class="btn-blue">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            View History
                        </a>
                    </div>
                    
                    @if($canManageAccounts && $user->id !== auth()->id())
                        @if($user->is_active)
                            <div class="action-full">
                                <button onclick="toggleAccountStatus('{{ $user->id }}', '{{ $user->full_name }}', 'deactivate')" class="btn-red">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                                    </svg>
                                    Deactivate Account
                                </button>
                            </div>
                        @else
                            <div class="action-full">
                                <button onclick="toggleAccountStatus('{{ $user->id }}', '{{ $user->full_name }}', 'activate')" class="btn-green">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Activate Account
                                </button>
                            </div>
                        @endif
                    @endif
                    
                    <div class="action-full">
                        <a href="{{ route('members.index') }}" class="btn-outline">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back to Members
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Stats and Activity --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Stats Cards --}}
            <div class="stats-grid anim-2">
                <div class="stat-card">
                    <div class="stat-icon" style="background: rgba(245,158,11,0.1);">
                        <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div class="stat-value">{{ $documentsCount }}</div>
                    <div class="stat-label">Documents Uploaded</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: rgba(5,150,105,0.1);">
                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="stat-value">{{ $financialTransactionsCount ?? 0 }}</div>
                    <div class="stat-label">Financial Transactions</div>
                </div>
            </div>

            {{-- Recent Activity --}}
            <div class="activity-card anim-3">
                <div class="activity-header">
                    <h3>📋 Recent Activity</h3>
                </div>
                <div class="divide-y divide-border">
                    @forelse($recentFinancialTransactions ?? [] as $activity)
                    <div class="activity-item">
                        <div class="flex items-center gap-3">
                            <div class="activity-icon" style="background: {{ $activity['type'] === 'document' ? 'rgba(245,158,11,0.1)' : 'rgba(5,150,105,0.1)' }};">
                                @if($activity['type'] === 'document')
                                    <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                @else
                                    <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                @endif
                            </div>
                            <div class="flex-1">
                                <p class="activity-description">{{ $activity['description'] }}</p>
                                <p class="activity-time">{{ $activity['time'] }}</p>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="activity-empty">
                        <p>No recent activity to display.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleAccountStatus(userId, userName, action) {
    const isDeactivate = action === 'deactivate';
    const message = isDeactivate 
        ? `⚠️ Are you sure you want to deactivate ${userName}'s account?\n\nDeactivated accounts cannot log in or access the system until reactivated.\n\nThis action can be undone by activating the account later.`
        : `✅ Are you sure you want to activate ${userName}'s account?\n\nActivated accounts will be able to log in and access the system again.`;
    
    if (confirm(message)) {
        const button = event.currentTarget;
        const originalText = button.innerHTML;
        
        button.innerHTML = '<svg class="animate-spin w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg> Processing...';
        button.disabled = true;
        
        fetch(`/members/${userId}/${action}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                setTimeout(() => window.location.reload(), 1000);
            } else {
                alert(data.error || 'Failed to process request');
                button.innerHTML = originalText;
                button.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
            button.innerHTML = originalText;
            button.disabled = false;
        });
    }
}
</script>

@endsection