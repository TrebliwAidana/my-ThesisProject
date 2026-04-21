<!DOCTYPE html>
<html lang="en" :class="$store.theme.dark ? 'dark' : ''" x-data>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>@yield('title', 'VSULHS SSLG')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet">

    {{-- Flash Notifications (corrected JSON output) --}}
       @if(session()->hasAny(['success','error','warning','info']))
        @php
            $flashData = [
                'success' => session('success'),
                'error'   => session('error'),
                'warning' => session('warning'),
                'info'    => session('info'),
            ];
        @endphp
        <meta name="flash-data" content="{{ json_encode($flashData) }}">
    @endif

    <style>
        /* ─────────────────────────────────────────────────────────────
           MODERN DESIGN TOKENS (emerald + gold)
        ───────────────────────────────────────────────────────────── */
        :root {
            --emerald: #059669;
            --emerald-dark: #047857;
            --emerald-light: #10B981;
            --gold: #D4AF37;
            --gold-dark: #B8942E;
            --gold-light: #E6C358;
            --surface: #FFFFFF;
            --surface-dark: #0F172A;
            --text: #1E293B;
            --text-dark: #E2E8F0;
            --border: #E2E8F0;
            --border-dark: #334155;
        }
        .dark {
            --emerald: #10B981;
            --emerald-dark: #059669;
            --surface: #0F172A;
            --text: #E2E8F0;
            --border: #334155;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #F1F5F9 0%, #E2E8F0 100%);
            color: var(--text);
            transition: background 0.3s, color 0.3s;
        }
        .dark body {
            background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%);
        }
        /* glassmorphism */
        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }
        .dark .glass {
            background: rgba(15, 23, 42, 0.7);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        /* sidebar */
        .sidebar {
            background: var(--surface);
            border-right: 1px solid var(--border);
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.625rem 1rem;
            margin: 0.25rem 0.75rem;
            border-radius: 0.75rem;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s ease;
            color: #475569;
        }
        .dark .sidebar-link {
            color: #94A3B8;
        }
        .sidebar-link:hover {
            background: var(--gold);
            color: white;
            transform: translateX(4px);
        }
        .sidebar-link.active {
            background: var(--emerald);
            color: white;
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3);
        }
        .sidebar-sub-link {
            display: flex;
            align-items: center;
            gap: 0.625rem;
            padding: 0.5rem 1rem 0.5rem 2.5rem;
            margin: 0.25rem 0.75rem;
            border-radius: 0.75rem;
            font-size: 0.75rem;
            font-weight: 500;
            transition: all 0.2s ease;
            color: #64748B;
        }
        .dark .sidebar-sub-link {
            color: #94A3B8;
        }
        .sidebar-sub-link:hover {
            background: var(--gold);
            color: white;
            transform: translateX(4px);
        }
        .sidebar-sub-link.active {
            background: var(--emerald);
            color: white;
        }
        /* sidebar tooltip for collapsed state */
        .sidebar-tooltip {
            position: absolute;
            left: calc(100% + 8px);
            top: 50%;
            transform: translateY(-50%);
            background: #1E293B;
            color: #fff;
            font-size: 0.75rem;
            white-space: nowrap;
            padding: 4px 10px;
            border-radius: 6px;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.15s ease;
            z-index: 999;
        }
        .dark .sidebar-tooltip {
            background: #334155;
        }
        .sidebar-link:hover .sidebar-tooltip,
        .sidebar-sub-link:hover .sidebar-tooltip {
            opacity: 1;
        }
        /* custom scrollbar */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: var(--border); border-radius: 10px; }
        ::-webkit-scrollbar-thumb { background: var(--gold); border-radius: 10px; }
        /* animations */
        @keyframes fadeSlideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .page-transition {
            animation: fadeSlideUp 0.4s ease-out forwards;
        }
    </style>

    @stack('styles')
</head>

<body
    x-data="{
        sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true',
        mobileMenuOpen: false,
        adminOpen: {{ Str::startsWith(Route::currentRouteName() ?? '', 'admin.') || in_array(Route::currentRouteName() ?? '', ['admin.auditlogs.index']) ? 'true' : 'false' }},
        activeRoute: '{{ Route::currentRouteName() ?? '' }}',
        userDropdownOpen: false
    }"
    :class="sidebarCollapsed ? 'sidebar-collapsed' : ''"
    class="antialiased"
    x-init="() => {
        $watch('sidebarCollapsed', value => {
            localStorage.setItem('sidebarCollapsed', value);
            document.body.classList.toggle('sidebar-collapsed', value);
        });
    }"
>

