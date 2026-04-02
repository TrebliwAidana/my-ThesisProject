@php
    $roleColors = [
        'System Administrator' => 'purple',
        'Supreme Admin'        => 'indigo',
        'Supreme Officer'      => 'blue',
        'Org Admin'            => 'emerald',
        'Org Officer'          => 'sky',
        'Club Adviser'         => 'amber',
        'Org Member'           => 'gray',
    ];
    $roleBadgeClasses = [
        'System Administrator' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/50 dark:text-purple-300',
        'Supreme Admin'        => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300',
        'Supreme Officer'      => 'bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300',
        'Org Admin'            => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300',
        'Org Officer'          => 'bg-sky-100 text-sky-700 dark:bg-sky-900/50 dark:text-sky-300',
        'Club Adviser'         => 'bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-300',
        'Org Member'           => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
        'Guest'                => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400',
    ];
@endphp

@forelse ($users as $member)
@php
    $color = $roleColors[$member->role->name] ?? 'gray';
    $badgeClass = $roleBadgeClasses[$member->role->name] ?? 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300';
@endphp
<tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-all duration-150">
    <td class="px-6 py-4">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-{{ $color }}-100 to-{{ $color }}-200 dark:from-{{ $color }}-900/50 dark:to-{{ $color }}-800/50 flex items-center justify-center text-sm font-bold text-{{ $color }}-700 dark:text-{{ $color }}-300 shadow-sm">
                {{ strtoupper(substr($member->full_name, 0, 2)) }}
            </div>
            <div>
                <p class="font-semibold text-gray-900 dark:text-white">{{ $member->full_name }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $member->position ?? 'No position' }}</p>
            </div>
        </div>
    </td>
    <td class="px-6 py-4 text-gray-600 dark:text-gray-400 text-sm">{{ $member->email }}</td>
    <td class="px-6 py-4">
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium {{ $badgeClass }}">
            {{ $member->role->abbreviation ?? $member->role->name }}
        </span>
    </td>
    <td class="px-6 py-4">
        @if($member->email_verified_at)
            <span class="inline-flex items-center gap-1 text-green-600 dark:text-green-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Verified
            </span>
        @else
            <span class="inline-flex items-center gap-1 text-yellow-600 dark:text-yellow-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                Unverified
            </span>
        @endif
    </td>
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
                <div class="h-full rounded-full bg-primary-500" style="width: {{ min(($member->role->level / 8) * 100, 100) }}%"></div>
            </div>
            <span class="text-xs text-gray-500">Lv.{{ $member->role->level }}</span>
        </div>
    </td>
    <td class="px-6 py-4 text-gray-500 dark:text-gray-400 text-sm">
        {{ optional($member->created_at)->format('M d, Y') }}
    </td>
    <td class="px-6 py-4 text-right">
        <div class="flex items-center justify-end gap-2">
            <a href="{{ route('members.show', $member->id) }}" class="p-1.5 text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 transition-colors rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700" title="View member">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
            </a>
            @if(auth()->user()->role_id == 1 || auth()->user()->hasPermission('members.edit'))
            <a href="{{ route('members.edit', $member->id) }}" class="p-1.5 text-gray-500 hover:text-primary-600 dark:text-gray-400 dark:hover:text-primary-400 transition-colors rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700" title="Edit member">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
            </a>
            @endif
            <a href="{{ route('members.edit-history', $member->id) }}" class="p-1.5 text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 transition-colors rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700" title="View history">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </a>
            @if((auth()->user()->role_id == 1 || auth()->user()->hasPermission('members.delete')) && $member->id !== auth()->id())
            <button type="button" onclick="confirmDelete('{{ $member->id }}', '{{ $member->full_name }}', '{{ $member->role->name }}')" class="p-1.5 text-gray-500 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-400 transition-colors rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700" title="Delete member">
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
    <td colspan="8" class="px-6 py-12 text-center">
        <div class="flex flex-col items-center gap-3">
            <svg class="w-12 h-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            <p class="text-gray-500 dark:text-gray-400 text-sm italic">No members found.</p>
        </div>
    </td>
</tr>
@endforelse