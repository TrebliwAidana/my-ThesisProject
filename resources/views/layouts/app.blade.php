<!DOCTYPE html>
<html lang="en" :class="$store.theme.dark ? 'dark' : ''" x-data>
<head>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    @php
        $flashData = [];
        if (session('success')) $flashData['success'] = session('success');
        if (session('error'))   $flashData['error']   = session('error');
        if (session('warning')) $flashData['warning'] = session('warning');
        if (session('info'))    $flashData['info']    = session('info');
        session()->forget(['success', 'error', 'warning', 'info']);
    @endphp

    @if(!empty($flashData))
        <meta name="flash-data" content='{{ json_encode($flashData) }}'>
    @endif

    <title>@yield('title', 'VSULHS_SSLG')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <script>
        (function () {
            if (localStorage.getItem('dark') === 'true') {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

    <style>
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes slideUp  { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes spin     { from { transform: rotate(0deg); }   to { transform: rotate(360deg); } }
        @keyframes loading  { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }
        .page-transition { animation: fadeInUp 0.5s cubic-bezier(0.4,0,0.2,1) forwards; }
        .stagger-item { opacity:0; animation: fadeInUp 0.4s cubic-bezier(0.4,0,0.2,1) forwards; }
        .stagger-item:nth-child(1)  { animation-delay:0.05s; } .stagger-item:nth-child(2)  { animation-delay:0.10s; }
        .stagger-item:nth-child(3)  { animation-delay:0.15s; } .stagger-item:nth-child(4)  { animation-delay:0.20s; }
        .stagger-item:nth-child(5)  { animation-delay:0.25s; } .stagger-item:nth-child(6)  { animation-delay:0.30s; }
        .stagger-item:nth-child(7)  { animation-delay:0.35s; } .stagger-item:nth-child(8)  { animation-delay:0.40s; }
        .stagger-item:nth-child(9)  { animation-delay:0.45s; } .stagger-item:nth-child(10) { animation-delay:0.50s; }
        .skeleton { background: linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%); background-size:200% 100%; animation:loading 1.5s infinite; }
        .dark .skeleton { background: linear-gradient(90deg,#1f2937 25%,#374151 50%,#1f2937 75%); background-size:200% 100%; }
        .timeline-item { opacity:0; transform:translateY(20px); transition:all 0.5s cubic-bezier(0.4,0,0.2,1); }
        .timeline-item.visible { opacity:1; transform:translateY(0); }
        .sidebar-transition { transition:width 0.3s cubic-bezier(0.4,0,0.2,1); }
        .sidebar-content { transition:opacity 0.2s ease; }
    </style>
</head>

<body
    x-data="{
        sidebarOpen: false,
        sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true',
        adminOpen: {{ Str::startsWith(Route::currentRouteName() ?? '', 'admin.') || in_array(Route::currentRouteName() ?? '', ['settings.index','audit.logs']) ? 'true' : 'false' }},
        activeRoute: '{{ Route::currentRouteName() ?? '' }}'
    }"
    class="bg-gray-50 dark:bg-gray-900 font-sans min-h-screen flex transition-colors duration-200"
    x-init="() => {
        if (sidebarCollapsed) $el.classList.add('sidebar-collapsed');
        $watch('sidebarCollapsed', value => {
            localStorage.setItem('sidebarCollapsed', value);
            value ? $el.classList.add('sidebar-collapsed') : $el.classList.remove('sidebar-collapsed');
        });
    }"
>

{{-- ══════════════════════ SIDEBAR (gold hover & borders) ══════════════════════ --}}
<aside
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    class="bg-emerald-50 dark:bg-emerald-950 fixed z-40 top-0 left-0 h-full transform transition-transform duration-200 lg:translate-x-0 flex flex-col border-r border-gold-300 dark:border-gold-800 sidebar-transition"
    :style="sidebarCollapsed ? 'width: 4rem' : 'width: 16rem'"
>
    {{-- Brand & Toggle Button --}}
    <div class="flex items-center justify-between h-14 px-3 border-b border-gold-300 dark:border-gold-800 flex-shrink-0"
         :class="sidebarCollapsed ? 'justify-center' : 'justify-between'">
        <div x-show="!sidebarCollapsed" class="text-left">
            <span class="text-emerald-700 dark:text-emerald-300 font-semibold text-sm tracking-wide block">VSULHS_SSLG</span>
            <span class="text-emerald-600 dark:text-emerald-400 text-xs block mt-0.5">Student Gov Portal</span>
        </div>
        <button @click="sidebarCollapsed = !sidebarCollapsed"
                class="text-gold-600 dark:text-gold-400 hover:text-gold-800 dark:hover:text-gold-200 transition-colors"
                :title="sidebarCollapsed ? 'Expand sidebar' : 'Collapse sidebar'">
            <svg x-show="!sidebarCollapsed" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
            </svg>
            <svg x-show="sidebarCollapsed" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
            </svg>
        </button>
    </div>

    {{-- Nav --}}
    <nav class="flex-1 py-3 overflow-y-auto">

        @php
            $user     = auth()->user();
            $userRole = $user?->role->name ?? 'Guest';

            $canSee = function(array $roles, string $permission = '') use ($user, $userRole) {
                if (!$user) return false;
                if (in_array($userRole, $roles)) return true;
                if ($permission && $user->hasPermission($permission)) return true;
                return false;
            };

            $menuItems = [
                [
                    'label'   => 'Dashboard',
                    'route'   => 'dashboard',
                    'icon'    => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
                    'visible' => (bool) $user,
                ],
                [
                    'label'   => 'Members',
                    'route'   => 'members.index',
                    'icon'    => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
                    'visible' => $canSee(
                        ['System Administrator','Supreme Admin','Supreme Officer','Org Admin','Org Officer','Club Adviser'],
                        'members.view'
                    ),
                ],
                [
                    'label'   => 'Documents',
                    'route'   => 'documents.index',
                    'icon'    => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                    'visible' => $canSee(
                        ['System Administrator','Supreme Admin','Supreme Officer','Org Admin','Org Officer','Club Adviser'],
                        'documents.view'
                    ),
                ],
                [
                    'label'   => 'Budgets',
                    'route'   => 'budgets.index',
                    'icon'    => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                    'visible' => $canSee(
                        ['System Administrator','Supreme Admin','Supreme Officer','Org Admin','Org Officer','Club Adviser'],
                        'budgets.view'
                    ),
                ],
                [
                    'label'   => 'My Organization',
                    'route'   => 'my.organization',
                    'icon'    => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
                    'visible' => $canSee(
                        ['System Administrator','Supreme Admin','Supreme Officer','Org Admin','Org Officer','Club Adviser'],
                        'organization.view'
                    ),
                ],
            ];

            $adminMenu = [
                'label'   => 'Administration',
                'icon'    => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
                'submenu' => [
                    [
                        'label'   => 'User Management',
                        'route'   => 'admin.users.index',
                        'icon'    => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
                        'visible' => $canSee(['System Administrator','Supreme Admin','Club Adviser'], 'admin.users'),
                    ],
                    [
                        'label'   => 'Organizations',
                        'route'   => 'admin.organizations.index',
                        'icon'    => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
                        'visible' => $canSee(['System Administrator'], 'organization.manage'),
                    ],
                    [
                        'label'   => 'Roles',
                        'route'   => 'admin.roles.index',
                        'icon'    => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
                        'visible' => $canSee(['System Administrator'], 'admin.roles'),
                    ],
                    [
                        'label'   => 'Permissions',
                        'route'   => 'admin.permissions.index',
                        'icon'    => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
                        'visible' => $canSee(['System Administrator'], 'admin.permissions'),
                    ],
                    [
                        'label'   => 'System Settings',
                        'route'   => 'settings.index',
                        'icon'    => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z',
                        'path'    => 'M15 12a3 3 0 11-6 0 3 3 0 016 0z',
                        'visible' => $canSee(['System Administrator','Supreme Admin','Club Adviser']),
                    ],
                    [
                        'label'   => 'Audit Logs',
                        'route'   => 'audit.logs',
                        'icon'    => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                        'visible' => $canSee(['System Administrator','Supreme Admin'], 'admin.audit'),
                    ],
                ],
            ];

            // Show Administration section only if at least one submenu item is visible
            $adminMenu['visible'] = collect($adminMenu['submenu'])->contains('visible', true);

            $profileItem = [
                'label'   => 'My Profile',
                'route'   => 'profile.index',
                'icon'    => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
                'visible' => (bool) $user,
            ];
        @endphp

        @if($user)
            {{-- Main menu items --}}
            @foreach($menuItems as $item)
                @if($item['visible'])
                    <a href="{{ route($item['route']) }}"
                       :class="activeRoute === '{{ $item['route'] }}' || activeRoute.startsWith('{{ $item['route'] }}')
                           ? 'bg-emerald-100 dark:bg-emerald-900/50 text-emerald-800 dark:text-emerald-200'
                           : 'text-emerald-700 dark:text-emerald-300 hover:bg-gold-500 hover:text-white dark:hover:bg-gold-600 transition-colors'"
                       class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium rounded-lg mx-2 my-1 transition-colors duration-150 hover:translate-x-1"
                       :title="sidebarCollapsed ? '{{ $item['label'] }}' : ''">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/>
                        </svg>
                        <span x-show="!sidebarCollapsed" class="sidebar-content">{{ $item['label'] }}</span>
                    </a>
                @endif
            @endforeach

            {{-- Profile --}}
            @if($profileItem['visible'])
                <a href="{{ route($profileItem['route']) }}"
                   :class="activeRoute === '{{ $profileItem['route'] }}'
                       ? 'bg-emerald-100 dark:bg-emerald-900/50 text-emerald-800 dark:text-emerald-200'
                       : 'text-emerald-700 dark:text-emerald-300 hover:bg-gold-500 hover:text-white dark:hover:bg-gold-600 transition-colors'"
                   class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium rounded-lg mx-2 my-1 transition-colors duration-150 hover:translate-x-1"
                   :title="sidebarCollapsed ? '{{ $profileItem['label'] }}' : ''">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $profileItem['icon'] }}"/>
                    </svg>
                    <span x-show="!sidebarCollapsed" class="sidebar-content">{{ $profileItem['label'] }}</span>
                </a>
            @endif

            {{-- Administration submenu --}}
            @if($adminMenu['visible'])
                <div class="mt-2">
                    <button @click="adminOpen = !adminOpen"
                            :class="adminOpen
                                ? 'bg-emerald-100 dark:bg-emerald-900/50 text-emerald-800 dark:text-emerald-200'
                                : 'text-emerald-700 dark:text-emerald-300 hover:bg-gold-500 hover:text-white dark:hover:bg-gold-600 transition-colors'"
                            class="w-full flex items-center justify-between px-4 py-2.5 text-sm font-medium rounded-lg mx-2 transition-colors duration-150 hover:translate-x-1"
                            :title="sidebarCollapsed ? '{{ $adminMenu['label'] }}' : ''">
                        <span class="flex items-center gap-3">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $adminMenu['icon'] }}"/>
                            </svg>
                            <span x-show="!sidebarCollapsed" class="sidebar-content">{{ $adminMenu['label'] }}</span>
                        </span>
                        <svg x-show="!sidebarCollapsed" :class="adminOpen ? 'rotate-90' : ''"
                             class="w-3 h-3 transition-transform duration-200 opacity-50"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                    <div x-show="adminOpen"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0">
                        @foreach($adminMenu['submenu'] as $sub)
                            @if($sub['visible'])
                                <a href="{{ route($sub['route']) }}"
                                   :class="activeRoute === '{{ $sub['route'] }}' || activeRoute.startsWith('{{ $sub['route'] }}')
                                       ? 'bg-emerald-100 dark:bg-emerald-900/50 text-emerald-800 dark:text-emerald-200'
                                       : 'text-emerald-600 dark:text-emerald-400 hover:bg-gold-500 hover:text-white dark:hover:bg-gold-600 transition-colors'"
                                   class="flex items-center gap-2.5 pl-11 pr-4 py-2 text-xs font-medium rounded-lg mx-2 my-1 transition-colors duration-150 hover:translate-x-1"
                                   :title="sidebarCollapsed ? '{{ $sub['label'] }}' : ''">
                                    @if(isset($sub['icon']))
                                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sub['icon'] }}"/>
                                        </svg>
                                    @else
                                        <span class="w-1.5 h-1.5 rounded-full bg-current opacity-50 flex-shrink-0"></span>
                                    @endif
                                    <span x-show="!sidebarCollapsed" class="sidebar-content">{{ $sub['label'] }}</span>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif
        @endif
    </nav>

    {{-- User footer --}}
    @auth
        <div class="border-t border-gold-300 dark:border-gold-800 px-3 py-3 flex items-center gap-3 flex-shrink-0"
             :class="sidebarCollapsed ? 'justify-center' : 'justify-start'">
            <div class="w-8 h-8 rounded-full bg-gradient-to-r from-gold-500 to-gold-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                {{ strtoupper(substr(auth()->user()->full_name, 0, 2)) }}
            </div>
            <div x-show="!sidebarCollapsed" class="min-w-0 sidebar-content">
                <p class="text-emerald-800 dark:text-emerald-200 text-xs font-semibold truncate">{{ auth()->user()->full_name }}</p>
                <p class="text-emerald-600 dark:text-emerald-400 text-xs truncate">{{ auth()->user()->role->name }}</p>
                @if(auth()->user()->role->abbreviation)
                    <p class="text-emerald-500 dark:text-emerald-500 text-xs truncate">{{ auth()->user()->role->abbreviation }}</p>
                @endif
            </div>
        </div>
    @endauth
