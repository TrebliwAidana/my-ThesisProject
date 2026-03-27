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
                /* push theme change to the root x-data so sidebar updates live */
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
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-900">Appearance</h2>
                <p class="text-xs text-gray-500 mt-0.5">Customize how the portal looks for you</p>
            </div>

            {{-- Theme picker --}}
            <div class="px-5 py-5 border-b border-gray-100">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">
                    Sidebar theme
                </label>

                <div class="flex flex-wrap gap-4">
                    @php
                    $themes = [
                        ['key' => 'navy',    'label' => 'Navy',    'color' => '#1a1a2e'],
                        ['key' => 'forest',  'label' => 'Forest',  'color' => '#0F2D1A'],
                        ['key' => 'crimson', 'label' => 'Crimson', 'color' => '#2D0F0F'],
                        ['key' => 'slate',   'label' => 'Slate',   'color' => '#1C2333'],
                        ['key' => 'amber',   'label' => 'Amber',   'color' => '#2B1F05'],
                        ['key' => 'rose',    'label' => 'Rose',    'color' => '#2D0F1E'],
                        ['key' => 'light',   'label' => 'Light',   'color' => '#f0ede8', 'border' => true],
                    ];
                    @endphp

                    @foreach($themes as $t)
                    <button
                        type="button"
                        @click="selectedTheme = '{{ $t['key'] }}'"
                        :class="selectedTheme === '{{ $t['key'] }}' ? 'ring-2 ring-offset-2 ring-gray-800 scale-110' : 'ring-1 ring-gray-200 hover:scale-105'"
                        class="flex flex-col items-center gap-1.5 transition-transform"
                        title="{{ $t['label'] }}"
                    >
                        <span
                            class="w-8 h-8 rounded-full block {{ isset($t['border']) ? 'border border-gray-300' : '' }}"
                            style="background-color: {{ $t['color'] }}"
                        ></span>
                        <span class="text-xs text-gray-500">{{ $t['label'] }}</span>
                    </button>
                    @endforeach
                </div>

                {{-- Live preview strip --}}
                <div class="mt-4 rounded-lg overflow-hidden border border-gray-200 flex h-10">
                    <div class="w-24 flex items-center justify-center text-xs font-medium transition-colors duration-200"
                         :style="`background-color: var(--sb-bg, #1a1a2e); color: rgba(255,255,255,0.8)`">
                        Sidebar
                    </div>
                    <div class="flex-1 flex items-center px-3 text-xs text-gray-500 bg-gray-50">
                        Main content area
                    </div>
                </div>
                <p class="text-xs text-gray-400 mt-1.5">Preview updates after saving</p>
            </div>

            {{-- Save button --}}
            <div class="px-5 py-4 flex items-center gap-3">
                <button
                    type="button"
                    @click="saveTheme()"
                    :disabled="saving"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg bg-gray-900 text-white hover:bg-gray-700 disabled:opacity-50 transition"
                >
                    <span x-show="!saving">Save appearance</span>
                    <span x-show="saving">Saving…</span>
                </button>
                <span
                    x-show="saved"
                    x-transition.opacity
                    class="text-xs text-green-600 font-medium flex items-center gap-1"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Saved
                </span>
            </div>
        </div>

        {{-- ── Account info section ── --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-900">Account</h2>
                <p class="text-xs text-gray-500 mt-0.5">Your profile details</p>
            </div>
            @php $u = auth()->user(); @endphp
            <dl class="divide-y divide-gray-100">
                <div class="flex items-center justify-between px-5 py-3">
                    <dt class="text-xs text-gray-500">Full name</dt>
                    <dd class="text-xs font-medium text-gray-900">{{ $u->full_name }}</dd>
                </div>
                <div class="flex items-center justify-between px-5 py-3">
                    <dt class="text-xs text-gray-500">Email</dt>
                    <dd class="text-xs font-medium text-gray-900">{{ $u->email }}</dd>
                </div>
                <div class="flex items-center justify-between px-5 py-3">
                    <dt class="text-xs text-gray-500">Role</dt>
                    <dd class="text-xs font-medium text-gray-900">{{ $u->role->name }}</dd>
                </div>
                <div class="flex items-center justify-between px-5 py-3">
                    <dt class="text-xs text-gray-500">Member since</dt>
                    <dd class="text-xs font-medium text-gray-900">{{ $u->created_at->format('M d, Y') }}</dd>
                </div>
            </dl>
        </div>

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