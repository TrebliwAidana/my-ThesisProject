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

    {{-- <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script> --}}
    {{-- <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=DM+Serif+Display:ital@0;1&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">

    @php
        $flashSuccess = session('success') ?? session('password_success');
        $flashData = array_filter([
            'success' => $flashSuccess,
            'error'   => session('error'),
            'warning' => session('warning'),
            'info'    => session('info'),
        ]);
    @endphp
    @if(!empty($flashData))
        <meta name="flash-data" content="{{ json_encode($flashData) }}">
    @endif

    <style>
        /* ═══════════════════════════════════════════════════════════
           DESIGN TOKENS — Emerald & Gold
        ═══════════════════════════════════════════════════════════ */
        :root {
            --emerald:        #059669;
            --emerald-dark:   #047857;
            --emerald-light:  #10B981;
            --emerald-pale:   #D1FAE5;
            --emerald-mist:   #ECFDF5;
            --gold:           #D4AF37;
            --gold-dark:      #B8942E;
            --gold-light:     #F0CC55;
            --gold-pale:      #FEF9E7;
            --surface:        #FFFFFF;
            --surface-2:      #F8FAFC;
            --surface-3:      #F1F5F9;
            --text:           #0F172A;
            --text-2:         #334155;
            --text-3:         #64748B;
            --border:         #E2E8F0;
            --border-2:       #CBD5E1;
            --shadow-sm:      0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
            --shadow-md:      0 4px 16px rgba(0,0,0,0.08), 0 2px 4px rgba(0,0,0,0.04);
            --shadow-gold:    0 4px 20px rgba(212,175,55,0.25);
            --shadow-emerald: 0 4px 20px rgba(5,150,105,0.25);
            --radius:         0.875rem;
        }
        html.dark {
            --emerald:        #10B981;
            --emerald-dark:   #059669;
            --emerald-light:  #34D399;
            --emerald-pale:   rgba(16,185,129,0.12);
            --emerald-mist:   rgba(16,185,129,0.06);
            --gold:           #E6C558;
            --gold-dark:      #D4AF37;
            --gold-light:     #F0CC55;
            --gold-pale:      rgba(212,175,55,0.12);
            --surface:        #0F172A;
            --surface-2:      #1E293B;
            --surface-3:      #273548;
            --text:           #F1F5F9;
            --text-2:         #CBD5E1;
            --text-3:         #94A3B8;
            --border:         #1E293B;
            --border-2:       #334155;
            --shadow-sm:      0 1px 3px rgba(0,0,0,0.3);
            --shadow-md:      0 4px 16px rgba(0,0,0,0.4);
        }

        * { margin:0; padding:0; box-sizing:border-box; }

        body {
            font-family: 'Outfit', sans-serif;
            background: var(--surface-2);
            color: var(--text);
            transition: background 0.3s ease, color 0.3s ease;
            -webkit-font-smoothing: antialiased;
        }

        /* Light mode: soft warm-white with a very faint emerald tint */
        body {
            background-image:
                radial-gradient(ellipse at 0% 0%, rgba(5,150,105,0.04) 0%, transparent 50%),
                radial-gradient(ellipse at 100% 100%, rgba(212,175,55,0.04) 0%, transparent 50%);
            background-attachment: fixed;
        }
        html.dark body {
            background: linear-gradient(160deg, #0a1628 0%, #0f172a 50%, #0a1f18 100%);
            background-attachment: fixed;
        }

        /* ── Topbar Glass ── */
        .topbar-glass {
            background: rgba(255,255,255,0.88);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(212,175,55,0.18);
            box-shadow: 0 1px 0 rgba(212,175,55,0.1), 0 2px 12px rgba(0,0,0,0.06);
        }
        html.dark .topbar-glass {
            background: rgba(15,23,42,0.88);
            border-bottom: 1px solid rgba(212,175,55,0.12);
            box-shadow: 0 1px 0 rgba(212,175,55,0.08), 0 2px 12px rgba(0,0,0,0.3);
        }

        /* ── Sidebar ── */
        .sidebar {
            background: var(--surface);
            border-right: 1px solid var(--border);
            transition: width 0.3s cubic-bezier(0.4,0,0.2,1);
            box-shadow: 2px 0 24px rgba(0,0,0,0.05);
        }
        html.dark .sidebar {
            background: rgba(10,22,40,0.98);
            border-right: 1px solid rgba(212,175,55,0.1);
            box-shadow: 2px 0 24px rgba(0,0,0,0.4);
        }

        /* ── Sidebar Brand bar ── */
        .sidebar-brand {
            border-bottom: 1px solid rgba(212,175,55,0.15);
            background: linear-gradient(135deg, rgba(5,150,105,0.05) 0%, rgba(212,175,55,0.04) 100%);
        }
        html.dark .sidebar-brand {
            border-bottom: 1px solid rgba(212,175,55,0.12);
            background: linear-gradient(135deg, rgba(5,150,105,0.08) 0%, rgba(212,175,55,0.05) 100%);
        }

        /* ── Section labels ── */
        .sidebar-section-label {
            font-size: 0.62rem;
            font-weight: 700;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: var(--text-3);
            padding: 1rem 1.5rem 0.35rem;
            font-family: 'DM Mono', monospace;
        }
        html.dark .sidebar-section-label {
            color: rgba(212,175,55,0.55);
        }

        /* ── Nav links ── */
        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.6rem 1rem;
            margin: 0.15rem 0.6rem;
            border-radius: 0.75rem;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.18s ease;
            color: var(--text-2);
            position: relative;
            text-decoration: none;
        }
        html.dark .sidebar-link {
            color: #CBD5E1;
        }

        /* Gold hover — the signature interaction */
        .sidebar-link:hover {
            background: linear-gradient(135deg, rgba(212,175,55,0.12) 0%, rgba(212,175,55,0.06) 100%);
            color: var(--gold-dark);
            transform: translateX(3px);
            box-shadow: inset 3px 0 0 var(--gold);
        }
        html.dark .sidebar-link:hover {
            background: linear-gradient(135deg, rgba(212,175,55,0.12) 0%, rgba(212,175,55,0.05) 100%);
            color: var(--gold-light);
            box-shadow: inset 3px 0 0 var(--gold);
        }

        /* Emerald active */
        .sidebar-link.active {
            background: linear-gradient(135deg, var(--emerald) 0%, var(--emerald-dark) 100%);
            color: white !important;
            box-shadow: var(--shadow-emerald);
            transform: none;
        }
        html.dark .sidebar-link.active {
            background: linear-gradient(135deg, rgba(16,185,129,0.22) 0%, rgba(5,150,105,0.18) 100%);
            color: #6EE7B7 !important;
            box-shadow: 0 0 0 1px rgba(16,185,129,0.3);
        }

        /* Sub links */
        .sidebar-sub-link {
            display: flex;
            align-items: center;
            gap: 0.625rem;
            padding: 0.5rem 1rem 0.5rem 2.75rem;
            margin: 0.1rem 0.6rem;
            border-radius: 0.625rem;
            font-size: 0.8rem;
            font-weight: 500;
            transition: all 0.18s ease;
            color: var(--text-3);
            position: relative;
            text-decoration: none;
        }
        html.dark .sidebar-sub-link { color: #94A3B8; }

        .sidebar-sub-link:hover {
            background: rgba(212,175,55,0.1);
            color: var(--gold-dark);
            transform: translateX(3px);
            box-shadow: inset 3px 0 0 var(--gold);
        }
        html.dark .sidebar-sub-link:hover {
            background: rgba(212,175,55,0.1);
            color: var(--gold-light);
            box-shadow: inset 3px 0 0 var(--gold);
        }
        .sidebar-sub-link.active {
            background: linear-gradient(135deg, var(--emerald) 0%, var(--emerald-dark) 100%);
            color: white !important;
            box-shadow: var(--shadow-emerald);
        }
        html.dark .sidebar-sub-link.active {
            background: rgba(16,185,129,0.18);
            color: #6EE7B7 !important;
        }

        /* Tooltip for collapsed */
        .sidebar-tooltip {
            position: absolute;
            left: calc(100% + 12px);
            top: 50%;
            transform: translateY(-50%);
            background: #0F172A;
            color: #fff;
            font-size: 0.72rem;
            white-space: nowrap;
            padding: 5px 12px;
            border-radius: 8px;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.15s ease;
            z-index: 9999;
            box-shadow: 0 4px 16px rgba(0,0,0,0.25);
            border: 1px solid rgba(212,175,55,0.2);
            font-family: 'DM Mono', monospace;
        }
        .sidebar-tooltip::before {
            content: '';
            position: absolute;
            right: 100%; top: 50%;
            transform: translateY(-50%);
            border: 5px solid transparent;
            border-right-color: #0F172A;
        }
        .sidebar-link:hover .sidebar-tooltip,
        .sidebar-sub-link:hover .sidebar-tooltip { opacity: 1; }

        /* ── User footer ── */
        .sidebar-footer {
            border-top: 1px solid rgba(212,175,55,0.12);
            background: linear-gradient(135deg, rgba(5,150,105,0.03) 0%, rgba(212,175,55,0.03) 100%);
        }

        /* ── Scrollbar ── */
        ::-webkit-scrollbar { width: 4px; height: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(212,175,55,0.3); border-radius: 99px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(212,175,55,0.55); }

        /* ── Page fade ── */
        @keyframes fadeSlideUp {
            from { opacity: 0; transform: translateY(14px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .page-transition { animation: fadeSlideUp 0.32s ease-out forwards; }

        /* ── Top progress bar ── */
        #nav-progress {
            position: fixed; top: 0; left: 0; height: 2px; width: 0%;
            background: linear-gradient(90deg, var(--emerald-light), var(--gold), var(--emerald-light));
            background-size: 200% 100%;
            animation: shimmerBar 1.5s ease infinite;
            z-index: 9999; border-radius: 0 2px 2px 0; opacity: 0;
            pointer-events: none;
        }
        @keyframes shimmerBar {
            0%   { background-position: 100% 0; }
            100% { background-position: -100% 0; }
        }

        /* ── Skeleton ── */
        @keyframes shimmer {
            0%   { background-position: -900px 0; }
            100% { background-position:  900px 0; }
        }
        #nav-skeleton {
            position: fixed; top: 64px; left: 0; right: 0; bottom: 0; z-index: 48;
            background: var(--surface-2); overflow-y: auto;
            display: none; flex-direction: column; gap: 0;
            padding: 1.5rem 1.5rem 2rem;
            opacity: 1; transition: opacity 0.2s ease;
        }
        #nav-skeleton.sidebar-expanded  { left: 16rem; }
        #nav-skeleton.sidebar-collapsed { left: 4.5rem; }
        @media (max-width: 1023px) {
            #nav-skeleton.sidebar-expanded,
            #nav-skeleton.sidebar-collapsed { left: 0; }
        }
        .sk {
            background: linear-gradient(90deg, rgba(212,175,55,0.06) 25%, rgba(212,175,55,0.12) 50%, rgba(212,175,55,0.06) 75%);
            background-size: 900px 100%;
            animation: shimmer 1.5s ease-in-out infinite;
            border-radius: 0.5rem; flex-shrink: 0;
        }
        html.dark .sk {
            background: linear-gradient(90deg, rgba(30,41,59,1) 25%, rgba(51,65,85,1) 50%, rgba(30,41,59,1) 75%);
            background-size: 900px 100%;
        }
        .sk-line   { height: .75rem; border-radius: 9999px; }
        .sk-line.h-sm { height: .5rem; }
        .sk-line.h-lg { height: 1.1rem; }
        .sk-line.h-xl { height: 1.6rem; }
        .sk-circle { border-radius: 9999px; }
        .sk-rect   { border-radius: .75rem; }
        .sk-btn    { height: 2.25rem; border-radius: .625rem; }
        .sk-badge  { height: 1.4rem; width: 4.5rem; border-radius: 9999px; }
        .sk-page-title { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; gap: 1rem; }
        .sk-stat-grid  { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 1rem; margin-bottom: 1.5rem; }
        .sk-stat-card  { background: var(--surface); border: 1px solid var(--border); border-radius: 1rem; padding: 1.25rem; display: flex; flex-direction: column; gap: .6rem; }
        .sk-stat-card .sk-icon { width: 2.25rem; height: 2.25rem; border-radius: .625rem; }
        .sk-content-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; flex: 1; }
        @media (max-width: 768px) { .sk-content-grid { grid-template-columns: 1fr; } .sk-stat-grid { grid-template-columns: repeat(2, 1fr); } }
        .sk-panel { background: var(--surface); border: 1px solid var(--border); border-radius: 1rem; overflow: hidden; display: flex; flex-direction: column; }
        .sk-panel-header { padding: 1rem 1.25rem; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; }
        .sk-table-row { display: grid; grid-template-columns: 2.5rem 1fr 1fr 5rem; align-items: center; gap: 1rem; padding: .875rem 1.25rem; border-bottom: 1px solid var(--border); }
        .sk-table-row:last-child { border-bottom: none; }

        /* ── Toast ── */
        #notification-container > div { transition: opacity 0.3s ease, transform 0.3s ease; }

        /* ── Topbar user pill ── */
        .user-pill {
            background: linear-gradient(135deg, rgba(5,150,105,0.08) 0%, rgba(212,175,55,0.06) 100%);
            border: 1px solid rgba(212,175,55,0.2);
            transition: all 0.2s ease;
        }
        .user-pill:hover {
            background: linear-gradient(135deg, rgba(212,175,55,0.12) 0%, rgba(212,175,55,0.08) 100%);
            border-color: rgba(212,175,55,0.4);
            box-shadow: var(--shadow-gold);
        }
        html.dark .user-pill {
            background: rgba(212,175,55,0.06);
            border-color: rgba(212,175,55,0.15);
        }
        html.dark .user-pill:hover {
            background: rgba(212,175,55,0.12);
            border-color: rgba(212,175,55,0.3);
        }

        /* ── Icon button ── */
        .icon-btn {
            width: 2.25rem; height: 2.25rem;
            display: flex; align-items: center; justify-content: center;
            border-radius: 0.625rem;
            transition: all 0.18s ease;
            color: var(--text-3);
        }
        .icon-btn:hover {
            background: rgba(212,175,55,0.1);
            color: var(--gold-dark);
        }
        html.dark .icon-btn:hover {
            background: rgba(212,175,55,0.1);
            color: var(--gold-light);
        }

        /* ── Dropdown ── */
        .dropdown-menu {
            background: var(--surface);
            border: 1px solid rgba(212,175,55,0.18);
            box-shadow: 0 8px 32px rgba(0,0,0,0.12), 0 2px 8px rgba(212,175,55,0.08);
            border-radius: 1rem;
            overflow: hidden;
        }
        html.dark .dropdown-menu {
            background: #1A2744;
            border-color: rgba(212,175,55,0.15);
            box-shadow: 0 8px 32px rgba(0,0,0,0.5);
        }
        .dropdown-item {
            display: flex; align-items: center; gap: 0.625rem;
            padding: 0.625rem 1rem;
            font-size: 0.875rem;
            color: var(--text-2);
            transition: all 0.15s ease;
            text-decoration: none;
        }
        .dropdown-item:hover {
            background: rgba(212,175,55,0.08);
            color: var(--gold-dark);
        }
        html.dark .dropdown-item { color: #CBD5E1; }
        html.dark .dropdown-item:hover { background: rgba(212,175,55,0.1); color: var(--gold-light); }
        .dropdown-item.danger { color: #E11D48; }
        .dropdown-item.danger:hover { background: rgba(225,29,72,0.06); color: #E11D48; }

        /* ── Avatar gradient ── */
        .avatar-gradient {
            background: linear-gradient(135deg, var(--gold) 0%, var(--emerald) 100%);
        }

        /* ── Topbar title font ── */
        .topbar-title {
            font-family: 'DM Serif Display', serif;
            font-size: 1.1rem;
            color: var(--emerald-dark);
        }
        html.dark .topbar-title { color: var(--emerald-light); }

        /* ── Gold accent line under brand ── */
        .brand-name {
            font-family: 'DM Serif Display', serif;
            color: var(--emerald-dark);
        }
        html.dark .brand-name { color: var(--emerald-light); }

        /* ── Admin toggle button ── */
        button.sidebar-link { width: 100%; text-align: left; background: none; border: none; cursor: pointer; }
        button.sidebar-link:hover {
            background: linear-gradient(135deg, rgba(212,175,55,0.12) 0%, rgba(212,175,55,0.06) 100%);
            color: var(--gold-dark);
            box-shadow: inset 3px 0 0 var(--gold);
        }
        html.dark button.sidebar-link:hover { color: var(--gold-light); }
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
    class="sidebar fixed top-0 left-0 h-full z-40 transform transition-transform duration-300 lg:translate-x-0 flex flex-col"
    :style="sidebarCollapsed ? 'width:4.5rem' : 'width:16rem'"
    aria-label="Main navigation"
>
    {{-- Brand --}}
    <div class="sidebar-brand flex items-center h-16 px-4 flex-shrink-0"
         :class="sidebarCollapsed ? 'justify-center' : 'justify-start'">
        <div x-show="!sidebarCollapsed" class="flex items-center gap-2.5">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center shadow-sm overflow-hidden flex-shrink-0"
                 style="border: 1.5px solid rgba(212,175,55,0.3);">
                <img src="{{ asset('images/vsulhs_logo.png') }}" alt="VSULHS Logo" class="w-full h-full object-contain">
            </div>
            <div>
                <span class="brand-name font-bold text-sm leading-tight block">VSULHS SSLG</span>
                <span class="text-[10px] font-medium tracking-wide" style="color: var(--gold-dark); font-family: 'DM Mono', monospace;">Student Gov Portal</span>
            </div>
        </div>
        <div x-show="sidebarCollapsed"
             class="w-9 h-9 rounded-xl overflow-hidden"
             style="border: 1.5px solid rgba(212,175,55,0.3);">
            <img src="{{ asset('images/vsulhs_logo.png') }}" alt="VSULHS Logo" class="w-full h-full object-contain">
        </div>
    </div>

    {{-- Navigation --}}
    
    <nav class="flex-1 py-3 overflow-y-auto" aria-label="Sidebar">
        @php
            $user = auth()->user();
            $isGuest = $user && (($user->is_guest ?? false) || strtolower($user->email) === 'guest@gmail.com');
            if ($user && !$user->relationLoaded('role')) { $user->load('role.permissions'); }
            $isActive = fn(string $r) => Route::is($r) || Route::is($r . '.*');

            $adminItems = $user ? [
                ['label' => 'User Management',       'route' => 'admin.users.index',                'match' => 'admin.users',                'perm' => 'users.view',                 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
                ['label' => 'Roles',                 'route' => 'admin.roles.index',                'match' => 'admin.roles',                'perm' => 'roles.view',                 'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
                ['label' => 'Permissions',           'route' => 'admin.permissions.index',          'match' => 'admin.permissions',          'perm' => 'permissions.view',           'icon' => 'M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z'],
                ['label' => 'System Logs',           'route' => 'admin.auditlogs.index',            'match' => 'admin.auditlogs',            'perm' => 'audit.view',                 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['label' => 'Backup & Restore',      'route' => 'admin.document-backups.index',     'match' => 'admin.document-backups',     'perm' => 'backups.view',               'icon' => 'M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10'],
                ['label' => 'Doc Categories',        'route' => 'admin.document-categories.index',  'match' => 'admin.document-categories',  'perm' => 'categories.view',            'icon' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a2 2 0 012-2h2z'],
                ['label' => 'Financial Categories',  'route' => 'admin.financial-categories.index', 'match' => 'admin.financial-categories', 'perm' => 'financial_categories.manage','icon' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a2 2 0 012-2h2z'],
            ] : [];

            $visibleAdminItems = collect($adminItems)->filter(fn($item) => $user && $user->hasPermission($item['perm']));
        @endphp

        @if($user)
            {{-- MAIN --}}
            <div x-show="!sidebarCollapsed" class="sidebar-section-label">Main</div>

            <a href="{{ route('dashboard') }}"
            class="sidebar-link {{ $isActive('dashboard') ? 'active' : '' }}"
            data-nav-link>
                <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span x-show="!sidebarCollapsed">Dashboard</span>
                <span x-show="sidebarCollapsed" class="sidebar-tooltip">Dashboard</span>
            </a>

            @if($user->hasPermission('members.view'))
            <a href="{{ route('members.index') }}"
            class="sidebar-link {{ $isActive('members') ? 'active' : '' }}"
            data-nav-link>
                <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span x-show="!sidebarCollapsed">Members</span>
                <span x-show="sidebarCollapsed" class="sidebar-tooltip">Members</span>
            </a>
            @endif

            {{-- RECORDS --}}
            @if($user->hasPermission('documents.view') || $user->hasPermission('financial.view'))
            <div x-show="!sidebarCollapsed" class="sidebar-section-label">Records</div>
            @endif

            @if($user->hasPermission('documents.view'))
            <a href="{{ route('documents.index') }}"
            class="sidebar-link {{ $isActive('documents') ? 'active' : '' }}"
            data-nav-link>
                <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span x-show="!sidebarCollapsed" class="leading-tight">Fin. Reports</span>
                <span x-show="sidebarCollapsed" class="sidebar-tooltip">Approved Financial Reports</span>
            </a>
            @endif

            @if($user->hasPermission('financial.view'))
            <a href="{{ route('financial.index') }}"
            class="sidebar-link {{ $isActive('financial') ? 'active' : '' }}"
            data-nav-link>
                <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                        d="M9 7h6m0 10v-3m-6 3v-3m-6 3h18M3 5h18a2 2 0 012 2v10a2 2 0 01-2 2H3a2 2 0 01-2-2V7a2 2 0 012-2z"/>
                </svg>
                <span x-show="!sidebarCollapsed">Financial Records</span>
                <span x-show="sidebarCollapsed" class="sidebar-tooltip">Financial Records</span>
            </a>
            @endif

            {{-- ACCOUNT --}}
            @if(!$isGuest)
            <div x-show="!sidebarCollapsed" class="sidebar-section-label">Account</div>
            <a href="{{ route('profile.index') }}"
            class="sidebar-link {{ $isActive('profile') ? 'active' : '' }}"
            data-nav-link>
                <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span x-show="!sidebarCollapsed">My Profile</span>
                <span x-show="sidebarCollapsed" class="sidebar-tooltip">My Profile</span>
            </a>
            @endif

            {{-- ADMINISTRATION --}}
            @if($visibleAdminItems->isNotEmpty())
            <div x-show="!sidebarCollapsed" class="sidebar-section-label">Administration</div>

            @if($visibleAdminItems->count() === 1)
                @php $singleItem = $visibleAdminItems->first(); @endphp
                <a href="{{ route($singleItem['route']) }}"
                class="sidebar-link {{ $isActive($singleItem['match']) ? 'active' : '' }}"
                data-nav-link>
                    <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $singleItem['icon'] }}"/>
                    </svg>
                    <span x-show="!sidebarCollapsed">{{ $singleItem['label'] }}</span>
                    <span x-show="sidebarCollapsed" class="sidebar-tooltip">{{ $singleItem['label'] }}</span>
                </a>

            @else
                <button @click="adminOpen = !adminOpen"
                        :class="adminOpen ? 'active' : ''"
                        class="sidebar-link w-full justify-between">
                    <span class="flex items-center gap-3">
                        <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        <span x-show="!sidebarCollapsed">Administration</span>
                    </span>
                    <svg x-show="!sidebarCollapsed"
                        :class="adminOpen ? 'rotate-90' : ''"
                        class="w-3 h-3 transition-transform flex-shrink-0"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    <span x-show="sidebarCollapsed" class="sidebar-tooltip">Administration</span>
                </button>

                <div x-show="adminOpen" x-collapse id="admin-submenu">
                    @foreach($visibleAdminItems as $item)
                    <a href="{{ route($item['route']) }}"
                    class="sidebar-sub-link {{ $isActive($item['match']) ? 'active' : '' }}"
                    data-nav-link>
                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $item['icon'] }}"/>
                        </svg>
                        <span x-show="!sidebarCollapsed">{{ $item['label'] }}</span>
                        <span x-show="sidebarCollapsed" class="sidebar-tooltip">{{ $item['label'] }}</span>
                    </a>
                    @endforeach
                </div>
            @endif

            @endif
        @endif
    </nav>

    {{-- User footer --}}

    @auth
    <div class="sidebar-footer p-3 flex items-center gap-3 flex-shrink-0"
        :class="sidebarCollapsed ? 'justify-center' : 'justify-start'">

        {{-- Avatar: photo if set, else initials --}}
        @if(auth()->user()->avatar)
            <div class="w-9 h-9 rounded-full flex-shrink-0 shadow-md overflow-hidden"
                style="border: 2px solid rgba(212,175,55,0.35); min-width: 2.25rem;">
                <img src="{{ Str::startsWith(auth()->user()->avatar, 'http') 
                            ? auth()->user()->avatar 
                            : asset('storage/' . auth()->user()->avatar) }}"
                    alt="{{ auth()->user()->full_name }}"
                    class="w-full h-full object-cover">
            </div>
        @else
            <div class="w-9 h-9 rounded-full avatar-gradient flex items-center justify-center text-white text-xs font-bold shadow-md flex-shrink-0"
                style="font-family: 'DM Mono', monospace;">
                {{ strtoupper(substr(auth()->user()->full_name, 0, 2)) }}
            </div>
        @endif

        <div x-show="!sidebarCollapsed" class="min-w-0">
            <p class="text-xs font-semibold truncate" style="color: var(--text);">{{ auth()->user()->full_name }}</p>
            <p class="text-[10px] font-medium" style="color: var(--gold-dark); font-family: 'DM Mono', monospace;">{{ auth()->user()->role->name }}</p>
        </div>
    </div>
    @endauth
</aside>

{{-- Mobile overlay --}}
<div x-show="mobileMenuOpen"
     x-transition.opacity.duration.200
     @click="mobileMenuOpen = false"
     class="fixed inset-0 bg-black/40 backdrop-blur-sm z-30 lg:hidden"></div>

{{-- ══════════════════════ SKELETON ══════════════════════ --}}
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

    {{-- Topbar --}}
    <nav id="topbar"
         class="topbar-glass fixed top-0 left-0 right-0 z-50 flex items-center justify-between px-4 md:px-6"
         :class="sidebarCollapsed ? 'lg:left-[4.5rem]' : 'lg:left-64'"
         style="min-height:64px; padding-top:10px; padding-bottom:10px;"
         aria-label="Top navigation">

        <div class="flex items-center gap-3">
            {{-- Hamburger --}}
            <button @click="window.innerWidth < 1024 ? mobileMenuOpen = !mobileMenuOpen : sidebarCollapsed = !sidebarCollapsed"
                    class="icon-btn flex-shrink-0 rounded-xl">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            {{-- Page title --}}
            <span class="topbar-title font-bold">@yield('page-title', 'Dashboard')</span>
        </div>

        <div class="flex items-center gap-2">
            {{-- Dark mode toggle --}}
            <button @click="$store.theme.toggle()" class="icon-btn rounded-xl"
                    :aria-label="$store.theme.dark ? 'Light mode' : 'Dark mode'">
                <svg x-show="$store.theme.dark" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                     style="color: var(--gold);">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <svg x-show="!$store.theme.dark" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                </svg>
            </button>

            {{-- User dropdown --}}
            @auth
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open"
                        class="user-pill flex items-center gap-2 px-3 py-1.5 rounded-xl">

                    {{-- Avatar: photo if set, else initials --}}
                    @if(auth()->user()->avatar)
                        <div class="w-7 h-7 rounded-full flex-shrink-0 overflow-hidden"
                            style="border: 1.5px solid rgba(212,175,55,0.35); min-width: 1.75rem;">
                            <img src="{{ Str::startsWith(auth()->user()->avatar, 'http') 
                                        ? auth()->user()->avatar 
                                        : asset('storage/' . auth()->user()->avatar) }}"
                                alt="{{ auth()->user()->full_name }}"
                                class="w-full h-full object-cover">
                        </div>
                    @else
                        <div class="w-7 h-7 rounded-full avatar-gradient flex items-center justify-center text-white text-[11px] font-bold flex-shrink-0"
                            style="font-family: 'DM Mono', monospace;">
                            {{ strtoupper(substr(auth()->user()->full_name, 0, 2)) }}
                        </div>
                    @endif

                    <span class="hidden sm:inline text-sm font-semibold max-w-[120px] truncate"
                        style="color: var(--emerald-dark);">
                        {{ auth()->user()->full_name }}
                    </span>
                    <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        style="color: var(--gold-dark);">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="open" @click.away="open = false" x-transition
                     class="dropdown-menu absolute right-0 mt-2 w-48 z-50">
                    @if(!$isGuest)
                    <a href="{{ route('profile.index') }}" class="dropdown-item" data-nav-link>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Profile
                    </a>
                    <hr style="border-color: rgba(212,175,55,0.15); margin: 2px 0;">
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item danger w-full">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                      d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
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
            .observe(topbar, { attributes: true, attributeFilter: ['class', 'style'] });
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
        function init() { sk = document.getElementById('nav-skeleton'); progress = document.getElementById('nav-progress'); }
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
            if (sk) { sk.style.opacity = '0'; setTimeout(function () { if (sk) sk.style.display = 'none'; }, 220); }
            completeProgress();
        }
        function startProgress() {
            if (!progress) return;
            clearInterval(progTimer); progVal = 0;
            progress.style.transition = 'none'; progress.style.width = '0%'; progress.style.opacity = '1';
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
            progress.style.width = '100%'; progress.style.opacity = '0';
            setTimeout(function () { if (progress) { progress.style.transition = 'none'; progress.style.width = '0%'; } }, 750);
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
        document.addEventListener('DOMContentLoaded', function () { init(); attachNavListeners(); hideSkeleton(); });
        window.showNavSkeleton = showSkeleton;
        window.hideNavSkeleton = hideSkeleton;
    })();

    window.showNotification = function (message, type, duration) {
        type = type || 'info'; duration = duration || 4000;
        var container = document.getElementById('notification-container');
        if (!container) return;
        var palette = {
            success: { bg:'bg-emerald-50 dark:bg-emerald-900/90', border:'border-emerald-500', icon:'text-emerald-600 dark:text-emerald-400', text:'text-emerald-900 dark:text-emerald-100', path:'M5 13l4 4L19 7' },
            error:   { bg:'bg-red-50 dark:bg-red-900/80',         border:'border-red-500',     icon:'text-red-600 dark:text-red-400',         text:'text-red-900 dark:text-red-100',         path:'M6 18L18 6M6 6l12 12' },
            warning: { bg:'bg-amber-50 dark:bg-amber-900/80',     border:'border-amber-500',   icon:'text-amber-600 dark:text-amber-400',     text:'text-amber-900 dark:text-amber-100',     path:'M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z' },
            info:    { bg:'bg-blue-50 dark:bg-blue-900/80',       border:'border-blue-500',    icon:'text-blue-600 dark:text-blue-400',       text:'text-blue-900 dark:text-blue-100',       path:'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
        };
        var c = palette[type] || palette.info;
        var toast = document.createElement('div');
        toast.setAttribute('data-toast', ''); toast.setAttribute('role', 'alert');
        toast.className = ['pointer-events-auto flex items-start gap-3 px-4 py-3 rounded-xl shadow-xl border-l-4 backdrop-blur-sm',
            'transition-all duration-300 opacity-0 translate-x-4', c.bg, c.border].join(' ');
        var msgEl = document.createElement('p');
        msgEl.className = 'text-sm font-medium flex-1 ' + c.text;
        msgEl.textContent = message;
        toast.innerHTML =
            '<svg class="w-5 h-5 flex-shrink-0 mt-0.5 '+c.icon+'" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="'+c.path+'"/></svg>' +
            '<button onclick="this.closest(\'[data-toast]\').remove()" class="flex-shrink-0 '+c.icon+' hover:opacity-60 ml-auto">' +
                '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>';
        toast.insertBefore(msgEl, toast.children[1]);
        container.appendChild(toast);
        requestAnimationFrame(function () { requestAnimationFrame(function () { toast.classList.remove('opacity-0', 'translate-x-4'); }); });
        var timer = setTimeout(function () { dismiss(toast); }, duration);
        toast.addEventListener('mouseenter', function () { clearTimeout(timer); });
        toast.addEventListener('mouseleave', function () { timer = setTimeout(function () { dismiss(toast); }, 1500); });
        function dismiss(el) {
            el.classList.add('opacity-0', 'translate-x-4');
            el.addEventListener('transitionend', function () { el.remove(); }, { once: true });
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