{{-- ══════════════════════ SIDEBAR ══════════════════════ --}}
<aside
    :class="mobileMenuOpen ? 'translate-x-0' : '-translate-x-full'"
    class="sidebar fixed top-0 left-0 h-full z-40 transform transition-transform duration-300 lg:translate-x-0 flex flex-col shadow-xl"
    :style="sidebarCollapsed ? 'width: 4.5rem' : 'width: 16rem'"
    aria-label="Main navigation"
>
    {{-- Brand --}}
    <div class="flex items-center h-16 px-4 border-b border-border dark:border-gray-800 flex-shrink-0"
         :class="sidebarCollapsed ? 'justify-center' : 'justify-start'">
        <div x-show="!sidebarCollapsed" class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center shadow-sm overflow-hidden bg-transparent">
                <img src="{{ asset('images/vsulhs_logo.png') }}" alt="VSULHS Logo" class="w-full h-full object-contain">
            </div>
            <div>
                <span class="font-serif font-semibold text-emerald-700 dark:text-emerald-300 text-sm">VSULHS SSLG</span>
                <span class="text-emerald-600 dark:text-emerald-400 text-xs block">Student Gov Portal</span>
            </div>
        </div>
        <div x-show="sidebarCollapsed" class="w-8 h-8 rounded-lg flex items-center justify-center shadow-sm overflow-hidden bg-transparent">
            <img src="{{ asset('images/vsulhs_logo.png') }}" alt="VSULHS Logo" class="w-full h-full object-contain">
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 py-4 overflow-y-auto" aria-label="Sidebar">
        @php
            $user = auth()->user();
            $userRole = $user?->role->name ?? 'Guest';
            $isGuest = $user && $user->email === 'guest@gmail.com';

            $canSee = function(array $roles, string $permission = '') use ($user, $userRole, $isGuest) {
                if (!$user) return false;
                if ($isGuest) return false;
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
                    'visible' => $canSee(['System Administrator','Supreme Admin','Supreme Officer','Org Admin','Org Officer','Club Adviser'], 'members.view'),
                ],
                [
                    'label'   => 'Aprroved Financial Reports',
                    'route'   => 'documents.index',
                    'icon'    => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                    'visible' => $isGuest || $canSee(['System Administrator','Supreme Admin','Supreme Officer','Org Admin','Org Officer','Club Adviser'], 'documents.view'),
                ],
                [
                    'label'   => 'Financial Records',
                    'route'   => 'financial.index',
                    'icon'    => 'M9 7h6m0 10v-3m-6 3v-3m-6 3h18M3 5h18a2 2 0 012 2v10a2 2 0 01-2 2H3a2 2 0 01-2-2V7a2 2 0 012-2z',
                    'visible' => $isGuest || $canSee(['System Administrator','Supreme Admin','Supreme Officer','Org Admin','Org Officer','Club Adviser'], 'financial.view'),
                ],
            ];

            $adminMenu = [
                'label' => 'Administration',
                'icon'  => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
                'submenu' => [
                    ['label' => 'User Management', 'route' => 'admin.users.index',               'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',                                                                                            'visible' => $canSee(['System Administrator',], 'admin.users')],
                    ['label' => 'Roles',            'route' => 'admin.roles.index',               'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',          'visible' => $canSee(['System Administrator'], 'admin.roles')],
                    ['label' => 'Permissions',      'route' => 'admin.permissions.index',         'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',          'visible' => $canSee(['System Administrator'], 'admin.permissions')],
                    ['label' => 'Audit Logs',       'route' => 'admin.auditlogs.index',           'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',                                                                                                                                                             'visible' => $canSee(['System Administrator'], 'audit.view')],
                    ['label' => 'Backup & Restore',  'route' => 'admin.document-backups.index',   'icon' => '',                                                                                                                                                                                                        'visible' => $canSee(['System Administrator', 'admin.index' ])],
                    ['label' => 'Doc Categories',   'route' => 'admin.document-categories.index', 'icon' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a2 2 0 012-2h2z',                                                                                  'visible' => $canSee(['System Administrator'], 'admin.document-categories')],
                
                ],
            ];
            $adminMenu['visible'] = !$isGuest && collect($adminMenu['submenu'])->contains('visible', true);

            $profileItem = [
                'label'   => 'My Profile',
                'route'   => 'profile.index',
                'icon'    => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
                'visible' => (bool) $user && !$isGuest,
            ];

            $isActive = fn(string $routeName) =>
                Route::is($routeName) || Route::is($routeName . '.*');
        @endphp

        @if($user)
            @foreach($menuItems as $item)
                @if($item['visible'])
                    <a href="{{ route($item['route']) }}"
                       class="sidebar-link relative {{ $isActive($item['route']) ? 'active' : '' }}"
                       aria-current="{{ $isActive($item['route']) ? 'page' : 'false' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/>
                        </svg>
                        <span x-show="!sidebarCollapsed" class="whitespace-nowrap">{{ $item['label'] }}</span>
                        <span x-show="sidebarCollapsed" class="sidebar-tooltip" aria-hidden="true">{{ $item['label'] }}</span>
                    </a>
                @endif
            @endforeach

            @if($profileItem['visible'])
                <a href="{{ route($profileItem['route']) }}"
                   class="sidebar-link relative {{ $isActive($profileItem['route']) ? 'active' : '' }}"
                   aria-current="{{ $isActive($profileItem['route']) ? 'page' : 'false' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $profileItem['icon'] }}"/>
                    </svg>
                    <span x-show="!sidebarCollapsed">{{ $profileItem['label'] }}</span>
                    <span x-show="sidebarCollapsed" class="sidebar-tooltip" aria-hidden="true">{{ $profileItem['label'] }}</span>
                </a>
            @endif

            @if($adminMenu['visible'])
                <div class="mt-2">
                    <button @click="adminOpen = !adminOpen"
                            :aria-expanded="adminOpen.toString()"
                            aria-controls="admin-submenu"
                            :class="adminOpen ? 'active' : ''"
                            class="sidebar-link w-full justify-between relative">
                        <span class="flex items-center gap-3">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $adminMenu['icon'] }}"/>
                            </svg>
                            <span x-show="!sidebarCollapsed">{{ $adminMenu['label'] }}</span>
                        </span>
                        <svg x-show="!sidebarCollapsed" :class="adminOpen ? 'rotate-90' : ''" class="w-3 h-3 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                        <span x-show="sidebarCollapsed" class="sidebar-tooltip" aria-hidden="true">{{ $adminMenu['label'] }}</span>
                    </button>

                    <div x-show="adminOpen" x-collapse id="admin-submenu">
                        @foreach($adminMenu['submenu'] as $sub)
                            @if($sub['visible'])
                                <a href="{{ route($sub['route']) }}"
                                   class="sidebar-sub-link relative {{ $isActive($sub['route']) ? 'active' : '' }}"
                                   aria-current="{{ $isActive($sub['route']) ? 'page' : 'false' }}">
                                    @if(isset($sub['icon']))
                                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sub['icon'] }}"/>
                                        </svg>
                                    @else
                                        <span class="w-1.5 h-1.5 rounded-full bg-current opacity-50"></span>
                                    @endif
                                    <span x-show="!sidebarCollapsed">{{ $sub['label'] }}</span>
                                    <span x-show="sidebarCollapsed" class="sidebar-tooltip" aria-hidden="true">{{ $sub['label'] }}</span>
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
        <div class="border-t border-border dark:border-gray-800 p-3 flex items-center gap-3"
             :class="sidebarCollapsed ? 'justify-center' : 'justify-start'">
            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-gold to-gold-dark flex items-center justify-center text-white text-xs font-bold shadow-md flex-shrink-0">
                {{ strtoupper(substr(auth()->user()->full_name, 0, 2)) }}
            </div>
            <div x-show="!sidebarCollapsed" class="min-w-0">
                <p class="text-emerald-800 dark:text-emerald-200 text-xs font-semibold truncate">{{ auth()->user()->full_name }}</p>
                <p class="text-emerald-600 dark:text-emerald-400 text-xs">{{ auth()->user()->role->name }}</p>
            </div>
        </div>
    @endauth
