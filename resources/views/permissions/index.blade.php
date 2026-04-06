@extends('layouts.app')

@section('title', 'Permission Matrix — VSULHS_SSLG')
@section('page-title', 'Permission Management')

@section('content')

{{-- Pre-build the full permission map for ALL roles in PHP so Alpine
     never has to read DOM checkboxes. Each role's granted permission IDs
     are passed as a JSON object keyed by role ID. --}}
@php
    $rolePermMap = [];
    foreach ($roles as $role) {
        $rolePermMap[$role->id] = $role->permissions->pluck('id')->toArray();
    }
    $allPermIds = $permissions->flatten()->pluck('id')->toArray();
@endphp

<div
    x-data="{
        selectedRoleId: {{ $selectedRoleId ?? 'null' }},
        saving: false,
        saved: false,
        changed: false,
        search: '',

        // Master map: roleId → Set of granted permission IDs
        // Built once from PHP data — no DOM reading needed
        rolePermMap: {{ json_encode($rolePermMap) }},

        // Working copy for the currently selected role
        checkedMap: {},

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
            {{ json_encode($allPermIds) }}.forEach(id => {
                map[id] = granted.includes(id);
            });
            this.checkedMap = map;
        },

        toggle(permId) {
            this.checkedMap[permId] = !this.checkedMap[permId];
            this.changed = true;
            this.saved   = false;
        },

        selectAll(permIds) {
            permIds.forEach(id => { this.checkedMap[id] = true; });
            this.changed = true; this.saved = false;
        },

        clearAll(permIds) {
            permIds.forEach(id => { this.checkedMap[id] = false; });
            this.changed = true; this.saved = false;
        },

        countGranted(permIds) {
            return permIds.filter(id => this.checkedMap[id]).length;
        },

        totalGranted() {
            return Object.values(this.checkedMap).filter(Boolean).length;
        },

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

            const formData = new FormData();
            formData.append('_method', 'PUT');
            formData.append('_token', document.querySelector('meta[name=csrf-token]').content);
            granted.forEach(id => formData.append('permissions[]', id));

            try {
                const res = await fetch(`/admin/permissions/${this.selectedRoleId}`, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json' },
                    body: formData,
                });
                const data = await res.json();
                if (data.success) {
                    // Update the master map so switching away and back reflects saved state
                    this.rolePermMap[this.selectedRoleId] = granted;
                    this.saved   = true;
                    this.changed = false;
                    showNotification(data.message, 'success');
                } else {
                    showNotification('Failed to save permissions.', 'error');
                }
            } catch(e) {
                showNotification('Network error. Please try again.', 'error');
            } finally {
                this.saving = false;
            }
        }
    }"
>

{{-- ── Header ──────────────────────────────────────────────────────────────── --}}
<div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-primary-600 to-primary-700 dark:from-primary-800 dark:to-primary-900 p-6 mb-6">
    <div class="relative z-10 flex items-start justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-bold text-white">Permission Matrix</h1>
            <p class="text-primary-100 text-sm mt-1">Control which actions each role can perform across modules</p>
        </div>
        <div class="flex items-center gap-2 mt-1">
            <span x-show="changed && !saved"
                  class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-yellow-400/20 text-yellow-200 border border-yellow-400/30">
                <span class="w-1.5 h-1.5 rounded-full bg-yellow-400 animate-pulse"></span>
                Unsaved changes
            </span>
            <span x-show="saved"
                  class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-green-400/20 text-green-200 border border-green-400/30">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Saved
            </span>
        </div>
    </div>
    <div class="absolute top-0 right-0 -mt-8 -mr-8 w-64 h-64 bg-white/5 rounded-full blur-3xl pointer-events-none"></div>
</div>