</aside>

{{-- Mobile overlay --}}
<div x-show="sidebarOpen"
     x-transition.opacity.duration.200
     @click="sidebarOpen = false"
     class="fixed inset-0 bg-black/40 backdrop-blur-sm z-30 lg:hidden"></div>

{{-- ══════════════════════ MAIN (topbar gold accents) ══════════════════════ --}}
<div class="flex-1 flex flex-col min-h-screen transition-all duration-300"
     :class="sidebarCollapsed ? 'lg:ml-16' : 'lg:ml-64'">

    {{-- Topbar with gold border and hover --}}
    <nav class="bg-emerald-50 dark:bg-emerald-950 border-b border-gold-300 dark:border-gold-800 h-14 flex items-center
                justify-between px-5 fixed top-0 left-0 right-0 z-50 shadow-sm"
         :class="sidebarCollapsed ? 'lg:left-16' : 'lg:left-64'">
        <div class="flex items-center gap-3">
            <button @click="sidebarOpen = !sidebarOpen"
                    class="text-gold-600 dark:text-gold-400 hover:text-gold-800 dark:hover:text-gold-200 transition-colors lg:hidden">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <span class="font-semibold text-sm text-emerald-800 dark:text-emerald-200">@yield('page-title', 'Dashboard')</span>
        </div>
        <div class="flex items-center gap-3">
            <button @click="$store.theme.toggle()"
                    class="w-8 h-8 rounded-lg flex items-center justify-center text-gold-600 dark:text-gold-400
                           hover:bg-gold-500 hover:text-white dark:hover:bg-gold-600 transition-colors"
                    :title="$store.theme.dark ? 'Switch to light mode' : 'Switch to dark mode'">
                <svg x-show="$store.theme.dark" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <svg x-show="!$store.theme.dark" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                </svg>
            </button>
            @auth
                <span class="text-sm text-emerald-700 dark:text-emerald-300 hidden sm:block">{{ auth()->user()->full_name }}</span>
                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300">
                    {{ auth()->user()->role->name }}
                </span>
                @if(auth()->user()->role->abbreviation)
                    <span class="px-2 py-0.5 rounded-full text-xs font-mono bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 hidden sm:inline-block">
                        {{ auth()->user()->role->abbreviation }}
                    </span>
                @endif
            @endauth
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="text-xs border border-gold-300 dark:border-gold-700 text-gold-700 dark:text-gold-300
                               px-3 py-1.5 rounded-lg hover:bg-gold-500 hover:text-white dark:hover:bg-gold-600 transition-colors">
                    Logout
                </button>
            </form>
        </div>
    </nav>

    {{-- Page content --}}
    <main class="flex-1 pt-14 p-6 page-transition">
        @yield('content')
    </main>
