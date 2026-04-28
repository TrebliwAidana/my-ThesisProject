@extends('layouts.app')

@section('title', 'Create Member')
@section('page-title', 'Create New Member')

@section('content')

<div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-emerald-600 to-emerald-700 dark:from-emerald-800 dark:to-emerald-900 p-6 mb-6">
    <div class="relative z-10">
        <h1 class="text-2xl font-bold text-white">Create New Member</h1>
        <p class="text-emerald-100 text-sm mt-1">Add a new member to your organization</p>
    </div>
    <div class="absolute top-0 right-0 -mt-8 -mr-8 w-64 h-64 bg-white/5 rounded-full blur-3xl"></div>
</div>

<div class="max-w-3xl mx-auto" x-data="memberCreateForm()" x-init="init()">

    <form method="POST" action="{{ route('members.store') }}" class="space-y-6" enctype="multipart/form-data">
        @csrf

        @if ($errors->any())
            <div class="bg-red-50 dark:bg-red-900/30 border-l-4 border-red-500 p-4 rounded-lg">
                <div class="flex items-center gap-2 mb-2">
                    <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <h3 class="text-sm font-semibold text-red-800 dark:text-red-200">Please fix the following errors:</h3>
                </div>
                <ul class="text-sm text-red-700 dark:text-red-300 list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- ── Basic Information ──────────────────────────────────────────── --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gold-200 dark:border-gold-800 overflow-hidden">
            <div class="bg-gradient-to-r from-emerald-600 to-emerald-700 dark:from-emerald-800 dark:to-emerald-900 px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-semibold text-white">Basic Information</h2>
                        <p class="text-emerald-100 text-xs">Personal and account details</p>
                    </div>
                </div>
            </div>

            <div class="p-6 space-y-4">

                {{-- First & Last Name --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                            First Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="first_name" value="{{ old('first_name') }}" required
                               class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 {{ $errors->has('first_name') ? 'border-red-400' : '' }}">
                        @error('first_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                            Last Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="last_name" value="{{ old('last_name') }}" required
                               class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 {{ $errors->has('last_name') ? 'border-red-400' : '' }}">
                        @error('last_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Middle Name --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                        Middle Name <span class="text-gray-400 font-normal">(Optional)</span>
                    </label>
                    <input type="text" name="middle_name" value="{{ old('middle_name') }}"
                           class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                </div>

                {{-- Gender --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                        Gender <span class="text-red-500">*</span>
                    </label>
                    <select name="gender" required
                            class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 {{ $errors->has('gender') ? 'border-red-400' : '' }}">
                        <option value="">Select Gender</option>
                        <option value="Male"   {{ old('gender') === 'Male'   ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('gender') === 'Female' ? 'selected' : '' }}>Female</option>
                        <option value="Other"  {{ old('gender') === 'Other'  ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('gender') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Birthday --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Birthday</label>
                    <input type="date" name="birthday" value="{{ old('birthday') }}"
                           class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                </div>

                {{-- Phone --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Phone Number</label>
                    <div class="flex">
                        <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gold-300 dark:border-gold-600 bg-gray-50 dark:bg-gray-700 text-gray-500 text-sm">+63</span>
                        <input type="tel" name="phone" value="{{ old('phone') }}"
                               maxlength="10" placeholder="9123456789"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,10)"
                               class="flex-1 border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-r-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 {{ $errors->has('phone') ? 'border-red-400' : '' }}">
                    </div>
                    <p class="text-xs text-gray-400 mt-1">10-digit number, +63 added automatically.</p>
                    @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 {{ $errors->has('email') ? 'border-red-400' : '' }}">
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Role --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                        Role <span class="text-red-500">*</span>
                    </label>
                    <select name="role_id"
                            x-model="selectedRoleId"
                            required
                            class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 {{ $errors->has('role_id') ? 'border-red-400' : '' }}">
                        <option value="">Select Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('role_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{--
                    Position — uses x-if so only ONE element named "position" ever
                    exists in the DOM at a time, preventing a hidden blank value from
                    shadowing the real select on submit.
                --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                        Position <span class="text-red-500">*</span>
                    </label>

                    <template x-if="!selectedRoleId">
                        <select disabled class="w-full border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 text-gray-400 rounded-lg px-4 py-2.5 text-sm">
                            <option>Select a role first</option>
                        </select>
                    </template>

                    <template x-if="selectedRoleId && positionOptions.length > 0">
                        <select name="position"
                                x-model="selectedPosition"
                                class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 {{ $errors->has('position') ? 'border-red-400' : '' }}">
                            <option value="">Select Position</option>
                            <template x-for="pos in positionOptions" :key="pos">
                                <option :value="pos" x-text="pos" :selected="pos === selectedPosition"></option>
                            </template>
                        </select>
                    </template>

                    <template x-if="selectedRoleId && positionOptions.length === 0">
                        <div>
                            <input type="hidden" name="position" value="">
                            <p class="text-sm text-gray-400 italic py-2">No position required for this role.</p>
                        </div>
                    </template>

                    <p class="text-xs text-gray-400 mt-1">Positions are based on the selected role.</p>
                    @error('position') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{--
                    Student ID & Year Level — shown only for student roles.
                    "Student role" = role whose VALID_POSITIONS list contains at least
                    one position NOT in Member::NON_STUDENT_POSITIONS.
                    The controller always passes $positionMapping; the JS derives
                    $nonStudentPositions from Member::NON_STUDENT_POSITIONS (also
                    passed) so no hardcoded arrays exist here.
                --}}
                <div x-show="isStudentRole" x-cloak>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Student ID</label>
                    <input type="text" name="student_id" value="{{ old('student_id') }}" placeholder="2020-12345"
                           class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 {{ $errors->has('student_id') ? 'border-red-400' : '' }}">
                    <p class="text-xs text-gray-400 mt-1">Format: YYYY-XXXXX (e.g., 2020-12345)</p>
                    @error('student_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div x-show="isStudentRole" x-cloak>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Year Level</label>
                    <select x-model="yearLevelValue"
                            class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                        <option value="">Select Grade</option>
                        @for($i = 7; $i <= 12; $i++)
                            <option value="Grade {{ $i }}" {{ old('year_level') === 'Grade '.$i ? 'selected' : '' }}>
                                Grade {{ $i }}
                            </option>
                        @endfor
                    </select>
                </div>
                {{-- Always submit year_level — controller clears it server-side for non-student roles --}}
                <input type="hidden" name="year_level" :value="yearLevelValue">

                {{-- Active Status --}}
                <div class="flex items-center gap-3">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Account Status</label>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1"
                               {{ old('is_active', '1') == '1' ? 'checked' : '' }}
                               class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-gold-500 rounded-full peer dark:bg-gray-700
                                    peer-checked:bg-emerald-500 transition-colors after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                                    after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Active</span>
                    </label>
                </div>

                {{-- Profile Photo --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                        Profile Photo <span class="text-gray-400 font-normal">(Optional)</span>
                    </label>
                    <input type="file" name="avatar" accept="image/jpg,image/jpeg,image/png,image/gif,image/webp"
                           class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500
                                  file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-semibold
                                  file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100
                                  dark:file:bg-emerald-900/50 dark:file:text-emerald-300">
                    <p class="text-xs text-gray-400 mt-1">JPG, PNG, GIF, WEBP — max 2MB</p>
                    @error('avatar') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

            </div>
        </div>

        {{-- ── Security / Password ─────────────────────────────────────────── --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gold-200 dark:border-gold-800 overflow-hidden">
            <div class="bg-gradient-to-r from-emerald-600 to-emerald-700 dark:from-emerald-800 dark:to-emerald-900 px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-semibold text-white">Security</h2>
                        <p class="text-emerald-100 text-xs">Leave blank to auto-generate a secure password</p>
                    </div>
                </div>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Password</label>
                        <input type="password" name="password" autocomplete="new-password"
                               class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 {{ $errors->has('password') ? 'border-red-400' : '' }}">
                        @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Confirm Password</label>
                        <input type="password" name="password_confirmation" autocomplete="new-password"
                               class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                    </div>
                </div>
                <p class="text-xs text-gray-400">
                    Minimum 8 characters. If left blank, a random password is generated and emailed to the member.
                </p>
            </div>
        </div>

        {{-- ── Membership Details ───────────────────────────────────────────── --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gold-200 dark:border-gold-800 overflow-hidden">
            <div class="bg-gradient-to-r from-emerald-600 to-emerald-700 dark:from-emerald-800 dark:to-emerald-900 px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-semibold text-white">Membership Details</h2>
                        <p class="text-emerald-100 text-xs">Term and joining information</p>
                    </div>
                </div>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Joined Date</label>
                        <input type="date" name="joined_at" value="{{ old('joined_at', date('Y-m-d')) }}"
                               class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Term Start</label>
                        <input type="date" name="term_start" value="{{ old('term_start') }}"
                               class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                        Term End <span class="text-gray-400 font-normal">(Leave empty if ongoing)</span>
                    </label>
                    <input type="date" name="term_end" value="{{ old('term_end') }}"
                           class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                </div>
            </div>
        </div>

        {{-- ── Actions ──────────────────────────────────────────────────────── --}}
        <div class="flex justify-between items-center gap-3">
            <button type="submit"
                    class="bg-emerald-600 hover:bg-gold-500 text-white text-sm font-semibold px-6 py-2.5 rounded-lg transition shadow-sm">
                Create Member
            </button>
            <a href="{{ route('members.index') }}"
               class="bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-6 py-2.5 rounded-lg transition shadow-sm">
                Cancel
            </a>
        </div>

    </form>
</div>

<style>[x-cloak] { display: none !important; }</style>

@push('scripts')
<script>
    {{--
        positionMapping — passed from MemberController@create, derived from Member::VALID_POSITIONS.
        nonStudentPositions — passed from MemberController@create, derived from Member::NON_STUDENT_POSITIONS.
        Neither array is hardcoded here; all logic is data-driven from the model constants.
    --}}
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

                // Clear a stale selection when role changes
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

                // A position is "student" if it is NOT in nonStudentPositions.
                // If no position is selected yet, assume student until we know otherwise.
                const pos = this.selectedPosition;
                if (!pos) {
                    // Check if any position for this role is a student position
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