@extends('layouts.app')

@section('title', 'Edit Member')
@section('page-title', 'Edit Member')

@php
    $guestEmail = 'guest@gmail.com';
    $isGuest    = $user->email === $guestEmail;
@endphp

@section('content')
<div x-data="memberEditForm()" x-init="init()" class="max-w-3xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gold-200 dark:border-gold-800 p-6">
        <form method="POST" action="{{ route('members.update', $user->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            @if($errors->any())
                <div class="mb-4 p-3 bg-red-100 dark:bg-red-900/30 border border-red-300 dark:border-red-700 text-red-700 dark:text-red-300 rounded-lg">
                    <strong class="block mb-1">Please fix the following errors:</strong>
                    <ul class="mt-1 list-disc list-inside text-sm space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- ── Avatar ───────────────────────────────────────────────────── --}}
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Profile Picture</label>
                <div class="flex items-center gap-4">
                    @if($user->avatar)
                        {{--
                            Cloudinary URLs are absolute (https://res.cloudinary.com/…).
                            Local storage paths are relative — prefix with asset('storage/').
                            Never use Storage::url() alone; it cannot handle Cloudinary URLs.
                        --}}
                        <img src="{{ str_starts_with($user->avatar, 'http') ? $user->avatar : asset('storage/' . $user->avatar) }}"
                             alt="{{ $user->full_name }}"
                             class="w-16 h-16 rounded-full object-cover border-2 border-gold-200 dark:border-gold-700">
                    @else
                        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-gold-400 to-gold-600 flex items-center justify-center text-white text-xl font-bold shadow-md">
                            {{ strtoupper(mb_substr($user->full_name, 0, 2)) }}
                        </div>
                    @endif
                    <div class="flex-1">
                        <input type="file" name="avatar" accept="image/jpeg,image/png,image/gif,image/webp"
                               class="text-sm text-gray-600 dark:text-gray-300 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-gold-100 file:text-gold-700 hover:file:bg-gold-200 dark:file:bg-gold-900/30 dark:file:text-gold-300">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">JPG, PNG, GIF, WEBP — Max 2MB</p>
                    </div>
                </div>
            </div>

            {{-- ── Name ─────────────────────────────────────────────────────── --}}
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        First Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}" required
                           {{ $isGuest ? 'disabled' : '' }}
                           class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                    @if($isGuest)<input type="hidden" name="first_name" value="{{ $user->first_name }}">@endif
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Last Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}" required
                           {{ $isGuest ? 'disabled' : '' }}
                           class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                    @if($isGuest)<input type="hidden" name="last_name" value="{{ $user->last_name }}">@endif
                </div>
            </div>

            {{-- Middle Name --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Middle Name <span class="text-gray-400">(Optional)</span>
                </label>
                <input type="text" name="middle_name" value="{{ old('middle_name', $user->middle_name) }}"
                       {{ $isGuest ? 'disabled' : '' }}
                       class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                @if($isGuest)<input type="hidden" name="middle_name" value="{{ $user->middle_name }}">@endif
            </div>

            {{-- Gender --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Gender <span class="text-red-500">*</span>
                </label>
                <select name="gender" required {{ $isGuest ? 'disabled' : '' }}
                        class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                    <option value="">Select Gender</option>
                    @foreach(['Male', 'Female', 'Other'] as $g)
                        <option value="{{ $g }}" {{ old('gender', $user->gender) === $g ? 'selected' : '' }}>{{ $g }}</option>
                    @endforeach
                </select>
                @if($isGuest)<input type="hidden" name="gender" value="{{ $user->gender }}">@endif
            </div>

            {{-- Birthday --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Birthday</label>
                <input type="date" name="birthday"
                       value="{{ old('birthday', optional($user->birthday)->format('Y-m-d')) }}"
                       {{ $isGuest ? 'disabled' : '' }}
                       class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                @if($isGuest)<input type="hidden" name="birthday" value="{{ optional($user->birthday)->format('Y-m-d') }}">@endif
            </div>

            {{-- Phone --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Phone Number</label>
                <div class="flex">
                    <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gold-300 dark:border-gold-600 bg-gray-50 dark:bg-gray-700 text-gray-500 text-sm">+63</span>
                    {{--
                        Strip the stored +63 prefix for display; normalizePhone() re-adds it on save.
                        Guard against null with the null-coalescing operator.
                    --}}
                    <input type="tel" name="phone"
                           value="{{ old('phone', $user->phone ? ltrim(substr($user->phone, 3)) : '') }}"
                           maxlength="10" placeholder="9123456789"
                           {{ $isGuest ? 'disabled' : '' }}
                           oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,10)"
                           class="flex-1 border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-r-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">10-digit number — +63 is added automatically.</p>
                @if($isGuest)
                    {{-- Re-submit the raw digits so normalizePhone() runs correctly --}}
                    <input type="hidden" name="phone" value="{{ $user->phone ? substr($user->phone, 3) : '' }}">
                @endif
            </div>

            {{-- Email --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Email <span class="text-red-500">*</span>
                </label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                       {{ $isGuest ? 'disabled' : '' }}
                       class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                @if($isGuest)<input type="hidden" name="email" value="{{ $user->email }}">@endif
            </div>

            {{-- ── Role ─────────────────────────────────────────────────────── --}}
            @if($isGuest)
                <input type="hidden" name="role_id" value="{{ $user->role_id }}">
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Role</label>
                    <p class="text-gray-800 dark:text-white bg-gray-100 dark:bg-gray-700 px-4 py-2 rounded-lg text-sm">
                        {{ $user->role->name }} <span class="text-gray-400">(Fixed)</span>
                    </p>
                </div>
            @else
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Role <span class="text-red-500">*</span>
                    </label>
                    <select name="role_id" x-model="selectedRoleId" required
                            class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            {{-- ── Position ─────────────────────────────────────────────────── --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Position</label>

                @if($isGuest)
                    <p class="text-gray-800 dark:text-white bg-gray-100 dark:bg-gray-700 px-4 py-2 rounded-lg text-sm">
                        {{ $user->position ?? '—' }} <span class="text-gray-400">(Fixed)</span>
                    </p>
                    <input type="hidden" name="position" value="{{ $user->position }}">
                @else
                    <select name="position" x-model="selectedPosition"
                            class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                        <option value="">Select Position</option>
                        <template x-for="pos in positionOptions" :key="pos">
                            <option :value="pos" x-text="pos" :selected="pos === selectedPosition"></option>
                        </template>
                    </select>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Positions are based on the selected role.</p>
                @endif
            </div>

            {{-- Track whether position was changed manually (used by controller for reason gate) --}}
            @if(!$isGuest)
            <input type="hidden" name="position_manually_changed" x-model="positionManuallyChanged">

            {{-- Reason for position change — shown only when position differs from original --}}
            <div x-show="positionChanged" x-cloak class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Reason for Change <span class="text-red-500">*</span>
                </label>
                <textarea name="position_change_reason" rows="2"
                          :required="positionChanged"
                          class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500"
                          placeholder="Please provide a reason for changing the position…"></textarea>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Required when changing position.</p>
            </div>
            @endif

            {{-- ── Student fields ───────────────────────────────────────────── --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Student ID</label>
                <input type="text" name="student_id" value="{{ old('student_id', $user->student_id) }}"
                       placeholder="2020-12345"
                       {{ $isGuest ? 'disabled' : '' }}
                       class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Format: YYYY-XXXXX (e.g., 2020-12345)</p>
                @if($isGuest)<input type="hidden" name="student_id" value="{{ $user->student_id }}">@endif
            </div>

            <div x-show="isStudentRole" x-cloak class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Year Level</label>
                <select x-model="yearLevelValue"
                        class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                    <option value="">Select Grade</option>
                    @for($i = 7; $i <= 12; $i++)
                        <option value="Grade {{ $i }}" {{ old('year_level', $user->year_level) === 'Grade '.$i ? 'selected' : '' }}>
                            Grade {{ $i }}
                        </option>
                    @endfor
                </select>
            </div>
            <div x-show="!isStudentRole" class="text-sm text-gray-500 dark:text-gray-400 italic mb-4">
                Year level is not applicable for this role.
            </div>
            {{-- Always submit — controller clears it server-side for non-student roles --}}
            <input type="hidden" name="year_level" :value="yearLevelValue">
            @if($isGuest)<input type="hidden" name="year_level" value="{{ $user->year_level }}">@endif

            {{-- ── Change Password ──────────────────────────────────────────── --}}
            <div class="mb-4" x-data="{ showNew: false, showConfirm: false }">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" x-model="changePassword"
                           class="rounded border-gold-300 text-gold-600 focus:ring-gold-500"
                           {{ $isGuest ? 'disabled' : '' }}>
                    <span class="text-sm text-gray-700 dark:text-gray-300">Change Password</span>
                </label>

                <div x-show="changePassword" x-cloak class="mt-3 space-y-3">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">New Password</label>
                        <div class="relative">
                            <input :type="showNew ? 'text' : 'password'" name="password" autocomplete="new-password"
                                   class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                            <button type="button" @click="showNew = !showNew"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700 dark:hover:text-gray-200">
                                <svg x-show="!showNew" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg x-show="showNew" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                        @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Confirm Password</label>
                        <div class="relative">
                            <input :type="showConfirm ? 'text' : 'password'" name="password_confirmation" autocomplete="new-password"
                                   class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                            <button type="button" @click="showConfirm = !showConfirm"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700 dark:hover:text-gray-200">
                                <svg x-show="!showConfirm" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg x-show="showConfirm" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Leave blank to keep the current password.</p>
            </div>

            {{-- ── Account Status ───────────────────────────────────────────── --}}
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Account Status</label>
                <div class="flex gap-4">
                    <label class="inline-flex items-center gap-2">
                        <input type="radio" name="is_active" value="1"
                               {{ old('is_active', $user->is_active ? '1' : '0') === '1' ? 'checked' : '' }}
                               {{ $isGuest ? 'disabled' : '' }}
                               class="text-gold-600 focus:ring-gold-500">
                        <span class="text-sm">Active</span>
                    </label>
                    <label class="inline-flex items-center gap-2">
                        <input type="radio" name="is_active" value="0"
                               {{ old('is_active', $user->is_active ? '1' : '0') === '0' ? 'checked' : '' }}
                               {{ $isGuest ? 'disabled' : '' }}
                               class="text-gold-600 focus:ring-gold-500">
                        <span class="text-sm">Inactive</span>
                    </label>
                </div>
                @if($isGuest)<input type="hidden" name="is_active" value="{{ $user->is_active ? '1' : '0' }}">@endif
            </div>

            {{-- ── Buttons ──────────────────────────────────────────────────── --}}
            <div class="flex gap-3">
                <button type="submit"
                        class="flex-1 bg-primary-600 hover:bg-gold-500 text-white font-semibold py-2 px-4 rounded-lg transition shadow-sm hover:shadow-md">
                    Update Member
                </button>
                <a href="{{ route('members.edit-history', $user->id) }}"
                   class="flex-1 text-center bg-gray-100 hover:bg-gold-500 hover:text-white dark:bg-gray-700 dark:hover:bg-gold-600 text-gray-700 dark:text-gray-300 font-semibold py-2 px-4 rounded-lg transition">
                    View History
                </a>
                <a href="{{ route('members.index') }}"
                   class="flex-1 text-center bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<style>[x-cloak] { display: none !important; }</style>

<script>
    {{--
        SINGLE SOURCE OF TRUTH — both arrays come from the controller, which derives
        them from Member::VALID_POSITIONS and Member::NON_STUDENT_POSITIONS.
        Zero hardcoded role IDs or position strings in this file.
    --}}
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

            // Computed — true when the selected position differs from the saved one
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

                // Auto-select first option when role changes and current selection is invalid
                if (this.selectedPosition && !this.positionOptions.includes(this.selectedPosition)) {
                    this.selectedPosition = this.positionOptions[0] ?? '';
                }

                this.updateStudentFlag();
            },

            updateStudentFlag() {
                const pos = this.selectedPosition;

                if (!pos) {
                    // If no position yet, show student fields if any option is a student position
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