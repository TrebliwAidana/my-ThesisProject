    @extends('layouts.app')

    @section('title', 'Edit Member — VSULHS SSLG')
    @section('page-title', 'Edit Member')

    @push('styles')
    <style>
    /* ════════════════════════════════════════════════
    EDIT MEMBER — Emerald & Gold Luxury Theme
    Matching Create Member and other management views
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
        background: var(--surface-3);
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
    .form-readonly {
        background: var(--surface-3);
        color: var(--text-2);
        padding: 0.625rem 1rem;
        border-radius: 0.75rem;
        font-size: 0.85rem;
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

    /* ── Radio Group ── */
    .radio-group {
        display: flex;
        gap: 1.5rem;
    }
    .radio-label {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
        font-size: 0.8rem;
        color: var(--text-2);
    }
    .radio-label input[type="radio"] {
        width: 1rem;
        height: 1rem;
        accent-color: var(--emerald);
        cursor: pointer;
    }

    /* ── Checkbox Styling ── */
    .checkbox-label {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
        font-size: 0.8rem;
        color: var(--text-2);
    }
    .checkbox-label input[type="checkbox"] {
        width: 1rem;
        height: 1rem;
        border-radius: 0.25rem;
        accent-color: var(--emerald);
        cursor: pointer;
    }

    /* ── Avatar Section ── */
    .avatar-container {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .avatar-image {
        width: 4rem;
        height: 4rem;
        border-radius: 9999px;
        object-fit: cover;
        border: 2px solid var(--gold);
        box-shadow: 0 2px 8px rgba(212,175,55,0.2);
    }
    .avatar-placeholder {
        width: 4rem;
        height: 4rem;
        border-radius: 9999px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.25rem;
        color: white;
        background: linear-gradient(135deg, var(--gold), var(--emerald));
        box-shadow: 0 2px 8px rgba(212,175,55,0.2);
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

    .btn-history {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.6rem 1rem;
        font-size: 0.85rem;
        font-weight: 700;
        background: linear-gradient(135deg, #7c3aed, #6d28d9);
        color: #fff;
        border: none;
        border-radius: 0.75rem;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        font-family: 'Outfit', sans-serif;
        box-shadow: 0 2px 10px rgba(124,58,237,0.22);
    }
    .btn-history:hover {
        background: linear-gradient(135deg, var(--gold), var(--gold-dark));
        color: #0f172a;
        transform: translateY(-1px);
        box-shadow: 0 4px 16px rgba(212,175,55,0.35);
    }

    .btn-cancel {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.6rem 1rem;
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

    /* ── Info Box ── */
    .info-box {
        background: rgba(5,150,105,0.05);
        border: 1px solid rgba(5,150,105,0.15);
        border-radius: 0.75rem;
        padding: 0.75rem 1rem;
    }
    html.dark .info-box {
        background: rgba(16,185,129,0.08);
    }
    </style>
    @endpush

    @section('content')

    @php
        $guestEmail = 'guest@gmail.com';
        $isGuest    = $user->email === $guestEmail;
    @endphp

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
                    {{ now()->format('F Y') }} · Member Management
                </p>
                <h1 class="text-2xl md:text-3xl font-bold text-white tracking-tight">Edit Member</h1>
                <p class="text-emerald-100/80 text-sm mt-1.5 max-w-lg">Update member information and account details</p>
            </div>
        </div>

        <div class="form-container" x-data="memberEditForm()" x-init="init()">
            <form method="POST" action="{{ route('members.update', $user->id) }}" class="space-y-6" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Error Alerts --}}
                @if($errors->any())
                    <div class="error-alert">
                        <div class="error-alert-title">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <h3>Please fix the following errors:</h3>
                        </div>
                        <ul class="error-list">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Profile Picture Card --}}
                <div class="form-card">
                    <div class="form-card-header">
                        <div class="flex items-center gap-3">
                            <div class="form-card-header-icon">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <div>
                                <h2>Profile Picture</h2>
                                <p>Update member avatar</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="avatar-container">
                            @if($user->avatar)
                                <img src="{{ str_starts_with($user->avatar, 'http') ? $user->avatar : asset('storage/' . $user->avatar) }}"
                                    alt="{{ $user->full_name }}"
                                    class="avatar-image">
                            @else
                                <div class="avatar-placeholder">
                                    {{ strtoupper(mb_substr($user->full_name, 0, 2)) }}
                                </div>
                            @endif
                            <div class="flex-1">
                                <input type="file" name="avatar" accept="image/jpeg,image/png,image/gif,image/webp"
                                    class="form-file">
                                <p  class="form-hint">JPG, PNG, GIF, WEBP — Max 2MB</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Personal Information Card --}}
                <div class="form-card">
                    <div class="form-card-header">
                        <div class="flex items-center gap-3">
                            <div class="form-card-header-icon">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <div>
                                <h2>Personal Information</h2>
                                <p>Basic member details</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="form-label">First Name <span class="form-label-required">*</span></label>
                                <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}" required
                                    {{ $isGuest ? 'disabled' : '' }}
                                    class="form-input {{ $errors->has('first_name') ? 'error' : '' }}">
                                @if($isGuest)<input type="hidden" name="first_name" value="{{ $user->first_name }}">@endif
                                @error('first_name') <p class="form-error">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="form-label">Last Name <span class="form-label-required">*</span></label>
                                <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}" required
                                    {{ $isGuest ? 'disabled' : '' }}
                                    class="form-input {{ $errors->has('last_name') ? 'error' : '' }}">
                                @if($isGuest)<input type="hidden" name="last_name" value="{{ $user->last_name }}">@endif
                                @error('last_name') <p class="form-error">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="form-label">Middle Name <span class="font-normal text-text-3">(Optional)</span></label>
                            <input type="text" name="middle_name" value="{{ old('middle_name', $user->middle_name) }}"
                                {{ $isGuest ? 'disabled' : '' }}
                                class="form-input">
                            @if($isGuest)<input type="hidden" name="middle_name" value="{{ $user->middle_name }}">@endif
                        </div>

                        <div>
                            <label class="form-label">Gender <span class="form-label-required">*</span></label>
                            <select name="gender" required {{ $isGuest ? 'disabled' : '' }}
                                    class="form-select {{ $errors->has('gender') ? 'error' : '' }}">
                                <option value="">Select Gender</option>
                                @foreach(['Male', 'Female', 'Other'] as $g)
                                    <option value="{{ $g }}" {{ old('gender', $user->gender) === $g ? 'selected' : '' }}>{{ $g }}</option>
                                @endforeach
                            </select>
                            @if($isGuest)<input type="hidden" name="gender" value="{{ $user->gender }}">@endif
                            @error('gender') <p class="form-error">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="form-label">Birthday</label>
                            <input type="date" name="birthday"
                                value="{{ old('birthday', optional($user->birthday)->format('Y-m-d')) }}"
                                {{ $isGuest ? 'disabled' : '' }}
                                class="form-input">
                            @if($isGuest)<input type="hidden" name="birthday" value="{{ optional($user->birthday)->format('Y-m-d') }}">@endif
                        </div>

                        <div>
                            <label class="form-label">Phone Number</label>
                            <div class="input-group">
                                <span class="input-group-prepend">+63</span>
                                <input type="tel" name="phone"
                                    value="{{ old('phone', $user->phone ? ltrim(substr($user->phone, 3)) : '') }}"
                                    maxlength="10" placeholder="9123456789"
                                    {{ $isGuest ? 'disabled' : '' }}
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,10)"
                                    class="form-input {{ $errors->has('phone') ? 'error' : '' }}">
                            </div>
                            <p class="form-hint">10-digit number — +63 is added automatically.</p>
                            @if($isGuest)
                                <input type="hidden" name="phone" value="{{ $user->phone ? substr($user->phone, 3) : '' }}">
                            @endif
                            @error('phone') <p class="form-error">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="form-label">Email <span class="form-label-required">*</span></label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                {{ $isGuest ? 'disabled' : '' }}
                                class="form-input {{ $errors->has('email') ? 'error' : '' }}">
                            @if($isGuest)<input type="hidden" name="email" value="{{ $user->email }}">@endif
                            @error('email') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- Role & Position Card --}}
                <div class="form-card">
                    <div class="form-card-header">
                        <div class="flex items-center gap-3">
                            <div class="form-card-header-icon">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            </div>
                            <div>
                                <h2>Role & Position</h2>
                                <p>Member organizational role</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 space-y-4">
                        @if($isGuest)
                            <input type="hidden" name="role_id" value="{{ $user->role_id }}">
                            <div>
                                <label class="form-label">Role</label>
                                <div class="form-readonly">
                                    {{ $user->role->name }} <span class="text-text-3">(Fixed)</span>
                                </div>
                            </div>
                        @else
                            <div>
                                <label class="form-label">Role <span class="form-label-required">*</span></label>
                                <select name="role_id" x-model="selectedRoleId" required
                                        class="form-select {{ $errors->has('role_id') ? 'error' : '' }}">
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role_id') <p class="form-error">{{ $message }}</p> @enderror
                            </div>
                        @endif

                        <div>
                            <label class="form-label">Position</label>
                            @if($isGuest)
                                <div class="form-readonly">
                                    {{ $user->position ?? '—' }} <span class="text-text-3">(Fixed)</span>
                                </div>
                                <input type="hidden" name="position" value="{{ $user->position }}">
                            @else
                                <select name="position" x-model="selectedPosition"
                                        class="form-select">
                                    <option value="">Select Position</option>
                                    <template x-for="pos in positionOptions" :key="pos">
                                        <option :value="pos" x-text="pos" :selected="pos === selectedPosition"></option>
                                    </template>
                                </select>
                                <p class="form-hint">Positions are based on the selected role.</p>
                            @endif
                        </div>

                        @if(!$isGuest)
                        <input type="hidden" name="position_manually_changed" x-model="positionManuallyChanged">

                        <div x-show="positionChanged" x-cloak>
                            <label class="form-label">Reason for Change <span class="form-label-required">*</span></label>
                            <textarea name="position_change_reason" rows="2"
                                    :required="positionChanged"
                                    class="form-input"
                                    placeholder="Please provide a reason for changing the position…"></textarea>
                            <p class="form-hint">Required when changing position.</p>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Academic Information Card --}}
                <div class="form-card">
                    <div class="form-card-header">
                        <div class="flex items-center gap-3">
                            <div class="form-card-header-icon">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                                </svg>
                            </div>
                            <div>
                                <h2>Academic Information</h2>
                                <p>Student-specific details</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="form-label">Student ID</label>
                            <input type="text" name="student_id" value="{{ old('student_id', $user->student_id) }}"
                                placeholder="2020-12345"
                                {{ $isGuest ? 'disabled' : '' }}
                                class="form-input">
                            <p class="form-hint">Format: YYYY-XXXXX (e.g., 2020-12345)</p>
                            @if($isGuest)<input type="hidden" name="student_id" value="{{ $user->student_id }}">@endif
                            @error('student_id') <p class="form-error">{{ $message }}</p> @enderror
                        </div>

                        <div x-show="isStudentRole" x-cloak>
                            <label class="form-label">Year Level</label>
                            <select x-model="yearLevelValue" class="form-select">
                                <option value="">Select Grade</option>
                                @for($i = 7; $i <= 12; $i++)
                                    <option value="Grade {{ $i }}" {{ old('year_level', $user->year_level) === 'Grade '.$i ? 'selected' : '' }}>
                                        Grade {{ $i }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div x-show="!isStudentRole" class="info-box">
                            <p class="text-sm text-text-3 italic">Year level is not applicable for this role.</p>
                        </div>
                        <input type="hidden" name="year_level" :value="yearLevelValue">
                        @if($isGuest)<input type="hidden" name="year_level" value="{{ $user->year_level }}">@endif
                    </div>
                </div>

                {{-- Security Card --}}
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
                                <p>Change password and account status</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 space-y-4">
                        <div x-data="{ showNew: false, showConfirm: false }">
                            <label class="checkbox-label">
                                <input type="checkbox" x-model="changePassword" {{ $isGuest ? 'disabled' : '' }}>
                                <span>Change Password</span>
                            </label>

                            <div x-show="changePassword" x-cloak class="mt-3 space-y-3">
                                <div>
                                    <label class="form-label">New Password</label>
                                    <div class="relative">
                                        <input :type="showNew ? 'text' : 'password'" name="password" autocomplete="new-password"
                                            class="form-input pr-10">
                                        <button type="button" @click="showNew = !showNew" class="password-toggle">
                                            <svg x-show="!showNew" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            <svg x-show="showNew" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                            </svg>
                                        </button>
                                    </div>
                                    @error('password') <p class="form-error">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="form-label">Confirm Password</label>
                                    <div class="relative">
                                        <input :type="showConfirm ? 'text' : 'password'" name="password_confirmation" autocomplete="new-password"
                                            class="form-input pr-10">
                                        <button type="button" @click="showConfirm = !showConfirm" class="password-toggle">
                                            <svg x-show="!showConfirm" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            <svg x-show="showConfirm" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <p class="form-hint mt-2">Leave blank to keep the current password.</p>
                        </div>

                        <div>
                            <label class="form-label">Account Status</label>
                            <div class="radio-group">
                                <label class="radio-label">
                                    <input type="radio" name="is_active" value="1"
                                        {{ old('is_active', $user->is_active ? '1' : '0') === '1' ? 'checked' : '' }}
                                        {{ $isGuest ? 'disabled' : '' }}>
                                    <span>Active</span>
                                </label>
                                <label class="radio-label">
                                    <input type="radio" name="is_active" value="0"
                                        {{ old('is_active', $user->is_active ? '1' : '0') === '0' ? 'checked' : '' }}
                                        {{ $isGuest ? 'disabled' : '' }}>
                                    <span>Inactive</span>
                                </label>
                            </div>
                            @if($isGuest)<input type="hidden" name="is_active" value="{{ $user->is_active ? '1' : '0' }}">@endif
                        </div>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="flex flex-wrap gap-3">
                    <button type="submit"
                            x-data="{ busy: false }"
                            @click="if (busy) { $event.preventDefault(); $event.stopImmediatePropagation(); return; }"
                            @submit.window="if ($event.target === $el.closest('form')) { busy = true; }"
                            :disabled="busy"
                            class="btn-emerald">
                        <span x-show="!busy">Update Member</span>
                        <span x-show="busy" class="flex items-center gap-2">
                            <svg class="spinner" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                            </svg>
                            Updating...
                        </span>
                    </button>
                    <a href="{{ route('members.edit-history', $user->id) }}" class="btn-history">
                        View History
                    </a>
                    <a href="{{ route('members.index') }}" class="btn-cancel">
                        Cancel
                    </a>
                </div>

            </form>
        </div>
    </div>

    <script>
        {{-- SINGLE SOURCE OF TRUTH --}}
        const positionMapping     = @json($positionMapping ?? []);
        const nonStudentPositions = @json($nonStudentPositions ?? []);
        const isGuest             = @json($isGuest);

        function memberEditForm() {
            return {
                selectedRoleId:       {{ old('role_id', $user->role_id) }},
                selectedPosition:     '{{ old('position', $user->position) }}',
                originalPosition:     '{{ $user->position }}',
                yearLevelValue:       '{{ old('year_level', $user->year_level) }}',
                positionOptions:      [],
                changePassword:       false,
                isStudentRole:        false,
                positionManuallyChanged: 0,

                get positionChanged() {
                    return this.selectedPosition.trim() !== this.originalPosition.trim();
                },

                init() {
                    if (isGuest) {
                        this.positionOptions = ['{{ $user->position }}'];
                        this.isStudentRole   = false;
                        return;
                    }

                    this.updatePositionOptions();

                    this.$watch('selectedRoleId', () => {
                        this.updatePositionOptions();
                    });

                    this.$watch('selectedPosition', (newVal) => {
                        this.updateStudentFlag();
                        this.positionManuallyChanged = newVal.trim() !== this.originalPosition.trim() ? 1 : 0;
                    });
                },

                updatePositionOptions() {
                    const id = parseInt(this.selectedRoleId);
                    this.positionOptions = (id && positionMapping[id] !== undefined)
                        ? positionMapping[id]
                        : [];

                    if (this.selectedPosition && !this.positionOptions.includes(this.selectedPosition)) {
                        this.selectedPosition = this.positionOptions[0] ?? '';
                    }

                    this.updateStudentFlag();
                },

                updateStudentFlag() {
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
    @endsection