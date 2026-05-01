@extends('layouts.app')

@section('title', 'Roles — VSULHS SSLG')
@section('page-title', 'Roles')

@push('styles')
<style>
/* ════════════════════════════════════════════════
   ROLES MANAGEMENT — Emerald & Gold Luxury Theme
   Matching Members, Financial & User Management design
════════════════════════════════════════════════ */

/* ── Hero Section ── */
.roles-hero {
    position: relative;
    overflow: hidden;
    border-radius: 1.25rem;
    padding: 1.75rem 2rem;
    isolation: isolate;
    background: linear-gradient(135deg, #064E3B 0%, #065F46 35%, #047857 60%, #0A3A28 100%);
}
.roles-hero::before {
    content: '';
    position: absolute; inset: 0;
    background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,1) 1px, transparent 0);
    background-size: 28px 28px;
    opacity: 0.04; z-index: 0;
}
.roles-hero::after {
    content: '';
    position: absolute;
    top: -60px; right: -60px;
    width: 280px; height: 280px;
    background: radial-gradient(circle, rgba(212,175,55,0.35), transparent 65%);
    filter: blur(48px); z-index: 0;
}
.roles-hero-content { position: relative; z-index: 1; }

.roles-hero-title {
    font-family: 'DM Serif Display', serif;
    font-size: clamp(1.5rem, 3.5vw, 2.2rem);
    color: #fff;
    letter-spacing: -0.02em;
    line-height: 1.1;
}
.roles-hero-title span {
    background: linear-gradient(90deg, #F0CC55, #D4AF37);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.roles-hero-pill {
    display: inline-flex; align-items: center; gap: 0.4rem;
    padding: 0.3rem 0.75rem;
    background: rgba(255,255,255,0.1);
    border: 1px solid rgba(212,175,55,0.28);
    border-radius: 999px;
    font-size: 0.72rem; font-weight: 600;
    color: rgba(255,255,255,0.88);
    font-family: 'DM Mono', monospace;
}

/* ── Roles Grid Layout ── */
.roles-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.5rem;
}
@media (min-width: 1024px) {
    .roles-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

/* ── Roles Card ── */
.roles-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 1.25rem;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
}
html.dark .roles-card { box-shadow: 0 4px 20px rgba(0,0,0,0.22); }

.roles-card-header {
    background: linear-gradient(135deg, #064E3B 0%, #047857 60%, #065F46 100%);
    padding: 0.75rem 1.25rem;
    border-bottom: 1px solid rgba(212,175,55,0.2);
}

.roles-card-title {
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: rgba(255,255,255,0.85);
    font-family: 'DM Mono', monospace;
}

/* ── Search Input ── */
.roles-search {
    position: relative;
}
.roles-search-input {
    padding: 0.5rem 0.875rem 0.5rem 2rem;
    font-size: 0.83rem;
    background: var(--surface-2);
    border: 1.5px solid var(--border);
    border-radius: 0.75rem;
    color: var(--text);
    font-family: 'Outfit', sans-serif;
    transition: all 0.2s ease;
    width: 100%;
}
.roles-search-input:focus {
    outline: none;
    border-color: var(--gold);
    box-shadow: 0 0 0 3px rgba(212,175,55,0.12);
    background: var(--surface);
}
.roles-search-input::placeholder { color: var(--text-3); }
.roles-search-icon {
    position: absolute;
    left: 0.65rem;
    top: 50%;
    transform: translateY(-50%);
    width: 1rem;
    height: 1rem;
    color: var(--text-3);
}

/* ── Toggle Buttons ── */
.roles-toggle-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.4rem 0.875rem;
    font-size: 0.7rem;
    font-weight: 600;
    border-radius: 0.65rem;
    border: 1.5px solid var(--border);
    background: var(--surface-3);
    color: var(--text-2);
    text-decoration: none;
    transition: all 0.18s ease;
    font-family: 'Outfit', sans-serif;
}
.roles-toggle-btn:hover {
    border-color: rgba(212,175,55,0.4);
    color: var(--gold-dark);
    background: rgba(212,175,55,0.06);
}
.roles-toggle-btn.active {
    background: linear-gradient(135deg, var(--gold), var(--gold-dark));
    color: #0f172a;
    border-color: transparent;
}