{{-- ── Stats Cards ─────────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gold-200 dark:border-gold-800 p-5 shadow-sm hover:shadow-lg transition-all text-center">
        <p class="text-2xl font-bold text-gray-800 dark:text-white" x-text="totalGranted()">—</p>
        <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mt-1">Granted</p>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">For selected role</p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gold-200 dark:border-gold-800 p-5 shadow-sm hover:shadow-lg transition-all text-center">
        <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $permissions->flatten()->count() }}</p>
        <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mt-1">Total Permissions</p>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Across all modules</p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gold-200 dark:border-gold-800 p-5 shadow-sm hover:shadow-lg transition-all text-center">
        <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $modules->count() }}</p>
        <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mt-1">Modules</p>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Feature groups</p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gold-200 dark:border-gold-800 p-5 shadow-sm hover:shadow-lg transition-all text-center">
        <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $roles->count() }}</p>
        <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mt-1">Roles</p>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">User roles configured</p>
    </div>
</div>

{{-- ── Role Selector + Save ─────────────────────────────────────────────────── --}}
<div class="bg-white dark:bg-gray-800 rounded-2xl border border-gold-200 dark:border-gold-800 shadow-sm p-4 mb-5">
    <div class="flex flex-wrap items-center gap-3">
        <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider flex-shrink-0">Select Role</span>

        <div class="flex flex-wrap gap-2 flex-1">
            @foreach($roles as $role)
            @php
                $total = $permissions->flatten()->count();
                $granted = $role->permissions->count();
                $pct = $total > 0 ? round($granted / $total * 100) : 0;
            @endphp
            <button
                @click="selectedRoleId = {{ $role->id }}"
                :class="selectedRoleId == {{ $role->id }}
                    ? 'bg-primary-600 text-white border-primary-600 shadow-md'
                    : 'bg-white dark:bg-gray-700/50 text-gray-700 dark:text-gray-300 border-gold-200 dark:border-gold-800 hover:border-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/20'"
                class="flex flex-col items-start px-3 py-2 rounded-xl border-2 text-left transition-all duration-150 min-w-[110px]"
            >
                <span class="text-xs font-bold leading-tight">{{ $role->name }}</span>
                <span class="text-xs opacity-70 mt-0.5">{{ $role->abbreviation }}</span>
                <div class="mt-1.5 w-full">
                    <div class="h-1 w-full rounded-full bg-black/10 dark:bg-white/10 overflow-hidden">
                        <div class="h-full rounded-full bg-current opacity-50 transition-all"
                             style="width: {{ $pct }}%"></div>
                    </div>
                    <span class="text-xs opacity-50 mt-0.5 block">{{ $granted }}/{{ $total }}</span>
                </div>
            </button>
            @endforeach
        </div>

        <button
            @click="save()"
            :disabled="!changed || saving"
            :class="changed && !saving
                ? 'bg-primary-600 hover:bg-gold-500 text-white shadow-md'
                : 'bg-gray-100 dark:bg-gray-700 text-gray-400 dark:text-gray-500 cursor-not-allowed'"
            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-150 flex-shrink-0"
        >
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

{{-- ── Search + Discard ─────────────────────────────────────────────────────── --}}
<div class="flex flex-wrap items-center justify-between gap-3 mb-5">
    <div class="relative">
        <input
            type="text"
            x-model="search"
            placeholder="Filter permissions..."
            class="pl-10 pr-4 py-2.5 text-sm border border-gold-200 dark:border-gold-800 rounded-xl bg-gray-50 dark:bg-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-gold-500 w-64 transition"
        >
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
    </div>
    <button
        x-show="changed"
        @click="discard()"
        class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 px-4 py-2.5 rounded-xl border border-gold-200 dark:border-gold-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition"
    >
        Discard Changes
    </button>
</div>