</aside>

{{-- Mobile overlay --}}
<div x-show="mobileMenuOpen" x-transition.opacity.duration.200 @click="mobileMenuOpen = false" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-30 lg:hidden"></div>

{{-- ══════════════════════ MAIN CONTENT AREA ══════════════════════ --}}
<div class="flex-1 flex flex-col min-h-screen transition-all duration-300"
     :class="sidebarCollapsed ? 'lg:ml-[4.5rem]' : 'lg:ml-64'">

    {{-- Glassmorphic Top Bar --}}
    <nav id="topbar"
         class="glass fixed top-0 left-0 right-0 z-50 flex items-center justify-between px-4 md:px-6 shadow-sm"
         :class="sidebarCollapsed ? 'lg:left-[4.5rem]' : 'lg:left-64'"
         style="min-height: 64px; padding-top: 8px; padding-bottom: 8px;"
         aria-label="Top navigation">

        <div class="flex items-center gap-3 flex-nowrap">
            <button @click="window.innerWidth < 1024 ? mobileMenuOpen = !mobileMenuOpen : sidebarCollapsed = !sidebarCollapsed"
                    :aria-expanded="(window.innerWidth < 1024 ? mobileMenuOpen : !sidebarCollapsed).toString()"
                    aria-controls="main-nav"
                    class="text-gray-600 dark:text-gray-300 hover:text-gold transition-colors focus:outline-none focus:ring-2 focus:ring-gold rounded-lg p-1 flex-shrink-0"
                    :title="sidebarCollapsed ? 'Expand sidebar' : 'Collapse sidebar'">
                <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <span class="font-serif font-semibold text-base md:text-lg text-emerald-700 dark:text-emerald-300 whitespace-nowrap">@yield('page-title', 'Dashboard')</span>
        </div>

        <div class="flex items-center gap-2 md:gap-3 flex-nowrap">
            <button @click="$store.theme.toggle()"
                    class="w-8 h-8 md:w-9 md:h-9 rounded-full flex items-center justify-center text-gold-600 hover:bg-gold/10 transition-colors flex-shrink-0"
                    :aria-label="$store.theme.dark ? 'Switch to light mode' : 'Switch to dark mode'">
                <svg x-show="$store.theme.dark" class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <svg x-show="!$store.theme.dark" class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                </svg>
            </button>

            @auth
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open"
                        :aria-expanded="open.toString()"
                        aria-controls="user-dropdown-menu"
                        aria-haspopup="true"
                        class="flex items-center gap-1 md:gap-2 px-2 md:px-3 py-1.5 rounded-full bg-emerald-100 dark:bg-emerald-900/50 hover:bg-gold/20 transition-colors flex-nowrap">
                    <div class="w-7 h-7 rounded-full bg-gradient-to-br from-gold to-gold-dark flex items-center justify-center text-white text-xs font-bold flex-shrink-0" aria-hidden="true">
                        {{ strtoupper(substr(auth()->user()->full_name, 0, 2)) }}
                    </div>
                    <span class="hidden sm:inline text-sm font-medium text-emerald-700 dark:text-emerald-300 max-w-[100px] md:max-w-[150px] truncate">
                        {{ auth()->user()->full_name }}
                    </span>
                    <svg class="w-3 h-3 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="open"
                     @click.away="open = false"
                     x-transition
                     id="user-dropdown-menu"
                     role="menu"
                     class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 z-50 overflow-hidden">

                    {{-- Profile link – hidden for guest --}}
                    @if(auth()->user()->email !== 'guest@gmail.com')
                        <a href="{{ route('profile.index') }}"
                           role="menuitem"
                           class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-emerald-100 dark:hover:bg-gold/20 hover:text-emerald-700 dark:hover:text-white transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Profile
                        </a>
                        <hr class="border-gray-200 dark:border-gray-700">
                    @endif

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                role="menuitem"
                                class="flex w-full items-center gap-2 px-4 py-2.5 text-sm text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/50 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
            @endauth
        </div>
    </nav>

    <main id="main-content" class="flex-1 px-4 md:px-6 pb-8 page-transition">
        @yield('content')
    </main>
