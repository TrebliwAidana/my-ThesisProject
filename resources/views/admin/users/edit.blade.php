@extends('layouts.app')

@section('title', 'Edit User — ' . $user->full_name . ' — VSULHS SSLG')
@section('page-title', 'Edit User')

@push('styles')
<style>
/* ════════════════════════════════════════════════
   EDIT USER — Emerald & Gold Luxury Theme
   Matching all management views
════════════════════════════════════════════════ */

.form-container {
    max-width: 56rem;
    margin: 0 auto;
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
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    color: var(--text-3);
    margin-bottom: 0.5rem;
    font-family: 'DM Mono', monospace;
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
}
.form-select.error {
    border-color: #ef4444;
}

/* ── Radio Group Styling ── */
.radio-group {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
}
.radio-option {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border: 1.5px solid var(--border);
    border-radius: 0.75rem;
    cursor: pointer;
    transition: all 0.18s ease;
}
.radio-option.active {
    border-color: var(--gold);
    background: rgba(212,175,55,0.08);
}
.radio-option input[type="radio"] {
    width: 1rem;
    height: 1rem;
    accent-color: var(--emerald);
    cursor: pointer;
}
.radio-option span {
    font-size: 0.8rem;
    color: var(--text-2);
}
.radio-option.active span {
    color: var(--gold-dark);
}
html.dark .radio-option.active span {
    color: var(--gold-light);
}

/* ── Password Toggle Button ── */
.password-toggle {
    position: absolute;
    inset-y: 0;
    right: 0;
    display: flex;
    align-items: center;
    padding-right: 0.75rem;
    color: var(--text-3);
    background: none;
    border: none;
    cursor: pointer;
    transition: color 0.15s ease;
}
.password-toggle:hover {
    color: var(--gold-dark);
}
html.dark .password-toggle:hover {
    color: var(--gold-light);
}

/* ── Error Alert ── */
.error-alert {
    background: rgba(220,38,38,0.08);
    border-left: 3px solid #ef4444;
    border-radius: 0.75rem;
    padding: 1rem;
    margin-bottom: 1rem;
}
.error-title {
    font-size: 0.75rem;
    font-weight: 700;
    color: #ef4444;
    margin-bottom: 0.5rem;
}
.error-list {
    list-style: disc;
    list-style-position: inside;
    font-size: 0.7rem;
    color: #dc2626;
}
html.dark .error-alert {
    background: rgba(248,113,113,0.12);
}
html.dark .error-list {
    color: #fca5a5;
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
}
.btn-emerald:hover {
    background: linear-gradient(135deg, var(--gold), var(--gold-dark));
    color: #0f172a;
    transform: translateY(-1px);
    box-shadow: 0 4px 16px rgba(212,175,55,0.35);
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

.btn-warning {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    font-size: 0.75rem;
    font-weight: 700;
    background: transparent;
    color: #dc2626;
    border: 1.5px solid rgba(220,38,38,0.3);
    border-radius: 0.65rem;
    cursor: pointer;
    transition: all 0.18s ease;
    text-decoration: none;
    font-family: 'Outfit', sans-serif;
}
.btn-warning:hover {
    background: rgba(220,38,38,0.1);
    border-color: #dc2626;
    transform: translateY(-1px);
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

/* ── Form Hint ── */
.form-hint {
    font-size: 0.65rem;
    color: var(--text-3);
    margin-top: 0.25rem;
}

/* ── Password Change Box ── */
.password-change-box {
    background: var(--surface-2);
    border: 1px solid var(--border);
    border-radius: 0.75rem;
    padding: 1rem;
    margin-top: 0.5rem;
}

/* ── Grid Layout ── */
.form-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1rem;
}
@media (min-width: 768px) {
    .form-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
    }
}
.form-grid-3 {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1rem;
}
@media (min-width: 768px) {
    .form-grid-3 {
        grid-template-columns: repeat(3, 1fr);
        gap: 1.5rem;
    }
}
.form-grid-2 {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1rem;
}
@media (min-width: 768px) {
    .form-grid-2 {
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
    }
}

/* ── Animations ── */
@keyframes fadeUp {
    from { opacity: 0; transform: translateY(14px); }
    to   { opacity: 1; transform: translateY(0); }
}
.anim-1 { animation: fadeUp 0.38s ease 0.04s both; }
.anim-2 { animation: fadeUp 0.38s ease 0.10s both; }