{{-- ── Permission Module Cards ──────────────────────────────────────────────── --}}
<div class="space-y-4">
    @foreach($permissions as $module => $modulePerms)
    @php
        $permIds = $modulePerms->pluck('id')->toArray();
        $moduleIcons = [
            'users'        => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
            'members'      => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
            'documents'    => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
            'budgets'      => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
            'reports'      => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
            'roles'        => 'M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z',
            'permissions'  => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
            'organization' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
            'admin'        => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
        ];
        $icon = $moduleIcons[$module] ?? 'M5 12h14M12 5l7 7-7 7';
    @endphp

    <div
        x-show="search === '' || '{{ strtolower($module) }}'.includes(search.toLowerCase()) || {{ json_encode($modulePerms->pluck('name')->map(fn($n) => strtolower($n))->values()) }}.some(n => n.includes(search.toLowerCase()))"
        class="bg-white dark:bg-gray-800 rounded-2xl border border-gold-200 dark:border-gold-800 shadow-xl overflow-hidden"
    >
        {{-- Module Header --}}
        <div class="flex items-center justify-between px-5 py-3.5 bg-primary-600 dark:bg-primary-700 border-b border-gold-200 dark:border-gold-800">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-white/10 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-white capitalize">{{ $module }}</h3>
                    <p class="text-xs text-primary-100">
                        <span x-text="countGranted({{ json_encode($permIds) }})"></span>
                        / {{ count($permIds) }} permissions granted
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button
                    @click="selectAll({{ json_encode($permIds) }})"
                    class="text-xs px-2.5 py-1 rounded-lg bg-white/10 hover:bg-white/20 text-white transition font-medium"
                >All</button>
                <button
                    @click="clearAll({{ json_encode($permIds) }})"
                    class="text-xs px-2.5 py-1 rounded-lg bg-white/10 hover:bg-white/20 text-white transition font-medium"
                >None</button>
            </div>
        </div>

        {{-- Permissions Grid --}}
        <div class="p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3">
            @foreach($modulePerms as $perm)
            @php
                $checkedBg = [
                    'view'        => 'bg-blue-50 dark:bg-blue-900/20 border-blue-300 dark:border-blue-700',
                    'manage'      => 'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-300 dark:border-emerald-700',
                    'create'      => 'bg-green-50 dark:bg-green-900/20 border-green-300 dark:border-green-700',
                    'edit'        => 'bg-yellow-50 dark:bg-yellow-900/20 border-yellow-300 dark:border-yellow-700',
                    'delete'      => 'bg-red-50 dark:bg-red-900/20 border-red-300 dark:border-red-700',
                    'upload'      => 'bg-indigo-50 dark:bg-indigo-900/20 border-indigo-300 dark:border-indigo-700',
                    'review'      => 'bg-purple-50 dark:bg-purple-900/20 border-purple-300 dark:border-purple-700',
                    'approve'     => 'bg-teal-50 dark:bg-teal-900/20 border-teal-300 dark:border-teal-700',
                    'submit'      => 'bg-cyan-50 dark:bg-cyan-900/20 border-cyan-300 dark:border-cyan-700',
                    'disburse'    => 'bg-orange-50 dark:bg-orange-900/20 border-orange-300 dark:border-orange-700',
                    'audit'       => 'bg-pink-50 dark:bg-pink-900/20 border-pink-300 dark:border-pink-700',
                    'roles'       => 'bg-violet-50 dark:bg-violet-900/20 border-violet-300 dark:border-violet-700',
                    'permissions' => 'bg-rose-50 dark:bg-rose-900/20 border-rose-300 dark:border-rose-700',
                    'users'       => 'bg-slate-50 dark:bg-slate-900/20 border-slate-300 dark:border-slate-600',
                ];
                $actionBadge = [
                    'view'        => 'bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300',
                    'manage'      => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300',
                    'create'      => 'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-300',
                    'edit'        => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/50 dark:text-yellow-300',
                    'delete'      => 'bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-300',
                    'upload'      => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300',
                    'review'      => 'bg-purple-100 text-purple-700 dark:bg-purple-900/50 dark:text-purple-300',
                    'approve'     => 'bg-teal-100 text-teal-700 dark:bg-teal-900/50 dark:text-teal-300',
                    'submit'      => 'bg-cyan-100 text-cyan-700 dark:bg-cyan-900/50 dark:text-cyan-300',
                    'disburse'    => 'bg-orange-100 text-orange-700 dark:bg-orange-900/50 dark:text-orange-300',
                    'audit'       => 'bg-pink-100 text-pink-700 dark:bg-pink-900/50 dark:text-pink-300',
                    'roles'       => 'bg-violet-100 text-violet-700 dark:bg-violet-900/50 dark:text-violet-300',
                    'permissions' => 'bg-rose-100 text-rose-700 dark:bg-rose-900/50 dark:text-rose-300',
                    'users'       => 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300',
                ];
                $cardChecked = $checkedBg[$perm->action]   ?? $checkedBg['manage'];
                $badge       = $actionBadge[$perm->action] ?? $actionBadge['manage'];
            @endphp

            <div
                @click="toggle({{ $perm->id }})"
                :class="checkedMap[{{ $perm->id }}]
                    ? '{{ $cardChecked }} ring-2 ring-primary-400/30'
                    : 'bg-gray-50 dark:bg-gray-700/30 border-gold-200 dark:border-gold-800 opacity-50 hover:opacity-75'"
                class="flex items-start gap-3 p-3 rounded-xl border-2 cursor-pointer transition-all duration-150 select-none group"
                x-show="search === '' || '{{ strtolower($perm->name) }}'.includes(search.toLowerCase()) || '{{ strtolower($perm->slug) }}'.includes(search.toLowerCase())"
            >
                {{-- Checkbox indicator --}}
                <div
                    :class="checkedMap[{{ $perm->id }}]
                        ? 'bg-primary-600 border-primary-600'
                        : 'bg-white dark:bg-gray-600 border-gray-300 dark:border-gray-500 group-hover:border-primary-400'"
                    class="w-4 h-4 rounded border-2 flex items-center justify-center flex-shrink-0 mt-0.5 transition-colors"
                >
                    <svg x-show="checkedMap[{{ $perm->id }}]" class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>

                <div class="min-w-0 flex-1">
                    <p class="text-xs font-semibold text-gray-900 dark:text-white leading-tight">{{ $perm->name }}</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 font-mono mt-0.5 truncate">{{ $perm->slug }}</p>
                    <span class="inline-flex items-center mt-1.5 px-1.5 py-0.5 rounded-full text-xs font-medium {{ $badge }}">
                        {{ $perm->action }}
                    </span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
