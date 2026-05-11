@extends('layouts.app')

@section('title', 'My Profile')
@section('page-title', 'My Profile')

@push('styles')
<style>
.profile-page {
    --em:   var(--emerald);
    --em-d: var(--emerald-dark);
    --em-l: var(--emerald-light);
    --gd:   var(--gold);
    --gd-d: var(--gold-dark);
    --gd-l: var(--gold-light);
}

.prof-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 1.25rem;
    overflow: hidden;
    box-shadow: 0 4px 24px rgba(0,0,0,0.05);
    transition: box-shadow 0.25s ease;
}
html.dark .prof-card { box-shadow: 0 4px 24px rgba(0,0,0,0.25); }
.prof-card:hover { box-shadow: 0 8px 32px rgba(0,0,0,0.08), 0 0 0 1px rgba(212,175,55,0.12); }

.prof-card-header {
    position: relative;
    padding: 1rem 1.4rem;
    background: linear-gradient(135deg, #047857 0%, #059669 55%, #065f46 100%);
    overflow: hidden;
}
.prof-card-header::before {
    content: '';
    position: absolute;
    inset: 0;
    background:
        radial-gradient(ellipse at 80% 50%, rgba(212,175,55,0.2) 0%, transparent 60%),
        radial-gradient(ellipse at 10% 50%, rgba(16,185,129,0.15) 0%, transparent 50%);
}
.prof-card-header::after {
    content: '';
    position: absolute;
    bottom: 0; left: 0; right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(212,175,55,0.5), transparent);
}
.prof-card-header-content {
    position: relative;
    z-index: 1;
    display: flex;
    align-items: center;
    gap: 0.6rem;
}
.prof-card-icon {
    width: 2rem; height: 2rem;
    border-radius: 0.5rem;
    background: rgba(212,175,55,0.2);
    border: 1px solid rgba(212,175,55,0.35);
    display: flex; align-items: center; justify-content: center;
    color: var(--gold-light);
    flex-shrink: 0;
}
.prof-card-title {
    font-family: 'DM Mono', monospace;
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: rgba(255,255,255,0.95);
}
.prof-card-sub {
    font-size: 0.7rem;
    color: rgba(255,255,255,0.6);
    margin-top: 0.1rem;
}

.avatar-ring { position: relative; flex-shrink: 0; }
.avatar-ring::before {
    content: '';
    position: absolute;
    inset: -3px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--gold), var(--emerald));
    z-index: 0;
}
.avatar-ring > * { position: relative; z-index: 1; }

.prof-label {
    display: block;
    font-size: 0.65rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: var(--text-3);
    margin-bottom: 0.3rem;
    font-family: 'DM Mono', monospace;
}

.prof-input {
    width: 100%;
    padding: 0.5rem 0.75rem;
    font-size: 0.85rem;
    background: var(--surface-2);
    border: 1.5px solid var(--border);
    border-radius: 0.625rem;
    color: var(--text);
    font-family: 'Outfit', sans-serif;
    transition: all 0.2s ease;
}
.prof-input:focus {
    outline: none;
    border-color: var(--gold);
    background: var(--surface);
    box-shadow: 0 0 0 3px rgba(212,175,55,0.12);
}
.prof-input::placeholder { color: var(--text-3); }
html.dark .prof-input { background: rgba(15,23,42,0.6); }
html.dark .prof-input:focus { background: rgba(15,23,42,0.8); }
.prof-input.error { border-color: #f43f5e !important; }

.prof-select {
    width: 100%;
    padding: 0.5rem 0.75rem;
    font-size: 0.85rem;
    background: var(--surface-2);
    border: 1.5px solid var(--border);
    border-radius: 0.625rem;
    color: var(--text);
    font-family: 'Outfit', sans-serif;
    cursor: pointer;
    transition: all 0.2s ease;
}
.prof-select:focus {
    outline: none;
    border-color: var(--gold);
    box-shadow: 0 0 0 3px rgba(212,175,55,0.12);
}
html.dark .prof-select { background: rgba(15,23,42,0.6); }

.phone-prefix {
    display: inline-flex;
    align-items: center;
    padding: 0 0.625rem;
    background: var(--surface-3);
    border: 1.5px solid var(--border);
    border-right: none;
    border-radius: 0.625rem 0 0 0.625rem;
    font-size: 0.82rem;
    color: var(--text-3);
    font-family: 'DM Mono', monospace;
    white-space: nowrap;
}
.phone-input {
    flex: 1;
    padding: 0.5rem 0.75rem;
    font-size: 0.85rem;
    background: var(--surface-2);
    border: 1.5px solid var(--border);
    border-left: none;
    border-radius: 0 0.625rem 0.625rem 0;
    color: var(--text);
    font-family: 'Outfit', sans-serif;
    transition: all 0.2s ease;
}
.phone-input:focus {
    outline: none;
    border-color: var(--gold);
    background: var(--surface);
    box-shadow: 0 0 0 3px rgba(212,175,55,0.12);
}
html.dark .phone-input { background: rgba(15,23,42,0.6); }

.prof-divider {
    border: none;
    border-top: 1px solid var(--border);
    margin: 1rem 0;
    position: relative;
}
.prof-divider::after {
    content: '';
    position: absolute;
    top: -1px; left: 0; right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(212,175,55,0.3), transparent);
}

.btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.55rem 1.25rem;
    font-size: 0.82rem;
    font-weight: 700;
    background: linear-gradient(135deg, var(--emerald) 0%, var(--emerald-dark) 100%);
    color: #fff;
    border: none;
    border-radius: 0.625rem;
    cursor: pointer;
    transition: all 0.2s ease;
    font-family: 'Outfit', sans-serif;
    box-shadow: 0 2px 10px rgba(5,150,105,0.25);
}
.btn-primary:hover {
    background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
    color: #0f172a;
    box-shadow: 0 4px 16px rgba(212,175,55,0.35);
    transform: translateY(-1px);
}
.btn-primary:active { transform: translateY(0); }

.btn-secondary {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.55rem 1.25rem;
    font-size: 0.82rem;
    font-weight: 600;
    background: var(--surface-3);
    color: var(--text-2);
    border: 1.5px solid var(--border);
    border-radius: 0.625rem;
    cursor: pointer;
    transition: all 0.18s ease;
    font-family: 'Outfit', sans-serif;
}
.btn-secondary:hover {
    border-color: rgba(212,175,55,0.4);
    color: var(--gold-dark);
    background: rgba(212,175,55,0.06);
}
html.dark .btn-secondary:hover { color: var(--gold-light); }

.pass-toggle {
    position: absolute;
    right: 0.625rem;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    cursor: pointer;
    color: var(--text-3);
    padding: 0.2rem;
    border-radius: 0.35rem;
    transition: color 0.15s ease;
    display: flex;
}
.pass-toggle:hover { color: var(--gold-dark); }
html.dark .pass-toggle:hover { color: var(--gold-light); }

.activity-stat {
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
    padding: 1.1rem 1.25rem;
    position: relative;
}
.activity-stat::after {
    content: '';
    position: absolute;
    bottom: 0; left: 1.25rem; right: 1.25rem;
    height: 2px;
    border-radius: 999px;
    background: linear-gradient(90deg, var(--emerald-light), var(--gold));
    transform: scaleX(0);
    transform-origin: left;
    transition: transform 0.3s ease;
}
.activity-stat:hover::after { transform: scaleX(1); }
.activity-stat-label {
    font-size: 0.65rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: var(--text-3);
    font-family: 'DM Mono', monospace;
}
.activity-stat-num {
    font-family: 'DM Mono', monospace;
    font-size: 2.25rem;
    font-weight: 700;
    color: var(--text);
    line-height: 1;
    letter-spacing: -0.04em;
}
.activity-stat-num.green { color: var(--emerald); }
.activity-stat-num.gold  { color: var(--gold-dark); }
html.dark .activity-stat-num.gold { color: var(--gold-light); }

.file-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.35rem 0.75rem;
    font-size: 0.75rem;
    font-weight: 600;
    background: rgba(5,150,105,0.08);
    color: var(--emerald-dark);
    border: 1px solid rgba(5,150,105,0.25);
    border-radius: 0.5rem;
    cursor: pointer;
    transition: all 0.18s ease;
    font-family: 'Outfit', sans-serif;
}
.file-btn:hover {
    background: rgba(212,175,55,0.1);
    color: var(--gold-dark);
    border-color: rgba(212,175,55,0.35);
}
html.dark .file-btn { color: var(--emerald-light); }
html.dark .file-btn:hover { color: var(--gold-light); }