.spinner {
    width: 1rem;
    height: 1rem;
    animation: spin 0.8s linear infinite;
}
@keyframes spin {
    to { transform: rotate(360deg); }
}
</style>
@endpush

@php
    $roleColorMap = [
        'System Administrator' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/50 dark:text-purple-300',
        'Club Adviser'         => 'bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-300',
        'Treasurer'            => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300',
        'Auditor'              => 'bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300',
        'Guest'                => 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400',
    ];

    $colorClass = $roleColorMap[$user->role?->name] ?? 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300';
@endphp

@section('content')

<div class="space-y-5">
    
    {{-- Hero Section --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-emerald-600 to-emerald-700 dark:from-emerald-800 dark:to-emerald-900 p-6 md:p-7">
        <div class="absolute inset-0 opacity-[0.05]"
             style="background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,1) 1px, transparent 0); background-size: 28px 28px;"></div>
        <div class="absolute -top-16 right-0 w-64 h-64 rounded-full opacity-20"
             style="background: radial-gradient(circle, #D4AF37, transparent 65%); filter: blur(48px);"></div>
        <div class="relative z-10">
            <div class="mb-2">
                <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-1 text-sm text-emerald-200 hover:text-white transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to Users
                </a>
            </div>
            <h1 class="text-2xl md:text-3xl font-bold text-white tracking-tight">Edit User</h1>
            <p class="text-emerald-100/80 text-sm mt-1.5">
                Editing <span class="font-semibold">{{ $user->full_name }}</span>
                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $colorClass }}">
                    {{ $user->role?->name ?? 'No Role' }}
                </span>
            </p>
        </div>
    </div>

    {{-- Form Card --}}
    <div class="form-container anim-2">
        <div class="form-card">
            <div class="form-card-header">
                <div class="flex items-center gap-3">
                    <div class="form-card-header-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <h2>Edit User Details</h2>
                        <p>Update user account information</p>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <form method="POST" action="{{ route('admin.users.update', $user->id) }}" id="userEditForm">
                    @csrf
                    @method('PUT')

                    {{-- Error Alerts --}}
                    @if ($errors->any())
                        <div class="error-alert">
                            <div class="error-title">Please fix the following errors:</div>
                            <ul class="error-list">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Name Fields --}}
                    <div class="form-grid-3 mb-4">
                        <div>
                            <label class="form-label">First Name <span class="form-label-required">*</span></label>
                            <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}" required
                                   class="form-input {{ $errors->has('first_name') ? 'error' : '' }}">
                            @error('first_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="form-label">Middle Name</label>
                            <input type="text" name="middle_name" value="{{ old('middle_name', $user->middle_name) }}"
                                   class="form-input">
                        </div>
                        <div>
                            <label class="form-label">Last Name <span class="form-label-required">*</span></label>
                            <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}" required
                                   class="form-input {{ $errors->has('last_name') ? 'error' : '' }}">
                            @error('last_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Email --}}
                    <div class="mb-4">
                        <label class="form-label">Email Address <span class="form-label-required">*</span></label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                               class="form-input {{ $errors->has('email') ? 'error' : '' }}">
                        @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Role --}}
                    <div class="mb-4">
                        <label class="form-label">Role <span class="form-label-required">*</span></label>
                        <select name="role_id" required class="form-select {{ $errors->has('role_id') ? 'error' : '' }}">
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                    {{ $role->name }} (Level {{ $role->level }})
                                </option>
                            @endforeach
                        </select>
                        @error('role_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Position --}}
                    <div class="mb-4">
                        <label class="form-label">Position</label>
                        <input type="text" name="position" value="{{ old('position', $user->position) }}"
                               placeholder="e.g., President, Secretary, Auditor"
                               class="form-input {{ $errors->has('position') ? 'error' : '' }}">
                        @error('position') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Student ID & Year Level --}}
                    <div class="form-grid-2 mb-4">
                        <div>
                            <label class="form-label">Student ID</label>
                            <input type="text" name="student_id" value="{{ old('student_id', $user->student_id) }}"
                                   placeholder="YYYY-XXXXX"
                                   class="form-input {{ $errors->has('student_id') ? 'error' : '' }}">
                            @error('student_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="form-label">Year Level</label>
                            <select name="year_level" class="form-select {{ $errors->has('year_level') ? 'error' : '' }}">
                                <option value="">— Select Year Level —</option>
                                @foreach (['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12'] as $grade)
                                    <option value="{{ $grade }}" {{ old('year_level', $user->year_level) == $grade ? 'selected' : '' }}>
                                        {{ $grade }}
                                    </option>
                                @endforeach
                            </select>
                            @error('year_level') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Gender --}}
                    <div class="mb-4">
                        <label class="form-label">Gender <span class="form-label-required">*</span></label>
                        <div class="radio-group">
                            @foreach (['Male', 'Female', 'Other'] as $gender)
                                <label class="radio-option {{ old('gender', $user->gender) == $gender ? 'active' : '' }}">
                                    <input type="radio" name="gender" value="{{ $gender }}"
                                           {{ old('gender', $user->gender) == $gender ? 'checked' : '' }}>
                                    <span>{{ $gender }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('gender') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Phone --}}
                    <div class="mb-4">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                               placeholder="+639123456789"
                               class="form-input {{ $errors->has('phone') ? 'error' : '' }}">
                        @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Birthday --}}
                    <div class="mb-4">
                        <label class="form-label">Birthday</label>
                        <input type="date" name="birthday" value="{{ old('birthday', $user->birthday?->format('Y-m-d')) }}"
                               class="form-input {{ $errors->has('birthday') ? 'error' : '' }}">
                        @error('birthday') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Account Status --}}
                    <div class="mb-4">
                        <label class="form-label">Account Status</label>
                        <div class="radio-group">
                            <label class="radio-option {{ old('is_active', $user->is_active) ? 'active' : '' }}">
                                <input type="radio" name="is_active" value="1"
                                       {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                                <span>✅ Active</span>
                            </label>
                            <label class="radio-option {{ !old('is_active', $user->is_active) ? 'active' : '' }}">
                                <input type="radio" name="is_active" value="0"
                                       {{ !old('is_active', $user->is_active) ? 'checked' : '' }}>
                                <span>⭕ Inactive</span>
                            </label>
                        </div>
                        @error('is_active') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Change Password Section --}}
                    <div class="password-change-box mb-4">
                        <p class="text-sm font-semibold text-text mb-1">Change Password</p>
                        <p class="text-xs text-text-3 mb-3">Leave blank to keep the current password.</p>
                        <div class="form-grid-2">
                            <div>
                                <label class="form-label">New Password</label>
                                <div class="relative">
                                    <input type="password" id="password" name="password" minlength="8"
                                           class="form-input pr-10 {{ $errors->has('password') ? 'error' : '' }}"
                                           placeholder="Minimum 8 characters">
                                    <button type="button"
                                            onclick="togglePasswordVisibility('password', 'eyeIcon')"
                                            class="password-toggle">
                                        <svg id="eyeIcon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>
                                </div>
                                @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="form-label">Confirm Password</label>
                                <div class="relative">
                                    <input type="password" id="password_confirmation" name="password_confirmation"
                                           class="form-input pr-10">
                                    <button type="button"
                                            onclick="togglePasswordVisibility('password_confirmation', 'eyeIconConfirm')"
                                            class="password-toggle">
                                        <svg id="eyeIconConfirm" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Form Actions --}}
                    <div class="flex gap-3 pt-2">
                        <button type="submit" class="btn-emerald">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Update User
                        </button>
                        <a href="{{ route('admin.users.index') }}" class="btn-red">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Danger Zone --}}
    <div class="danger-zone anim-2">
        <div class="danger-zone-header">
            <h3>⚠️ Danger Zone</h3>
        </div>
        <div class="danger-zone-body">
            <div class="danger-zone-text">
                <p>Reset Password & Send Email</p>
                <p>Generate a new random password and email it to the user.</p>
            </div>
            <form method="POST"
                  action="{{ route('admin.users.reset-password', $user->id) }}"
                  onsubmit="return confirm('Reset password for {{ addslashes($user->full_name) }}?\n\nA new password will be generated and emailed.');">
                @csrf
                <button type="submit" class="btn-warning">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Reset Password
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    function togglePasswordVisibility(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.innerHTML = `
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.05 10.05 0 013.125-4.125m4.542-1.042A9.977 9.977 0 0112 5c4.478 0 8.268 2.943 9.542 7a9.977 9.977 0 01-3.125 4.125m-4.542 1.042L3 3l18 18"/>
            `;
        } else {
            input.type = 'password';
            icon.innerHTML = `
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            `;
        }
    }
</script>

@endsection