</div>

{{-- ── Floating Save Bar ────────────────────────────────────────────────────── --}}
<div
    x-show="changed"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-4"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0 translate-y-4"
    class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50"
>
    <div class="flex items-center gap-4 bg-gray-900 dark:bg-gray-700 text-white px-5 py-3 rounded-2xl shadow-2xl border border-gray-700">
        <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-yellow-400 animate-pulse"></span>
            <span class="text-sm font-medium">Unsaved changes</span>
        </div>
        <div class="flex items-center gap-2">
            <button
                @click="discard()"
                class="text-xs px-3 py-1.5 rounded-lg border border-gray-600 hover:bg-gray-800 transition"
            >Discard</button>
            <button
                @click="save()"
                :disabled="saving"
                class="text-xs px-4 py-1.5 rounded-lg bg-primary-600 hover:bg-gold-500 font-semibold transition flex items-center gap-1.5"
            >
                <svg x-show="saving" class="w-3 h-3 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                <span x-text="saving ? 'Saving...' : 'Save Changes'"></span>
            </button>
        </div>
    </div>
</div>

</div>

@push('scripts')
<script>
function showNotification(message, type = 'success') {
    const container = document.getElementById('notification-container');
    if (!container) return;
    const colors = { success: 'bg-primary-600', error: 'bg-red-600', warning: 'bg-yellow-600' };
    const el = document.createElement('div');
    el.className = `pointer-events-auto ${colors[type] ?? colors.success} text-white text-sm font-medium px-4 py-3 rounded-xl shadow-lg mb-2 flex items-center gap-2 transition-all duration-300`;
    el.innerHTML = `
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="${type === 'success' ? 'M5 13l4 4L19 7' : 'M6 18L18 6M6 6l12 12'}"/>
        </svg>
        <span>${message}</span>
    `;
    container.appendChild(el);
    setTimeout(() => { el.style.opacity = '0'; setTimeout(() => el.remove(), 300); }, 3500);
}
</script>
@endpush
@endsection