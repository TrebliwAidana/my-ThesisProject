@extends('layouts.app')
@section('title', 'Permission Matrix')
@section('page-title', 'Permission Management')
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
                this.rolePermMap[this.selectedRoleId] = granted;
                this.saved   = true;
                this.changed = false;
                showNotification(data.message, 'success');
            } else {
                showNotification(data.message ?? 'Failed to save.', 'error');
            }
        } catch (e) {
            showNotification('Network error. Try again.', 'error');
        } finally {
            this.saving = false;
        }
    }
}">

{{-- ── Header ───────────────────────────────────────────────────────────────── --}}
<div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-emerald-600 to-emerald-700 dark:from-emerald-800 dark:to-emerald-900 p-6 mb-6">
    <div class="relative z-10 flex items-start justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-bold text-white">Permission Matrix</h1>
            <p class="text-emerald-100 text-sm mt-1">Control which actions each role can perform across modules</p>
        </div>
        <div class="flex items-center gap-2 mt-1">
            <span x-show="changed && !saved" x-cloak
                  class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-yellow-400/20 text-yellow-200 border border-yellow-400/30">
                <span class="w-1.5 h-1.5 rounded-full bg-yellow-400 animate-pulse"></span>
                Unsaved changes
            </span>
            <span x-show="saved" x-cloak
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

{{-- ── Stats ────────────────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gold-200 dark:border-gold-800 p-5 shadow-sm text-center">
        <p class="text-2xl font-bold text-gray-800 dark:text-white" x-text="totalGranted()">—</p>
        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mt-1">Granted</p>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">For selected role</p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gold-200 dark:border-gold-800 p-5 shadow-sm text-center">
        <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $permissions->flatten()->count() }}</p>
        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mt-1">Total Permissions</p>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Across all modules</p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gold-200 dark:border-gold-800 p-5 shadow-sm text-center">
        <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $modules->count() }}</p>
        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mt-1">Modules</p>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Feature groups</p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gold-200 dark:border-gold-800 p-5 shadow-sm text-center">
        <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $roles->count() }}</p>
        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mt-1">Roles</p>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">User roles configured</p>
    </div>
</div>

{{-- ── Role Selector + Save ─────────────────────────────────────────────────── --}}
<div class="bg-white dark:bg-gray-800 rounded-2xl border border-gold-200 dark:border-gold-800 shadow-sm p-4 mb-5">
    <div class="flex flex-wrap items-center gap-3">
        <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider flex-shrink-0">
            Select Role
        </span>
        <div class="flex flex-wrap gap-2 flex-1">
            @foreach($roles as $role)
                @php
                    $total = $permissions->flatten()->count();
                    $cnt   = $role->permissions->count();
                    $pct   = $total > 0 ? round($cnt / $total * 100) : 0;
                @endphp
                <button type="button"
                        @click="selectedRoleId = {{ $role->id }}"
                        :class="selectedRoleId == {{ $role->id }}
                            ? 'bg-emerald-600 text-white border-emerald-600 shadow-md'
                            : 'bg-white dark:bg-gray-700/50 text-gray-700 dark:text-gray-300 border-gold-200 dark:border-gold-800 hover:border-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/20'"
                        class="flex flex-col items-start px-3 py-2 rounded-xl border-2 text-left transition-all min-w-[100px]">
                    <span class="text-xs font-bold leading-tight">{{ $role->name }}</span>
                    <span class="text-xs opacity-60 mt-0.5">{{ $role->abbreviation }}</span>
                    <div class="mt-1.5 w-full">
                        <div class="h-1 w-full rounded-full bg-black/10 dark:bg-white/10 overflow-hidden">
                            <div class="h-full rounded-full bg-current opacity-50" style="width: {{ $pct }}%"></div>
                        </div>
                        <span class="text-xs opacity-50 mt-0.5 block">{{ $cnt }}/{{ $total }}</span>
                    </div>
                </button>
            @endforeach
        </div>
        <button type="button"
                @click="save()"
                :disabled="!changed || saving"
                :class="changed && !saving
                    ? 'bg-emerald-600 hover:bg-emerald-700 text-white shadow-md'
                    : 'bg-gray-100 dark:bg-gray-700 text-gray-400 dark:text-gray-500 cursor-not-allowed'"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold transition flex-shrink-0">
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
<div class="flex flex-wrap items-center justify-between gap-3 mb-4">
    <div class="relative">
        <input type="text"
               x-model="search"
               placeholder="Filter permissions..."
               class="pl-10 pr-4 py-2.5 text-sm border border-gold-200 dark:border-gold-800 rounded-xl bg-gray-50 dark:bg-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 w-64 transition">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
    </div>

    {{-- Option C legend --}}
    <div class="flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
        <span class="flex items-center gap-1.5">
            <span class="w-3 h-3 rounded-full bg-emerald-500 inline-block"></span>
            manage = full module access
        </span>
        <span class="flex items-center gap-1.5">
            <span class="w-3 h-3 rounded-full bg-blue-400 inline-block"></span>
            individual = partial access
        </span>
    </div>

    <button type="button"
            x-show="changed"
            x-cloak
            @click="discard()"
            class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 px-4 py-2.5 rounded-xl border border-gold-200 dark:border-gold-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
        Discard Changes
    </button>
