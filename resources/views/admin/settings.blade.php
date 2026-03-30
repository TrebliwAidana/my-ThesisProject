@extends('layouts.app')

@section('title', 'Settings')
@section('page-title', 'Settings')

@section('content')
<div
    x-data="{
        selectedTheme: '{{ auth()->user()->theme ?? 'navy' }}',
        saving: false,
        saved: false,

        async saveTheme() {
            this.saving = true
            this.saved  = false
            try {
                await fetch('{{ route('settings.theme.update') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type':  'application/json',
                        'X-CSRF-TOKEN':  document.querySelector('meta[name=csrf-token]').content,
                        'Accept':        'application/json',
                    },
                    body: JSON.stringify({ theme: this.selectedTheme })
                })
                this.$dispatch('theme-changed', { theme: this.selectedTheme })
                this.saved = true
                setTimeout(() => this.saved = false, 2500)
            } finally {
                this.saving = false
            }
        }
    }"
    @theme-changed.window="$store.app ? $store.app.theme = $event.detail.theme : null"
>

    <div class="max-w-2xl space-y-6">

        {{-- ── Appearance section ── --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Appearance</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Customize how the portal looks for you</p>
            </div>

            {{-- Theme picker --}}
            <div class="px-5 py-5 border-b border-gray-100 dark:border-gray-700">
                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">
                    Sidebar theme
                </label>

                <div class="flex flex-wrap gap-4">
                    @php
                    $themes = [
                        ['key' => 'navy',    'label' => 'Navy',    'color' => '#1a1a2e', 'dark' => true],
                        ['key' => 'forest',  'label' => 'Forest',  'color' => '#0F2D1A', 'dark' => true],
                        ['key' => 'crimson', 'label' => 'Crimson', 'color' => '#2D0F0F', 'dark' => true],
                        ['key' => 'slate',   'label' => 'Slate',   'color' => '#1C2333', 'dark' => true],
                        ['key' => 'amber',   'label' => 'Amber',   'color' => '#2B1F05', 'dark' => true],
                        ['key' => 'rose',    'label' => 'Rose',    'color' => '#2D0F1E', 'dark' => true],
                        ['key' => 'light',   'label' => 'Light',   'color' => '#f0ede8', 'border' => true, 'dark' => false],
                    ];
                    @endphp

                    @foreach($themes as $t)
                    <button
                        type="button"
                        @click="selectedTheme = '{{ $t['key'] }}'"
                        :class="selectedTheme === '{{ $t['key'] }}' ? 'ring-2 ring-offset-2 ring-indigo-500 scale-110' : 'ring-1 ring-gray-200 dark:ring-gray-600 hover:scale-105'"
                        class="flex flex-col items-center gap-1.5 transition-all duration-200"
                        title="{{ $t['label'] }}"
                    >
                        <span
                            class="w-8 h-8 rounded-full block {{ isset($t['border']) ? 'border border-gray-300 dark:border-gray-600' : '' }} transition-transform duration-200"
                            style="background-color: {{ $t['color'] }}"
                        ></span>
                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $t['label'] }}</span>
                    </button>
                    @endforeach
                </div>

                {{-- Live preview strip --}}
                <div class="mt-4 rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700 flex h-10">
                    <div class="w-24 flex items-center justify-center text-xs font-medium transition-colors duration-200"
                         :style="`background-color: var(--sb-bg, #1a1a2e); color: rgba(255,255,255,0.8)`">
                        Sidebar
                    </div>
                    <div class="flex-1 flex items-center px-3 text-xs text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-800/50">
                        Main content area
                    </div>
                </div>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1.5">Preview updates after saving</p>
            </div>

            {{-- Save button --}}
            <div class="px-5 py-4 flex items-center gap-3 bg-gray-50 dark:bg-gray-800/50">
                <button
                    type="button"
                    @click="saveTheme()"
                    :disabled="saving"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white disabled:opacity-50 transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-[1.02]"
                >
                    <svg x-show="!saving" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                    </svg>
                    <span x-show="!saving">Save appearance</span>
                    <span x-show="saving" class="flex items-center gap-2">
                        <svg class="animate-spin w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Saving...
                    </span>
                </button>
                <span
                    x-show="saved"
                    x-transition.opacity
                    class="text-xs text-green-600 dark:text-green-400 font-medium flex items-center gap-1"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Saved
                </span>
            </div>
        </div>

        {{-- ── Account info section ── --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Account Information</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Your profile details and role information</p>
            </div>
            @php $u = auth()->user(); @endphp
            <dl class="divide-y divide-gray-100 dark:divide-gray-700">
                <div class="flex items-center justify-between px-5 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                    <dt class="text-xs text-gray-500 dark:text-gray-400">Full name</dt>
                    <dd class="text-xs font-medium text-gray-900 dark:text-white">{{ $u->full_name }}</dd>
                </div>
                <div class="flex items-center justify-between px-5 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                    <dt class="text-xs text-gray-500 dark:text-gray-400">Email address</dt>
                    <dd class="text-xs font-medium text-gray-900 dark:text-white">{{ $u->email }}</dd>
                </div>
                <div class="flex items-center justify-between px-5 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                    <dt class="text-xs text-gray-500 dark:text-gray-400">Role</dt>
                    <dd class="flex items-center gap-2">
                        @php
                            $roleColors = [
                                'System Administrator' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/50 dark:text-purple-300',
                                'Supreme Admin' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300',
                                'Supreme Officer' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300',
                                'Org Admin' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300',
                                'Org Officer' => 'bg-sky-100 text-sky-700 dark:bg-sky-900/50 dark:text-sky-300',
                                'Adviser' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-300',
                                'Org Member' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
                                'Guest' => 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400',
                            ];
                            $colorClass = $roleColors[$u->role->name] ?? 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300';
                        @endphp
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium {{ $colorClass }}">
                            {{ $u->role->name }}
                        </span>
                        @if($u->role->abbreviation)
                            <span class="text-xs text-gray-400 dark:text-gray-500 font-mono">
                                ({{ $u->role->abbreviation }})
                            </span>
                        @endif
                    </dd>
                </div>
                <div class="flex items-center justify-between px-5 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                    <dt class="text-xs text-gray-500 dark:text-gray-400">Member since</dt>
                    <dd class="text-xs font-medium text-gray-900 dark:text-white">{{ $u->created_at->format('F d, Y') }}</dd>
                </div>
                @if($u->position)
                <div class="flex items-center justify-between px-5 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                    <dt class="text-xs text-gray-500 dark:text-gray-400">Position</dt>
                    <dd class="text-xs font-medium text-gray-900 dark:text-white">{{ $u->position }}</dd>
                </div>
                @endif
                @if($u->last_login_at)
                <div class="flex items-center justify-between px-5 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                    <dt class="text-xs text-gray-500 dark:text-gray-400">Last login</dt>
                    <dd class="text-xs font-medium text-gray-900 dark:text-white">{{ $u->last_login_at->format('F d, Y H:i') }}</dd>
                </div>
                @endif
            </dl>
        </div>

        {{-- ── Quick Actions Section ── (Adviser and above only) --}}
        @if(in_array($u->role->name, ['System Administrator', 'Supreme Admin', 'Adviser']))
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Quick Actions</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">System management shortcuts</p>
            </div>
            <div class="px-5 py-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <a href="{{ route('admin.users.index') }}" 
                       class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition group">
                        <div class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center">
                            <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">User Management</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Add, edit, or remove users</p>
                        </div>
                    </a>
                    <a href="{{ route('admin.roles.index') }}" 
                       class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition group">
                        <div class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center">
                            <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">Role Management</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Manage roles and permissions</p>
                        </div>
                    </a>
                    <a href="{{ route('audit.logs') }}" 
                       class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition group">
                        <div class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center">
                            <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">Audit Logs</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">View system activity history</p>
                        </div>
                    </a>
                    <a href="{{ route('profile.index') }}" 
                       class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition group">
                        <div class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center">
                            <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">My Profile</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Update your personal information</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        @endif

    </div>
</div>

{{-- Propagate theme to root Alpine instance on save --}}
<script>
    window.addEventListener('theme-changed', e => {
        const root = document.querySelector('[x-data]')
        if (root && root._x_dataStack) {
            root._x_dataStack[0].theme = e.detail.theme
        }
    })
</script>
@endsection