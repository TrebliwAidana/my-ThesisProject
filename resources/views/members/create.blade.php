@extends('layouts.app')

@section('title', 'Create Member — VSULHS SSLG')
@section('page-title', 'Create New Member')

@push('styles')
<style>
/* ════════════════════════════════════════════════
   CREATE MEMBER — Emerald & Gold Luxury Theme
   Matching dashboard and management views
════════════════════════════════════════════════ */

[x-cloak] { display: none !important; }

/* ── Form Container ── */
.form-container {
    max-width: 56rem;
    margin: 0 auto;
}

/* ── Section Cards ── */
.form-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 1.25rem;
    overflow: hidden;
    transition: box-shadow 0.2s ease;
}
.form-card:hover {
    box-shadow: 0 8px 28px rgba(212,175,55,0.08), 0 2px 8px rgba(0,0,0,0.04);
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
}
.form-error {
    color: #ef4444;
    font-size: 0.7rem;
    margin-top: 0.25rem;
}
.form-hint {
    font-size: 0.65rem;
    color: var(--text-3);
    margin-top: 0.25rem;
}

/* ── Select Styling ── */
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
.form-select:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

/* ── Toggle Switch (Active Status) ── */
.switch {
    position: relative;
    display: inline-flex;
    align-items: center;
    cursor: pointer;
}
.switch-input {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
}
.switch-slider {
    position: relative;
    display: inline-block;
    width: 2.75rem;
    height: 1.5rem;
    background-color: #cbd5e1;
    border-radius: 9999px;
    transition: all 0.2s ease;
}
.switch-slider::before {
    content: '';
    position: absolute;
    height: 1.25rem;
    width: 1.25rem;
    left: 0.125rem;
    bottom: 0.125rem;
    background-color: white;
    border-radius: 50%;
    transition: transform 0.2s ease;
}
.switch-input:checked + .switch-slider {
    background-color: var(--emerald);
}
.switch-input:checked + .switch-slider::before {
    transform: translateX(1.25rem);
}
.switch-input:focus + .switch-slider {
    box-shadow: 0 0 0 3px rgba(212,175,55,0.3);
}
.switch-label {
    margin-left: 0.5rem;
    font-size: 0.8rem;
    color: var(--text-2);
}

/* ── File Input Styling ── */
.form-file {
    width: 100%;
    padding: 0.5rem;
    font-size: 0.85rem;
    background: var(--surface-2);
    border: 1.5px solid var(--border);
    border-radius: 0.75rem;
    color: var(--text);
    font-family: 'Outfit', sans-serif;
}
.form-file::-webkit-file-upload-button {
    margin-right: 0.75rem;
    padding: 0.375rem 0.875rem;
    border-radius: 0.5rem;
    border: none;
    font-size: 0.75rem;
    font-weight: 600;
    background: rgba(5,150,105,0.1);
    color: #047857;
    cursor: pointer;
    transition: background 0.15s ease;
}
.form-file::-webkit-file-upload-button:hover {
    background: rgba(5,150,105,0.2);
}
html.dark .form-file::-webkit-file-upload-button {
    background: rgba(16,185,129,0.15);
    color: #6ee7b7;
}

/* ── Phone Input Group ── */
.input-group {
    display: flex;
}
.input-group-prepend {
    display: inline-flex;
    align-items: center;
    padding: 0 0.875rem;
    border: 1.5px solid var(--border);
    border-right: none;
    border-radius: 0.75rem 0 0 0.75rem;
    background: var(--surface-3);
    color: var(--text-3);
    font-size: 0.85rem;
    white-space: nowrap;
}
.input-group .form-input {
    border-radius: 0 0.75rem 0.75rem 0;
}

