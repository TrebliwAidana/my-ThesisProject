@extends('layouts.app')

@section('title', 'Create User — VSULHS SSLG')
@section('page-title', 'Create New User')

@push('styles')
<style>
/* ════════════════════════════════════════════════
   CREATE USER — Emerald & Gold Luxury Theme
   Matching all management views
════════════════════════════════════════════════ */

[x-cloak] { display: none !important; }

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
.form-radio-group {
    display: flex;
    gap: 1.5rem;
}
.form-radio-label {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    font-size: 0.8rem;
    color: var(--text-2);
}
.form-radio-label input[type="radio"] {
    width: 1rem;
    height: 1rem;
    accent-color: var(--emerald);
    cursor: pointer;
}

/* ── Checkbox Styling ── */
.form-checkbox-label {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    font-size: 0.8rem;
    color: var(--text-2);
}
.form-checkbox-label input[type="checkbox"] {
    width: 1rem;
    height: 1rem;
    border-radius: 0.25rem;
    accent-color: var(--emerald);
    cursor: pointer;
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
.error-list {
    list-style: disc;
    list-style-position: inside;
    font-size: 0.75rem;
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
    justify-content: space-between;
    gap: 1rem;
    margin-top: 0.5rem;
}

/* ── Form Hint ── */
.form-hint {
    font-size: 0.65rem;
    color: var(--text-3);
    margin-top: 0.25rem;
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

/* ── Info Box ── */
.info-box {
    background: rgba(5,150,105,0.05);
    border: 1px solid rgba(5,150,105,0.15);
    border-radius: 0.75rem;
    padding: 0.75rem 1rem;
    margin-bottom: 1rem;
}
html.dark .info-box {
    background: rgba(16,185,129,0.08);
}
.info-box p {
    font-size: 0.7rem;
    color: var(--text-3);
}
.info-box svg {
    width: 1rem;
    height: 1rem;
    color: var(--emerald);
    flex-shrink: 0;
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
    $validPositions    = \App\Models\Member::VALID_POSITIONS;
    $nonStudentRoleIds = collect($validPositions)
        ->filter(fn($positions) =>
            collect($positions)->every(fn($p) =>
                in_array($p, \App\Models\Member::NON_STUDENT_POSITIONS)
            )
        )
        ->keys()
        ->toArray();
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
            <p class="text-emerald-300 text-[10px] font-bold tracking-[0.2em] uppercase mb-2"
               style="font-family:'DM Mono',monospace;">
                {{ now()->format('F Y') }} · User Management
            </p>
            <h1 class="text-2xl md:text-3xl font-bold text-white tracking-tight">Create New User</h1>
            <p class="text-emerald-100/80 text-sm mt-1.5 max-w-lg">Add a new user to the system</p>
        </div>
    </div>

    {{-- Form Card --}}
    <div class="form-container anim-2" x-data="userCreateForm()" x-init="init()">
        <div class="form-card">
            <div class="form-card-header">
                <div class="flex items-center gap-3">
                    <div class="form-card-header-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <h2>Create New User</h2>
                        <p>Enter user details below</p>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <form method="POST" action="{{ route('admin.users.store') }}">
                    @csrf

                    {{-- Error Alerts --}}
                    @if($errors->any())
                        <div class="error-alert">
                            <ul class="error-list">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Name Fields --}}
                    <div class="form-grid mb-4">
                        <div>
                            <label class="form-label">First Name <span class="form-label-required">*</span></label>
                            <input type="text" name="first_name" value="{{ old('first_name') }}" required
                                   class="form-input {{ $errors->has('first_name') ? 'error' : '' }}">
                            @error('first_name') <p class="form-error mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="form-label">Last Name <span class="form-label-required">*</span></label>
                            <input type="text" name="last_name" value="{{ old('last_name') }}" required
                                   class="form-input {{ $errors->has('last_name') ? 'error' : '' }}">
                            @error('last_name') <p class="form-error mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Middle Name --}}
                    <div class="mb-4">
                        <label class="form-label">Middle Name <span class="font-normal text-text-3">(Optional)</span></label>
                        <input type="text" name="middle_name" value="{{ old('middle_name') }}" class="form-input">
                    </div>

                    {{-- Gender --}}
                    <div class="mb-4">
                        <label class="form-label">Gender <span class="form-label-required">*</span></label>
                        <select name="gender" required class="form-select {{ $errors->has('gender') ? 'error' : '' }}">
                            <option value="">Select Gender</option>
                            <option value="Male"   {{ old('gender') === 'Male'   ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('gender') === 'Female' ? 'selected' : '' }}>Female</option>
                            <option value="Other"  {{ old('gender') === 'Other'  ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('gender') <p class="form-error mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Birthday --}}
                    <div class="mb-4">
                        <label class="form-label">Birthday</label>
                        <input type="date" name="birthday" value="{{ old('birthday') }}" class="form-input">
                    </div>

                    {{-- Phone Number --}}
                    <div class="mb-4">
                        <label class="form-label">Phone Number</label>
                        <div class="input-group">
                            <span class="input-group-prepend">+63</span>
                            <input type="tel" name="phone" value="{{ old('phone') }}" maxlength="10" placeholder="9123456789"
                                   class="form-input {{ $errors->has('phone') ? 'error' : '' }}"
                                   oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,10)">
                        </div>
                        <p class="form-hint">Enter 10-digit number (e.g., 9123456789). +63 will be added automatically.</p>
                        @error('phone') <p class="form-error mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Email --}}
                    <div class="mb-4">
                        <label class="form-label">Email <span class="form-label-required">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                               class="form-input {{ $errors->has('email') ? 'error' : '' }}">
                        <p class="form-hint">A welcome email with credentials will be sent to this address.</p>
                        @error('email') <p class="form-error mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Role --}}
                    <div class="mb-4">
                        <label class="form-label">Role <span class="form-label-required">*</span></label>
                        <select name="role_id" x-model="selectedRoleId" required
                                class="form-select {{ $errors->has('role_id') ? 'error' : '' }}">
                            <option value="">Select a role</option>
                            @foreach($roles as $role)
                                @php
                                    $rid   = is_array($role) ? $role['id']   : $role->id;
                                    $rname = is_array($role) ? $role['name'] : $role->name;
                                @endphp
                                <option value="{{ $rid }}" {{ old('role_id') == $rid ? 'selected' : '' }}>
                                    {{ $rname }}
                                </option>
                            @endforeach
                        </select>
                        @error('role_id') <p class="form-error mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Position --}}
                    <div class="mb-4">
                        <label class="form-label">Position</label>
                        <div x-show="positionOptions.length > 0">
                            <select name="position" x-model="selectedPosition"
                                    class="form-select {{ $errors->has('position') ? 'error' : '' }}">
                                <option value="">Select Position</option>
                                <template x-for="pos in positionOptions" :key="pos">
                                    <option :value="pos" x-text="pos"></option>
                                </template>
                            </select>
                            <p class="form-hint">Positions are based on the selected role.</p>
                            @error('position') <p class="form-error mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div x-show="positionOptions.length === 0 && selectedRoleId" x-cloak
                             class="text-sm text-text-3 italic py-2">
                            No positions defined for this role.
                        </div>
                        <div x-show="!selectedRoleId" x-cloak
                             class="text-sm text-text-3 italic py-2">
                            Select a role first to see available positions.
                        </div>
                    </div>

                    {{-- Student ID --}}
                    <div class="mb-4">
                        <label class="form-label">Student ID</label>
                        <input type="text" name="student_id" value="{{ old('student_id') }}" placeholder="2020-12345"
                               class="form-input {{ $errors->has('student_id') ? 'error' : '' }}">
                        <p class="form-hint">Format: YYYY-XXXXX (e.g., 2020-12345)</p>
                        @error('student_id') <p class="form-error mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Year Level (Student only) --}}
                    <div class="mb-4" x-show="isStudentRole" x-cloak>
                        <label class="form-label">Year Level</label>
                        <select x-model="yearLevelValue" class="form-select">
                            <option value="">Select Year Level</option>
                            <option value="Grade 7">Grade 7</option>
                            <option value="Grade 8">Grade 8</option>
                            <option value="Grade 9">Grade 9</option>
                            <option value="Grade 10">Grade 10</option>
                            <option value="Grade 11">Grade 11</option>
                            <option value="Grade 12">Grade 12</option>
                        </select>
                    </div>
                    <div x-show="!isStudentRole" x-cloak class="text-sm text-text-3 italic mb-4">
                        Year level is not applicable for this role.
                    </div>
                    <input type="hidden" name="year_level" :value="yearLevelValue">

                    {{-- Password Section --}}
                    <div class="mb-4">
                        <label class="form-checkbox-label">
                            <input type="checkbox" x-model="setPassword" class="rounded border-border">
                            <span>Set custom password <span class="text-text-3">(Optional)</span></span>
                        </label>

                        <div x-show="setPassword" x-cloak class="mt-3 space-y-3">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="form-label">Password</label>
                                    <div class="relative">
                                        <input :type="showPassword ? 'text' : 'password'"
                                               name="password"
                                               class="form-input pr-10 {{ $errors->has('password') ? 'error' : '' }}">
                                        <button type="button" @click="showPassword = !showPassword" class="password-toggle">
                                            <svg x-show="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            <svg x-show="showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                            </svg>
                                        </button>
                                    </div>
                                    @error('password') <p class="form-error mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="form-label">Confirm Password</label>
                                    <div class="relative">
                                        <input :type="showPasswordConfirm ? 'text' : 'password'"
                                               name="password_confirmation"
                                               class="form-input pr-10">
                                        <button type="button" @click="showPasswordConfirm = !showPasswordConfirm" class="password-toggle">
                                            <svg x-show="!showPasswordConfirm" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            <svg x-show="showPasswordConfirm" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <p class="form-hint mt-2">
                            If left blank, a random password will be generated and emailed to the user.
                        </p>
                    </div>

                    {{-- Account Status --}}
                    <div class="mb-6">
                        <label class="form-label">Account Status</label>
                        <div class="form-radio-group">
                            <label class="form-radio-label">
                                <input type="radio" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                                <span>Active</span>
                            </label>
                            <label class="form-radio-label">
                                <input type="radio" name="is_active" value="0" {{ old('is_active') == '0' ? 'checked' : '' }}>
                                <span>Inactive</span>
                            </label>
                        </div>
                    </div>

                    {{-- Form Actions --}}
                    <div class="button-group">
                        <button type="submit"
                                x-data="{ busy: false }"
                                @click="if (busy) { $event.preventDefault(); $event.stopImmediatePropagation(); return; }"
                                @submit.window="if ($event.target === $el.closest('form')) { busy = true; }"
                                :disabled="busy"
                                class="btn-emerald">
                            <span x-show="!busy">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Create User
                            </span>
                            <span x-show="busy" class="flex items-center gap-2">
                                <svg class="spinner" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                                </svg>
                                Creating...
                            </span>
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
</div>

<script>
    const validPositions   = @json(\App\Models\Member::VALID_POSITIONS);
    const nonStudentPositions = @json(\App\Models\Member::NON_STUDENT_POSITIONS);
    const nonStudentRoleIds = @json($nonStudentRoleIds);

    function userCreateForm() {
        return {
            selectedRoleId:     {{ old('role_id', 'null') }},
            selectedPosition:   '{{ old('position') }}',
            positionOptions:    [],
            setPassword:        false,
            isStudentRole:      true,
            yearLevelValue:     '{{ old('year_level') }}',
            showPassword:       false,
            showPasswordConfirm: false,

            init() {
                this.updatePositionOptions();
                this.checkIfStudentRole();
                this.$watch('selectedRoleId', () => {
                    this.updatePositionOptions();
                    this.checkIfStudentRole();
                });
                this.$watch('selectedPosition', () => {
                    this.checkIfStudentRole();
                });
            },

            updatePositionOptions() {
                const roleId = this.selectedRoleId;
                this.positionOptions = (roleId && validPositions[roleId])
                    ? validPositions[roleId]
                    : [];

                if (this.selectedPosition && !this.positionOptions.includes(this.selectedPosition)) {
                    this.selectedPosition = '';
                }
            },

            checkIfStudentRole() {
                const roleId = parseInt(this.selectedRoleId);

                if (!validPositions[roleId]) {
                    this.isStudentRole = true;
                    return;
                }

                if (nonStudentRoleIds.includes(roleId)) {
                    this.isStudentRole = false;
                    this.yearLevelValue = '';
                    return;
                }

                if (this.selectedPosition && nonStudentPositions.includes(this.selectedPosition)) {
                    this.isStudentRole = false;
                    this.yearLevelValue = '';
                    return;
                }

                this.isStudentRole = true;
            }
        };
    }
</script>

@endsection