@extends('layouts.app')

@section('title', 'Edit User — VSULHS_SSLG')
@section('page-title', 'Edit User')

@php
    $validPositions  = \App\Models\Member::VALID_POSITIONS;
    $nonStudentRoleIds = [1, 6, 8];
@endphp

@section('content')

{{-- Header --}}
<div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-primary-600 to-primary-700 dark:from-primary-800 dark:to-primary-900 p-6 mb-6">
    <div class="relative z-10">
        <h1 class="text-2xl font-bold text-white">Edit User</h1>
        <p class="text-primary-100 text-sm mt-1">Update details for {{ $user->full_name }}</p>
    </div>
    <div class="absolute top-0 right-0 -mt-8 -mr-8 w-64 h-64 bg-white/5 rounded-full blur-3xl"></div>
</div>

{{-- Global validation errors --}}
@if ($errors->any())
    <div class="max-w-3xl mx-auto mb-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl text-sm text-red-700 dark:text-red-400">
        <p class="font-semibold mb-1">Please fix the following errors:</p>
        <ul class="list-disc list-inside space-y-0.5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div x-data="userEditForm()" x-init="init()" class="max-w-3xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-6">
        <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
            @csrf
            @method('PUT')

            {{-- First Name & Last Name --}}
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="first_name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        First Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="first_name" name="first_name"
                           value="{{ old('first_name', $user->first_name) }}" required
                           class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm
                                  @error('first_name') border-red-500 @enderror">
                    @error('first_name')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="last_name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Last Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="last_name" name="last_name"
                           value="{{ old('last_name', $user->last_name) }}" required
                           class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm
                                  @error('last_name') border-red-500 @enderror">
                    @error('last_name')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Middle Name --}}
            <div class="mb-4">
                <label for="middle_name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Middle Name <span class="text-gray-400">(Optional)</span>
                </label>
                <input type="text" id="middle_name" name="middle_name"
                       value="{{ old('middle_name', $user->middle_name) }}"
                       class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm">
            </div>

            {{-- Gender --}}
            <div class="mb-4">
                <label for="gender" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Gender <span class="text-red-500">*</span>
                </label>
                <select id="gender" name="gender" required
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm
                               @error('gender') border-red-500 @enderror">
                    <option value="">Select Gender</option>
                    <option value="Male"   {{ old('gender', $user->gender) == 'Male'   ? 'selected' : '' }}>Male</option>
                    <option value="Female" {{ old('gender', $user->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                    <option value="Other"  {{ old('gender', $user->gender) == 'Other'  ? 'selected' : '' }}>Other</option>
                </select>
                @error('gender')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Birthday --}}
            <div class="mb-4">
                <label for="birthday" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Birthday</label>
                <input type="date" id="birthday" name="birthday"
                       value="{{ old('birthday', optional($user->birthday)->format('Y-m-d')) }}"
                       class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm">
                @error('birthday')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Phone Number --}}
            <div class="mb-4">
                <label for="phone" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Phone Number</label>
                <div class="flex">
                    <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-500">+63</span>
                    <input type="tel" id="phone" name="phone"
                           value="{{ old('phone', $user->phone ? substr($user->phone, 3) : '') }}"
                           maxlength="10" placeholder="9123456789"
                           class="flex-1 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-r-lg px-4 py-2 text-sm
                                  @error('phone') border-red-500 @enderror"
                           oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,10)">
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Enter 10-digit number (e.g., 9123456789). +63 will be added automatically.</p>
                @error('phone')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div class="mb-4">
                <label for="email" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Email <span class="text-red-500">*</span>
                </label>
                <input type="email" id="email" name="email"
                       value="{{ old('email', $user->email) }}" required
                       class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm
                              @error('email') border-red-500 @enderror">
                @error('email')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Role --}}
            <div class="mb-4">
                <label for="role_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Role <span class="text-red-500">*</span>
                </label>
                <select id="role_id" name="role_id" x-model="selectedRoleId" required
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm
                               @error('role_id') border-red-500 @enderror">
                    <option value="">Select a role</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
                @error('role_id')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Position --}}
            <div class="mb-4">
                <label for="position" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Position</label>
                <select id="position" name="position" x-model="selectedPosition"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm">
                    <option value="">Select Position</option>
                    <template x-for="pos in positionOptions" :key="pos">
                        <option :value="pos" x-text="pos"></option>
                    </template>
                </select>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Positions are based on the selected role</p>
                @error('position')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Student ID --}}
            <div class="mb-4">
                <label for="student_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Student ID</label>
                <input type="text" id="student_id" name="student_id"
                       value="{{ old('student_id', $user->student_id) }}" placeholder="2020-12345"
                       class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm
                              @error('student_id') border-red-500 @enderror">
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Format: YYYY-XXXXX (e.g., 2020-12345)</p>
                @error('student_id')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Year Level (conditional on role) --}}
            <div class="mb-4">
                <div x-show="isStudentRole" x-cloak>
                    <label for="year_level_select" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Year Level</label>
                    <select id="year_level_select" x-model="yearLevelValue"
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm">
                        <option value="">Select Year Level</option>
                        @foreach(['Grade 7','Grade 8','Grade 9','Grade 10','Grade 11','Grade 12'] as $level)
                            <option value="{{ $level }}" {{ old('year_level', $user->year_level) == $level ? 'selected' : '' }}>
                                {{ $level }}
                            </option>
                        @endforeach
                    </select>
                    @error('year_level')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <input type="hidden" name="year_level" :value="yearLevelValue">
                <div x-show="!isStudentRole" x-cloak class="text-sm text-gray-500 dark:text-gray-400 italic">
                    Year level is not applicable for this role.
                </div>
            </div>

            {{-- Change Password --}}
            <div class="mb-4">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" x-model="changePassword" class="rounded border-gray-300 text-primary-600"
                           @change="if (!changePassword) { $refs.password.value = ''; $refs.passwordConfirm.value = ''; }">
                    <span class="text-sm text-gray-700 dark:text-gray-300">Change Password</span>
                </label>
                <div x-show="changePassword" x-cloak class="mt-2 space-y-3">
                    {{-- New Password --}}
                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">New Password</label>
                        <div class="relative">
                            <input :type="showPassword ? 'text' : 'password'"
                                   id="password" name="password"
                                   x-ref="password"
                                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm pr-10
                                          @error('password') border-red-500 @enderror">
                            <button type="button" @click="showPassword = !showPassword"
                                    :aria-label="showPassword ? 'Hide password' : 'Show password'"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                                <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z" />
                                </svg>
                                <svg x-show="showPassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Confirm Password --}}
                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Confirm Password</label>
                        <div class="relative">
                            <input :type="showPasswordConfirm ? 'text' : 'password'"
                                   id="password_confirmation" name="password_confirmation"
                                   x-ref="passwordConfirm"
                                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm pr-10">
                            <button type="button" @click="showPasswordConfirm = !showPasswordConfirm"
                                    :aria-label="showPasswordConfirm ? 'Hide password' : 'Show password'"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                                <svg x-show="!showPasswordConfirm" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z" />
                                </svg>
                                <svg x-show="showPasswordConfirm" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Leave blank to keep current password.</p>
            </div>

            {{-- Account Status --}}
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Account Status</label>
                <div class="flex gap-4">
                    <label class="flex items-center gap-1.5 cursor-pointer">
                        <input type="radio" name="is_active" value="1" {{ old('is_active', $user->is_active) == 1 ? 'checked' : '' }}> Active
                    </label>
                    <label class="flex items-center gap-1.5 cursor-pointer">
                        <input type="radio" name="is_active" value="0" {{ old('is_active', $user->is_active) == 0 ? 'checked' : '' }}> Inactive
                    </label>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-primary-600 hover:bg-gold-500 text-white font-semibold py-2 px-4 rounded-lg transition">
                    Update User
                </button>
                <a href="{{ route('admin.users.index') }}"
                   class="flex-1 text-center bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-semibold py-2 px-4 rounded-lg transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    const validPositions   = @json(\App\Models\Member::VALID_POSITIONS);
    const nonStudentRoleIds = @json($nonStudentRoleIds);

    function userEditForm() {
        return {
            selectedRoleId:    '{{ old('role_id', $user->role_id) }}',
            selectedPosition:  '{{ old('position', $user->position) }}',
            positionOptions:   [],
            changePassword:    false,
            isStudentRole:     true,
            yearLevelValue:    '{{ old('year_level', $user->year_level) }}',
            showPassword:      false,
            showPasswordConfirm: false,

            init() {
                this.updatePositionOptions();
                this.checkIfStudentRole();
                this.$watch('selectedRoleId', () => {
                    this.updatePositionOptions();
                    this.checkIfStudentRole();
                });
            },
            updatePositionOptions() {
                const roleId = this.selectedRoleId;
                if (roleId && validPositions[roleId]) {
                    this.positionOptions = validPositions[roleId];
                } else {
                    this.positionOptions = [];
                }
                if (this.selectedPosition && !this.positionOptions.includes(this.selectedPosition)) {
                    this.selectedPosition = '';
                }
            },
            checkIfStudentRole() {
                const roleId = parseInt(this.selectedRoleId);
                this.isStudentRole = !nonStudentRoleIds.includes(roleId);
                if (!this.isStudentRole) {
                    this.yearLevelValue = '';
                }
            }
        };
    }
</script>
@endsection