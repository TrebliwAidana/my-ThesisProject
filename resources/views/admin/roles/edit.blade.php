@extends('layouts.app')

@section('title', 'Edit Role — ' . $role->name . ' — VSULHS SSLG')
@section('page-title', 'Edit Role')

@push('styles')
<style>
/* ════════════════════════════════════════════════
   EDIT ROLE — Emerald & Gold Luxury Theme
   Matching all management views
════════════════════════════════════════════════ */

.form-container {
    max-width: 42rem;
    margin: 0 auto;
}

/* ── Back Link ── */
.back-link {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.4rem 0.75rem;
    font-size: 0.75rem;
    font-weight: 500;
    background: transparent;
    color: var(--text-3);
    text-decoration: none;
    border-radius: 0.5rem;
    transition: all 0.18s ease;
}
.back-link:hover {
    color: var(--gold-dark);
    background: rgba(212,175,55,0.08);
    transform: translateX(-2px);
}
html.dark .back-link:hover {
    color: var(--gold-light);
}
.back-link svg {
    width: 1rem;
    height: 1rem;
    stroke: currentColor;
    fill: none;
}

/* ── Hero Section ── */
.role-hero {
    position: relative;
    overflow: hidden;
    border-radius: 1.25rem;
    padding: 1.75rem 2rem;
    isolation: isolate;
    background: linear-gradient(135deg, #064E3B 0%, #065F46 35%, #047857 60%, #0A3A28 100%);
}
.role-hero::before {
    content: '';
    position: absolute; inset: 0;
    background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,1) 1px, transparent 0);
    background-size: 28px 28px;
    opacity: 0.04; z-index: 0;
}
.role-hero::after {
    content: '';
    position: absolute;
    top: -60px; right: -60px;
    width: 280px; height: 280px;
    background: radial-gradient(circle, rgba(212,175,55,0.35), transparent 65%);
    filter: blur(48px); z-index: 0;
}
.role-hero-content { position: relative; z-index: 1; }

.role-hero-title {
    font-family: 'DM Serif Display', serif;
    font-size: clamp(1.5rem, 3.5vw, 2.2rem);
    color: #fff;
    letter-spacing: -0.02em;
    line-height: 1.1;
}
.role-hero-title span {
    background: linear-gradient(90deg, #F0CC55, #D4AF37);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.role-hero-pill {
    display: inline-flex; align-items: center; gap: 0.4rem;
    padding: 0.3rem 0.75rem;
    background: rgba(255,255,255,0.1);
    border: 1px solid rgba(212,175,55,0.28);
    border-radius: 999px;
    font-size: 0.72rem; font-weight: 600;
    color: rgba(255,255,255,0.88);
    font-family: 'DM Mono', monospace;
}

.role-badge-system {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.3rem 0.85rem;
    border-radius: 9999px;
    font-size: 0.7rem;
    font-weight: 700;
    background: rgba(212,175,55,0.15);
    color: #d97706;
    border: 1px solid rgba(212,175,55,0.3);
}
html.dark .role-badge-system {
    background: rgba(212,175,55,0.2);
    color: #fcd34d;
}

/* ── Form Card ── */
.form-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 1.25rem;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
}
html.dark .form-card {
    box-shadow: 0 4px 20px rgba(0,0,0,0.22);
}