.field-error {
    font-size: 0.7rem;
    color: #f43f5e;
    margin-top: 0.25rem;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

@keyframes fadeUp {
    from { opacity: 0; transform: translateY(14px); }
    to   { opacity: 1; transform: translateY(0); }
}
.anim-1 { animation: fadeUp 0.38s ease 0.04s both; }
.anim-2 { animation: fadeUp 0.38s ease 0.10s both; }
.anim-3 { animation: fadeUp 0.38s ease 0.16s both; }
</style>
@endpush

@section('content')
<div class="profile-page max-w-5xl mx-auto space-y-5">

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 items-start">

        {{-- ── LEFT: Profile Information ── --}}
        <div class="prof-card anim-1">
            <div class="prof-card-header">
                <div class="prof-card-header-content">
                    <div class="prof-card-icon">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="prof-card-title">Profile Information</div>
                        <div class="prof-card-sub">Update your personal details</div>
                    </div>
                </div>
            </div>

            <div class="p-5">
                {{-- ✅ Show validation errors summary if any --}}
                @if($errors->any() && !$errors->has('current_password') && !$errors->has('new_password'))
                    <div class="mb-4 p-3 rounded-xl text-sm"
                         style="background: rgba(244,63,94,0.08); border: 1px solid rgba(244,63,94,0.25); color: #f43f5e;">
                        <div class="font-semibold mb-1">Please fix the following errors:</div>
                        <ul class="list-disc list-inside space-y-0.5">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    {{-- Avatar --}}
                    <div class="flex items-center gap-4 mb-4 pb-4" style="border-bottom:1px solid var(--border);">
                        <div class="avatar-ring">
                            @if(auth()->user()->avatar)
                                <img id="avatarPreview"
                                     src="{{ Str::startsWith($user->avatar, 'http')
                                         ? $user->avatar
                                         : asset('storage/' . $user->avatar) }}"
                                     alt="Avatar"
                                     class="w-14 h-14 rounded-full object-cover">
                            @else
                                <div id="avatarInitials"
                                     class="w-14 h-14 rounded-full flex items-center justify-center text-white text-base font-bold"
                                     style="background: linear-gradient(135deg, var(--emerald), var(--emerald-dark)); font-family:'DM Mono',monospace;">
                                    {{ strtoupper(substr(auth()->user()->full_name, 0, 2)) }}
                                </div>
                                <img id="avatarPreview" src="" alt="Avatar"
                                     class="w-14 h-14 rounded-full object-cover hidden">
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold mb-2" style="color:var(--text-2);">Profile Picture</p>
                            <label class="file-btn cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                                Choose Image
                                <input type="file" name="avatar" id="avatarInput"
                                       accept="image/jpeg,image/png,image/gif,image/webp"
                                       class="hidden">
                            </label>
                            {{-- ✅ Show selected file name --}}
                            <p id="avatarFileName" class="text-xs mt-1" style="color:var(--emerald); display:none;"></p>
                            <p class="text-xs mt-1" style="color:var(--text-3);">JPG, PNG, GIF, WEBP — Max 2MB</p>
                            {{-- ✅ Avatar validation error --}}
                            @error('avatar')
                                <p class="field-error">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    {{-- First / Middle / Last --}}
                    <div class="grid grid-cols-3 gap-2 mb-3">
                        <div>
                            <label class="prof-label">First <span style="color:#f43f5e;">*</span></label>
                            <input type="text" name="first_name"
                                   value="{{ old('first_name', $user->first_name) }}"
                                   class="prof-input {{ $errors->has('first_name') ? 'error' : '' }}">
                            @error('first_name')
                                <p class="field-error">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="prof-label">Middle</label>
                            <input type="text" name="middle_name"
                                   value="{{ old('middle_name', $user->middle_name) }}"
                                   class="prof-input {{ $errors->has('middle_name') ? 'error' : '' }}">
                            @error('middle_name')
                                <p class="field-error">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="prof-label">Last <span style="color:#f43f5e;">*</span></label>
                            <input type="text" name="last_name"
                                   value="{{ old('last_name', $user->last_name) }}"
                                   class="prof-input {{ $errors->has('last_name') ? 'error' : '' }}">
                            @error('last_name')
                                <p class="field-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Email --}}
                    <div class="mb-3">
                        <label class="prof-label">Email <span style="color:#f43f5e;">*</span></label>
                        <input type="email" name="email"
                               value="{{ old('email', $user->email) }}"
                               required
                               class="prof-input {{ $errors->has('email') ? 'error' : '' }}">
                        @error('email')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Student ID + Year Level --}}
                    <div class="grid grid-cols-2 gap-2 mb-3">
                        <div>
                            <label class="prof-label">Student ID</label>
                            <input type="text" name="student_id"
                                   value="{{ old('student_id', $user->student_id) }}"
                                   placeholder="2020-12345"
                                   class="prof-input {{ $errors->has('student_id') ? 'error' : '' }}">
                            @error('student_id')
                                <p class="field-error">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="prof-label">Year Level</label>
                            <select name="year_level" class="prof-select">
                                <option value="">Select Grade</option>
                                @for($i = 7; $i <= 12; $i++)
                                    <option value="Grade {{ $i }}"
                                        {{ old('year_level', $user->year_level) == "Grade $i" ? 'selected' : '' }}>
                                        Grade {{ $i }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    {{-- Gender + Birthday --}}
                    <div class="grid grid-cols-2 gap-2 mb-3">
                        <div>
                            <label class="prof-label">Gender</label>
                            <select name="gender"
                                    class="prof-select {{ $errors->has('gender') ? 'error' : '' }}">
                                <option value="">Select Gender</option>
                                <option value="Male"   {{ old('gender', $user->gender) == 'Male'   ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('gender', $user->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                                <option value="Other"  {{ old('gender', $user->gender) == 'Other'  ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('gender')
                                <p class="field-error">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="prof-label">Birthday</label>
                            <input type="date" name="birthday"
                                   value="{{ old('birthday', optional($user->birthday)->format('Y-m-d')) }}"
                                   class="prof-input {{ $errors->has('birthday') ? 'error' : '' }}">
                            @error('birthday')
                                <p class="field-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Phone --}}
                    <div class="mb-4">
                        <label class="prof-label">Phone Number</label>
                        <div class="flex">
                            <span class="phone-prefix">+63</span>
                            <input type="tel" name="phone"
                                   value="{{ old('phone', $user->phone ? substr($user->phone, 3) : '') }}"
                                   maxlength="10"
                                   placeholder="9123456789"
                                   class="phone-input {{ $errors->has('phone') ? 'error' : '' }}">
                        </div>
                        @error('phone')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                        <p class="text-xs mt-1" style="color:var(--text-3);">Enter 10-digit number. +63 added automatically.</p>
                    </div>

                    <hr class="prof-divider">

                    <div class="flex gap-2">
                        <button type="submit" class="btn-primary">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                            Save Changes
                        </button>
                        <button type="reset" class="btn-secondary" onclick="resetAvatarPreview()">Reset</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ── RIGHT column ── --}}
        <div class="space-y-5">

            {{-- Change Password --}}
            <div class="prof-card anim-2">
                <div class="prof-card-header">
                    <div class="prof-card-header-content">
                        <div class="prof-card-icon">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                      d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="prof-card-title">Change Password</div>
                            <div class="prof-card-sub">Keep your account secure</div>
                        </div>
                    </div>
                </div>

                <div class="p-5">
                    {{-- ✅ Password errors summary --}}
                    @if($errors->has('current_password') || $errors->has('new_password') || $errors->has('new_password_confirmation'))
                        <div class="mb-4 p-3 rounded-xl text-sm"
                             style="background: rgba(244,63,94,0.08); border: 1px solid rgba(244,63,94,0.25); color: #f43f5e;">
                            <div class="font-semibold mb-1">Password update failed:</div>
                            <ul class="list-disc list-inside space-y-0.5">
                                @foreach($errors->only(['current_password','new_password','new_password_confirmation']) as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- ✅ Same password error --}}
                    @if(session('password_error') === 'same_password')
                        <div class="mb-4 p-3 rounded-xl text-sm"
                             style="background: rgba(244,63,94,0.08); border: 1px solid rgba(244,63,94,0.25); color: #f43f5e;">
                            New password cannot be the same as your current password.
                        </div>
                    @endif

                    <form method="POST" action="{{ route('profile.password') }}"
                          x-data="{ showCurrent: false, showNew: false, showConfirm: false }">
                        @csrf
                        @method('PUT')

                        @php
                            $eyeOpen = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
                            $eyeOff  = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>';
                        @endphp

                        {{-- Current --}}
                        <div class="mb-3">
                            <label class="prof-label">Current Password</label>
                            <div class="relative">
                                <input :type="showCurrent ? 'text' : 'password'"
                                       name="current_password" required
                                       class="prof-input {{ $errors->has('current_password') ? 'error' : '' }}"
                                       style="padding-right:2.25rem;">
                                <button type="button" @click="showCurrent = !showCurrent" class="pass-toggle">
                                    <svg x-show="!showCurrent" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $eyeOpen !!}</svg>
                                    <svg x-show="showCurrent"  class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $eyeOff !!}</svg>
                                </button>
                            </div>
                            @error('current_password')
                                <p class="field-error">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- New --}}
                        <div class="mb-3">
                            <label class="prof-label">New Password</label>
                            <div class="relative">
                                <input :type="showNew ? 'text' : 'password'"
                                       name="new_password" required
                                       class="prof-input {{ $errors->has('new_password') ? 'error' : '' }}"
                                       style="padding-right:2.25rem;">
                                <button type="button" @click="showNew = !showNew" class="pass-toggle">
                                    <svg x-show="!showNew" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $eyeOpen !!}</svg>
                                    <svg x-show="showNew"  class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $eyeOff !!}</svg>
                                </button>
                            </div>
                            @error('new_password')
                                <p class="field-error">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Confirm --}}
                        <div class="mb-4">
                            <label class="prof-label">Confirm Password</label>
                            <div class="relative">
                                <input :type="showConfirm ? 'text' : 'password'"
                                       name="new_password_confirmation" required
                                       class="prof-input {{ $errors->has('new_password_confirmation') ? 'error' : '' }}"
                                       style="padding-right:2.25rem;">
                                <button type="button" @click="showConfirm = !showConfirm" class="pass-toggle">
                                    <svg x-show="!showConfirm" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $eyeOpen !!}</svg>
                                    <svg x-show="showConfirm"  class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $eyeOff !!}</svg>
                                </button>
                            </div>
                            @error('new_password_confirmation')
                                <p class="field-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <hr class="prof-divider">
                        <button type="submit" class="btn-primary">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            Update Password
                        </button>
                    </form>
                </div>
            </div>

            {{-- Activity Summary --}}
            <div class="prof-card anim-3">
                <div class="prof-card-header">
                    <div class="prof-card-header-content">
                        <div class="prof-card-icon">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                      d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="prof-card-title">Activity Summary</div>
                            <div class="prof-card-sub">Your contribution overview</div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 divide-x" style="border-color:var(--border);">
                    <div class="activity-stat">
                        <span class="activity-stat-label">Documents Uploaded</span>
                        <span class="activity-stat-num green">{{ $documentsCount }}</span>
                        <span class="text-xs" style="color:var(--text-3); font-family:'DM Mono',monospace;">files</span>
                    </div>
                    <div class="activity-stat">
                        <span class="activity-stat-label">Financial Transactions</span>
                        <span class="activity-stat-num gold">{{ $transactionsCount ?? 0 }}</span>
                        <span class="text-xs" style="color:var(--text-3); font-family:'DM Mono',monospace;">records</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// ── Avatar preview ──────────────────────────────────────────────
