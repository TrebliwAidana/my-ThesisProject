<!DOCTYPE html>
<html lang="en" x-data="{ sidebarOpen: false, activeRoute: '{{ Route::currentRouteName() }}' }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'VSULHS_SSLG')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui'],
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans min-h-screen flex">

{{-- Sidebar --}}
<aside
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    class="fixed z-40 top-0 left-0 w-64 h-full bg-white border-r border-gray-200 transform transition-transform duration-200 lg:translate-x-0"
>
    <div class="flex flex-col h-full">
        <div class="flex items-center justify-between h-14 px-6 border-b border-gray-200">
            <span class="font-bold text-lg tracking-wide">VSULHS_SSLG</span>
            <button @click="sidebarOpen = false" class="lg:hidden text-gray-500 hover:text-black">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
        @php
            $user = auth()->user();
            $menu = [
                ['label'=>'Dashboard', 'route'=>'dashboard', 'permission'=>null, 'icon'=>'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                ['label'=>'Members', 'route'=>'members.index', 'permission'=>'manage-members', 'icon'=>'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
                ['label'=>'Documents', 'route'=>'documents.index', 'permission'=>'view-documents', 'icon'=>'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                ['label'=>'Budgets', 'route'=>'budgets.index', 'permission'=>'manage-budgets', 'icon'=>'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['label'=>'Admin', 'icon'=>'M5 13l4 4L19 7', 'permission'=>'manage-users', 'submenu'=>[
                    ['label'=>'User Management', 'route'=>'admin.users.index', 'permission'=>'manage-users'],
                    ['label'=>'Roles', 'route'=>'admin.roles.index', 'permission'=>'manage-users'],
                    ['label'=>'Permissions', 'route'=>'admin.permissions.index', 'permission'=>'manage-users'],
                    ['label'=>'Settings', 'route'=>'settings.index', 'permission'=>'manage-settings'],
                    ['label'=>'Audit Logs', 'route'=>'audit.logs', 'permission'=>'view-audit-logs'],
                ]],
            ];
        @endphp

            @foreach($menu as $item)
                @if(!$item['permission'] || $user->hasPermission($item['permission']))
                    @if(isset($item['submenu']))
                        {{-- Parent menu with submenu --}}
                        <div x-data="{ open: false }">
                            <button @click="open = !open"
                                    class="w-full flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100">
                                <span class="flex items-center gap-3">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] ?? '' }}"/>
                                    </svg>
                                    {{ $item['label'] }}
                                </span>
                                <svg :class="open ? 'rotate-90' : ''" class="w-3 h-3 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>
                            <div x-show="open" class="ml-6 mt-1 space-y-1">
                                @foreach($item['submenu'] as $sub)
                                    @if(!$sub['permission'] || $user->hasPermission($sub['permission']))
                                        <a href="{{ route($sub['route']) }}"
                                           :class="activeRoute === '{{ $sub['route'] }}' ? 'bg-black text-white' : 'text-gray-700 hover:bg-gray-100'"
                                           class="block px-3 py-2 rounded-lg text-sm font-medium transition">
                                           {{ $sub['label'] }}
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @else
                        {{-- Single menu item --}}
                        <a href="{{ route($item['route']) }}"
                           :class="activeRoute === '{{ $item['route'] }}' ? 'bg-black text-white' : 'text-gray-700 hover:bg-gray-100'"
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] ?? '' }}"/>
                            </svg>
                            {{ $item['label'] }}
                        </a>
                    @endif
                @endif
            @endforeach

        </nav>
    </div>
</aside>

{{-- Overlay --}}
<div
    x-show="sidebarOpen"
    @click="sidebarOpen = false"
    class="fixed inset-0 bg-black bg-opacity-25 z-30 lg:hidden transition-opacity"
></div>

{{-- Main content --}}
<div class="flex-1 flex flex-col min-h-screen lg:ml-64">
    {{-- Navbar --}}
    <nav class="bg-black text-white h-14 flex items-center justify-between px-6 shadow-md fixed top-0 left-0 right-0 z-50">
        <div class="flex items-center gap-4">
            <button @click="sidebarOpen = !sidebarOpen" class="text-gray-400 hover:text-white lg:hidden">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <span class="font-bold text-lg tracking-wide">VSULHS_SSLG</span>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-sm text-gray-300 hidden sm:block">{{ $user->full_name }}</span>
            <span class="text-xs font-bold bg-white text-black px-3 py-1 rounded-full">
                {{ $user->role->name }}
            </span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="text-sm border border-gray-600 text-gray-300 px-3 py-1 rounded hover:bg-white hover:text-black transition">
                    Logout
                </button>
            </form>
        </div>
    </nav>

    <main class="flex-1 pt-14 p-6">
        @yield('content')
    </main>
</div>
</body>
</html>