.roles-badge-active {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.2rem 0.6rem;
    border-radius: 9999px;
    font-size: 0.65rem;
    font-weight: 700;
    font-family: 'DM Mono', monospace;
}
.roles-badge-deleted {
    background: rgba(220,38,38,0.12);
    color: #dc2626;
    border: 1px solid rgba(220,38,38,0.25);
}
.roles-badge-hidden {
    background: rgba(107,114,128,0.12);
    color: #6b7280;
    border: 1px solid rgba(107,114,128,0.25);
}
.roles-badge-predefined {
    background: rgba(212,175,55,0.12);
    color: #b8942e;
    border: 1px solid rgba(212,175,55,0.25);
}
.roles-badge-system {
    background: rgba(239,68,68,0.12);
    color: #dc2626;
    border: 1px solid rgba(239,68,68,0.25);
}

html.dark .roles-badge-deleted { background: rgba(248,113,113,0.15); color: #fca5a5; }
html.dark .roles-badge-hidden { background: rgba(107,114,128,0.2); color: #cbd5e1; }
html.dark .roles-badge-predefined { background: rgba(212,175,55,0.15); color: #fcd34d; }
html.dark .roles-badge-system { background: rgba(248,113,113,0.15); color: #fca5a5; }

/* ── Roles Table ── */
.roles-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.82rem;
}
.roles-table thead tr {
    background: linear-gradient(135deg, #064E3B 0%, #047857 60%, #065F46 100%);
}
.roles-table th {
    padding: 0.7rem 1rem;
    text-align: left;
    font-size: 0.63rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: rgba(255,255,255,0.82);
    font-family: 'DM Mono', monospace;
    white-space: nowrap;
}
.roles-table th:last-child { text-align: right; }
.roles-table tbody tr {
    border-bottom: 1px solid var(--border);
    transition: background 0.15s ease;
}
.roles-table tbody tr:last-child { border-bottom: none; }
.roles-table tbody tr:hover { 
    background: rgba(212,175,55,0.025); 
    box-shadow: inset 3px 0 0 var(--gold);
}
.roles-table td {
    padding: 0.75rem 1rem;
    color: var(--text-2);
    vertical-align: middle;
}
.roles-table td:last-child { text-align: right; }

/* ── Role Avatar ── */
.role-avatar {
    width: 2rem;
    height: 2rem;
    border-radius: 0.6rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: 700;
    flex-shrink: 0;
}
.role-name {
    font-weight: 600;
    color: var(--text);
}
.role-name.deleted {
    text-decoration: line-through;
    color: var(--text-3);
}

/* ── Level Badge ── */
.role-level-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.2rem 0.6rem;
    border-radius: 9999px;
    font-size: 0.65rem;
    font-weight: 700;
    font-family: 'DM Mono', monospace;
}

/* ── User Count Circle ── */
.user-count-circle {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 1.75rem;
    height: 1.75rem;
    border-radius: 9999px;
    background: var(--surface-3);
    color: var(--text-3);
    font-size: 0.7rem;
    font-weight: 700;
    font-family: 'DM Mono', monospace;
}

/* ── Action Buttons ── */
.roles-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.25rem 0.6rem;
    font-size: 0.65rem;
    font-weight: 700;
    font-family: 'DM Mono', monospace;
    border-radius: 0.4rem;
    border: 1px solid transparent;
    cursor: pointer;
    transition: all 0.15s ease;
    text-decoration: none;
    white-space: nowrap;
    background: none;
}
.roles-action-restore { color: #059669; border-color: rgba(5,150,105,0.2); background: rgba(5,150,105,0.06); }
.roles-action-restore:hover { background: rgba(5,150,105,0.14); border-color: rgba(5,150,105,0.4); }
.roles-action-force-delete { color: #64748b; border-color: rgba(100,116,139,0.2); background: rgba(100,116,139,0.06); }
.roles-action-force-delete:hover { color: #dc2626; border-color: rgba(220,38,38,0.3); background: rgba(220,38,38,0.06); }
.roles-action-hide { color: #d97706; border-color: rgba(217,119,6,0.2); background: rgba(217,119,6,0.06); }
.roles-action-hide:hover { background: rgba(217,119,6,0.14); border-color: rgba(217,119,6,0.4); }
.roles-action-unhide { color: #059669; border-color: rgba(5,150,105,0.2); background: rgba(5,150,105,0.06); }
.roles-action-unhide:hover { background: rgba(5,150,105,0.14); border-color: rgba(5,150,105,0.4); }
.roles-action-edit { color: var(--gold-dark); border-color: rgba(212,175,55,0.25); background: rgba(212,175,55,0.07); }
.roles-action-edit:hover { background: rgba(212,175,55,0.16); border-color: rgba(212,175,55,0.45); }
.roles-action-delete { color: #64748b; border-color: rgba(100,116,139,0.2); background: rgba(100,116,139,0.06); }
.roles-action-delete:hover { color: #dc2626; border-color: rgba(220,38,38,0.3); background: rgba(220,38,38,0.06); }
.roles-action-disabled {
    color: var(--text-3);
    cursor: not-allowed;
    opacity: 0.6;
}

/* ── Add Role Form ── */
.roles-form {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 1.25rem;
    padding: 1.5rem;
}
.roles-form-title {
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: var(--text-3);
    font-family: 'DM Mono', monospace;
    padding-bottom: 0.75rem;
    margin-bottom: 1rem;
    border-bottom: 1px solid var(--border);
}
.roles-form-label {
    display: block;
    font-size: 0.75rem;
    font-weight: 700;
    color: var(--text-2);
    margin-bottom: 0.35rem;
    font-family: 'Outfit', sans-serif;
}
.roles-form-input {
    width: 100%;
    padding: 0.5rem 0.75rem;
    font-size: 0.85rem;
    background: var(--surface-2);
    border: 1.5px solid var(--border);
    border-radius: 0.65rem;
    color: var(--text);
    font-family: 'Outfit', sans-serif;
    transition: all 0.2s ease;
}
.roles-form-input:focus {
    outline: none;
    border-color: var(--gold);
    box-shadow: 0 0 0 3px rgba(212,175,55,0.12);
    background: var(--surface);
}
.roles-form-input.error {
    border-color: #dc2626;
}
.roles-form-error {
    color: #dc2626;
    font-size: 0.7rem;
    margin-top: 0.25rem;
}
.roles-form-hint {
    font-size: 0.65rem;
    color: var(--text-3);
    margin-top: 0.25rem;
}

.btn-create {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.5rem 1.25rem;
    font-size: 0.8rem;
    font-weight: 700;
    background: linear-gradient(135deg, var(--emerald), var(--emerald-dark));
    color: #fff;
    border: none;
    border-radius: 0.65rem;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: 0 2px 10px rgba(5,150,105,0.22);
    font-family: 'Outfit', sans-serif;
}
.btn-create:hover {
    background: linear-gradient(135deg, var(--gold), var(--gold-dark));
    color: #0f172a;
    box-shadow: 0 4px 16px rgba(212,175,55,0.35);
    transform: translateY(-1px);
}
.btn-reset {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.5rem 1rem;
    font-size: 0.8rem;
    font-weight: 600;
    background: var(--surface-3);
    color: var(--text-2);
    border: 1.5px solid var(--border);
    border-radius: 0.65rem;
    cursor: pointer;
    transition: all 0.18s ease;
    font-family: 'Outfit', sans-serif;
}
.btn-reset:hover {
    border-color: rgba(212,175,55,0.4);
    color: var(--gold-dark);
    background: rgba(212,175,55,0.06);
}

/* ── Info Card ── */
.roles-info-card {
    margin-top: 1.5rem;
    background: rgba(59,130,246,0.05);
    border: 1px solid rgba(59,130,246,0.15);
    border-radius: 1rem;
    padding: 1rem;
}
html.dark .roles-info-card {
    background: rgba(59,130,246,0.08);
    border-color: rgba(59,130,246,0.2);
}
.roles-info-icon {
    width: 1.25rem;
    height: 1.25rem;
    color: #3b82f6;
    flex-shrink: 0;
    margin-top: 0.125rem;
}
.roles-info-title {
    font-size: 0.8rem;
    font-weight: 700;
    color: #2563eb;
    margin-bottom: 0.5rem;
}
html.dark .roles-info-title { color: #60a5fa; }
.roles-info-text {
    font-size: 0.7rem;
    color: #1e40af;
    margin-bottom: 0.25rem;
}
html.dark .roles-info-text { color: #93c5fd; }

/* ── Pagination ── */
.pag-wrap {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    padding: 0.875rem 1.25rem;
    border-top: 1px solid var(--border);
    background: var(--surface-2);
}
.pag-info { font-size: 0.7rem; color: var(--text-3); font-family: 'DM Mono', monospace; }
.pag-btns { display: flex; gap: 0.25rem; }
.pag-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 2rem;
    height: 2rem;
    padding: 0 0.5rem;
    font-size: 0.7rem;
    font-family: 'DM Mono', monospace;
    font-weight: 600;
    border-radius: 0.5rem;
    border: 1.5px solid var(--border);
    color: var(--text-3);
    background: var(--surface);
    text-decoration: none;
    transition: all 0.15s ease;
}
.pag-btn:not(.disabled):not(.current):hover {
    border-color: var(--gold);
    color: var(--gold-dark);
    background: rgba(212,175,55,0.08);
}
html.dark .pag-btn:not(.disabled):not(.current):hover { color: var(--gold-light); }
.pag-btn.current {
    background: linear-gradient(135deg, var(--emerald), var(--emerald-dark));
    border-color: var(--emerald-dark);
    color: #fff;
    box-shadow: 0 2px 10px rgba(5,150,105,0.3);
}
.pag-btn.disabled { opacity: 0.35; cursor: not-allowed; pointer-events: none; }

/* ── Animations ── */
@keyframes fadeUp {
    from { opacity: 0; transform: translateY(14px); }
    to   { opacity: 1; transform: translateY(0); }
}
.anim-1 { animation: fadeUp 0.38s ease 0.04s both; }
.anim-2 { animation: fadeUp 0.38s ease 0.10s both; }
.anim-3 { animation: fadeUp 0.38s ease 0.16s both; }
.anim-4 { animation: fadeUp 0.38s ease 0.22s both; }
</style>
@endpush

@section('content')

@php
    $roleColorMap = [
        'System Administrator' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/50 dark:text-purple-300',
        'Club Adviser'         => 'bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-300',
        'Treasurer'            => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300',
        'Auditor'              => 'bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300',
        'Guest'                => 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400',
    ];

    $levelLabelMap = [
        1 => 'System',
        2 => 'Adviser',
        3 => 'Officer',
        4 => 'Member',
        5 => 'Guest',
    ];

    $authUserId = auth()->id();
    $authRoleId = auth()->user()->role_id;
@endphp

<div class="space-y-5">

    {{-- ── HERO SECTION ── --}}
    <div class="roles-hero anim-1">
        <div class="roles-hero-content">
            <p class="text-emerald-300/70 text-[10px] font-bold tracking-[0.2em] uppercase mb-2"
               style="font-family:'DM Mono',monospace;">
                {{ now()->format('F Y') }} · Access Control
            </p>
            <h1 class="roles-hero-title mb-3">Roles<br><span>Management</span></h1>
            <div class="flex flex-wrap gap-2">
                <span class="roles-hero-pill">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    {{ $roles->total() }} Total Roles
                </span>
                <span class="roles-hero-pill">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Manage permissions & access
                </span>
            </div>
        </div>
    </div>

    <div class="roles-grid">

        {{-- ------------------------------------------------------------------ --}}
        {{-- Roles List                                                          --}}
        {{-- ------------------------------------------------------------------ --}}
        <div class="roles-card anim-2">
            <div class="roles-card-header">
                <h2 class="roles-card-title">Role Directory</h2>
            </div>

            {{-- Toolbar --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-4 border-b border-border bg-surface-2">
                <div class="roles-search">
                    <input type="text"
                           id="roleSearch"
                           placeholder="Search by name or abbreviation…"
                           aria-label="Search roles"
                           class="roles-search-input">
                    <svg class="roles-search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>

                <div class="flex items-center gap-2 flex-wrap">
                    @if ($showTrashed)
                        <span class="roles-badge-active roles-badge-deleted">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-500 inline-block"></span>
                            Showing deleted roles
                        </span>
                    @elseif ($showHidden)
                        <span class="roles-badge-active roles-badge-hidden">
                            <span class="w-1.5 h-1.5 rounded-full bg-gray-500 inline-block"></span>
                            Showing hidden roles
                        </span>
                    @endif

                    @unless ($showTrashed)
                        <a href="{{ route('admin.roles.index', $showHidden ? [] : ['show_hidden' => 1]) }}"
                           class="roles-toggle-btn">
                            {{ $showHidden ? '← Hide hidden roles' : '👁️ Show hidden roles' }}
                        </a>
                    @endunless

                    <a href="{{ route('admin.roles.index', $showTrashed ? [] : ['show_trashed' => 1]) }}"
                       class="roles-toggle-btn">
                        {{ $showTrashed ? '← Back to active roles' : '🗑️ Show deleted roles' }}
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="roles-table" id="rolesTable">
                    <thead>
                        <tr>
                            <th>Role</th>
                            <th>Abbr</th>
                            <th>Level</th>
                            <th>Users</th>
                            <th>Perms</th>
                            <th class="right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($roles as $role)
                            @php
                                $colorClass   = $roleColorMap[$role->name] ?? 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300';
                                $levelLabel   = $levelLabelMap[$role->level] ?? "Level {$role->level}";
                                $isPredefined = (bool) ($role->is_predefined ?? false);
                                $isHidden     = ! $role->is_visible;
                                $isOwnRole    = ($authRoleId === $role->id);
                                $isTrashed    = ! is_null($role->deleted_at);
                                $roleInitial  = strtoupper(substr($role->name, 0, 1));
                            @endphp

                            <tr class="role-row transition-all duration-150
                                       {{ $isTrashed ? 'opacity-60' : 'hover:bg-gold/5' }}"
                                data-name="{{ strtolower($role->name) }}"
                                data-abbr="{{ strtolower($role->abbreviation ?? '') }}">

                                {{-- Role --}}
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="role-avatar {{ $colorClass }}">
                                            {{ $roleInitial }}
                                        </div>
                                        <div>
                                            <span class="role-name {{ $isTrashed ? 'deleted' : '' }}">
                                                {{ $role->name }}
                                            </span>
                                            <div class="flex flex-wrap gap-1 mt-1">
                                                @if ($isTrashed)
                                                    <span class="roles-badge-active roles-badge-deleted">deleted</span>
                                                @endif
                                                @if ($isHidden && ! $isTrashed)
                                                    <span class="roles-badge-active roles-badge-hidden">hidden</span>
                                                @endif
                                                @if ($isPredefined)
                                                    <span class="roles-badge-active roles-badge-predefined">predefined</span>
                                                @endif
                                                @if ($role->is_system)
                                                    <span class="roles-badge-active roles-badge-system">system</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                 </</td>

                                {{-- Abbreviation --}}
                                <td>
                                    @if ($role->abbreviation)
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-mono bg-surface-3 text-text-3">
                                            {{ $role->abbreviation }}
                                        </span>
                                    @else
                                        <span class="text-text-3 text-xs">—</span>
                                    @endif
                                 </</td>

                                {{-- Level --}}
                                <td>
                                    <span class="role-level-badge {{ $colorClass }}">
                                        Level {{ $role->level }}
                                    </span>
                                 </</td>

                                {{-- Users Count --}}
                                <td>
                                    <span class="user-count-circle">
                                        {{ $role->users_count }}
                                    </span>
                                 </</td>

                                {{-- Permissions Count --}}
                                <td class="text-text-3">{{ $role->permissions->count() }}</td>

                                {{-- Actions --}}
                                <td class="right">
                                    <div class="flex items-center justify-end gap-1.5">

                                        @if ($isTrashed)
                                            <form method="POST"
                                                  action="{{ route('admin.roles.restore', $role->id) }}"
                                                  class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        class="roles-action roles-action-restore">
                                                    Restore
                                                </button>
                                            </form>

                                            <form method="POST"
                                                  action="{{ route('admin.roles.force-delete', $role->id) }}"
                                                  onsubmit="return confirm('⚠️ Permanently delete &quot;{{ addslashes($role->name) }}&quot;?\n\nThis cannot be undone.')"
                                                  class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="roles-action roles-action-force-delete">
                                                    Force Del
                                                </button>
                                            </form>

                                        @else

                                            @if ($role->id === 1)
                                                <span class="roles-action-disabled" title="System Administrator cannot be hidden">Protected</span>

                                            @elseif ($isOwnRole && $role->is_visible)
                                                <span class="roles-action-disabled" title="Cannot hide your own current role.">Hide</span>

                                            @elseif ($role->is_visible && $role->users_count > 0)
                                                <span class="roles-action-disabled" title="Cannot hide — {{ $role->users_count }} user(s) assigned.">Hide</span>

                                            @else
                                                <form method="POST"
                                                      action="{{ route('admin.roles.toggle-visibility', $role->id) }}?show_hidden={{ $showHidden ? '1' : '0' }}"
                                                      onsubmit="return confirm('{{ $role->is_visible ? 'Hide' : 'Unhide' }} the role &quot;{{ addslashes($role->name) }}&quot;?')"
                                                      class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                            class="roles-action {{ $isHidden ? 'roles-action-unhide' : 'roles-action-hide' }}">
                                                        {{ $isHidden ? 'Unhide' : 'Hide' }}
                                                    </button>
                                                </form>
                                            @endif

                                            @if (! $isHidden)
                                                <a href="{{ route('admin.roles.edit', $role->id) }}"
                                                   class="roles-action roles-action-edit">
                                                    Edit
                                                </a>
                                            @endif

                                            @if (! $isPredefined && $role->users_count === 0 && ! $isHidden)
                                                <form method="POST"
                                                      action="{{ route('admin.roles.destroy', $role->id) }}"
                                                      onsubmit="return confirm('⚠️ Delete role &quot;{{ addslashes($role->name) }}&quot;?\n\nIt will be moved to trash and can be restored later.')"
                                                      class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="roles-action roles-action-delete">
                                                        Delete
                                                    </button>
                                                </form>

                                            @elseif ($isPredefined && ! $isHidden)
                                                <span class="roles-action-disabled" title="Predefined roles cannot be deleted">Predefined</span>

                                            @elseif ($role->users_count > 0 && ! $isHidden)
                                                <span class="roles-action-disabled" title="Role has {{ $role->users_count }} assigned user(s)">In use</span>
                                            @endif

                                        @endif

                                    </div>
                                 </td>
                             </tr>

                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-10 text-center text-text-3">
                                    {{ $showTrashed ? 'No deleted roles found.' : 'No roles found.' }}
                                 </td>
                             </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($roles->hasPages())
                <div class="pag-wrap">
                    <p class="pag-info">
                        {{ $roles->firstItem() }}–{{ $roles->lastItem() }} of {{ $roles->total() }} roles
                    </p>
                    <div class="pag-btns">
                        @if ($roles->onFirstPage())
                            <span class="pag-btn disabled">← Prev</span>
                        @else
                            <a href="{{ $roles->previousPageUrl() }}" class="pag-btn">← Prev</a>
                        @endif
                        @foreach($roles->getUrlRange(max(1, $roles->currentPage() - 2), min($roles->lastPage(), $roles->currentPage() + 2)) as $page => $url)
                            @if($page == $roles->currentPage())
                                <span class="pag-btn current">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="pag-btn">{{ $page }}</a>
                            @endif
                        @endforeach
                        @if ($roles->hasMorePages())
                            <a href="{{ $roles->nextPageUrl() }}" class="pag-btn">Next →</a>
                        @else
                            <span class="pag-btn disabled">Next →</span>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        {{-- ------------------------------------------------------------------ --}}
        {{-- Add New Role — hidden when viewing trash                            --}}
        {{-- ------------------------------------------------------------------ --}}
        @unless ($showTrashed)
        <div class="roles-form anim-3">
            <h2 class="roles-form-title">➕ Add New Role</h2>

            <form method="POST" action="{{ route('admin.roles.store') }}">
                @csrf

                <div class="mb-4">
                    <label for="role_name" class="roles-form-label">Role Name *</label>
                    <input type="text"
                           id="role_name"
                           name="name"
                           value="{{ old('name') }}"
                           required
                           placeholder="e.g., Secretary, PRO"
                           class="roles-form-input {{ $errors->has('name') ? 'error' : '' }}">
                    @error('name')
                        <p class="roles-form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="role_abbreviation" class="roles-form-label">
                        Abbreviation <span class="font-normal text-text-3">(Optional)</span>
                    </label>
                    <input type="text"
                           id="role_abbreviation"
                           name="abbreviation"
                           value="{{ old('abbreviation') }}"
                           placeholder="e.g., SEC, PRO"
                           maxlength="10"
                           class="roles-form-input">
                    <p class="roles-form-hint">Short code for this role (max 10 characters)</p>
                </div>

                <div class="mb-4">
                    <label for="role_level" class="roles-form-label">Level *</label>
                    <input type="number"
                           id="role_level"
                           name="level"
                           value="{{ old('level', 4) }}"
                           min="2"
                           max="10"
                           required
                           class="roles-form-input {{ $errors->has('level') ? 'error' : '' }}">
                    <p class="roles-form-hint">
                        Level 1 is reserved for System Administrator. Custom roles use level 2–10.
                        Lower number = higher authority.
                    </p>
                    @error('level')
                        <p class="roles-form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="role_desc" class="roles-form-label">
                        Description <span class="font-normal text-text-3">(Optional)</span>
                    </label>
                    <textarea id="role_desc"
                              name="desc"
                              rows="2"
                              placeholder="Describe the responsibilities of this role"
                              class="roles-form-input">{{ old('desc') }}</textarea>
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit" class="btn-create">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Create Role
                    </button>
                    <button type="reset" class="btn-reset">
                        Reset
                    </button>
                </div>
            </form>
        </div>
        @endunless

    </div>

    {{-- Info Card --}}
    <div class="roles-info-card anim-4">
        <div class="flex items-start gap-3">
            <svg class="roles-info-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div class="space-y-1">
                <p class="roles-info-title">Role Hierarchy & Visibility</p>
                <p class="roles-info-text">
                    <strong>Predefined roles:</strong>
                    System Administrator, Club Adviser, Treasurer, Auditor, and Guest are built-in roles.
                    Their names cannot be changed, but abbreviation, description, level, and permissions can be updated.
                </p>
                <p class="roles-info-text">
                    <strong>Custom roles</strong> are fully editable and deletable as long as no users are assigned to them.
                </p>
                <p class="roles-info-text">
                    <strong>Hiding a role</strong> removes it from all user creation and editing forms.
                    Roles with active users cannot be hidden until those users are reassigned.
                </p>
                <p class="roles-info-text">
                    <strong>Deleted roles</strong> are soft-deleted and can be restored from the "Show deleted roles" view.
                    Permanently deleted roles cannot be recovered.
                </p>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    (() => {
        const input = document.getElementById('roleSearch');
        const rows  = document.querySelectorAll('#rolesTable tbody tr.role-row');

        if (input) {
            input.addEventListener('input', () => {
                const filter = input.value.trim().toLowerCase();

                rows.forEach(row => {
                    const name = row.dataset.name ?? '';
                    const abbr = row.dataset.abbr ?? '';
                    row.style.display = (!filter || name.includes(filter) || abbr.includes(filter)) ? '' : 'none';
                });
            });
        }
    })();
</script>
@endpush