<!DOCTYPE html>
<html lang="en" :class="$store.theme.dark ? 'dark' : ''" x-data>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>@yield('title', 'VSULHS SSLG')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Alpine Collapse plugin MUST load before Alpine core --}}
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet">

    {{-- Flash data for JS toast system --}}
    @if(session()->hasAny(['success','error','warning','info']))
        @php
            $flashData = array_filter([
                'success' => session('success'),
                'error'   => session('error'),
                'warning' => session('warning'),
                'info'    => session('info'),
            ]);
        @endphp
        <meta name="flash-data" content="{{ json_encode($flashData) }}">
    @endif

    <style>
        /* ═══════════════════════════════════════════════════════════
           DESIGN TOKENS
        ═══════════════════════════════════════════════════════════ */
        :root {
            --emerald: #059669;
            --emerald-dark: #047857;
            --emerald-light: #10B981;
            --gold: #D4AF37;
            --gold-dark: #B8942E;
            --gold-light: #E6C358;
            --surface: #FFFFFF;
            --text: #1E293B;
            --border: #E2E8F0;
        }
        html.dark {
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
        html.dark body { background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%); }

        /* ── Glassmorphism ── */
        .glass {
            background: rgba(255,255,255,0.7);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }
        html.dark .glass {
            background: rgba(15,23,42,0.7);
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }

        /* ── Sidebar ── */
        .sidebar {
            background: var(--surface);
            border-right: 1px solid var(--border);
            transition: width 0.3s cubic-bezier(0.4,0,0.2,1);
        }
        .sidebar-link {
            display: flex; align-items: center; gap: 0.75rem;
            padding: 0.625rem 1rem; margin: 0.25rem 0.75rem;
            border-radius: 0.75rem; font-size: 0.875rem; font-weight: 500;
            transition: all 0.2s ease; color: #475569; position: relative;
        }
        html.dark .sidebar-link { color: #94A3B8; }
        .sidebar-link:hover { background: var(--gold); color: white; transform: translateX(4px); }
        .sidebar-link.active { background: var(--emerald); color: white; box-shadow: 0 4px 12px rgba(5,150,105,0.3); }

        .sidebar-sub-link {
            display: flex; align-items: center; gap: 0.625rem;
            padding: 0.5rem 1rem 0.5rem 2.5rem; margin: 0.25rem 0.75rem;
            border-radius: 0.75rem; font-size: 0.75rem; font-weight: 500;
            transition: all 0.2s ease; color: #64748B; position: relative;
        }
        html.dark .sidebar-sub-link { color: #94A3B8; }
        .sidebar-sub-link:hover { background: var(--gold); color: white; transform: translateX(4px); }
        .sidebar-sub-link.active { background: var(--emerald); color: white; }

        /* ── Section label ── */
        .sidebar-section-label {
            font-size: 0.65rem; font-weight: 600; letter-spacing: 0.08em;
            text-transform: uppercase; color: #94A3B8;
            padding: 0.75rem 1.75rem 0.25rem;
        }
        html.dark .sidebar-section-label { color: #475569; }

        /* ── Collapsed tooltip ── */
        .sidebar-tooltip {
            position: absolute; left: calc(100% + 10px); top: 50%;
            transform: translateY(-50%); background: #1E293B; color: #fff;
            font-size: 0.72rem; white-space: nowrap; padding: 5px 10px;
            border-radius: 6px; pointer-events: none; opacity: 0;
            transition: opacity 0.15s ease; z-index: 9999;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
        .sidebar-tooltip::before {
            content:''; position:absolute; right:100%; top:50%;
            transform:translateY(-50%); border:5px solid transparent;
            border-right-color: #1E293B;
        }
        html.dark .sidebar-tooltip { background: #334155; }
        html.dark .sidebar-tooltip::before { border-right-color: #334155; }
        .sidebar-link:hover .sidebar-tooltip,
        .sidebar-sub-link:hover .sidebar-tooltip { opacity: 1; }

        /* ── Scrollbar ── */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: var(--border); border-radius: 10px; }
        ::-webkit-scrollbar-thumb { background: var(--gold); border-radius: 10px; }

        /* ── Page fade-in ── */
        @keyframes fadeSlideUp {
            from { opacity:0; transform:translateY(16px); }
            to   { opacity:1; transform:translateY(0); }
        }
        .page-transition { animation: fadeSlideUp 0.35s ease-out forwards; }

        /* ── Top progress bar ── */
        #nav-progress {
            position:fixed; top:0; left:0; height:3px; width:0%;
            background: linear-gradient(90deg, var(--emerald), var(--gold));
            z-index:9999; border-radius:0 2px 2px 0; opacity:0;
            pointer-events:none; transition: width 0.1s linear, opacity 0.3s ease;
        }

        /* ══════════════════════════════════════════════════════════
           UNIVERSAL NAVIGATION SKELETON OVERLAY
        ══════════════════════════════════════════════════════════ */
        @keyframes shimmer {
            0%   { background-position: -900px 0; }
            100% { background-position:  900px 0; }
        }
        #nav-skeleton {
            position:fixed; top:64px; left:0; right:0; bottom:0; z-index:48;
            background: var(--surface); overflow-y:auto;
            display:none; flex-direction:column; gap:0;
            padding: 1.5rem 1.5rem 2rem;
            opacity:1; transition: opacity 0.2s ease;
        }
        #nav-skeleton.sidebar-expanded  { left: 16rem; }
        #nav-skeleton.sidebar-collapsed { left: 4.5rem; }
        @media (max-width:1023px) {
            #nav-skeleton.sidebar-expanded,
            #nav-skeleton.sidebar-collapsed { left: 0; }
        }
        .sk {
            background: linear-gradient(90deg,#e2e8f0 25%,#f8fafc 50%,#e2e8f0 75%);
            background-size: 900px 100%;
            animation: shimmer 1.5s ease-in-out infinite;
            border-radius: 0.5rem; flex-shrink:0;
        }
        html.dark .sk {
            background: linear-gradient(90deg,#1e293b 25%,#334155 50%,#1e293b 75%);
            background-size: 900px 100%;
        }
        .sk-line   { height:.75rem; border-radius:9999px; }
        .sk-line.h-sm { height:.55rem; }
        .sk-line.h-lg { height:1.1rem; }
        .sk-line.h-xl { height:1.6rem; }
        .sk-circle { border-radius:9999px; }
        .sk-rect   { border-radius:.75rem; }
        .sk-btn    { height:2.25rem; border-radius:.625rem; }
        .sk-badge  { height:1.4rem; width:4.5rem; border-radius:9999px; }
        .sk-page-title { display:flex; align-items:center; justify-content:space-between; margin-bottom:1.5rem; gap:1rem; }
        .sk-stat-grid  { display:grid; grid-template-columns:repeat(auto-fill,minmax(180px,1fr)); gap:1rem; margin-bottom:1.5rem; }
        .sk-stat-card  { background:white; border:1px solid var(--border); border-radius:1rem; padding:1.25rem; display:flex; flex-direction:column; gap:.6rem; }
        html.dark .sk-stat-card { background:#1e293b; border-color:#334155; }
        .sk-stat-card .sk-icon { width:2.25rem; height:2.25rem; border-radius:.625rem; }
        .sk-content-grid { display:grid; grid-template-columns:1fr 1fr; gap:1rem; flex:1; }
        @media (max-width:768px) { .sk-content-grid { grid-template-columns:1fr; } .sk-stat-grid { grid-template-columns:repeat(2,1fr); } }
        .sk-panel { background:white; border:1px solid var(--border); border-radius:1rem; overflow:hidden; display:flex; flex-direction:column; }
        html.dark .sk-panel { background:#1e293b; border-color:#334155; }
        .sk-panel-header { padding:1rem 1.25rem; border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; }
        html.dark .sk-panel-header { border-color:#334155; }
        .sk-table-row { display:grid; grid-template-columns:2.5rem 1fr 1fr 5rem; align-items:center; gap:1rem; padding:.875rem 1.25rem; border-bottom:1px solid var(--border); }
        html.dark .sk-table-row { border-color:#1e293b; }
        .sk-table-row:last-child { border-bottom:none; }

        /* ── Toast ── */
        #notification-container > div { transition: opacity 0.3s ease, transform 0.3s ease; }

        /* ════════════════════════════════════════════════════════════
           DARK MODE SIDEBAR TEXT VISIBILITY FIXES
           Improved contrast, sharper rendering, no blur effect
        ════════════════════════════════════════════════════════════ */
        html.dark .sidebar-link,
        html.dark .sidebar-sub-link {
            color: #F1F5F9 !important;        /* Bright, high-contrast slate-50 */
            font-weight: 500;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            text-rendering: geometricPrecision;
            letter-spacing: -0.01em;           /* subtle crispness */
        }
        
        /* Section labels (Main, Records, Account, Administration) */
        html.dark .sidebar-section-label {
            color: #A5B4FC !important;        /* Soft indigo tint for readability */
            font-weight: 600;
            letter-spacing: 0.07em;
            opacity: 1;
            -webkit-font-smoothing: antialiased;
        }
        
        /* Keep hover/active states consistent (gold or emerald backgrounds, white text) */
        .sidebar-link:hover,
        .sidebar-sub-link:hover,
        .sidebar-link.active,
        .sidebar-sub-link.active {
            color: white !important;            /* override any dark text on hover/active */
        }
        
        /* Additional safeguard for any text spans inside sidebar */
        html.dark .sidebar-link span,
        html.dark .sidebar-sub-link span,
        html.dark .sidebar-link svg + span,
        html.dark .sidebar-sub-link svg + span {
            color: inherit;
            font-weight: inherit;
        }
    </style>

    @stack('styles')
</head>

<body
    x-data="{
        sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true',
        mobileMenuOpen: false,
        adminOpen: {{ Str::startsWith(Route::currentRouteName() ?? '', 'admin.') ? 'true' : 'false' }},
        activeRoute: '{{ Route::currentRouteName() ?? '' }}',
        userDropdownOpen: false
    }"
    :class="sidebarCollapsed ? 'sidebar-collapsed' : ''"
    class="antialiased"
    x-init="() => {
        $watch('sidebarCollapsed', value => {
            localStorage.setItem('sidebarCollapsed', value);
            document.body.classList.toggle('sidebar-collapsed', value);
            window._updateSkeletonSidebar && window._updateSkeletonSidebar(value);
        });
    }"
>

<div id="nav-progress" aria-hidden="true"></div>

{{-- ══════════════════════ SIDEBAR ══════════════════════ --}}
<aside
    :class="mobileMenuOpen ? 'translate-x-0' : '-translate-x-full'"
    class="sidebar fixed top-0 left-0 h-full z-40 transform transition-transform duration-300 lg:translate-x-0 flex flex-col shadow-xl"
    :style="sidebarCollapsed ? 'width:4.5rem' : 'width:16rem'"
    aria-label="Main navigation"
>
    {{-- Brand --}}
    <div class="flex items-center h-16 px-4 border-b border-border dark:border-gray-800 flex-shrink-0"
         :class="sidebarCollapsed ? 'justify-center' : 'justify-start'">
        <div x-show="!sidebarCollapsed" class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center shadow-sm overflow-hidden">
                <img src="{{ asset('images/vsulhs_logo.png') }}" alt="VSULHS Logo" class="w-full h-full object-contain">
            </div>
            <div>
                <span class="font-serif font-semibold text-emerald-700 dark:text-emerald-300 text-sm">VSULHS SSLG</span>
                <span class="text-emerald-600 dark:text-emerald-400 text-xs block">Student Gov Portal</span>
            </div>
        </div>
        <div x-show="sidebarCollapsed" class="w-8 h-8 rounded-lg flex items-center justify-center shadow-sm overflow-hidden">
            <img src="{{ asset('images/vsulhs_logo.png') }}" alt="VSULHS Logo" class="w-full h-full object-contain">
        </div>
    </div>

    {{-- ── Navigation ─────────────────────────────────────────────────────── --}}
    <nav class="flex-1 py-4 overflow-y-auto" aria-label="Sidebar">
        @php
            $user = auth()->user();

            /*
             | ─────────────────────────────────────────────────────────────────
             | HOW SIDEBAR VISIBILITY WORKS
             | ─────────────────────────────────────────────────────────────────
             | Every item calls $user->hasPermission('slug') which:
             |   • Auto-returns true  for System Administrator (role.level === 1)
             |   • Checks role->permissions (cached 5 min) for everyone else
             |   • Accepts 'module.manage' as a wildcard that covers 'module.*'
             |
             | This means:
             |   - Giving a role 'members.view'  → shows Members link
             |   - Removing that permission      → link disappears automatically
             |   - No code changes needed when permissions change in the DB
             |
             | Guest detection: we block the guest account at the hasPermission
             | level — guests have no permissions assigned, so everything returns
             | false except items explicitly set to `$user && !$isGuest`.
             | ─────────────────────────────────────────────────────────────────
             */

            $isGuest = $user && (
                ($user->is_guest ?? false) ||
                strtolower($user->email) === 'guest@gmail.com'
            );

            /*
             | Eager-load role + permissions in one query so hasPermission()
             | hits the cache on every subsequent call — no N+1 in the sidebar.
             */
            if ($user && ! $user->relationLoaded('role')) {
                $user->load('role.permissions');
            }

            $isActive = fn(string $r) => Route::is($r) || Route::is($r . '.*');

            /*
             | Admin sub-item visibility — each item is independently
             | permission-gated so a partial admin (if you ever create one)
             | only sees the items their role has permission for.
             */
            $adminItems = $user ? [
                [
                    'label'   => 'User Management',
                    'route'   => 'admin.users.index',
                    'match'   => 'admin.users',
                    'perm'    => 'admin.users',        // hasPermission('admin.users')
                    'icon'    => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
                ],
                [
                    'label'   => 'Roles',
                    'route'   => 'admin.roles.index',
                    'match'   => 'admin.roles',
                    'perm'    => 'admin.roles',
                    'icon'    => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
                ],
                [
                    'label'   => 'Permissions',
                    'route'   => 'admin.permissions.index',
                    'match'   => 'admin.permissions',
                    'perm'    => 'admin.permissions',
                    'icon'    => 'M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z',
                ],
                [
                    'label'   => 'Audit Logs',
                    'route'   => 'admin.auditlogs.index',
                    'match'   => 'admin.auditlogs',
                    'perm'    => 'admin.audit',
                    'icon'    => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                ],
                [
                    'label'   => 'Backup & Restore',
                    'route'   => 'admin.document-backups.index',
                    'match'   => 'admin.document-backups',
                    'perm'    => 'admin.document-categories', // reuses same permission
                    'icon'    => 'M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10',
                ],
                [
                    'label'   => 'Doc Categories',
                    'route'   => 'admin.document-categories.index',
                    'match'   => 'admin.document-categories',
                    'perm'    => 'admin.document-categories',
                    'icon'    => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a2 2 0 012-2h2z',
                ],
                [
                    'label'   => 'Financial Categories',
                    'route'   => 'admin.financial-categories.index',
                    'match'   => 'admin.financial-categories',
                    'perm'    => 'financial_categories.manage',
                    'icon'    => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a2 2 0 012-2h2z',
                ],
            ] : [];

            /*
             | Filter admin items to only those the current user can see.
             | collect() so we can call ->isNotEmpty() cleanly.
             */
            $visibleAdminItems = collect($adminItems)
                ->filter(fn($item) => $user && $user->hasPermission($item['perm']));
        @endphp

        @if($user)

            {{-- ── MAIN ─────────────────────────────────────────────────────── --}}
            <div x-show="!sidebarCollapsed" class="sidebar-section-label">Main</div>

            {{--
                Dashboard — all authenticated users.
                hasPermission is NOT used here intentionally: every logged-in
                user (including Guest) should always be able to reach the dashboard.
            --}}
            <a href="{{ route('dashboard') }}"
               class="sidebar-link {{ $isActive('dashboard') ? 'active' : '' }}"
               aria-current="{{ $isActive('dashboard') ? 'page' : 'false' }}"
               data-nav-link>
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Dashboard</span>
                <span x-show="sidebarCollapsed" class="sidebar-tooltip" aria-hidden="true">Dashboard</span>
            </a>

            {{--
                Members — shown only when user has 'members.view' permission.
                hasPermission() auto-grants for SA (level 1), checks role->permissions
                for everyone else. Guests have no permissions → hidden automatically.
            --}}
            @if($user->hasPermission('members.view'))
                <a href="{{ route('members.index') }}"
                   class="sidebar-link {{ $isActive('members') ? 'active' : '' }}"
                   aria-current="{{ $isActive('members') ? 'page' : 'false' }}"
                   data-nav-link>
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Members</span>
                    <span x-show="sidebarCollapsed" class="sidebar-tooltip" aria-hidden="true">Members</span>
                </a>
            @endif

            {{-- ── RECORDS ──────────────────────────────────────────────────── --}}
            @if($user->hasPermission('documents.view') || $user->hasPermission('financial_transactions.view'))
                <div x-show="!sidebarCollapsed" class="sidebar-section-label">Records</div>
            @endif

            {{--
                Approved Financial Reports (Documents).
                Permission: 'documents.view'
                Guests can see this if their role has documents.view assigned.
            --}}
            @if($user->hasPermission('documents.view'))
                <a href="{{ route('documents.index') }}"
                   class="sidebar-link {{ $isActive('documents') ? 'active' : '' }}"
                   aria-current="{{ $isActive('documents') ? 'page' : 'false' }}"
                   data-nav-link>
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Approved Fin. Reports</span>
                    <span x-show="sidebarCollapsed" class="sidebar-tooltip" aria-hidden="true">Approved Financial Reports</span>
                </a>
            @endif

            {{--
                Financial Records.
                Permission: 'financial_transactions.view'
                Matches the slug used in hasPermission() and routePermissionMap.
            --}}
            @if($user->hasPermission('financial_transactions.view'))
                <a href="{{ route('financial.index') }}"
                   class="sidebar-link {{ $isActive('financial') ? 'active' : '' }}"
                   aria-current="{{ $isActive('financial') ? 'page' : 'false' }}"
                   data-nav-link>
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 7h6m0 10v-3m-6 3v-3m-6 3h18M3 5h18a2 2 0 012 2v10a2 2 0 01-2 2H3a2 2 0 01-2-2V7a2 2 0 012-2z"/>
                    </svg>
                    <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Financial Records</span>
                    <span x-show="sidebarCollapsed" class="sidebar-tooltip" aria-hidden="true">Financial Records</span>
                </a>
            @endif

            {{-- ── ACCOUNT ──────────────────────────────────────────────────── --}}
            {{--
                My Profile — hidden for guests (no meaningful profile to edit).
                Not permission-gated because profile access isn't a DB permission;
                it's purely a guest-vs-real-user distinction.
            --}}
            @if(!$isGuest)
                <div x-show="!sidebarCollapsed" class="sidebar-section-label">Account</div>

                <a href="{{ route('profile.index') }}"
                   class="sidebar-link {{ $isActive('profile') ? 'active' : '' }}"
                   aria-current="{{ $isActive('profile') ? 'page' : 'false' }}"
                   data-nav-link>
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span x-show="!sidebarCollapsed">My Profile</span>
                    <span x-show="sidebarCollapsed" class="sidebar-tooltip" aria-hidden="true">My Profile</span>
                </a>
            @endif

            {{-- ── ADMINISTRATION ───────────────────────────────────────────── --}}
            {{--
                The entire Administration section only renders when the user has
                at least one admin permission. $visibleAdminItems is pre-filtered
                above using hasPermission() per item — so even if the section
                appears, only permitted sub-items show inside it.
            --}}
            @if($visibleAdminItems->isNotEmpty())
                <div x-show="!sidebarCollapsed" class="sidebar-section-label">Administration</div>

                <button @click="adminOpen = !adminOpen"
                        :aria-expanded="adminOpen.toString()"
                        aria-controls="admin-submenu"
                        :class="adminOpen ? 'active' : ''"
                        class="sidebar-link w-full justify-between">
                    <span class="flex items-center gap-3">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        <span x-show="!sidebarCollapsed">Administration</span>
                    </span>
                    <svg x-show="!sidebarCollapsed"
                         :class="adminOpen ? 'rotate-90' : ''"
                         class="w-3 h-3 transition-transform"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    <span x-show="sidebarCollapsed" class="sidebar-tooltip" aria-hidden="true">Administration</span>
                </button>

                <div x-show="adminOpen" x-collapse id="admin-submenu">
                    @foreach($visibleAdminItems as $item)
                        <a href="{{ route($item['route']) }}"
                           class="sidebar-sub-link {{ $isActive($item['match']) ? 'active' : '' }}"
                           aria-current="{{ $isActive($item['match']) ? 'page' : 'false' }}"
                           data-nav-link>
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/>
                            </svg>
                            <span x-show="!sidebarCollapsed">{{ $item['label'] }}</span>
                            <span x-show="sidebarCollapsed" class="sidebar-tooltip" aria-hidden="true">{{ $item['label'] }}</span>
                        </a>
                    @endforeach
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
<div x-show="mobileMenuOpen"
     x-transition.opacity.duration.200
     @click="mobileMenuOpen = false"
     class="fixed inset-0 bg-black/50 backdrop-blur-sm z-30 lg:hidden"></div>

{{-- ══════════════════════ NAVIGATION SKELETON ══════════════════════ --}}
<div id="nav-skeleton" role="status" aria-label="Loading page content" aria-live="polite">
    <div class="sk-page-title">
        <div class="sk sk-line h-xl" style="width:220px;"></div>
        <div class="flex gap-2">
            <div class="sk sk-btn" style="width:90px;"></div>
            <div class="sk sk-btn" style="width:110px;"></div>
        </div>
    </div>
    <div class="sk-stat-grid">
        @for($i=0;$i<4;$i++)
            <div class="sk-stat-card">
                <div class="sk sk-icon sk-rect"></div>
                <div class="sk sk-line h-lg" style="width:55%;"></div>
                <div class="sk sk-line"      style="width:75%;"></div>
                <div class="sk sk-line h-sm" style="width:40%;"></div>
            </div>
        @endfor
    </div>
    <div class="sk-content-grid" style="min-height:320px;">
        <div class="sk-panel">
            <div class="sk-panel-header">
                <div class="sk sk-line h-lg" style="width:130px;"></div>
                <div class="sk sk-badge"></div>
            </div>
            <div class="sk-table-row" style="background:rgba(0,0,0,0.02);">
                <div class="sk sk-line h-sm" style="width:100%;"></div>
                <div class="sk sk-line h-sm" style="width:100%;"></div>
                <div class="sk sk-line h-sm" style="width:100%;"></div>
                <div class="sk sk-line h-sm" style="width:60%;"></div>
            </div>
            @for($r=0;$r<6;$r++)
                <div class="sk-table-row">
                    <div class="sk sk-circle" style="width:2rem;height:2rem;"></div>
                    <div style="display:flex;flex-direction:column;gap:.35rem;">
                        <div class="sk sk-line"      style="width:80%;"></div>
                        <div class="sk sk-line h-sm" style="width:55%;"></div>
                    </div>
                    <div class="sk sk-line" style="width:70%;"></div>
                    <div class="sk sk-badge" style="width:3.5rem;"></div>
                </div>
            @endfor
        </div>
        <div style="display:flex;flex-direction:column;gap:1rem;">
            <div class="sk-panel" style="flex:1;min-height:180px;">
                <div class="sk-panel-header">
                    <div class="sk sk-line h-lg" style="width:110px;"></div>
                    <div class="sk sk-badge"     style="width:70px;"></div>
                </div>
                <div style="padding:1rem;flex:1;display:flex;align-items:flex-end;gap:.5rem;">
                    @foreach([55,80,45,90,60,75,85] as $h)
                        <div class="sk sk-rect" style="flex:1;height:{{ $h }}px;"></div>
                    @endforeach
                </div>
            </div>
            <div class="sk-panel">
                <div class="sk-panel-header">
                    <div class="sk sk-line h-lg" style="width:120px;"></div>
                </div>
                @for($m=0;$m<4;$m++)
                    <div style="display:flex;align-items:center;gap:.75rem;padding:.75rem 1.25rem;border-bottom:1px solid var(--border);">
                        <div class="sk sk-circle" style="width:1.75rem;height:1.75rem;flex-shrink:0;"></div>
                        <div style="flex:1;display:flex;flex-direction:column;gap:.3rem;">
                            <div class="sk sk-line"      style="width:65%;"></div>
                            <div class="sk sk-line h-sm" style="width:40%;"></div>
                        </div>
                        <div class="sk sk-badge" style="width:2.5rem;"></div>
                    </div>
                @endfor
            </div>
        </div>
    </div>
    <span class="sr-only">Loading, please wait…</span>
</div>

{{-- ══════════════════════ MAIN CONTENT ══════════════════════ --}}
<div class="flex-1 flex flex-col min-h-screen transition-all duration-300"
     :class="sidebarCollapsed ? 'lg:ml-[4.5rem]' : 'lg:ml-64'">

    <nav id="topbar"
         class="glass fixed top-0 left-0 right-0 z-50 flex items-center justify-between px-4 md:px-6 shadow-sm"
         :class="sidebarCollapsed ? 'lg:left-[4.5rem]' : 'lg:left-64'"
         style="min-height:64px;padding-top:8px;padding-bottom:8px;"
         aria-label="Top navigation">

        <div class="flex items-center gap-3 flex-nowrap">
            <button @click="window.innerWidth < 1024 ? mobileMenuOpen = !mobileMenuOpen : sidebarCollapsed = !sidebarCollapsed"
                    :aria-expanded="(window.innerWidth < 1024 ? mobileMenuOpen : !sidebarCollapsed).toString()"
                    class="text-gray-600 dark:text-gray-300 hover:text-gold transition-colors focus:outline-none focus:ring-2 focus:ring-gold rounded-lg p-1 flex-shrink-0">
                <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <span class="sr-only" x-text="sidebarCollapsed ? 'Expand sidebar' : 'Collapse sidebar'"></span>
            </button>
            <span class="font-serif font-semibold text-base md:text-lg text-emerald-700 dark:text-emerald-300 whitespace-nowrap">
                @yield('page-title', 'Dashboard')
            </span>
        </div>

        <div class="flex items-center gap-2 md:gap-3 flex-nowrap">
            <button @click="$store.theme.toggle()"
                    class="w-8 h-8 md:w-9 md:h-9 rounded-full flex items-center justify-center hover:bg-gold/10 transition-colors flex-shrink-0"
                    :aria-label="$store.theme.dark ? 'Switch to light mode' : 'Switch to dark mode'">
                <svg x-show="$store.theme.dark" class="w-4 h-4 md:w-5 md:h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <svg x-show="!$store.theme.dark" class="w-4 h-4 md:w-5 md:h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
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

                <div x-show="open" @click.away="open = false" x-transition
                     id="user-dropdown-menu" role="menu"
                     class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 z-50 overflow-hidden">

                    @if(!$isGuest)
                        <a href="{{ route('profile.index') }}" role="menuitem" data-nav-link
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
                        <button type="submit" role="menuitem"
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

<div id="notification-container"
     class="fixed top-20 right-6 z-50 space-y-2 w-80 pointer-events-none"
     aria-live="polite" aria-atomic="true"></div>

{{-- ══════════════════════ SCRIPTS ══════════════════════ --}}
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

    function adjustMainPadding() {
        requestAnimationFrame(() => {
            var topbar = document.getElementById('topbar');
            var main   = document.getElementById('main-content');
            var sk     = document.getElementById('nav-skeleton');
            if (topbar) {
                var h = topbar.offsetHeight;
                if (main) main.style.paddingTop = h + 'px';
                if (sk)   sk.style.top          = h + 'px';
            }
        });
    }
    window.addEventListener('load',   adjustMainPadding);
    window.addEventListener('resize', adjustMainPadding);
    document.addEventListener('DOMContentLoaded', () => {
        adjustMainPadding();
        var topbar = document.getElementById('topbar');
        if (topbar) new MutationObserver(adjustMainPadding)
            .observe(topbar, { attributes: true, attributeFilter: ['class','style'] });
    });

    window._updateSkeletonSidebar = function (collapsed) {
        var sk = document.getElementById('nav-skeleton');
        if (!sk) return;
        sk.classList.toggle('sidebar-collapsed', !!collapsed);
        sk.classList.toggle('sidebar-expanded',  !collapsed);
    };
    document.addEventListener('DOMContentLoaded', function () {
        window._updateSkeletonSidebar(localStorage.getItem('sidebarCollapsed') === 'true');
    });

    (function () {
        var sk = null, progress = null, progTimer = null, progVal = 0, safeTimer = null;

        function init() {
            sk       = document.getElementById('nav-skeleton');
            progress = document.getElementById('nav-progress');
        }
        function showSkeleton() {
            if (!sk) init();
            if (sk) { sk.style.display = 'flex'; sk.style.opacity = '1'; }
            startProgress();
            clearTimeout(safeTimer);
            safeTimer = setTimeout(hideSkeleton, 8000);
        }
        function hideSkeleton() {
            clearTimeout(safeTimer);
            if (!sk) init();
            if (sk) {
                sk.style.opacity = '0';
                setTimeout(function () { if (sk) sk.style.display = 'none'; }, 220);
            }
            completeProgress();
        }
        function startProgress() {
            if (!progress) return;
            clearInterval(progTimer);
            progVal = 0;
            progress.style.transition = 'none';
            progress.style.width      = '0%';
            progress.style.opacity    = '1';
            setTimeout(function () {
                progress.style.transition = 'width 0.1s linear';
                progTimer = setInterval(function () {
                    progVal += (progVal < 70) ? 4 : (progVal < 85) ? 0.6 : 0.1;
                    if (progVal > 86) progVal = 86;
                    progress.style.width = progVal + '%';
                }, 80);
            }, 30);
        }
        function completeProgress() {
            if (!progress) return;
            clearInterval(progTimer);
            progress.style.transition = 'width 0.25s ease, opacity 0.4s ease 0.3s';
            progress.style.width      = '100%';
            progress.style.opacity    = '0';
            setTimeout(function () {
                if (progress) { progress.style.transition = 'none'; progress.style.width = '0%'; }
            }, 750);
        }
        function attachNavListeners() {
            document.querySelectorAll('[data-nav-link]').forEach(function (link) {
                link.addEventListener('click', function (e) {
                    if (e.ctrlKey || e.metaKey || e.shiftKey) return;
                    var href = link.getAttribute('href');
                    if (!href || href === '#' || href === window.location.href) return;
                    showSkeleton();
                });
            });
        }
        window.addEventListener('pageshow', hideSkeleton);
        document.addEventListener('DOMContentLoaded', function () {
            init(); attachNavListeners(); hideSkeleton();
        });
        window.showNavSkeleton = showSkeleton;
        window.hideNavSkeleton = hideSkeleton;
    })();

    window.showNotification = function (message, type, duration) {
        type     = type     || 'info';
        duration = duration || 4000;
        var container = document.getElementById('notification-container');
        if (!container) return;
        var palette = {
            success: { bg:'bg-emerald-50 dark:bg-emerald-900/90', border:'border-emerald-400 dark:border-emerald-600', icon:'text-emerald-600 dark:text-emerald-400', text:'text-emerald-800 dark:text-emerald-200', path:'M5 13l4 4L19 7' },
            error:   { bg:'bg-red-50 dark:bg-red-900/90',         border:'border-red-400 dark:border-red-600',         icon:'text-red-600 dark:text-red-400',         text:'text-red-800 dark:text-red-200',         path:'M6 18L18 6M6 6l12 12' },
            warning: { bg:'bg-yellow-50 dark:bg-yellow-900/90',   border:'border-yellow-400 dark:border-yellow-600',   icon:'text-yellow-600 dark:text-yellow-400',   text:'text-yellow-800 dark:text-yellow-200',   path:'M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z' },
            info:    { bg:'bg-blue-50 dark:bg-blue-900/90',       border:'border-blue-400 dark:border-blue-600',       icon:'text-blue-600 dark:text-blue-400',       text:'text-blue-800 dark:text-blue-200',       path:'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
        };
        var c = palette[type] || palette.info;
        var toast = document.createElement('div');
        toast.setAttribute('data-toast',''); toast.setAttribute('role','alert');
        toast.className = ['pointer-events-auto flex items-start gap-3 px-4 py-3',
            'rounded-xl shadow-lg border-l-4 backdrop-blur-sm',
            'transition-all duration-300 opacity-0 translate-x-4', c.bg, c.border].join(' ');
        var msgEl = document.createElement('p');
        msgEl.className = 'text-sm font-medium flex-1 ' + c.text;
        msgEl.textContent = message;
        toast.innerHTML =
            '<svg class="w-5 h-5 flex-shrink-0 mt-0.5 '+c.icon+'" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">' +
                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="'+c.path+'"/></svg>' +
            '<button onclick="this.closest(\'[data-toast]\').remove()" class="flex-shrink-0 '+c.icon+' hover:opacity-70 ml-auto" aria-label="Dismiss">' +
                '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>';
        toast.insertBefore(msgEl, toast.children[1]);
        container.appendChild(toast);
        requestAnimationFrame(function () {
            requestAnimationFrame(function () { toast.classList.remove('opacity-0','translate-x-4'); });
        });
        var timer = setTimeout(function () { dismiss(toast); }, duration);
        toast.addEventListener('mouseenter', function () { clearTimeout(timer); });
        toast.addEventListener('mouseleave', function () { timer = setTimeout(function () { dismiss(toast); }, 1500); });
        function dismiss(el) {
            el.classList.add('opacity-0','translate-x-4');
            el.addEventListener('transitionend', function () { el.remove(); }, { once:true });
        }
    };

    document.addEventListener('DOMContentLoaded', function () {
        var meta = document.querySelector('meta[name="flash-data"]');
        if (!meta) return;
        try {
            var flashes = JSON.parse(meta.getAttribute('content'));
            var i = 0;
            for (var type in flashes) {
                if (Object.prototype.hasOwnProperty.call(flashes, type) && flashes[type]) {
                    (function (t, msg, delay) {
                        setTimeout(function () { window.showNotification(msg, t); }, delay);
                    })(type, flashes[type], i * 250);
                    i++;
                }
            }
        } catch (e) { console.warn('Failed to parse flash-data:', e); }
    });
</script>

@stack('scripts')
</body>
</html>