document.getElementById('avatarInput').addEventListener('change', function (e) {
    const file = e.target.files[0];
    if (!file) return;

    // ✅ Client-side file size validation
    if (file.size > 2 * 1024 * 1024) {
        window.showNotification('File is too large. Maximum size is 2MB.', 'error');
        this.value = '';
        return;
    }

    // ✅ Show selected file name
    const fileNameEl = document.getElementById('avatarFileName');
    if (fileNameEl) {
        fileNameEl.textContent = '✓ ' + file.name;
        fileNameEl.style.display = 'block';
    }

    const reader = new FileReader();
    reader.onload = function (event) {
        const preview  = document.getElementById('avatarPreview');
        const initials = document.getElementById('avatarInitials');
        if (preview) {
            preview.src = event.target.result;
            preview.classList.remove('hidden');
        }
        if (initials) initials.classList.add('hidden');
    };
    reader.readAsDataURL(file);
});

// ── Reset avatar preview on form reset ─────────────────────────
function resetAvatarPreview() {
    const preview  = document.getElementById('avatarPreview');
    const initials = document.getElementById('avatarInitials');
    const fileNameEl = document.getElementById('avatarFileName');

    if (fileNameEl) { fileNameEl.style.display = 'none'; fileNameEl.textContent = ''; }

    // ✅ Restore original avatar or initials
    @if(auth()->user()->avatar)
        if (preview) {
            preview.src = "{{ Str::startsWith($user->avatar, 'http') ? $user->avatar : asset('storage/' . $user->avatar) }}";
            preview.classList.remove('hidden');
        }
    @else
        if (preview) { preview.src = ''; preview.classList.add('hidden'); }
        if (initials) initials.classList.remove('hidden');
    @endif
}
</script>
@endpush