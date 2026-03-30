@extends('layouts.app')

@section('title', 'Members — VSULHS_SSLG')

@section('content')

@php
    $currentUser = auth()->user();
    $isSystemAdmin = $currentUser->role_id == 1;
    $canManageAccounts = ($isSystemAdmin || $currentUser->role->name === 'Supreme Admin' || $currentUser->role->name === 'Adviser');
@endphp

{{-- Header --}}
<div class="mb-6">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 dark:from-white dark:to-gray-300 bg-clip-text text-transparent">
                Members
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                Manage organization members by role
            </p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
            {{-- Search Bar --}}
            <div class="relative group">
                <input type="text" 
                       id="searchMembers" 
                       placeholder="Search by name, email, role..." 
                       class="w-full sm:w-80 pl-11 pr-11 py-2.5 text-sm border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-800 dark:text-gray-200 transition-all duration-200 bg-gray-50 dark:bg-gray-800/50">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <svg class="h-4.5 w-4.5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <button id="clearSearch" class="absolute inset-y-0 right-0 pr-3.5 flex items-center hidden">
                    <svg class="h-4.5 w-4.5 text-gray-400 hover:text-gray-600 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            {{-- Add Member Button --}}
            @if($isSystemAdmin || $currentUser->hasPermission('members.create'))
            <a href="{{ route('members.create') }}" 
               class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-[1.02]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Member
            </a>
            @endif
        </div>
    </div>
</div>

