@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">

    {{-- ================================================================
         HEADER - Clean version with improved spacing
    ================================================================ --}}
    <div class="mb-4">
        <h1 class="text-3xl font-bold text-slate-800 dark:text-slate-200 tracking-tight">Welcome back, {{ $user->full_name }}!</h1>
        <p class="text-base text-slate-500 dark:text-slate-400 mt-1.5">Here's what's happening with your organization today.</p>
    </div>

    {{-- ================================================================
         STATS GRID - Improved card spacing and typography
    ================================================================ --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

        <div class="bg-white dark:bg-gray-800 rounded-xl border border-slate-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 bg-indigo-50 dark:bg-indigo-900/50 rounded-xl flex items-center justify-center mb-4 flex-shrink-0">
                <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <p class="text-3xl font-bold text-slate-800 dark:text-slate-200">{{ $totalMembers }}</p>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Total Members</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl border border-slate-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 bg-green-50 dark:bg-green-900/50 rounded-xl flex items-center justify-center mb-4 flex-shrink-0">
                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <p class="text-3xl font-bold text-slate-800 dark:text-slate-200">{{ $activeMembers }}</p>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Active Members</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl border border-slate-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 bg-sky-50 dark:bg-sky-900/50 rounded-xl flex items-center justify-center mb-4 flex-shrink-0">
                <svg class="w-6 h-6 text-sky-600 dark:text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <p class="text-3xl font-bold text-slate-800 dark:text-slate-200">{{ $officersCount }}</p>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Officers</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl border border-slate-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 bg-purple-50 dark:bg-purple-900/50 rounded-xl flex items-center justify-center mb-4 flex-shrink-0">
                <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
            </div>
            <p class="text-3xl font-bold text-slate-800 dark:text-slate-200">{{ $newMembersThisMonth }}</p>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">New in {{ now()->format('F Y') }}</p>
        </div>

    </div>

    {{-- ================================================================
         PROFILE CARD - Improved spacing
    ================================================================ --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200 dark:border-gray-700 overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-6 py-4">
            <h3 class="text-white font-semibold text-lg">Profile Information</h3>
            <p class="text-indigo-200 text-sm mt-1">Your personal details</p>
        </div>

        <div class="p-6 md:p-8">
            <div class="flex flex-col lg:flex-row gap-8">

                {{-- Avatar --}}
                <div class="flex-shrink-0 flex flex-col items-center lg:items-start gap-3">
                    <div class="w-24 h-24 rounded-full flex items-center justify-center text-white font-bold text-2xl shadow-lg uppercase"
                         style="background: linear-gradient(135deg, #818cf8, #4f46e5);">
                        {{ strtoupper(substr($user->full_name, 0, 2)) }}
                    </div>
                    <span class="px-3 py-1.5 bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-400 rounded-full text-sm font-medium flex items-center gap-2">
                        <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                        Active
                    </span>
                </div>

                {{-- Info Grid - Improved spacing --}}
                <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-5">

                    <div class="bg-slate-50 dark:bg-gray-700/50 rounded-xl p-4">
                        <label class="block text-xs font-semibold text-slate-400 dark:text-gray-500 uppercase tracking-wider mb-2">Full Name</label>
                        <p class="text-slate-800 dark:text-gray-200 font-medium">{{ $user->full_name }}</p>
                    </div>

                    <div class="bg-slate-50 dark:bg-gray-700/50 rounded-xl p-4">
                        <label class="block text-xs font-semibold text-slate-400 dark:text-gray-500 uppercase tracking-wider mb-2">Email Address</label>
                        <p class="text-slate-800 dark:text-gray-200 font-medium">{{ $user->email }}</p>
                    </div>

                    <div class="bg-slate-50 dark:bg-gray-700/50 rounded-xl p-4">
                        <label class="block text-xs font-semibold text-slate-400 dark:text-gray-500 uppercase tracking-wider mb-2">Member Since</label>
                        <p class="text-slate-800 dark:text-gray-200 font-medium">{{ optional($user->created_at)->format('F d, Y') ?? 'N/A' }}</p>
                    </div>

                    <div class="bg-slate-50 dark:bg-gray-700/50 rounded-xl p-4">
                        <label class="block text-xs font-semibold text-slate-400 dark:text-gray-500 uppercase tracking-wider mb-2">Last Updated</label>
                        <p class="text-slate-800 dark:text-gray-200 font-medium">{{ optional($user->updated_at)->format('F d, Y') ?? 'N/A' }}</p>
                    </div>

                    <div class="bg-slate-50 dark:bg-gray-700/50 rounded-xl p-4">
                        <label class="block text-xs font-semibold text-slate-400 dark:text-gray-500 uppercase tracking-wider mb-2">Position</label>
                        <p class="text-slate-800 dark:text-gray-200 font-medium">{{ $user->member?->position ?? '—' }}</p>
                    </div>

                    <div class="bg-slate-50 dark:bg-gray-700/50 rounded-xl p-4">
                        <label class="block text-xs font-semibold text-slate-400 dark:text-gray-500 uppercase tracking-wider mb-2">Role</label>
                        <span class="inline-flex px-3 py-1.5 bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-400 rounded-full text-sm font-medium">
                            {{ $user->role->name }}
                        </span>
                    </div>

                </div>
            </div>

            {{-- Badges --}}
            @if(!empty($userBadges))
            <div class="mt-8 pt-6 border-t border-slate-100 dark:border-gray-700">
                <div class="flex items-center gap-2 mb-4">
                    <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    <label class="text-sm font-semibold text-slate-700 dark:text-gray-300">Achievement Badges</label>
                </div>
                <div class="flex flex-wrap gap-2">
                    @foreach($userBadges as $badge)
                    <span class="px-4 py-2 rounded-full bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 text-sm font-medium border border-amber-200 dark:border-amber-800">
                        🏆 {{ $badge }}
                    </span>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- ================================================================
         MEMBERS TABLE - Improved table spacing
    ================================================================ --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200 dark:border-gray-700 overflow-hidden">

        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 dark:border-gray-700">
            <h3 class="font-semibold text-slate-700 dark:text-gray-300 text-base">All Members</h3>
            <input type="text" id="searchMembers" placeholder="Search members..."
                   class="px-4 py-2 text-sm border border-slate-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-200 w-64">
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 dark:bg-gray-700 border-b border-slate-200 dark:border-gray-600 text-xs uppercase text-slate-500 dark:text-gray-400 tracking-wider">
                    叉
                        <th class="px-6 py-4 text-left font-semibold">Member</th>
                        <th class="px-6 py-4 text-left font-semibold">Role</th>
                        <th class="px-6 py-4 text-left font-semibold">Position</th>
                        <th class="px-6 py-4 text-left font-semibold">Status</th>
                        <th class="px-6 py-4 text-left font-semibold">Joined</th>
                        <th class="px-6 py-4 text-right font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-gray-700" id="membersTableBody">
                    @forelse($members as $member)
                    @if($member && $member->user)
                    @php
                        $status      = $member->isActive() ? 'Active' : 'Inactive';
                        $roleName    = $member->user->role->name ?? 'Member';
                        $roleClass   = $roleColors[$roleName] ?? 'bg-slate-100 dark:bg-gray-700 text-slate-600 dark:text-gray-400';
                        $statusClass = $statusColors[$status] ?? 'bg-slate-100 dark:bg-gray-700 text-slate-500 dark:text-gray-400';
                        $initials    = strtoupper(substr($member->user->full_name ?? 'NA', 0, 2));
                    @endphp
                    <tr class="hover:bg-slate-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold text-white"
                                     style="background: linear-gradient(135deg, #818cf8, #4f46e5);">
                                    {{ $initials }}
                                </div>
                                <div>
                                    <p class="font-medium text-slate-700 dark:text-gray-200">{{ $member->user->full_name ?? 'Unknown' }}</p>
                                    <p class="text-xs text-slate-400 dark:text-gray-500 mt-0.5">{{ $member->user->email ?? 'No email' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs font-medium px-2.5 py-1 rounded-full {{ $roleClass }}">
                                {{ $roleName }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-slate-600 dark:text-gray-400">{{ $member->position ?? '—' }}</td>
                        <td class="px-6 py-4">
                            <span class="text-xs font-medium px-2.5 py-1 rounded-full flex w-fit items-center gap-1.5 {{ $statusClass }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $member->isActive() ? 'bg-green-500' : 'bg-slate-400' }}"></span>
                                {{ $status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-slate-500 dark:text-gray-400 text-xs">
                            {{ optional($member->term_start)->format('M d, Y') ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <span class="text-xs text-slate-400 dark:text-gray-500">View</span>
                        </td>
                    </tr>
                    @endif
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-400 dark:text-gray-500">No members found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 border-t border-slate-100 dark:border-gray-700">
            {{ $members->links() }}
        </div>
    </div>

    {{-- ================================================================
         RECENT ACTIVITY - Improved card spacing
    ================================================================ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <div class="bg-white dark:bg-gray-800 rounded-xl border border-slate-200 dark:border-gray-700 overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 dark:border-gray-700">
                <h3 class="font-semibold text-slate-700 dark:text-gray-300 text-base">Recent Documents</h3>
                <a href="{{ route('documents.index') }}" class="text-sm text-indigo-500 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium">View all →</a>
            </div>
            <ul class="divide-y divide-slate-100 dark:divide-gray-700">
                @forelse($recentDocuments as $doc)
                <li class="flex items-center gap-4 px-6 py-4">
                    <div class="w-10 h-10 bg-amber-50 dark:bg-amber-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-amber-500 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-slate-700 dark:text-gray-200 truncate">{{ $doc->title }}</p>
                        <p class="text-xs text-slate-400 dark:text-gray-500 mt-1">{{ $doc->uploader->full_name }} · {{ optional($doc->uploaded_at)->diffForHumans() ?? 'N/A' }}</p>
                    </div>
                </li>
                @empty
                <li class="px-6 py-8 text-center text-sm text-slate-400 dark:text-gray-500">No documents yet.</li>
                @endforelse
            </ul>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl border border-slate-200 dark:border-gray-700 overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 dark:border-gray-700">
                <h3 class="font-semibold text-slate-700 dark:text-gray-300 text-base">Recent Budgets</h3>
                <a href="{{ route('budgets.index') }}" class="text-sm text-indigo-500 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium">View all →</a>
            </div>
            <ul class="divide-y divide-slate-100 dark:divide-gray-700">
                @forelse($recentBudgets as $budget)
                @php $sc = ['approved'=>'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400','rejected'=>'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400','pending'=>'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400']; @endphp
                <li class="flex items-center justify-between px-6 py-4">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-slate-700 dark:text-gray-200 truncate">{{ Str::limit($budget->desc ?? '—', 40) }}</p>
                        <p class="text-xs text-slate-400 dark:text-gray-500 mt-1 font-mono">₱{{ number_format($budget->amount, 2) }}</p>
                    </div>
                    <span class="text-xs font-medium px-2.5 py-1 rounded-full ml-4 {{ $sc[$budget->status] ?? 'bg-slate-100 dark:bg-gray-700 text-slate-500 dark:text-gray-400' }}">
                        {{ ucfirst($budget->status) }}
                    </span>
                </li>
                @empty
                <li class="px-6 py-8 text-center text-sm text-slate-400 dark:text-gray-500">No budget entries yet.</li>
                @endforelse
            </ul>
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
    // Member search
    document.getElementById('searchMembers').addEventListener('input', function () {
        const query = this.value.toLowerCase();
        document.querySelectorAll('#membersTableBody tr').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(query) ? '' : 'none';
        });
    });
</script>
@endpush