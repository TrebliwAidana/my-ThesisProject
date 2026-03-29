<!DOCTYPE html>
<html lang="en"
    x-data="{
        sidebarOpen: false,
        activeRoute: '{{ Route::currentRouteName() }}',
        adminOpen: {{ Str::startsWith(Route::currentRouteName(), 'admin.') || in_array(Route::currentRouteName(), ['settings.index','audit.logs']) ? 'true' : 'false' }}
    }"
    :class="$store.theme.dark ? 'dark' : ''"
>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'VSULHS_SSLG')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <script>
        // Initialize theme before page loads to prevent flash
        (function () {
            const darkMode = localStorage.getItem('dark') === 'true';
            if (darkMode) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
</head>

<body class="bg-gray-50 dark:bg-gray-900 font-sans min-h-screen flex transition-colors duration-200">

{{-- ═══════════════════════════ SIDEBAR ═══════════════════════════ --}}
<aside
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    class="bg-white dark:bg-gray-800 fixed z-40 top-0 left-0 w-64 h-full transform transition-transform duration-200 lg:translate-x-0 flex flex-col border-r border-gray-200 dark:border-gray-700"
>
    {{-- Brand --}}
    <div class="flex items-center justify-between h-14 px-5 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
        <div>
            <span class="text-indigo-600 dark:text-indigo-400 font-semibold text-sm tracking-wide block">VSULHS_SSLG</span>
            <span class="text-gray-500 dark:text-gray-400 text-xs block mt-0.5">Student Gov Portal</span>
        </div>
        <button @click="sidebarOpen = false" class="lg:hidden text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    {{-- Nav --}}
    <nav class="flex-1 py-3 overflow-y-auto">
        @php
            $user = auth()->user();
            $userRole = $user->role->name ?? 'Member';
            
            // Menu items with role-based permissions
            $menuItems = [
                // Dashboard - All roles can see
                [
                    'label' => 'Dashboard', 
                    'route' => 'dashboard', 
                    'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', 
                    'roles' => ['Adviser', 'Officer', 'Auditor', 'Member']
                ],
                
                // Members - Adviser, Officer, Auditor can view
                [
                    'label' => 'Members', 
                    'route' => 'members.index', 
                    'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 
                    'roles' => ['Adviser', 'Officer', 'Auditor']
                ],
                
                // Documents - Adviser, Officer, Auditor can view
                [
                    'label' => 'Documents', 
                    'route' => 'documents.index', 
                    'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 
                    'roles' => ['Adviser', 'Officer', 'Auditor']
                ],
                
                // Budgets - Adviser, Officer, Auditor can view
                [
                    'label' => 'Budgets', 
                    'route' => 'budgets.index', 
                    'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 
                    'roles' => ['Adviser', 'Officer', 'Auditor']
                ],
            ];
            
            // Administration menu - Adviser only
            $adminMenu = [
                'label' => 'Administration',
                'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
                'roles' => ['Adviser'],
                'submenu' => [
                    ['label' => 'User Management', 'route' => 'admin.users.index', 'roles' => ['Adviser']],
                    ['label' => 'Roles', 'route' => 'admin.roles.index', 'roles' => ['Adviser']],
                    ['label' => 'Permissions', 'route' => 'admin.permissions.index', 'roles' => ['Adviser']],
                    ['label' => 'System Settings', 'route' => 'settings.index', 'roles' => ['Adviser']],
                    ['label' => 'Audit Logs', 'route' => 'audit.logs', 'roles' => ['Adviser']],
                ]
            ];
            
            // Filter menu items based on user role
            $filteredMenu = [];
            foreach ($menuItems as $item) {
                if (in_array($userRole, $item['roles'])) {
                    $filteredMenu[] = $item;
                }
            }
            
            // My Profile menu - All roles can see
            $profileMenu = [
                'label' => 'My Profile',
                'route' => 'profile.index',
                'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
                'roles' => ['Adviser', 'Officer', 'Auditor', 'Member']
            ];
            
            // Add My Profile to filtered menu (all roles)
            $filteredMenu[] = $profileMenu;
        @endphp

        {{-- Regular Menu Items --}}
        @foreach($filteredMenu as $item)
            <a href="{{ route($item['route']) }}"
               :class="activeRoute === '{{ $item['route'] }}' ? 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'"
               class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium transition-colors rounded-lg mx-2 my-1">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/>
                </svg>
                {{ $item['label'] }}
            </a>
        @endforeach

        {{-- Administration Menu (Adviser only) --}}
        @if(in_array($userRole, $adminMenu['roles']))
        <div class="mt-2">
            <button
                @click="adminOpen = !adminOpen"
                :class="adminOpen ? 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'"
                class="w-full flex items-center justify-between px-4 py-2.5 text-sm font-medium transition-colors rounded-lg mx-2"
            >
                <span class="flex items-center gap-3">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $adminMenu['icon'] }}"/>
                    </svg>
                    {{ $adminMenu['label'] }}
                </span>
                <svg :class="adminOpen ? 'rotate-90' : ''" class="w-3 h-3 transition-transform opacity-50"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
            <div x-show="adminOpen"
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 -translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0">
                @foreach($adminMenu['submenu'] as $sub)
                    <a href="{{ route($sub['route']) }}"
                       :class="activeRoute === '{{ $sub['route'] }}' ? 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700'"
                       class="flex items-center gap-2.5 pl-11 pr-4 py-2 text-xs font-medium transition-colors rounded-lg mx-2 my-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-current opacity-50 flex-shrink-0"></span>
                        {{ $sub['label'] }}
                    </a>
                @endforeach
            </div>
        </div>
        @endif
    </nav>

    {{-- User footer --}}
    <div class="border-t border-gray-200 dark:border-gray-700 px-4 py-3 flex items-center gap-3 flex-shrink-0">
        <div class="w-8 h-8 rounded-full bg-gradient-to-r from-indigo-500 to-indigo-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
            {{ strtoupper(substr($user->full_name, 0, 2)) }}
        </div>
        <div class="min-w-0">
            <p class="text-gray-700 dark:text-gray-200 text-xs font-semibold truncate">{{ $user->full_name }}</p>
            <p class="text-gray-500 dark:text-gray-400 text-xs truncate">{{ $user->role->name }}</p>
        </div>
    </div>