</div>

{{-- Notification container --}}
<div id="notification-container"
     class="fixed top-4 left-1/2 -translate-x-1/2 z-50 w-full max-w-md pointer-events-none"></div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.store('theme', {
            dark: localStorage.getItem('dark') === 'true',
            toggle() {
                this.dark = !this.dark;
                localStorage.setItem('dark', this.dark);
                document.documentElement.classList.toggle('dark', this.dark);
            },
            init() { document.documentElement.classList.toggle('dark', this.dark); },
        });
        Alpine.store('theme').init();
    });

    document.addEventListener('DOMContentLoaded', () => {
        const meta = document.querySelector('meta[name="flash-data"]');
        if (!meta) return;
        try {
            const flash = JSON.parse(meta.content);
            Object.entries(flash).forEach(([type, message]) => {
                setTimeout(() => showNotification(message, type), 300);
            });
        } catch (e) {}
    });

    function showNotification(message, type = 'success') {
        const container = document.getElementById('notification-container');
        if (!container) return;
        const colors = {
            success: 'bg-emerald-600',
            error:   'bg-red-600',
            warning: 'bg-yellow-600',
            info:    'bg-blue-600',
        };
        const el = document.createElement('div');
        el.className = `pointer-events-auto ${colors[type] ?? colors.success} text-white text-sm font-medium px-4 py-3 rounded-xl shadow-lg mb-2 flex items-center gap-2 transition-all duration-300`;
        el.innerHTML = `
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="${type === 'error' ? 'M6 18L18 6M6 6l12 12' : 'M5 13l4 4L19 7'}"/>
            </svg>
            <span>${message}</span>
        `;
        container.appendChild(el);
        setTimeout(() => { el.style.opacity = '0'; setTimeout(() => el.remove(), 300); }, 3500);
    }
</script>

@stack('scripts')
</body>
</html>