{{-- Role Statistics Cards --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-4 text-white shadow-lg">
        <p class="text-xs opacity-90">System Admins</p>
        <p class="text-2xl font-bold" id="system-admin-count">0</p>
    </div>
    <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl p-4 text-white shadow-lg">
        <p class="text-xs opacity-90">Supreme Level</p>
        <p class="text-2xl font-bold" id="supreme-count">0</p>
    </div>
    <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl p-4 text-white shadow-lg">
        <p class="text-xs opacity-90">Org Leaders</p>
        <p class="text-2xl font-bold" id="leaders-count">0</p>
    </div>
    <div class="bg-gradient-to-br from-slate-500 to-slate-600 rounded-xl p-4 text-white shadow-lg">
        <p class="text-xs opacity-90">Regular Members</p>
        <p class="text-2xl font-bold" id="members-count">0</p>
    </div>
</div>

{{-- Role Filter Tabs --}}
<div class="border-b border-gray-200 dark:border-gray-700 mb-6">
    <nav class="flex flex-wrap gap-4">
        <button class="role-filter active px-4 py-2 text-sm font-medium border-b-2 border-indigo-500 text-indigo-600 dark:text-indigo-400" data-role="all">
            All Members
            <span class="ml-1 text-xs bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded-full" id="all-count">0</span>
        </button>
        <button class="role-filter px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400" data-role="admin">
            System Admin
            <span class="ml-1 text-xs bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded-full" id="admin-count">0</span>
        </button>
        <button class="role-filter px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400" data-role="supreme">
            Supreme
            <span class="ml-1 text-xs bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded-full" id="supreme-filter-count">0</span>
        </button>
        <button class="role-filter px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400" data-role="org-leader">
            Org Leaders
            <span class="ml-1 text-xs bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded-full" id="org-leader-count">0</span>
        </button>
        <button class="role-filter px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400" data-role="adviser">
            Advisers
            <span class="ml-1 text-xs bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded-full" id="adviser-count">0</span>
        </button>
        <button class="role-filter px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400" data-role="member">
            Members
            <span class="ml-1 text-xs bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded-full" id="member-count">0</span>
        </button>
    </nav>
</div>

{{-- Role Color Legend --}}
<div class="mb-4 flex flex-wrap gap-3 text-xs">
    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-purple-500"></span> System Admin (SysAdmin)</span>
    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-indigo-500"></span> Supreme Admin (SA)</span>
    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-blue-500"></span> Supreme Officer (SO)</span>
    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-emerald-500"></span> Org Admin (OA)</span>
    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-sky-500"></span> Org Officer (OO)</span>
    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-amber-500"></span> Adviser (AD)</span>
    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-gray-500"></span> Org Member (OM)</span>
</div>

{{-- Members Table Card --}}
<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <th class="text-left px-6 py-4 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Member</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Email</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Role</th>
                    @if($isSystemAdmin || $currentUser->hasPermission('members.view_sensitive'))
                    <th class="text-left px-6 py-4 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Student ID</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Year Level</th>
                    @endif
                    <th class="text-left px-6 py-4 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Status</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Level</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Joined</th>
                    <th class="text-right px-6 py-4 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                 </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700" id="membersTableBody">
                @forelse ($users as $member)
                @php
                    $roleColors = [
                        'System Administrator' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/50 dark:text-purple-300',
                        'Supreme Admin' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300',
                        'Supreme Officer' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300',
                        'Org Admin' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300',
                        'Org Officer' => 'bg-sky-100 text-sky-700 dark:bg-sky-900/50 dark:text-sky-300',
                        'Adviser' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-300',
                        'Org Member' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
                    ];
                    $colorClass = $roleColors[$member->role->name] ?? 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300';
                @endphp
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-all duration-150 member-row" data-role-name="{{ $member->role->name }}" data-role-abbr="{{ $member->role->abbreviation }}" data-role-level="{{ $member->role->level }}">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-100 to-indigo-200 dark:from-indigo-900/50 dark:to-indigo-800/50 flex items-center justify-center text-sm font-bold text-indigo-700 dark:text-indigo-300 shadow-sm">
                                {{ strtoupper(substr($member->full_name, 0, 2)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-white member-name">{{ $member->full_name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $member->position ?? 'No position' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-600 dark:text-gray-400 member-email text-sm">{{ $member->email }}</td>
                    <td class="px-6 py-4">
                        <div class="relative group">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium {{ $colorClass }}">
                                {{ $member->role->abbreviation ?? $member->role->name }}
                            </span>
                            <div class="absolute bottom-full left-0 mb-2 hidden group-hover:block z-20">
                                <div class="bg-gray-900 text-white text-xs rounded-lg py-2 px-3 w-48 shadow-xl">
                                    <p class="font-semibold mb-1">{{ $member->role->name }}</p>
                                    <p class="text-gray-300 text-xs">{{ $member->role->desc ?? 'No description' }}</p>
                                    <p class="text-gray-400 text-xs mt-1">Level {{ $member->role->level }}</p>
                                    @if($member->role->parent)
                                    <p class="text-gray-400 text-xs">Reports to: {{ $member->role->parent->name }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </td>
                    @if($isSystemAdmin || $currentUser->hasPermission('members.view_sensitive'))
                    <td class="px-6 py-4 text-gray-500 dark:text-gray-400 text-sm">{{ $member->student_id ?? '—' }}</td>
                    <td class="px-6 py-4 text-gray-500 dark:text-gray-400 text-sm">{{ $member->year_level ?? '—' }}</td>
                    @endif
                    <td class="px-6 py-4">
                        @if ($member->is_active)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-300">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                Active
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-300">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                Inactive
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <div class="w-12 h-1.5 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                <div class="h-full rounded-full bg-indigo-500" style="width: {{ ($member->role->level / 8) * 100 }}%"></div>
                            </div>
                            <span class="text-xs text-gray-500">Lv.{{ $member->role->level }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-500 dark:text-gray-400 text-sm">
                        {{ optional($member->created_at)->format('M d, Y') }}
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('members.show', $member->id) }}" 
                               class="p-1.5 text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 transition-colors rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700" 
                               title="View member">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </a>
                            @if($isSystemAdmin || $currentUser->hasPermission('members.edit'))
                            <a href="{{ route('members.edit', $member->id) }}" 
                               class="p-1.5 text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400 transition-colors rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700" 
                               title="Edit member">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>
                            @endif
                            @if(($isSystemAdmin || $currentUser->hasPermission('members.delete')) && $member->id !== auth()->id())
                            <button type="button" 
                                    onclick="confirmDelete('{{ $member->id }}', '{{ $member->full_name }}', '{{ $member->role->name }}')"
                                    class="p-1.5 text-gray-500 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-400 transition-colors rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
                                    title="Remove member">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <svg class="w-12 h-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <p class="text-gray-500 dark:text-gray-400 text-sm italic">No members found.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($users->hasPages())
    <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
        {{ $users->links() }}
    </div>
    @endif
    
    {{-- Search results count --}}
    <div id="searchCount" class="px-6 py-3 text-xs text-gray-500 dark:text-gray-400 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 hidden">
        <div class="flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            Showing <span id="visibleCount" class="font-semibold">0</span> of <span id="totalCount" class="font-semibold">0</span> members
        </div>
    </div>
</div>

<script>
// Role colors mapping for statistics
const roleColors = {
    'System Administrator': 'purple',
    'Supreme Admin': 'indigo',
    'Supreme Officer': 'blue',
    'Org Admin': 'emerald',
    'Org Officer': 'sky',
    'Adviser': 'amber',
    'Org Member': 'gray'
};

// Update statistics counts
function updateStatistics() {
    const rows = document.querySelectorAll('.member-row');
    let systemAdmin = 0;
    let supreme = 0;
    let leaders = 0;
    let regularMembers = 0;
    let adminCount = 0;
    let supremeFilterCount = 0;
    let orgLeaderCount = 0;
    let adviserCount = 0;
    let memberCount = 0;
    
    rows.forEach(row => {
        const roleName = row.getAttribute('data-role-name');
        const roleLevel = parseInt(row.getAttribute('data-role-level'));
        
        if (roleName === 'System Administrator') systemAdmin++;
        if (roleLevel <= 3) supreme++;
        if (roleName === 'Org Admin' || roleName === 'Org Officer') leaders++;
        if (roleName === 'Org Member') regularMembers++;
        if (roleName === 'System Administrator') adminCount++;
        if (roleLevel <= 3) supremeFilterCount++;
        if (roleName === 'Org Admin' || roleName === 'Org Officer') orgLeaderCount++;
        if (roleName === 'Adviser') adviserCount++;
        if (roleName === 'Org Member') memberCount++;
    });
    
    document.getElementById('system-admin-count').textContent = systemAdmin;
    document.getElementById('supreme-count').textContent = supreme;
    document.getElementById('leaders-count').textContent = leaders;
    document.getElementById('members-count').textContent = regularMembers;
    document.getElementById('all-count').textContent = rows.length;
    document.getElementById('admin-count').textContent = adminCount;
    document.getElementById('supreme-filter-count').textContent = supremeFilterCount;
    document.getElementById('org-leader-count').textContent = orgLeaderCount;
    document.getElementById('adviser-count').textContent = adviserCount;
    document.getElementById('member-count').textContent = memberCount;
}

// Role filtering
function filterByRole(roleType) {
    const rows = document.querySelectorAll('.member-row');
    let visibleCount = 0;
    
    rows.forEach(row => {
        const roleName = row.getAttribute('data-role-name');
        const roleLevel = parseInt(row.getAttribute('data-role-level'));
        let show = false;
        
        switch(roleType) {
            case 'all':
                show = true;
                break;
            case 'admin':
                show = roleName === 'System Administrator';
                break;
            case 'supreme':
                show = roleLevel <= 3;
                break;
            case 'org-leader':
                show = roleName === 'Org Admin' || roleName === 'Org Officer';
                break;
            case 'adviser':
                show = roleName === 'Adviser';
                break;
            case 'member':
                show = roleName === 'Org Member';
                break;
        }
        
        if (show) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    // Update search count display
    const searchCountDiv = document.getElementById('searchCount');
    const visibleCountSpan = document.getElementById('visibleCount');
    const totalCountSpan = document.getElementById('totalCount');
    
    if (searchCountDiv && visibleCountSpan) {
        if (roleType !== 'all') {
            visibleCountSpan.textContent = visibleCount;
            searchCountDiv.classList.remove('hidden');
        } else {
            searchCountDiv.classList.add('hidden');
        }
    }
    
    // Show no results message
    const tbody = document.getElementById('membersTableBody');
    const existingNoResults = tbody.querySelector('.no-results-row');
    
    if (visibleCount === 0 && rows.length > 0) {
        if (!existingNoResults) {
            const noResultsTr = document.createElement('tr');
            noResultsTr.className = 'no-results-row';
            noResultsTr.innerHTML = `
                <td colspan="9" class="px-6 py-12 text-center">
                    <div class="flex flex-col items-center gap-3">
                        <svg class="w-12 h-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <p class="text-gray-500 dark:text-gray-400 text-sm italic">No members match this role filter.</p>
                    </div>
                </td>
            `;
            tbody.appendChild(noResultsTr);
        }
    } else {
        if (existingNoResults) {
            existingNoResults.remove();
        }
    }
}

function confirmDelete(userId, userName, userRole) {
    const systemRoles = ['System Administrator', 'Supreme Admin', 'Supreme Officer'];
    
    if (systemRoles.includes(userRole)) {
        if (window.notify && window.notify.warning) {
            window.notify.warning(`⚠️ Cannot delete "${userName}".\n\nThis user has a system role (${userRole}) which is required for the system to function properly.`);
        } else {
            alert(`⚠️ Cannot delete "${userName}". This user has a system role (${userRole}) which is required for the system to function properly.`);
        }
        return;
    }
    
    if (userRole === 'Adviser') {
        const message = `⚠️ WARNING: You are about to delete ${userName}, who is an ADVISER.\n\nMake sure this is NOT the last adviser in the system!\n\nDeleting the last adviser will lock you out of admin features.\n\nAre you absolutely sure you want to continue?`;
        if (confirm(message)) {
            document.getElementById(`delete-form-${userId}`).submit();
        }
    } else {
        const message = `⚠️ You are about to delete ${userName}.\n\nRole: ${userRole}\n\nThis action cannot be undone.\n\nAre you sure you want to delete this user?`;
        if (confirm(message)) {
            document.getElementById(`delete-form-${userId}`).submit();
        }
    }
}

// Search functionality
document.addEventListener('DOMContentLoaded', function() {
    // Update statistics
    updateStatistics();
    
    // Role filter tabs
    const roleFilters = document.querySelectorAll('.role-filter');
    roleFilters.forEach(filter => {
        filter.addEventListener('click', function() {
            // Update active tab styling
            roleFilters.forEach(f => f.classList.remove('active', 'border-indigo-500', 'text-indigo-600'));
            this.classList.add('active', 'border-indigo-500', 'text-indigo-600');
            
            const roleType = this.getAttribute('data-role');
            filterByRole(roleType);
        });
    });
    
    // Search functionality
    const searchInput = document.getElementById('searchMembers');
    const clearButton = document.getElementById('clearSearch');
    const rows = document.querySelectorAll('.member-row');
    const searchCountDiv = document.getElementById('searchCount');
    const visibleCountSpan = document.getElementById('visibleCount');
    const totalCountSpan = document.getElementById('totalCount');
    
    if (totalCountSpan) {
        totalCountSpan.textContent = rows.length;
    }
    
    if (clearButton) {
        clearButton.addEventListener('click', function() {
            searchInput.value = '';
            searchInput.dispatchEvent(new Event('input'));
            clearButton.classList.add('hidden');
            searchInput.focus();
        });
    }
    
    function performSearch() {
        const query = searchInput.value.toLowerCase().trim();
        let visibleCount = 0;
        
        if (clearButton) {
            if (query.length > 0) {
                clearButton.classList.remove('hidden');
            } else {
                clearButton.classList.add('hidden');
            }
        }
        
        rows.forEach(row => {
            const name = row.querySelector('.member-name')?.textContent.toLowerCase() || '';
            const email = row.querySelector('.member-email')?.textContent.toLowerCase() || '';
            const role = row.getAttribute('data-role-name')?.toLowerCase() || '';
            const roleAbbr = row.getAttribute('data-role-abbr')?.toLowerCase() || '';
            
            const searchableText = `${name} ${email} ${role} ${roleAbbr}`;
            
            if (row.style.display !== 'none') {
                if (query === '' || searchableText.includes(query)) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            } else {
                // Check if row is hidden by filter but matches search
                if (query !== '' && searchableText.includes(query)) {
                    row.style.display = '';
                    visibleCount++;
                }
            }
        });
        
        if (searchCountDiv && visibleCountSpan) {
            if (query !== '') {
                visibleCountSpan.textContent = visibleCount;
                searchCountDiv.classList.remove('hidden');
            } else {
                searchCountDiv.classList.add('hidden');
            }
        }
        
        const tbody = document.getElementById('membersTableBody');
        const existingNoResults = tbody.querySelector('.no-results-row');
        
        if (visibleCount === 0 && rows.length > 0) {
            if (!existingNoResults) {
                const noResultsTr = document.createElement('tr');
                noResultsTr.className = 'no-results-row';
                noResultsTr.innerHTML = `
                    <td colspan="9" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <svg class="w-12 h-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <p class="text-gray-500 dark:text-gray-400 text-sm italic">No members match your search.</p>
                        </div>
                    </td>
                `;
                tbody.appendChild(noResultsTr);
            }
        } else {
            if (existingNoResults) {
                existingNoResults.remove();
            }
        }
    }
    
    searchInput.addEventListener('input', performSearch);
    performSearch();
});
</script>

@foreach($users as $member)
<form id="delete-form-{{ $member->id }}" action="{{ route('members.destroy', $member->id) }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endforeach

@endsection