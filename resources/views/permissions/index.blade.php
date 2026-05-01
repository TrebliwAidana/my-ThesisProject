@extends('layouts.app')

@section('title', 'Permission Matrix — VSULHS SSLG')
@section('page-title', 'Permission Management')

@push('styles')
<style>
/* ════════════════════════════════════════════════
   PERMISSION MATRIX — Emerald & Gold Luxury Theme
   Matching all other management views
════════════════════════════════════════════════ */

/* ── Hero Section ── */
.permissions-hero {
    position: relative;
    overflow: hidden;
    border-radius: 1.25rem;
    padding: 1.75rem 2rem;
    isolation: isolate;
    background: linear-gradient(135deg, #064E3B 0%, #065F46 35%, #047857 60%, #0A3A28 100%);
}
.permissions-hero::before {
    content: '';
    position: absolute; inset: 0;
    background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,1) 1px, transparent 0);
    background-size: 28px 28px;
    opacity: 0.04; z-index: 0;
}
.permissions-hero::after {
    content: '';
    position: absolute;
    top: -60px; right: -60px;
    width: 280px; height: 280px;
    background: radial-gradient(circle, rgba(212,175,55,0.35), transparent 65%);
    filter: blur(48px); z-index: 0;
}
.permissions-hero-content { position: relative; z-index: 1; }

.permissions-hero-title {
    font-family: 'DM Serif Display', serif;
    font-size: clamp(1.5rem, 3.5vw, 2.2rem);
    color: #fff;
    letter-spacing: -0.02em;
    line-height: 1.1;
}
.permissions-hero-title span {
    background: linear-gradient(90deg, #F0CC55, #D4AF37);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.permissions-hero-pill {
    display: inline-flex; align-items: center; gap: 0.4rem;
    padding: 0.3rem 0.75rem;
    background: rgba(255,255,255,0.1);
    border: 1px solid rgba(212,175,55,0.28);
    border-radius: 999px;
    font-size: 0.72rem; font-weight: 600;
    color: rgba(255,255,255,0.88);
    font-family: 'DM Mono', monospace;
}

/* ── Stat Cards ── */
.permissions-stat-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 1rem;
}
.permissions-stat-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 1rem;
    padding: 1.1rem 1.2rem;
    position: relative;
    overflow: hidden;
    transition: all 0.25s cubic-bezier(0.4,0,0.2,1);
    cursor: default;
}
.permissions-stat-card::after {
    content: '';
    position: absolute;
    bottom: 0; left: 0; right: 0;
    height: 2px;
    transform: scaleX(0);
    transform-origin: left;
    transition: transform 0.3s ease;
    border-radius: 0 0 999px 999px;
}
.permissions-stat-card:hover {
    border-color: rgba(212,175,55,0.35);
    transform: translateY(-3px);
    box-shadow: 0 8px 28px rgba(0,0,0,0.08), 0 0 0 1px rgba(212,175,55,0.12);
}
.permissions-stat-card:hover::after { transform: scaleX(1); }

