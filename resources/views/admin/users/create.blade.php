@extends('layouts.app')

@section('title', 'Create User — VSULHS_SSLG')
@section('page-title', 'Create New User')

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
    // Derived from Member::VALID_POSITIONS — roles whose every position is
    // non-student: 1 (System Administrator), 2 (Club Adviser), 5 (Guest)
@endphp

@section('content')
{{-- Emerald Gradient Header --}}
<div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-primary-600 to-primary-700 dark:from-primary-800 dark:to-primary-900 p-6 mb-6">
    <div class="relative z-10">
        <h1 class="text-2xl font-bold text-white">Create New User</h1>
        <p class="text-primary-100 text-sm mt-1">Add a new user to the system</p>
    </div>
    <div class="absolute top-0 right-0 -mt-8 -mr-8 w-64 h-64 bg-white/5 rounded-full blur-3xl"></div>
</div>

<div x-data="userCreateForm()" x-init="init()" class="max-w-3xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-6">
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf

            {{-- Validation errors --}}
            @if($errors->any())
                <div class="mb-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                    <ul class="text-sm text-red-600 dark:text-red-400 space-y-1 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- First Name & Last Name -->
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        First Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}" required
                           class="w-full border dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm {{ $errors->has('first_name') ? 'border-red-500' : 'border-gray-300 dark:border-gray-600' }}">
                    @error('first_name')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Last Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}" required
                           class="w-full border dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm {{ $errors->has('last_name') ? 'border-red-500' : 'border-gray-300 dark:border-gray-600' }}">
                    @error('last_name')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Middle Name -->
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Middle Name <span class="text-gray-400">(Optional)</span>
                </label>
                <input type="text" name="middle_name" value="{{ old('middle_name') }}"
                       class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm">
            </div>

            <!-- Gender -->
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Gender <span class="text-red-500">*</span>
                </label>
                <select name="gender" required
                        class="w-full border dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm {{ $errors->has('gender') ? 'border-red-500' : 'border-gray-300 dark:border-gray-600' }}">
                    <option value="">Select Gender</option>
                    <option value="Male"   {{ old('gender') === 'Male'   ? 'selected' : '' }}>Male</option>
                    <option value="Female" {{ old('gender') === 'Female' ? 'selected' : '' }}>Female</option>
                    <option value="Other"  {{ old('gender') === 'Other'  ? 'selected' : '' }}>Other</option>
                </select>
                @error('gender')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Birthday -->
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Birthday</label>
                <input type="date" name="birthday" value="{{ old('birthday') }}"
                       class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm">
            </div>

            <!-- Phone Number -->
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Phone Number</label>
                <div class="flex">
                    <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-500 text-sm">+63</span>
                    <input type="tel" name="phone" value="{{ old('phone') }}" maxlength="10" placeholder="9123456789"
                           class="flex-1 border dark:bg-gray-700 dark:text-white rounded-r-lg px-4 py-2 text-sm {{ $errors->has('phone') ? 'border-red-500' : 'border-gray-300 dark:border-gray-600' }}"
                           oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,10)">
                </div>
                @error('phone')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @else
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Enter 10-digit number (e.g., 9123456789). +63 will be added automatically.</p>
                @enderror
            </div>

            <!-- Email -->
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Email <span class="text-red-500">*</span>
                </label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       class="w-full border dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm {{ $errors->has('email') ? 'border-red-500' : 'border-gray-300 dark:border-gray-600' }}">
                @error('email')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @else
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">A welcome email with credentials will be sent to this address.</p>
                @enderror
            </div>

            <!-- Role -->
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Role <span class="text-red-500">*</span>
                </label>
                <select name="role_id" x-model="selectedRoleId" required
                        class="w-full border dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm {{ $errors->has('role_id') ? 'border-red-500' : 'border-gray-300 dark:border-gray-600' }}">
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
                @error('role_id')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Position (driven by Member::VALID_POSITIONS via Alpine) -->
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Position</label>
                <div x-show="positionOptions.length > 0">
                    <select name="position" x-model="selectedPosition"
                            class="w-full border dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm {{ $errors->has('position') ? 'border-red-500' : 'border-gray-300 dark:border-gray-600' }}">
                        <option value="">Select Position</option>
                        <template x-for="pos in positionOptions" :key="pos">
                            <option :value="pos" x-text="pos"></option>
                        </template>
                    </select>
                    @error('position')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @else
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Positions are based on the selected role.
                        </p>
                    @enderror
                </div>
                <div x-show="positionOptions.length === 0 && selectedRoleId"
                     class="text-sm text-gray-500 dark:text-gray-400 italic">
                    No positions defined for this role.
                </div>
                <div x-show="!selectedRoleId"
                     class="text-sm text-gray-500 dark:text-gray-400 italic">
                    Select a role first to see available positions.
                </div>
            </div>

            <!-- Student ID -->
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Student ID</label>
                <input type="text" name="student_id" value="{{ old('student_id') }}" placeholder="2020-12345"
                       class="w-full border dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm {{ $errors->has('student_id') ? 'border-red-500' : 'border-gray-300 dark:border-gray-600' }}">
                @error('student_id')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @else
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Format: YYYY-XXXXX (e.g., 2020-12345)</p>
                @enderror
            </div>

            <!-- Year Level — hidden for non-student roles per NON_STUDENT_POSITIONS -->
            <div class="mb-4" x-show="isStudentRole" x-cloak>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Year Level</label>
                <select x-model="yearLevelValue"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm">
                    <option value="">Select Year Level</option>
                    <option value="Grade 7">Grade 7</option>
                    <option value="Grade 8">Grade 8</option>
                    <option value="Grade 9">Grade 9</option>
                    <option value="Grade 10">Grade 10</option>
                    <option value="Grade 11">Grade 11</option>
                    <option value="Grade 12">Grade 12</option>
                </select>
            </div>
            {{-- Always submitted; Alpine clears value when role is non-student --}}
            <input type="hidden" name="year_level" :value="yearLevelValue">

            <div x-show="!isStudentRole" x-cloak class="text-sm text-gray-500 dark:text-gray-400 italic mb-4">
                Year level is not applicable for this role.
            </div>

            <!-- Password (optional — random generated if blank) -->
            <div class="mb-4">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" x-model="setPassword" class="rounded border-gray-300 text-indigo-600">
                    <span class="text-sm text-gray-700 dark:text-gray-300">Set custom password (optional)</span>
                </label>

                <div x-show="setPassword" x-cloak class="mt-3 space-y-3">
                    <!-- Password -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Password</label>
                        <div class="relative">
                            <input :type="showPassword ? 'text' : 'password'"
                                   name="password"
                                   class="w-full border dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm pr-10 {{ $errors->has('password') ? 'border-red-500' : 'border-gray-300 dark:border-gray-600' }}">
                            <button type="button" @click="showPassword = !showPassword"
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

                    <!-- Confirm Password -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Confirm Password</label>
                        <div class="relative">
                            <input :type="showPasswordConfirm ? 'text' : 'password'"
                                   name="password_confirmation"
                                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm pr-10">
                            <button type="button" @click="showPasswordConfirm = !showPasswordConfirm"
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

                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                    If left blank, a random password will be generated and emailed to the user.
                </p>
            </div>

            <!-- Account Status -->
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Account Status</label>
                <div class="flex gap-4 text-sm text-gray-700 dark:text-gray-300">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}
                               class="text-emerald-600">
                        Active
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="is_active" value="0" {{ old('is_active') == '0' ? 'checked' : '' }}
                               class="text-emerald-600">
                        Inactive
                    </label>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex gap-3">
                <button
                    type="submit"
                    x-data="{ busy: false }"
                    @click="if (busy) { $event.preventDefault(); $event.stopImmediatePropagation(); return; }"
                    @submit.window="if ($event.target === $el.closest('form')) { busy = true; }"
                    :disabled="busy"
                    :class="busy ? 'opacity-60 cursor-not-allowed' : ''"
                    class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2 rounded-lg transition font-semibold text-sm"
                >
                    <span x-show="!busy">Create User</span>
                    <span x-show="busy" class="flex items-center gap-2">
                        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                        </svg>
                        Creating...
                    </span>
                </button>
                <a href="{{ route('admin.users.index') }}"
                   class="flex-1 text-center bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-semibold py-2 px-4 rounded-lg transition text-sm">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    {{-- Member::VALID_POSITIONS keyed by role ID --}}
    const validPositions   = @json(\App\Models\Member::VALID_POSITIONS);

    {{-- Member::NON_STUDENT_POSITIONS — positions that have no year level --}}
    const nonStudentPositions = @json(\App\Models\Member::NON_STUDENT_POSITIONS);

    {{-- Role IDs whose entire position list is non-student (computed in @php above) --}}
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

                // Clear selected position if it is no longer in the list
                if (this.selectedPosition && !this.positionOptions.includes(this.selectedPosition)) {
                    this.selectedPosition = '';
                }
            },

            checkIfStudentRole() {
                const roleId = parseInt(this.selectedRoleId);

                // Role has no positions defined at all — treat as student role
                if (!validPositions[roleId]) {
                    this.isStudentRole = true;
                    return;
                }

                // Role is in the pre-computed non-student role list
                if (nonStudentRoleIds.includes(roleId)) {
                    this.isStudentRole = false;
                    this.yearLevelValue = '';
                    return;
                }

                // Role has mixed positions — check the currently selected position
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