</div>

{{-- ══════════════════════ NOTIFICATION CONTAINER ══════════════════════ --}}
<div id="notification-container"
     class="fixed top-20 right-6 z-50 space-y-2 w-80 pointer-events-none"
     aria-live="polite"
     aria-atomic="true"></div>

{{-- ══════════════════════ SCRIPTS ══════════════════════ --}}
<script>
    // Define Alpine theme store BEFORE Alpine initializes
    document.addEventListener('alpine:init', () => {
        Alpine.store('theme', {
            dark: localStorage.getItem('dark') === 'true',
            toggle() {
                this.dark = !this.dark;
                localStorage.setItem('dark', this.dark);
                document.documentElement.classList.toggle('dark', this.dark);
            },
            init() {
                document.documentElement.classList.toggle('dark', this.dark);
            }
        });
        Alpine.store('theme').init();
    });

    function adjustMainPadding() {
        requestAnimationFrame(() => {
            const topbar = document.getElementById('topbar');
            const main   = document.getElementById('main-content');
            if (topbar && main) {
                main.style.paddingTop = topbar.offsetHeight + 'px';
            }
        });
    }

    window.addEventListener('load', adjustMainPadding);
    window.addEventListener('resize', adjustMainPadding);
    document.addEventListener('DOMContentLoaded', () => {
        adjustMainPadding();
        const topbar = document.getElementById('topbar');
        if (topbar) {
            new MutationObserver(adjustMainPadding)
                .observe(topbar, { attributes: true, attributeFilter: ['class', 'style'] });
        }
    });
</script>

@stack('scripts')
</body>
</html>