.permissions-stat-card.granted::after { background: linear-gradient(90deg, var(--emerald), var(--gold)); }
.permissions-stat-card.total::after { background: linear-gradient(90deg, #2563eb, #60a5fa); }
.permissions-stat-card.modules::after { background: linear-gradient(90deg, #7c3aed, #a78bfa); }
.permissions-stat-card.roles::after { background: linear-gradient(90deg, #d97706, #f59e0b); }

.permissions-stat-icon {
    width: 2.1rem; height: 2.1rem;
    border-radius: 0.6rem;
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 0.75rem;
    flex-shrink: 0;
}
.permissions-stat-num {
    font-family: 'DM Mono', monospace;
    font-size: 1.6rem;
    font-weight: 700;
    letter-spacing: -0.04em;
    line-height: 1;
    margin-bottom: 0.25rem;
    color: var(--text);
}
.permissions-stat-label {
    font-size: 0.67rem; font-weight: 700;
    letter-spacing: 0.09em; text-transform: uppercase;
    color: var(--text-3); font-family: 'DM Mono', monospace;
}
.permissions-stat-sub {
    font-size: 0.65rem; color: var(--text-3);
    margin-top: 0.2rem; opacity: 0.75;
}

/* ── Role Selector Buttons ── */
.role-selector-btn {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    padding: 0.5rem 0.875rem;
    border-radius: 0.75rem;
    border: 2px solid var(--border);
    text-align: left;
    transition: all 0.2s ease;
    min-width: 100px;
    background: var(--surface);
    cursor: pointer;
}
.role-selector-btn.active {
    background: linear-gradient(135deg, var(--emerald), var(--emerald-dark));
    color: #fff;
    border-color: transparent;
    box-shadow: 0 4px 16px rgba(5,150,105,0.3);
}
.role-selector-btn:not(.active):hover {
    border-color: rgba(212,175,55,0.4);
    background: rgba(212,175,55,0.06);
    transform: translateY(-1px);
}
.role-selector-name {
    font-size: 0.7rem;
    font-weight: 700;
    line-height: 1.2;
}
.role-selector-abbr {
    font-size: 0.65rem;
    opacity: 0.6;
    margin-top: 0.125rem;
}
.role-progress-bar {
    margin-top: 0.375rem;
    width: 100%;
}
.role-progress-track {
    height: 0.25rem;
    width: 100%;
    border-radius: 9999px;
    background: rgba(0,0,0,0.1);
    overflow: hidden;
}
html.dark .role-progress-track { background: rgba(255,255,255,0.1); }
.role-progress-fill {
    height: 100%;
    border-radius: 9999px;
    background: currentColor;
    opacity: 0.5;
}
.role-progress-text {
    font-size: 0.6rem;
    margin-top: 0.25rem;
    display: block;
}

/* ── Save Button ── */
.btn-save {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.6rem 1.25rem;
    font-size: 0.8rem;
    font-weight: 700;
    background: linear-gradient(135deg, var(--emerald), var(--emerald-dark));
    color: #fff;
    border: none;
    border-radius: 0.75rem;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: 0 2px 10px rgba(5,150,105,0.22);
    font-family: 'Outfit', sans-serif;
}
.btn-save:hover:not(:disabled) {
    background: linear-gradient(135deg, var(--gold), var(--gold-dark));
    color: #0f172a;
    box-shadow: 0 4px 16px rgba(212,175,55,0.35);
    transform: translateY(-1px);
}
.btn-save:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

/* ── Search Input ── */
.permissions-search {
    position: relative;
}
.permissions-search-input {
    padding: 0.6rem 0.875rem 0.6rem 2.5rem;
    font-size: 0.83rem;
    background: var(--surface-2);
    border: 1.5px solid var(--border);
    border-radius: 0.75rem;
    color: var(--text);
    font-family: 'Outfit', sans-serif;
    transition: all 0.2s ease;
    width: 100%;
}
.permissions-search-input:focus {
    outline: none;
    border-color: var(--gold);
    box-shadow: 0 0 0 3px rgba(212,175,55,0.12);
    background: var(--surface);
}
.permissions-search-icon {
    position: absolute;
    left: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    width: 1rem;
    height: 1rem;
    color: var(--text-3);
}

/* ── Module Cards ── */
.permissions-module-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 1.25rem;
    overflow: hidden;
    box-shadow: 0 2px 12px rgba(0,0,0,0.04);
    transition: all 0.2s ease;
}
.permissions-module-card:hover {
    box-shadow: 0 8px 24px rgba(0,0,0,0.08);
}
.permissions-module-header {
    background: linear-gradient(135deg, #064E3B 0%, #047857 60%, #065F46 100%);
    padding: 0.875rem 1.25rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 0.5rem;
}
.permissions-module-title {
    font-size: 0.8rem;
    font-weight: 700;
    color: #fff;
    text-transform: capitalize;
    font-family: 'Outfit', sans-serif;
}
.permissions-module-stats {
    font-size: 0.65rem;
    color: rgba(255,255,255,0.7);
    font-family: 'DM Mono', monospace;
}
.permissions-module-actions {
    display: flex;
    gap: 0.5rem;
}
.permissions-module-btn {
    font-size: 0.65rem;
    padding: 0.25rem 0.75rem;
    border-radius: 0.5rem;
    background: rgba(255,255,255,0.1);
    color: #fff;
    border: none;
    cursor: pointer;
    transition: all 0.15s ease;
    font-weight: 600;
}
.permissions-module-btn:hover {
    background: rgba(255,255,255,0.2);
    transform: translateY(-1px);
}

/* ── Permission Grid ── */
.permissions-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 0.75rem;
    padding: 1.25rem;
}
@media (min-width: 640px) {
    .permissions-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
@media (min-width: 1024px) {
    .permissions-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}
@media (min-width: 1280px) {
    .permissions-grid {
        grid-template-columns: repeat(4, 1fr);
    }
}

/* ── Permission Card ── */
.permission-card {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 0.75rem;
    border-radius: 0.75rem;
    border: 2px solid var(--border);
    cursor: pointer;
    transition: all 0.15s ease;
    background: var(--surface-2);
}
.permission-card:hover {
    transform: translateX(2px);
}
.permission-card.checked {
    border-color: rgba(5,150,105,0.5);
    box-shadow: 0 2px 8px rgba(5,150,105,0.1);
}
.permission-checkbox {
    width: 1rem;
    height: 1rem;
    border-radius: 0.25rem;
    border: 2px solid var(--border);
    background: var(--surface);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    margin-top: 0.125rem;
    transition: all 0.1s ease;
}
.permission-checkbox.checked {
    background: var(--emerald);
    border-color: var(--emerald);
}
.permission-content {
    flex: 1;
    min-width: 0;
}
.permission-name {
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--text);
    line-height: 1.2;
    margin-bottom: 0.125rem;
}
.permission-slug {
    font-size: 0.6rem;
    color: var(--text-3);
    font-family: 'DM Mono', monospace;
    margin-bottom: 0.25rem;
    word-break: break-all;
}
.permission-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.125rem 0.5rem;
    border-radius: 9999px;
    font-size: 0.55rem;
    font-weight: 700;
    font-family: 'DM Mono', monospace;
}
.permission-badge-view { background: rgba(59,130,246,0.1); color: #3b82f6; border: 1px solid rgba(59,130,246,0.2); }
.permission-badge-create { background: rgba(5,150,105,0.1); color: #059669; border: 1px solid rgba(5,150,105,0.2); }
.permission-badge-edit { background: rgba(245,158,11,0.1); color: #d97706; border: 1px solid rgba(245,158,11,0.2); }
.permission-badge-delete { background: rgba(220,38,38,0.1); color: #dc2626; border: 1px solid rgba(220,38,38,0.2); }
.permission-badge-approve { background: rgba(20,184,166,0.1); color: #0d9488; border: 1px solid rgba(20,184,166,0.2); }
.permission-badge-upload { background: rgba(99,102,241,0.1); color: #6366f1; border: 1px solid rgba(99,102,241,0.2); }
.permission-badge-audit { background: rgba(236,72,153,0.1); color: #ec4899; border: 1px solid rgba(236,72,153,0.2); }
.permission-badge-manage { background: rgba(5,150,105,0.15); color: #059669; border: 1px solid rgba(5,150,105,0.3); }

html.dark .permission-badge-view { background: rgba(59,130,246,0.2); color: #60a5fa; }
html.dark .permission-badge-create { background: rgba(16,185,129,0.2); color: #34d399; }
html.dark .permission-badge-edit { background: rgba(245,158,11,0.2); color: #fbbf24; }
html.dark .permission-badge-delete { background: rgba(248,113,113,0.2); color: #f87171; }
html.dark .permission-badge-approve { background: rgba(45,212,191,0.2); color: #2dd4bf; }
html.dark .permission-badge-upload { background: rgba(129,140,248,0.2); color: #818cf8; }
html.dark .permission-badge-audit { background: rgba(244,114,182,0.2); color: #f472b6; }
html.dark .permission-badge-manage { background: rgba(16,185,129,0.2); color: #34d399; }

/* ── Floating Save Bar ── */
.floating-save-bar {
    position: fixed;
    bottom: 2rem;
    left: 50%;
    transform: translateX(-50%);
    z-index: 50;
    display: flex;
    align-items: center;
    gap: 1rem;
    background: var(--surface);
    padding: 0.75rem 1.5rem;
    border-radius: 1rem;
    box-shadow: 0 20px 40px rgba(0,0,0,0.2), 0 0 0 1px rgba(212,175,55,0.2);
    border: 1px solid rgba(212,175,55,0.3);
}
.floating-save-indicator {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.floating-save-dot {
    width: 0.5rem;
    height: 0.5rem;
    border-radius: 9999px;
    background: #f59e0b;
    animation: pulse 1.5s ease-in-out infinite;
}
@keyframes pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.6; transform: scale(0.9); }
}
.floating-save-text {
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--text-2);
}
.floating-save-btn {
    padding: 0.375rem 0.875rem;
    font-size: 0.7rem;
    font-weight: 600;
    border-radius: 0.5rem;
    transition: all 0.15s ease;
}
.floating-save-btn-discard {
    background: var(--surface-3);
    color: var(--text-2);
    border: 1px solid var(--border);
}
.floating-save-btn-discard:hover {
    border-color: rgba(212,175,55,0.4);
    color: var(--gold-dark);
}
.floating-save-btn-save {
    background: linear-gradient(135deg, var(--emerald), var(--emerald-dark));
    color: #fff;
    border: none;
}
.floating-save-btn-save:hover:not(:disabled) {
    background: linear-gradient(135deg, var(--gold), var(--gold-dark));
    color: #0f172a;
}

/* ── Legend ── */
.permissions-legend {
    display: flex;
    align-items: center;
    gap: 1rem;
    font-size: 0.7rem;
    color: var(--text-3);
}
.permissions-legend-dot {
    width: 0.75rem;
    height: 0.75rem;
    border-radius: 9999px;
    display: inline-block;
}
.permissions-legend-dot.emerald { background: var(--emerald); }
.permissions-legend-dot.blue { background: #3b82f6; }

/* ── Animations ── */
@keyframes fadeUp {
    from { opacity: 0; transform: translateY(14px); }
    to   { opacity: 1; transform: translateY(0); }
}
.anim-1 { animation: fadeUp 0.38s ease 0.04s both; }
.anim-2 { animation: fadeUp 0.38s ease 0.10s both; }
.anim-3 { animation: fadeUp 0.38s ease 0.16s both; }
.anim-4 { animation: fadeUp 0.38s ease 0.22s both; }

[x-cloak] { display: none !important; }
</style>
@endpush

@section('content')
@php
    // Build a map of module => [manage_id, [individual_ids]]
    // so Alpine can implement the smart toggle logic.
    $rolePermMap  = [];
    foreach ($roles as $role) {
        $rolePermMap[$role->id] = $role->permissions->pluck('id')->toArray();
    }
    $allPermIds = $permissions->flatten()->pluck('id')->toArray();

    // Build module manage map: module => manage permission id (if exists)
    $moduleManageMap = [];
    // Build module members map: module => [non-manage permission ids]
    $moduleIndividualMap = [];
    foreach ($permissions as $module => $modulePerms) {
        foreach ($modulePerms as $perm) {
            if ($perm->action === 'manage') {
                $moduleManageMap[$module] = $perm->id;
            } else {
                $moduleIndividualMap[$module][] = $perm->id;
            }
        }
    }
    
    $totalPerms = $permissions->flatten()->count();
@endphp

<div x-data="{
    selectedRoleId: {{ $selectedRoleId ?? 'null' }},
    saving: false,
    saved: false,
    changed: false,
    search: '',
    rolePermMap: {{ json_encode($rolePermMap) }},
    checkedMap: {},

    // Option C smart toggle maps — passed from Blade
    moduleManageMap: {{ json_encode($moduleManageMap) }},
    moduleIndividualMap: {{ json_encode($moduleIndividualMap) }},

    init() {
        this.loadRole(this.selectedRoleId);
        this.$watch('selectedRoleId', id => {
            this.changed = false;
            this.saved   = false;
            this.loadRole(id);
        });
    },

    loadRole(roleId) {
        const granted = this.rolePermMap[roleId] ?? [];
        const map = {};
        {{ json_encode($allPermIds) }}.forEach(id => { map[id] = granted.includes(id); });
        this.checkedMap = map;
    },

    // Option C: smart toggle
    // - If toggling ON a manage permission → auto-uncheck all individual permissions
    //   in that module (manage is the superset, individuals become redundant)
    // - If toggling ON an individual permission → auto-uncheck manage for that module
    //   (switching to partial access mode)
    // - Toggling OFF has no side effects — just unchecks the clicked permission
    toggle(permId, module) {
        const newState = !this.checkedMap[permId];
        this.checkedMap[permId] = newState;

        if (newState && module) {
            const manageId     = this.moduleManageMap[module] ?? null;
            const individualIds = this.moduleIndividualMap[module] ?? [];

            if (manageId && permId === manageId) {
                // Checked manage → uncheck all individual permissions in this module
                individualIds.forEach(id => { this.checkedMap[id] = false; });
            } else if (manageId && individualIds.includes(permId)) {
                // Checked individual → uncheck manage for this module
                this.checkedMap[manageId] = false;
            }
        }

        this.changed = true;
        this.saved   = false;
    },

    // Select all: if manage exists for module, only check manage (not individuals)
    // If no manage, check all individuals
    selectAll(ids, module) {
        const manageId      = module ? (this.moduleManageMap[module] ?? null) : null;
        const individualIds = module ? (this.moduleIndividualMap[module] ?? []) : [];

        if (manageId && ids.includes(manageId)) {
            // Check manage only, uncheck individuals
            this.checkedMap[manageId] = true;
            individualIds.forEach(id => { this.checkedMap[id] = false; });
        } else {
            // No manage permission in this module — check everything
            ids.forEach(id => { this.checkedMap[id] = true; });
        }
        this.changed = true;
        this.saved   = false;
    },

    clearAll(ids) {
        ids.forEach(id => { this.checkedMap[id] = false; });
        this.changed = true;
        this.saved   = false;
    },

    countGranted(ids) { return ids.filter(id => this.checkedMap[id]).length; },
    totalGranted()    { return Object.values(this.checkedMap).filter(Boolean).length; },

    discard() {
        this.loadRole(this.selectedRoleId);
        this.changed = false;
        this.saved   = false;
    },

    async save() {
        if (!this.selectedRoleId || this.saving) return;
        this.saving = true;

        const granted = Object.entries(this.checkedMap)
            .filter(([, v]) => v)
            .map(([k]) => parseInt(k));

        try {
            const res = await fetch('/admin/permissions/' + this.selectedRoleId, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept':       'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                },
                body: JSON.stringify({ permissions: granted }),
            });

            const data = await res.json();

            if (res.ok && data.success) {
                showNotification(data.message, 'success');
                // Reload the page after a short delay so the user sees the notification
                setTimeout(() => {
                    window.location.reload();
                }, 800);
            } else {
                showNotification(data.message ?? 'Failed to save.', 'error');
                this.saving = false;
            }
        } catch (e) {
            showNotification('Network error. Try again.', 'error');
            this.saving = false;
        }
    }
}" class="space-y-5" x-cloak>

{{-- ── HERO SECTION ── --}}
<div class="permissions-hero anim-1">
    <div class="permissions-hero-content">
        <p class="text-emerald-300/70 text-[10px] font-bold tracking-[0.2em] uppercase mb-2"
           style="font-family:'DM Mono',monospace;">
            {{ now()->format('F Y') }} · Access Control
        </p>
        <h1 class="permissions-hero-title mb-3">Permission<br><span>Matrix</span></h1>
        <div class="flex flex-wrap gap-2">
            <span class="permissions-hero-pill">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                {{ $totalPerms }} Total Permissions
            </span>
            <span class="permissions-hero-pill">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                {{ $roles->count() }} Roles Configured
            </span>
        </div>
    </div>
</div>

{{-- ── STATS CARDS (FIXED - removed dynamic key) ── --}}
<div class="permissions-stat-grid anim-2">
    {{-- Granted Card (Dynamic) --}}
    <div class="permissions-stat-card granted">
        <div class="permissions-stat-icon" style="background: #05966915;">
            <svg class="w-4 h-4" fill="none" stroke="#059669" viewBox="0 0 24 24" stroke-width="1.75">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div class="permissions-stat-num" x-text="totalGranted()">0</div>
        <div class="permissions-stat-label">Granted</div>
        <div class="permissions-stat-sub">For selected role</div>
    </div>

    {{-- Total Permissions Card --}}
    <div class="permissions-stat-card total">
        <div class="permissions-stat-icon" style="background: #2563eb15;">
            <svg class="w-4 h-4" fill="none" stroke="#2563eb" viewBox="0 0 24 24" stroke-width="1.75">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
        </div>
        <div class="permissions-stat-num">{{ number_format($totalPerms) }}</div>
        <div class="permissions-stat-label">Total Permissions</div>
        <div class="permissions-stat-sub">Across all modules</div>
    </div>

    {{-- Modules Card --}}
    <div class="permissions-stat-card modules">
        <div class="permissions-stat-icon" style="background: #7c3aed15;">
            <svg class="w-4 h-4" fill="none" stroke="#7c3aed" viewBox="0 0 24 24" stroke-width="1.75">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
            </svg>
        </div>
        <div class="permissions-stat-num">{{ number_format($modules->count()) }}</div>
        <div class="permissions-stat-label">Modules</div>
        <div class="permissions-stat-sub">Feature groups</div>
    </div>

    {{-- Roles Card --}}
    <div class="permissions-stat-card roles">
        <div class="permissions-stat-icon" style="background: #d9770615;">
            <svg class="w-4 h-4" fill="none" stroke="#d97706" viewBox="0 0 24 24" stroke-width="1.75">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
        </div>
        <div class="permissions-stat-num">{{ number_format($roles->count()) }}</div>
        <div class="permissions-stat-label">Roles</div>
        <div class="permissions-stat-sub">User roles configured</div>
    </div>
</div>

{{-- ── Role Selector + Save ── --}}
<div class="bg-surface rounded-2xl border border-border shadow-sm p-4 anim-3">
    <div class="flex flex-wrap items-center gap-3">
        <span class="text-xs font-semibold text-text-3 uppercase tracking-wider flex-shrink-0 font-mono">
            Select Role
        </span>
        <div class="flex flex-wrap gap-2 flex-1">
            @foreach($roles as $role)
                @php
                    $cnt   = $role->permissions->count();
                    $pct   = $totalPerms > 0 ? round($cnt / $totalPerms * 100) : 0;
                @endphp
                <button type="button"
                        @click="selectedRoleId = {{ $role->id }}"
                        :class="selectedRoleId == {{ $role->id }} ? 'active' : ''"
                        class="role-selector-btn">
                    <span class="role-selector-name">{{ $role->name }}</span>
                    <span class="role-selector-abbr">{{ $role->abbreviation }}</span>
                    <div class="role-progress-bar">
                        <div class="role-progress-track">
                            <div class="role-progress-fill" style="width: {{ $pct }}%"></div>
                        </div>
                        <span class="role-progress-text">{{ $cnt }}/{{ $totalPerms }}</span>
                    </div>
                </button>
            @endforeach
        </div>
        <button type="button"
                @click="save()"
                :disabled="!changed || saving"
                class="btn-save">
            <svg x-show="!saving" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <svg x-show="saving" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            <span x-text="saving ? 'Saving...' : 'Save Changes'"></span>
        </button>
    </div>
</div>

{{-- ── Search + Legend + Discard ── --}}
<div class="flex flex-wrap items-center justify-between gap-3 anim-3">
    <div class="permissions-search">
        <input type="text"
               x-model="search"
               placeholder="Filter permissions by name or module..."
               class="permissions-search-input w-64">
        <svg class="permissions-search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
    </div>

    <div class="permissions-legend">
        <span class="flex items-center gap-1.5">
            <span class="permissions-legend-dot emerald"></span>
            manage = full module access
        </span>
        <span class="flex items-center gap-1.5">
            <span class="permissions-legend-dot blue"></span>
            individual = partial access
        </span>
    </div>

    <button type="button"
            x-show="changed"
            x-cloak
            @click="discard()"
            class="text-sm text-text-3 hover:text-gold dark:hover:text-gold-light px-4 py-2 rounded-xl border border-border hover:border-gold/50 hover:bg-gold/5 transition-all duration-200">
        Discard Changes
    </button>
</div>

{{-- ── Permission Module Cards ── --}}
<div class="space-y-4 anim-4">
    @foreach($permissions as $module => $modulePerms)
        @php
            $permIds   = $modulePerms->pluck('id')->toArray();
            $manageId  = $moduleManageMap[$module] ?? null;
        @endphp

        <div x-show="search === '' || '{{ strtolower($module) }}'.includes(search.toLowerCase()) || {{ json_encode($modulePerms->pluck('name')->map(fn($n) => strtolower($n))->values()) }}.some(n => n.includes(search.toLowerCase()))"
             class="permissions-module-card">
            
            <div class="permissions-module-header">
                <div>
                    <h3 class="permissions-module-title">{{ $module }}</h3>
                    <p class="permissions-module-stats">
                        <span x-text="countGranted({{ json_encode($permIds) }})"></span>
                        / {{ count($permIds) }} granted
                        @if($manageId)
                            <span class="ml-2">— check "manage" for full access</span>
                        @endif
                    </p>
                </div>
                <div class="permissions-module-actions">
                    <button type="button"
                            @click="selectAll({{ json_encode($permIds) }}, '{{ $module }}')"
                            class="permissions-module-btn">
                        All
                    </button>
                    <button type="button"
                            @click="clearAll({{ json_encode($permIds) }})"
                            class="permissions-module-btn">
                        None
                    </button>
                </div>
            </div>

            <div class="permissions-grid">
                @foreach($modulePerms as $perm)
                    @php
                        $isManage = $perm->action === 'manage';
                        $badgeClass = 'permission-badge-' . ($perm->action ?? 'default');
                    @endphp

                    <div @click="toggle({{ $perm->id }}, '{{ $module }}')"
                         :class="checkedMap[{{ $perm->id }}] ? 'checked' : ''"
                         class="permission-card"
                         x-show="search === '' || '{{ strtolower($perm->name) }}'.includes(search.toLowerCase()) || '{{ strtolower($perm->slug) }}'.includes(search.toLowerCase())">

                        <div :class="checkedMap[{{ $perm->id }}] ? 'checked' : ''" class="permission-checkbox">
                            <svg x-show="checkedMap[{{ $perm->id }}]"
                                 class="w-2 h-2 text-white"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>

                        <div class="permission-content">
                            <div class="permission-name">
                                {{ $perm->name }}
                                @if($isManage)
                                    <span class="ml-1 text-xs text-emerald-600 dark:text-emerald-400">★</span>
                                @endif
                            </div>
                            <div class="permission-slug">{{ $perm->slug }}</div>
                            @if($isManage)
                                <p class="text-xs text-emerald-600 dark:text-emerald-400 mt-0.5">Grants full module access</p>
                            @endif
                            <span class="permission-badge {{ $badgeClass }}">
                                {{ ucfirst($perm->action ?? 'permission') }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
</div>

{{-- ── Floating Save Bar ── --}}
<div x-show="changed" x-cloak
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 translate-y-4"
     x-transition:enter-end="opacity-100 translate-y-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0 translate-y-4"
     class="floating-save-bar">
    <div class="floating-save-indicator">
        <span class="floating-save-dot"></span>
        <span class="floating-save-text">Unsaved changes</span>
    </div>
    <div class="flex items-center gap-2">
        <button type="button"
                @click="discard()"
                class="floating-save-btn floating-save-btn-discard">
            Discard
        </button>
        <button type="button"
                @click="save()"
                :disabled="saving"
                class="floating-save-btn floating-save-btn-save">
            <span x-text="saving ? 'Saving...' : 'Save Changes'"></span>
        </button>
    </div>
</div>

</div>
@endsection