</aside>

{{-- Mobile overlay --}}
<div x-show="sidebarOpen" @click="sidebarOpen = false"
     class="fixed inset-0 bg-black/40 z-30 lg:hidden"></div>

{{-- ═══════════════════════════ MAIN ═══════════════════════════ --}}
<div class="flex-1 flex flex-col min-h-screen lg:ml-64">

    {{-- Topbar --}}
    <nav class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 h-14 flex items-center justify-between px-5
                fixed top-0 left-0 right-0 z-50 lg:left-64">
        <div class="flex items-center gap-3">
            <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 lg:hidden">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <span class="font-semibold text-sm text-gray-800 dark:text-gray-200">@yield('page-title', 'Dashboard')</span>
        </div>

        <div class="flex items-center gap-3">
            <button
                @click="$store.theme.toggle()"
                class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-500 dark:text-gray-400
                       hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                :title="$store.theme.dark ? 'Switch to light mode' : 'Switch to dark mode'"
            >
                <svg x-show="$store.theme.dark" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <svg x-show="!$store.theme.dark" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                </svg>
            </button>

            <span class="text-sm text-gray-600 dark:text-gray-400 hidden sm:block">{{ $user->full_name }}</span>
            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-400">
                {{ $user->role->name }}
            </span>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="text-xs border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400 px-3 py-1.5 rounded-lg
                               hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                    Logout
                </button>
            </form>
        </div>
    </nav>

    <main class="flex-1 pt-14 p-6">
        @yield('content')
    </main>
</div>

{{-- Global Notification Container --}}
@if(!request()->routeIs('profile.*'))
<div id="notification-container" class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50 w-full max-w-md pointer-events-none">
    <div class="pointer-events-auto space-y-2">
        @if(session('success'))
            <x-notification type="success" message="{!! session('success') !!}" />
        @endif

        @if(session('error'))
            <x-notification type="error" message="{!! session('error') !!}" />
        @endif

        @if(session('warning'))
            <x-notification type="warning" message="{!! session('warning') !!}" />
        @endif

        @if(session('info'))
            <x-notification type="info" message="{!! session('info') !!}" />
        @endif
    </div>
</div>
@endif

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.store('theme', {
            dark: localStorage.getItem('dark') === 'true',
            toggle() {
                this.dark = !this.dark;
                localStorage.setItem('dark', this.dark);
                document.documentElement.classList.toggle('dark', this.dark);
            },
            init() {
                if (this.dark) {
                    document.documentElement.classList.add('dark');
                }
            }
        });
        
        // Initialize the store
        Alpine.store('theme').init();
    });
</script>

</body>
</html>