.form-card-header {
    background: linear-gradient(135deg, #064E3B 0%, #047857 60%, #065F46 100%);
    padding: 1rem 1.5rem;
}

.form-card-header-icon {
    width: 2.5rem;
    height: 2.5rem;
    background: rgba(255,255,255,0.15);
    border-radius: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.form-card-header-icon svg {
    width: 1.25rem;
    height: 1.25rem;
    stroke: #fff;
}
.form-card-header h2 {
    font-size: 0.9rem;
    font-weight: 700;
    color: #fff;
    margin-bottom: 0.125rem;
}
.form-card-header p {
    font-size: 0.7rem;
    color: rgba(255,255,255,0.7);
}

/* ── Form Fields ── */
.form-label {
    display: block;
    font-size: 0.75rem;
    font-weight: 700;
    color: var(--text-2);
    margin-bottom: 0.5rem;
}
.form-label-required {
    color: #ef4444;
}
.form-input {
    width: 100%;
    padding: 0.625rem 1rem;
    font-size: 0.85rem;
    background: var(--surface-2);
    border: 1.5px solid var(--border);
    border-radius: 0.75rem;
    color: var(--text);
    font-family: 'Outfit', sans-serif;
    transition: all 0.2s ease;
}
.form-input:focus {
    outline: none;
    border-color: var(--gold);
    box-shadow: 0 0 0 3px rgba(212,175,55,0.12);
    background: var(--surface);
}
.form-input.error {
    border-color: #ef4444;
}
.form-input:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    background: var(--surface-3);
}
.form-textarea {
    width: 100%;
    padding: 0.625rem 1rem;
    font-size: 0.85rem;
    background: var(--surface-2);
    border: 1.5px solid var(--border);
    border-radius: 0.75rem;
    color: var(--text);
    font-family: 'Outfit', sans-serif;
    transition: all 0.2s ease;
    resize: vertical;
}
.form-textarea:focus {
    outline: none;
    border-color: var(--gold);
    box-shadow: 0 0 0 3px rgba(212,175,55,0.12);
    background: var(--surface);
}
.form-select {
    width: 100%;
    padding: 0.625rem 1rem;
    font-size: 0.85rem;
    background: var(--surface-2);
    border: 1.5px solid var(--border);
    border-radius: 0.75rem;
    color: var(--text);
    font-family: 'Outfit', sans-serif;
    cursor: pointer;
    transition: all 0.2s ease;
}
.form-select:focus {
    outline: none;
    border-color: var(--gold);
    box-shadow: 0 0 0 3px rgba(212,175,55,0.12);
    background: var(--surface);
}
.form-select:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

/* ── Checkbox Styling ── */
.form-checkbox {
    width: 1rem;
    height: 1rem;
    border-radius: 0.25rem;
    accent-color: var(--emerald);
    cursor: pointer;
}
.checkbox-label {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    font-size: 0.8rem;
    color: var(--text-2);
}

/* ── Readonly Display ── */
.readonly-field {
    width: 100%;
    padding: 0.625rem 1rem;
    font-size: 0.85rem;
    background: var(--surface-3);
    border: 1.5px solid var(--border);
    border-radius: 0.75rem;
    color: var(--text-3);
    font-family: 'Outfit', sans-serif;
}

/* ── Info Box ── */
.info-box {
    background: rgba(5,150,105,0.05);
    border: 1px solid rgba(5,150,105,0.15);
    border-radius: 0.75rem;
    padding: 1rem;
    margin-bottom: 1.5rem;
}
html.dark .info-box {
    background: rgba(16,185,129,0.08);
}
.info-box p {
    font-size: 0.75rem;
    color: var(--text-3);
}
.info-box svg {
    width: 1rem;
    height: 1rem;
    color: var(--emerald);
    flex-shrink: 0;
}

/* ── Buttons ── */
.btn-emerald {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.6rem 1.5rem;
    font-size: 0.85rem;
    font-weight: 700;
    background: linear-gradient(135deg, var(--emerald), var(--emerald-dark));
    color: #fff;
    border: none;
    border-radius: 0.75rem;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    font-family: 'Outfit', sans-serif;
    box-shadow: 0 2px 10px rgba(5,150,105,0.22);
    flex: 1;
}
.btn-emerald:hover {
    background: linear-gradient(135deg, var(--gold), var(--gold-dark));
    color: #0f172a;
    transform: translateY(-1px);
    box-shadow: 0 4px 16px rgba(212,175,55,0.35);
}

.btn-gray {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.6rem 1.5rem;
    font-size: 0.85rem;
    font-weight: 700;
    background: var(--surface-3);
    color: var(--text-2);
    border: 1.5px solid var(--border);
    border-radius: 0.75rem;
    cursor: pointer;
    transition: all 0.18s ease;
    text-decoration: none;
    font-family: 'Outfit', sans-serif;
    flex: 1;
}
.btn-gray:hover {
    border-color: rgba(212,175,55,0.4);
    color: var(--gold-dark);
    background: rgba(212,175,55,0.06);
}

.btn-red {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.6rem 1.5rem;
    font-size: 0.85rem;
    font-weight: 700;
    background: linear-gradient(135deg, #dc2626, #b91c1c);
    color: #fff;
    border: none;
    border-radius: 0.75rem;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    font-family: 'Outfit', sans-serif;
    box-shadow: 0 2px 10px rgba(220,38,38,0.22);
}
.btn-red:hover {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    transform: translateY(-1px);
    box-shadow: 0 4px 16px rgba(220,38,38,0.35);
}

.button-group {
    display: flex;
    gap: 1rem;
    margin-top: 0.5rem;
}

/* ── Danger Zone ── */
.danger-zone {
    margin-top: 1.5rem;
    background: var(--surface);
    border: 1px solid rgba(220,38,38,0.3);
    border-radius: 1.25rem;
    overflow: hidden;
}
.danger-zone-header {
    padding: 0.875rem 1.25rem;
    border-bottom: 1px solid rgba(220,38,38,0.2);
    background: rgba(220,38,38,0.05);
}
.danger-zone-header h3 {
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: #dc2626;
    font-family: 'DM Mono', monospace;
}
.danger-zone-body {
    padding: 1.25rem;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
}
.danger-zone-text p:first-child {
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--text);
    margin-bottom: 0.25rem;
}
.danger-zone-text p:last-child {
    font-size: 0.7rem;
    color: var(--text-3);
}

/* ── Animations ── */
@keyframes fadeUp {
    from { opacity: 0; transform: translateY(14px); }
    to   { opacity: 1; transform: translateY(0); }
}
.anim-1 { animation: fadeUp 0.38s ease 0.04s both; }
.anim-2 { animation: fadeUp 0.38s ease 0.10s both; }
</style>
@endpush

@php
    $levelLabels = [
        1  => '🔒 System Reserved',
        2  => '👨‍🏫 Club Advisor',
        3  => '💰 Treasurer',
        4  => '📊 Auditor',
        5  => '👤 Guest',
        6  => 'Level 6',
        7  => 'Level 7',
        8  => 'Level 8',
        9  => 'Level 9',
        10 => 'Level 10',
    ];
    $editableLevels = collect($levelLabels)->except(1);
@endphp

@section('content')

<div class="space-y-5">
    
    {{-- Hero Section --}}
    <div class="role-hero anim-1">
        <div class="role-hero-content">
            <div class="mb-2">
                <a href="{{ route('admin.roles.index') }}" class="back-link">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Roles
                </a>
            </div>
            <p class="text-emerald-300/70 text-[10px] font-bold tracking-[0.2em] uppercase mb-2"
               style="font-family:'DM Mono',monospace;">
                {{ now()->format('F Y') }} · Role Management
            </p>
            <h1 class="role-hero-title mb-3">Edit<br><span>Role</span></h1>
            <div class="flex flex-wrap gap-2">
                <span class="role-hero-pill">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    {{ $role->name }}
                </span>
                @if ($role->is_system)
                <span class="role-badge-system">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    System Role — Locked
                </span>
                @endif
            </div>
        </div>
    </div>

    {{-- Flash Messages --}}
    @foreach (['success' => 'emerald', 'error' => 'red', 'info' => 'blue'] as $type => $color)
        @if (session($type))
            <div class="bg-{{ $color }}-50 dark:bg-{{ $color }}-900/20 border border-{{ $color }}-200 dark:border-{{ $color }}-800 rounded-lg p-3 anim-1">
                <p class="text-sm font-medium text-{{ $color }}-700 dark:text-{{ $color }}-400">{{ session($type) }}</p>
            </div>
        @endif
    @endforeach

    @if ($errors->any())
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-3 anim-1">
            <p class="text-sm font-semibold text-red-700 dark:text-red-400 mb-1">Please fix the following errors:</p>
            <ul class="text-sm text-red-600 dark:text-red-400 space-y-0.5 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Main Card --}}
    <div class="form-container anim-2">

        @if ($role->is_system)
            {{-- Read-only view for system roles --}}
            <div class="form-card">
                <div class="form-card-header">
                    <div class="flex items-center gap-3">
                        <div class="form-card-header-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <div>
                            <h2>System Role Details</h2>
                            <p>This role is protected and cannot be modified</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="text-center p-6 bg-amber-50 dark:bg-amber-900/20 rounded-xl mb-6">
                        <svg class="w-12 h-12 mx-auto text-amber-500 dark:text-amber-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <p class="text-amber-700 dark:text-amber-400 font-semibold">System Role — Cannot be edited</p>
                        <p class="text-sm text-amber-600 dark:text-amber-500 mt-1">System roles are protected and cannot be modified.</p>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="form-label">Role Name</label>
                            <div class="readonly-field">{{ $role->name }}</div>
                        </div>
                        <div>
                            <label class="form-label">Abbreviation</label>
                            <div class="readonly-field">{{ $role->abbreviation ?? '—' }}</div>
                        </div>
                        <div>
                            <label class="form-label">Description</label>
                            <div class="readonly-field">{{ $role->desc ?? '—' }}</div>
                        </div>
                        <div>
                            <label class="form-label">Level</label>
                            <div class="readonly-field">Level {{ $role->level }} — {{ $levelLabels[$role->level] ?? 'Unknown' }}</div>
                        </div>
                        <div>
                            <label class="form-label">System Role</label>
                            <div class="readonly-field">
                                <span class="inline-flex items-center gap-2">✓ This role is marked as a system role</span>
                            </div>
                        </div>
                    </div>

                    <div class="button-group mt-6">
                        <a href="{{ route('admin.roles.index') }}" class="btn-gray">
                            Back to Roles
                        </a>
                    </div>
                </div>
            </div>

        @elseif ($role->is_predefined)
            {{-- Predefined roles: level-only form --}}
            <div class="form-card">
                <div class="form-card-header">
                    <div class="flex items-center gap-3">
                        <div class="form-card-header-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </div>
                        <div>
                            <h2>Edit Predefined Role</h2>
                            <p>Only the level can be modified</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="info-box mb-6">
                        <div class="flex items-start gap-2">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p><strong>Predefined role</strong> — only the <strong>Level</strong> can be changed. Name, description, and other fields are locked.</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('admin.roles.update', $role->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-6">
                            <label class="form-label">Level <span class="form-label-required">*</span></label>
                            <select name="level" class="form-select {{ $errors->has('level') ? 'error' : '' }}">
                                @foreach ($editableLevels as $lvl => $label)
                                    <option value="{{ $lvl }}" {{ old('level', $role->level) == $lvl ? 'selected' : '' }}>
                                        Level {{ $lvl }} — {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-text-3 mt-1">Lower number = higher authority. Level 1 is reserved for system roles.</p>
                            @error('level') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="button-group">
                            <button type="submit" class="btn-emerald">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Save Changes
                            </button>
                            <a href="{{ route('admin.roles.index') }}" class="btn-gray">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>

        @else
            {{-- Full editable form for custom roles --}}
            <div class="form-card">
                <div class="form-card-header">
                    <div class="flex items-center gap-3">
                        <div class="form-card-header-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </div>
                        <div>
                            <h2>Edit Custom Role</h2>
                            <p>Modify role details and permissions</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.roles.update', $role->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-5">
                            <label class="form-label">Role Name <span class="form-label-required">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $role->name) }}" required
                                   class="form-input {{ $errors->has('name') ? 'error' : '' }}">
                            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="mb-5">
                            <label class="form-label">Abbreviation <span class="font-normal text-text-3">(Optional)</span></label>
                            <input type="text" name="abbreviation" value="{{ old('abbreviation', $role->abbreviation) }}"
                                   maxlength="10" class="form-input {{ $errors->has('abbreviation') ? 'error' : '' }}">
                            @error('abbreviation') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="mb-5">
                            <label class="form-label">Description <span class="font-normal text-text-3">(Optional)</span></label>
                            <textarea name="desc" rows="3" maxlength="500"
                                      class="form-textarea {{ $errors->has('desc') ? 'error' : '' }}">{{ old('desc', $role->desc) }}</textarea>
                            @error('desc') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="mb-5" x-data="{ isSystem: {{ old('is_system', $role->is_system) ? 'true' : 'false' }} }">
                            <label class="form-label">Level <span class="form-label-required">*</span></label>
                            <select name="level" class="form-select {{ $errors->has('level') ? 'error' : '' }}">
                                <template x-if="isSystem">
                                    <option value="1" {{ old('level', $role->level) == 1 ? 'selected' : '' }}>
                                        Level 1 — 🔒 System Reserved
                                    </option>
                                </template>
                                @foreach ($editableLevels as $lvl => $label)
                                    <option value="{{ $lvl }}" {{ old('level', $role->level) == $lvl ? 'selected' : '' }}>
                                        Level {{ $lvl }} — {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-text-3 mt-1">Lower number = higher authority. Level 1 is reserved for system roles.</p>
                            @error('level') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror

                            <div class="mt-4">
                                <label class="form-label">System Role</label>
                                <div class="flex items-start gap-3 p-3 bg-surface-2 border border-border rounded-lg">
                                    <input type="hidden" name="is_system" value="0">
                                    <input type="checkbox" id="is_system" name="is_system" value="1"
                                           x-model="isSystem"
                                           {{ old('is_system', $role->is_system) ? 'checked' : '' }}
                                           class="form-checkbox mt-0.5">
                                    <div>
                                        <label for="is_system" class="text-sm text-text-2 cursor-pointer font-medium">
                                            Mark as system role
                                        </label>
                                        <p class="text-xs text-text-3 mt-0.5">
                                            System roles are protected — names cannot be changed and they
                                            cannot be deleted. Enables level 1 selection above.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="button-group">
                            <button type="submit" class="btn-emerald">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Save Changes
                            </button>
                            <a href="{{ route('admin.roles.index') }}" class="btn-gray">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        {{-- Danger Zone — custom roles only --}}
        @if (!$role->is_system && !$role->is_predefined)
            <div class="danger-zone">
                <div class="danger-zone-header">
                    <h3>⚠️ Danger Zone</h3>
                </div>
                <div class="danger-zone-body">
                    <div class="danger-zone-text">
                        <p>Delete this Role</p>
                        <p>Deleting this role is permanent. All users currently assigned this role must be reassigned first.</p>
                    </div>
                    <button type="button"
                            onclick="confirmDelete({{ $role->id }}, '{{ addslashes($role->name) }}')"
                            class="btn-red">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Delete this Role
                    </button>
                </div>
            </div>

            <form id="delete-role-form" action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        @endif
    </div>
</div>

@push('scripts')
<script>
function confirmDelete(roleId, roleName) {
    if (confirm(`⚠️ Delete role "${roleName}"?\n\nThis action cannot be undone.\nAll users must be unassigned from this role first.`)) {
        document.getElementById('delete-role-form').submit();
    }
}
</script>
@endpush

@endsection