/* ── Error Alert ── */
.error-alert {
    background: rgba(220,38,38,0.08);
    border-left: 3px solid #ef4444;
    border-radius: 0.75rem;
    padding: 1rem;
}
.error-alert-title {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
}
.error-alert-title svg {
    width: 1.25rem;
    height: 1.25rem;
    stroke: #ef4444;
}
.error-alert-title h3 {
    font-size: 0.8rem;
    font-weight: 700;
    color: #ef4444;
}
.error-list {
    list-style: disc;
    list-style-position: inside;
    font-size: 0.75rem;
    color: #dc2626;
    space-y: 0.25rem;
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
.btn-emerald:hover:not(:disabled) {
    background: linear-gradient(135deg, var(--gold), var(--gold-dark));
    color: #0f172a;
    transform: translateY(-1px);
    box-shadow: 0 4px 16px rgba(212,175,55,0.35);
}
.btn-emerald:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.btn-cancel {
    display: inline-flex;
    align-items: center;
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
.btn-cancel:hover {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    transform: translateY(-1px);
    box-shadow: 0 4px 16px rgba(220,38,38,0.35);
}

/* ── Loading Spinner ── */
@keyframes spin {
    to { transform: rotate(360deg); }
}
.spinner {
    width: 1rem;
    height: 1rem;
    animation: spin 0.8s linear infinite;
}
</style>
@endpush

@section('content')

<div class="space-y-5">
    {{-- Hero Section --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-emerald-600 to-emerald-700 dark:from-emerald-800 dark:to-emerald-900 p-6 md:p-7">
        <div class="absolute inset-0 opacity-[0.05]"
             style="background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,1) 1px, transparent 0); background-size: 28px 28px;"></div>
        <div class="absolute -top-16 right-0 w-64 h-64 rounded-full opacity-20"
             style="background: radial-gradient(circle, #D4AF37, transparent 65%); filter: blur(48px);"></div>
        <div class="relative z-10">
            <p class="text-emerald-300 text-[10px] font-bold tracking-[0.2em] uppercase mb-2"
               style="font-family:'DM Mono',monospace;">
                {{ now()->format('F Y') }} · Member Registration
            </p>
            <h1 class="text-2xl md:text-3xl font-bold text-white tracking-tight">Create New Member</h1>
            <p class="text-emerald-100/80 text-sm mt-1.5 max-w-lg">Add a new member to your organization</p>
        </div>
    </div>

    <div class="form-container" x-data="memberCreateForm()" x-init="init()">
        <form method="POST" action="{{ route('members.store') }}" class="space-y-6" enctype="multipart/form-data">
            @csrf

            {{-- Error Alerts --}}
            @if ($errors->any())
                <div class="error-alert">
                    <div class="error-alert-title">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <h3>Please fix the following errors:</h3>
                    </div>
                    <ul class="error-list">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Basic Information Card --}}
            <div class="form-card">
                <div class="form-card-header">
                    <div class="flex items-center gap-3">
                        <div class="form-card-header-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div>
                            <h2>Basic Information</h2>
                            <p>Personal and account details</p>
                        </div>
                    </div>
                </div>

                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">First Name <span class="form-label-required">*</span></label>
                            <input type="text" name="first_name" value="{{ old('first_name') }}" required
                                   class="form-input {{ $errors->has('first_name') ? 'error' : '' }}">
                            @error('first_name') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="form-label">Last Name <span class="form-label-required">*</span></label>
                            <input type="text" name="last_name" value="{{ old('last_name') }}" required
                                   class="form-input {{ $errors->has('last_name') ? 'error' : '' }}">
                            @error('last_name') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="form-label">Middle Name <span class="font-normal text-text-3">(Optional)</span></label>
                        <input type="text" name="middle_name" value="{{ old('middle_name') }}" class="form-input">
                    </div>

                    <div>
                        <label class="form-label">Gender <span class="form-label-required">*</span></label>
                        <select name="gender" required class="form-select {{ $errors->has('gender') ? 'error' : '' }}">
                            <option value="">Select Gender</option>
                            <option value="Male"   {{ old('gender') === 'Male'   ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('gender') === 'Female' ? 'selected' : '' }}>Female</option>
                            <option value="Other"  {{ old('gender') === 'Other'  ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('gender') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="form-label">Birthday</label>
                        <input type="date" name="birthday" value="{{ old('birthday') }}" class="form-input">
                    </div>

                    <div>
                        <label class="form-label">Phone Number</label>
                        <div class="input-group">
                            <span class="input-group-prepend">+63</span>
                            <input type="tel" name="phone" value="{{ old('phone') }}"
                                   maxlength="10" placeholder="9123456789"
                                   oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,10)"
                                   class="form-input {{ $errors->has('phone') ? 'error' : '' }}">
                        </div>
                        <p class="form-hint">10-digit number, +63 added automatically.</p>
                        @error('phone') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="form-label">Email <span class="form-label-required">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                               class="form-input {{ $errors->has('email') ? 'error' : '' }}">
                        @error('email') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="form-label">Role <span class="form-label-required">*</span></label>
                        <select name="role_id" x-model="selectedRoleId" required
                                class="form-select {{ $errors->has('role_id') ? 'error' : '' }}">
                            <option value="">Select Role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('role_id') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    {{-- Position Field --}}
                    <div>
                        <label class="form-label">Position <span class="form-label-required">*</span></label>

                        <template x-if="!selectedRoleId">
                            <select disabled class="form-select bg-surface-3 text-text-3">
                                <option>Select a role first</option>
                            </select>
                        </template>

                        <template x-if="selectedRoleId && positionOptions.length > 0">
                            <select name="position" x-model="selectedPosition"
                                    class="form-select {{ $errors->has('position') ? 'error' : '' }}">
                                <option value="">Select Position</option>
                                <template x-for="pos in positionOptions" :key="pos">
                                    <option :value="pos" x-text="pos" :selected="pos === selectedPosition"></option>
                                </template>
                            </select>
                        </template>

                        <template x-if="selectedRoleId && positionOptions.length === 0">
                            <div>
                                <input type="hidden" name="position" value="">
                                <p class="text-sm text-text-3 italic py-2">No position required for this role.</p>
                            </div>
                        </template>

                        <p class="form-hint">Positions are based on the selected role.</p>
                        @error('position') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    {{-- Student Fields (shown only for student roles) --}}
                    <div x-show="isStudentRole" x-cloak>
                        <label class="form-label">Student ID</label>
                        <input type="text" name="student_id" value="{{ old('student_id') }}" placeholder="2020-12345"
                               class="form-input {{ $errors->has('student_id') ? 'error' : '' }}">
                        <p class="form-hint">Format: YYYY-XXXXX (e.g., 2020-12345)</p>
                        @error('student_id') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div x-show="isStudentRole" x-cloak>
                        <label class="form-label">Year Level</label>
                        <select x-model="yearLevelValue" class="form-select">
                            <option value="">Select Grade</option>
                            @for($i = 7; $i <= 12; $i++)
                                <option value="Grade {{ $i }}" {{ old('year_level') === 'Grade '.$i ? 'selected' : '' }}>
                                    Grade {{ $i }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <input type="hidden" name="year_level" :value="yearLevelValue">

                    {{-- Account Status Toggle --}}
                    <div class="flex items-center gap-3">
                        <label class="form-label mb-0">Account Status</label>
                        <label class="switch">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1"
                                   {{ old('is_active', '1') == '1' ? 'checked' : '' }}
                                   class="switch-input">
                            <span class="switch-slider"></span>
                            <span class="switch-label">Active</span>
                        </label>
                    </div>

                    {{-- Profile Photo --}}
                    <div>
                        <label class="form-label">Profile Photo <span class="font-normal text-text-3">(Optional)</span></label>
                        <input type="file" name="avatar" accept="image/jpg,image/jpeg,image/png,image/gif,image/webp"
                               class="form-file">
                        <p class="form-hint">JPG, PNG, GIF, WEBP — max 2MB</p>
                        @error('avatar') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- Security Card --}}
            {{-- Security Card with Password Toggle --}}
<div class="form-card">
    <div class="form-card-header">
        <div class="flex items-center gap-3">
            <div class="form-card-header-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <div>
                <h2>Security</h2>
                <p>Leave blank to auto-generate a secure password</p>
            </div>
        </div>
    </div>

    <div class="p-6 space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Password Field with Toggle --}}
            <div x-data="{ showPassword: false }">
                <label class="form-label">Password</label>
                <div class="relative">
                    <input 
                        :type="showPassword ? 'text' : 'password'" 
                        name="password" 
                        autocomplete="new-password"
                        class="form-input pr-10 {{ $errors->has('password') ? 'error' : '' }}">
                    <button 
                        type="button"
                        @click="showPassword = !showPassword"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-text-3 hover:text-gold transition"
                        :aria-label="showPassword ? 'Hide password' : 'Show password'">
                        <svg x-show="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <svg x-show="showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 01-4.243-4.243m4.242 4.242L9.88 9.88"/>
                        </svg>
                    </button>
                </div>
                @error('password') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            {{-- Confirm Password Field with Toggle --}}
            <div class="p-6 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <div x-data="{ showPassword: false }">
                    <label class="form-label">Password</label>
                    <div class="relative">
                        <input :type="showPassword ? 'text' : 'password'" name="password"
                            autocomplete="new-password"
                            class="form-input pr-10 {{ $errors->has('password') ? 'error' : '' }}">
                        {{-- toggle button --}}
                    </div>
                    @error('password') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div x-data="{ showConfirmPassword: false }">
                    <label class="form-label">Confirm Password</label>
                    <div class="relative">
                        <input :type="showConfirmPassword ? 'text' : 'password'" name="password_confirmation"
                            autocomplete="new-password"
                            class="form-input pr-10">
                        {{-- toggle button --}}
                    </div>
                </div>

            </div>{{-- ← closes grid --}}

            <p class="form-hint">Minimum 8 characters. If left blank, a random password is generated and emailed.</p>

        </div>{{-- ← closes p-6 --}}

            {{-- Membership Details Card --}}
            <div class="form-card">
                <div class="form-card-header">
                    <div class="flex items-center gap-3">
                        <div class="form-card-header-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <div>
                            <h2>Membership Details</h2>
                            <p>Term and joining information</p>
                        </div>
                    </div>
                </div>

                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Joined Date</label>
                            <input type="date" name="joined_at" value="{{ old('joined_at', date('Y-m-d')) }}" class="form-input">
                        </div>
                        <div>
                            <label class="form-label">Term Start</label>
                            <input type="date" name="term_start" value="{{ old('term_start') }}" class="form-input">
                        </div>
                    </div>
                    <div>
                        <label class="form-label">Term End <span class="font-normal text-text-3">(Leave empty if ongoing)</span></label>
                        <input type="date" name="term_end" value="{{ old('term_end') }}" class="form-input">
                    </div>
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="flex justify-between items-center gap-3">
                <button type="submit"
                        x-data="{ busy: false }"
                        @click="if (busy) { $event.preventDefault(); $event.stopImmediatePropagation(); return; }"
                        @submit.window="if ($event.target === $el.closest('form')) { busy = true; }"
                        :disabled="busy"
                        class="btn-emerald">
                    <span x-show="!busy">Create Member</span>
                    <span x-show="busy" class="flex items-center gap-2">
                        <svg class="spinner" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                        </svg>
                        Creating...
                    </span>
                </button>
                <a href="{{ route('members.index') }}" class="btn-cancel">
                    Cancel
                </a>
            </div>

        </form>
    </div>
</div>

@push('scripts')
<script>
    {{-- positionMapping — passed from MemberController@create, derived from Member::VALID_POSITIONS.
        nonStudentPositions — passed from MemberController@create, derived from Member::NON_STUDENT_POSITIONS.
        Neither array is hardcoded here; all logic is data-driven from the model constants. --}}
    const positionMapping      = @json($positionMapping ?? []);
    const nonStudentPositions  = @json($nonStudentPositions ?? []);

    function memberCreateForm() {
        return {
            selectedRoleId:   '{{ old('role_id', '') }}',
            selectedPosition: '{{ old('position', '') }}',
            yearLevelValue:   '{{ old('year_level', '') }}',
            positionOptions:  [],
            isStudentRole:    false,

            init() {
                this.updatePositionOptions();

                this.$watch('selectedRoleId', () => {
                    this.selectedPosition = '';
                    this.yearLevelValue   = '';
                    this.updatePositionOptions();
                });

                this.$watch('selectedPosition', () => {
                    this.updateStudentFlag();
                });
            },

            updatePositionOptions() {
                const id = parseInt(this.selectedRoleId);
                this.positionOptions = (id && positionMapping[id] !== undefined)
                    ? positionMapping[id]
                    : [];

                if (this.selectedPosition && !this.positionOptions.includes(this.selectedPosition)) {
                    this.selectedPosition = '';
                }

                this.updateStudentFlag();
            },

            updateStudentFlag() {
                if (!this.selectedRoleId) {
                    this.isStudentRole = false;
                    return;
                }

                const pos = this.selectedPosition;
                if (!pos) {
                    this.isStudentRole = this.positionOptions.some(p => !nonStudentPositions.includes(p));
                } else {
                    this.isStudentRole = !nonStudentPositions.includes(pos);
                }

                if (!this.isStudentRole) {
                    this.yearLevelValue = '';
                }
            },
        };
    }
</script>
@endpush

@endsection