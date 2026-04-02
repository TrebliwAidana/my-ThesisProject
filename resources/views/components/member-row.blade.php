<tr class="hover:bg-gray-50 transition">
    <!-- Avatar + Name -->
    <td class="px-6 py-4 flex items-center gap-4">
        <div class="w-10 h-10 rounded-full bg-primary-500 flex items-center justify-center text-white font-bold">
            {{ $initials }}
        </div>

        <div>
            <p class="font-semibold text-gray-900">{{ $name }}</p>
            <p class="text-sm text-gray-500">{{ $email }}</p>
        </div>
    </td>

    <!-- Role -->
    <td class="px-6 py-4">
        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $roleColor }}">
            {{ $role ?? 'N/A' }}
        </span>
    </td>

    <!-- Status -->
    <td class="px-6 py-4">
        <span class="flex items-center gap-2 text-sm">
            <span class="w-2 h-2 rounded-full {{ $dotColor }}"></span>
            <span class="px-2 py-1 rounded-full {{ $statusColor }}">
                {{ $status ?? 'Unknown' }}
            </span>
        </span>
    </td>

    <!-- Joined -->
    <td class="px-6 py-4 text-sm text-gray-500">
        {{ $joined }}
    </td>

    <!-- Actions -->
    <td class="px-6 py-4 text-right">
        <button 
            onclick="openMemberModal('{{ $name }}', '{{ $email }}', '{{ $role }}')"
            class="text-primary-600 hover:text-primary-800 text-sm font-medium"
        >
            View
        </button>
    </td>
</tr>