</div>

{{-- ── Permission Module Cards ──────────────────────────────────────────────── --}}
<div class="space-y-4">
    @foreach($permissions as $module => $modulePerms)
        @php
            $permIds   = $modulePerms->pluck('id')->toArray();
            $manageId  = $moduleManageMap[$module] ?? null;
        @endphp

        <div x-show="search === '' || '{{ strtolower($module) }}'.includes(search.toLowerCase()) || {{ json_encode($modulePerms->pluck('name')->map(fn($n) => strtolower($n))->values()) }}.some(n => n.includes(search.toLowerCase()))"
             class="bg-white dark:bg-gray-800 rounded-2xl border border-gold-200 dark:border-gold-800 shadow-sm overflow-hidden">

            {{-- Module Header --}}
            <div class="flex items-center justify-between px-5 py-3.5 bg-emerald-600 dark:bg-emerald-700">
                <div>
                    <h3 class="text-sm font-bold text-white capitalize">{{ $module }}</h3>
                    <p class="text-xs text-emerald-100">
                        <span x-text="countGranted({{ json_encode($permIds) }})"></span>
                        / {{ count($permIds) }} granted
                        @if($manageId)
                            <span class="ml-2 opacity-75">— check "manage" for full access</span>
                        @endif
                    </p>
                </div>
                <div class="flex gap-2">
                    <button type="button"
                            @click="selectAll({{ json_encode($permIds) }}, '{{ $module }}')"
                            class="text-xs px-2.5 py-1 rounded-lg bg-white/10 hover:bg-white/20 text-white transition font-medium">
                        All
                    </button>
                    <button type="button"
                            @click="clearAll({{ json_encode($permIds) }})"
                            class="text-xs px-2.5 py-1 rounded-lg bg-white/10 hover:bg-white/20 text-white transition font-medium">
                        None
                    </button>
                </div>
            </div>

            {{-- Permissions Grid --}}
            <div class="p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3">
                @foreach($modulePerms as $perm)
                    @php
                        $isManage = $perm->action === 'manage';
                        $colors   = match($perm->action ?? '') {
                            'view'    => ['card' => 'bg-blue-50 dark:bg-blue-900/20 border-blue-300 dark:border-blue-700',         'badge' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300'],
                            'create'  => ['card' => 'bg-green-50 dark:bg-green-900/20 border-green-300 dark:border-green-700',     'badge' => 'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-300'],
                            'edit'    => ['card' => 'bg-yellow-50 dark:bg-yellow-900/20 border-yellow-300 dark:border-yellow-700', 'badge' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/50 dark:text-yellow-300'],
                            'delete'  => ['card' => 'bg-red-50 dark:bg-red-900/20 border-red-300 dark:border-red-700',             'badge' => 'bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-300'],
                            'approve' => ['card' => 'bg-teal-50 dark:bg-teal-900/20 border-teal-300 dark:border-teal-700',         'badge' => 'bg-teal-100 text-teal-700 dark:bg-teal-900/50 dark:text-teal-300'],
                            'upload'  => ['card' => 'bg-indigo-50 dark:bg-indigo-900/20 border-indigo-300 dark:border-indigo-700', 'badge' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300'],
                            'audit'   => ['card' => 'bg-pink-50 dark:bg-pink-900/20 border-pink-300 dark:border-pink-700',         'badge' => 'bg-pink-100 text-pink-700 dark:bg-pink-900/50 dark:text-pink-300'],
                            // manage gets a special emerald style to stand out visually
                            'manage'  => ['card' => 'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-400 dark:border-emerald-600', 'badge' => 'bg-emerald-200 text-emerald-800 dark:bg-emerald-800 dark:text-emerald-200'],
                            default   => ['card' => 'bg-gray-50 dark:bg-gray-700/30 border-gray-300 dark:border-gray-600',         'badge' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300'],
                        };
                    @endphp

                    <div @click="toggle({{ $perm->id }}, '{{ $module }}')"
                         :class="checkedMap[{{ $perm->id }}]
                             ? '{{ $colors['card'] }} ring-2 ring-emerald-400/30'
                             : 'bg-gray-50 dark:bg-gray-700/30 border-gray-200 dark:border-gray-700 opacity-50 hover:opacity-80'"
                         class="flex items-start gap-3 p-3 rounded-xl border-2 cursor-pointer transition-all duration-150 select-none group {{ $isManage ? 'col-span-1 sm:col-span-2 lg:col-span-1' : '' }}"
                         x-show="search === '' || '{{ strtolower($perm->name) }}'.includes(search.toLowerCase()) || '{{ strtolower($perm->slug) }}'.includes(search.toLowerCase())">

                        {{-- Checkbox --}}
                        <div :class="checkedMap[{{ $perm->id }}]
                                 ? 'bg-emerald-600 border-emerald-600'
                                 : 'bg-white dark:bg-gray-600 border-gray-300 dark:border-gray-500 group-hover:border-emerald-400'"
                             class="w-4 h-4 rounded border-2 flex items-center justify-center flex-shrink-0 mt-0.5 transition-colors">
                            <svg x-show="checkedMap[{{ $perm->id }}]"
                                 class="w-2.5 h-2.5 text-white"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-1.5">
                                <p class="text-xs font-semibold text-gray-900 dark:text-white leading-tight">{{ $perm->name }}</p>
                                @if($isManage)
                                    <span class="text-xs px-1 py-0.5 rounded bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300 font-bold leading-none">★</span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-400 dark:text-gray-500 font-mono mt-0.5 truncate">{{ $perm->slug }}</p>
                            @if($isManage)
                                <p class="text-xs text-emerald-600 dark:text-emerald-400 mt-0.5">Grants full module access</p>
                            @endif
                            <span class="inline-flex items-center mt-1.5 px-1.5 py-0.5 rounded-full text-xs font-medium {{ $colors['badge'] }}">
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
<div x-show="changed" x-cloak
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 translate-y-4"
     x-transition:enter-end="opacity-100 translate-y-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0 translate-y-4"
     class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50">
    <div class="flex items-center gap-4 bg-gray-900 dark:bg-gray-700 text-white px-5 py-3 rounded-2xl shadow-2xl border border-gray-700">
        <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-yellow-400 animate-pulse"></span>
            <span class="text-sm font-medium">Unsaved changes</span>
        </div>
        <div class="flex items-center gap-2">
            <button type="button"
                    @click="discard()"
                    class="text-xs px-3 py-1.5 rounded-lg border border-gray-600 hover:bg-gray-800 transition">
                Discard
            </button>
            <button type="button"
                    @click="save()"
                    :disabled="saving"
                    class="text-xs px-4 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 font-semibold transition flex items-center gap-1.5">
                <svg x-show="saving" class="w-3 h-3 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                <span x-text="saving ? 'Saving...' : 'Save Changes'"></span>
            </button>
        </div>
    </div>
</div>

</div>
@endsection