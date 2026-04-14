<!DOCTYPE html>
<html lang="en"
      x-data="{
          darkMode: localStorage.getItem('dark') === 'true',
          loginModalOpen: {{ $errors->any() ? 'true' : 'false' }}
      }"
      x-init="
          $watch('darkMode', val => localStorage.setItem('dark', val));
          document.documentElement.classList.toggle('dark', darkMode);
          $watch('loginModalOpen', val => document.body.classList.toggle('modal-open', val));
      "
      :class="darkMode ? 'dark' : ''">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Official management portal for VSULHS Supreme Student Learner Government. Manage finances, documents, and members securely.">
    <meta property="og:title" content="VSULHS SSLG Portal">
    <meta property="og:description" content="The official portal of the VSU Laboratory High School Supreme Student Learner Government.">
    <title>VSULHS SSLG — Student Government Portal</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        /* ── x-cloak: hide Alpine-controlled elements before boot ── */
        [x-cloak] { display: none !important; }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;
            color: #111827;
            background: #ffffff;
            font-size: 15px;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        /* ── Dark mode overrides ── */
        .dark body                          { background: #0f172a; color: #e2e8f0; }
        .dark .nav                          { background: #1e293b; border-bottom-color: #334155; }
        .dark .nav-name                     { color: #fff; }
        .dark .nav-links a                  { color: #94a3b8; }
        .dark .nav-links a:hover            { color: #fbbf24; }
        .dark .mobile-menu                  { background: #1e293b; border-bottom-color: #334155; }
        .dark .mobile-menu a,
        .dark .mobile-menu button           { color: #cbd5e1; border-bottom-color: #334155; }
        .dark .mobile-menu a:hover,
        .dark .mobile-menu button:hover     { color: #fbbf24; }
        .dark .hamburger span               { background: #e2e8f0; }
        .dark .stat                         { border-right-color: #334155; border-bottom-color: #334155; }
        .dark .stat-val                     { color: #34d399; }
        .dark .stat-label                   { color: #94a3b8; }
        .dark .notice                       { background: #064e3b; border-color: #fbbf24; }
        .dark .notice p                     { color: #bbf7d0; }
        .dark .notice-icon                  { stroke: #bbf7d0; }
        .dark .feat                         { background: #1e293b; border-color: #334155; }
        .dark .feat h3                      { color: #fff; }
        .dark .feat p                       { color: #94a3b8; }
        .dark .feat-icon                    { background: #064e3b; }
        .dark .feat-icon svg                { stroke: #34d399; }
        .dark .about                        { background: #02221c; }
        .dark .roles                        { background: #0f172a; }
        .dark .role-item                    { background: #1e293b; border-color: #334155; }
        .dark .role-info h4                 { color: #fff; }
        .dark .role-info p                  { color: #94a3b8; }
        .dark .role-visual                  { background: #1e293b; border-color: #334155; }
        .dark .rv-head                      { border-bottom-color: #334155; }
        .dark .rv-head span                 { color: #fff; }
        .dark .rv-row                       { border-bottom-color: #334155; }
        .dark .rv-name                      { color: #fff; }
        .dark .rv-badge                     { background: #1D9E75; color: #fff; }
        .dark .rv-tag                       { filter: brightness(1.3); }
        /* Prevent body scroll when modal is open */
        body.modal-open                     { overflow: hidden; }
        .dark .section-title                { color: #fff; }
        .dark .section-sub                  { color: #94a3b8; }
        .dark .cta                          { border-top-color: #334155; }
        .dark .cta h2                       { color: #fff; }
        .dark .cta p                        { color: #94a3b8; }
        .dark footer                        { background: #0f172a; border-top-color: #334155; }
        .dark footer h4                     { color: #fff; }
        .dark footer a                      { color: #94a3b8; }
        .dark footer a:hover                { color: #fbbf24; }
        .dark .footer-desc                  { color: #94a3b8; }
        .dark .footer-bottom                { border-top-color: #334155; color: #64748b; }
        .dark .footer-privacy               { color: #64748b; }
        .dark .stats                        { border-bottom-color: #334155; }
        @media (max-width: 768px) {
            .dark .stat:nth-child(odd)      { border-right-color: #334155; }
        }

        /* ── Focus rings ── */
        a:focus-visible,
        button:focus-visible {
            outline: 2px solid #D4A11E;
            outline-offset: 2px;
            border-radius: 4px;
        }

        /* ── Back to top ── */
        #backToTop { transition: opacity 0.3s, visibility 0.3s, background 0.15s; }
        #backToTop:hover { background: #D4A11E !important; }

        /* ══════════════════════════════════════
           NAV
        ══════════════════════════════════════ */
        .nav {
            display: flex; justify-content: space-between; align-items: center;
            padding: 18px 40px;
            border-bottom: 1px solid #f0f0ee;
            background: #ffffff;
            position: sticky; top: 0; z-index: 50;
        }
        .nav-logo { display: flex; align-items: center; gap: 10px; }
        .nav-badge {
            width: 32px; height: 32px; background: #0F6E56; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 10px; font-weight: 500;
        }
        .nav-name { font-weight: 500; font-size: 15px; color: #111827; }
        .nav-links { display: flex; gap: 28px; }
        .nav-links a { font-size: 14px; color: #6b7280; text-decoration: none; transition: color 0.15s; }
        .nav-links a:hover { color: #0F6E56; }
        .nav-right { display: flex; align-items: center; gap: 12px; }
        .nav-cta {
            background: #0F6E56; color: #fff;
            padding: 8px 18px; border-radius: 8px;
            font-size: 13px; font-weight: 500;
            border: none; cursor: pointer;
            transition: background 0.15s;
        }
        .nav-cta:hover { background: #D4A11E; }

        /* ── Dark mode toggle button ── */
        .dark-toggle {
            background: none; border: none; cursor: pointer;
            padding: 6px; border-radius: 8px;
            color: #6b7280;
            transition: background 0.15s, color 0.15s;
            display: flex; align-items: center; justify-content: center;
        }
        .dark-toggle:hover { background: #f3f4f6; color: #111827; }
        .dark .dark-toggle { color: #94a3b8; }
        .dark .dark-toggle:hover { background: #334155; color: #fff; }

        /* ── Hamburger ── */
        .hamburger {
            display: none; flex-direction: column; justify-content: center;
            gap: 5px; width: 36px; height: 36px;
            background: none; border: none; cursor: pointer; padding: 4px;
        }
        .hamburger span {
            display: block; height: 1.5px; background: #111827;
            border-radius: 2px;
            transition: transform 0.25s ease, opacity 0.2s ease;
            transform-origin: center;
        }
        .hamburger.open span:nth-child(1) { transform: translateY(6.5px) rotate(45deg); }
        .hamburger.open span:nth-child(2) { opacity: 0; }
        .hamburger.open span:nth-child(3) { transform: translateY(-6.5px) rotate(-45deg); }

        /* ── Mobile drawer ── */
        .mobile-menu {
            display: none; position: fixed;
            top: 65px; left: 0; right: 0;
            background: #fff; border-bottom: 1px solid #f0f0ee;
            padding: 16px 24px 20px; z-index: 49;
            flex-direction: column; gap: 4px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.06);
        }
        .mobile-menu.open { display: flex; }
        .mobile-menu a,
        .mobile-menu button {
            font-size: 15px; color: #374151; text-decoration: none;
            padding: 10px 4px; border-bottom: 1px solid #f9fafb;
            text-align: left; background: none; border-left: none;
            border-right: none; border-top: none; cursor: pointer;
            width: 100%;
        }
        .mobile-menu a:last-child,
        .mobile-menu button:last-child { border-bottom: none; }
        .mobile-menu a:hover,
        .mobile-menu button:hover { color: #D4A11E; }

        /* ══════════════════════════════════════
           HERO
        ══════════════════════════════════════ */
        .hero {
            position: relative; background: #04342C;
            padding: 80px 40px 60px; text-align: center; overflow: hidden;
        }
        .hero::before {
            content: ''; position: absolute; inset: 0;
            background-image: radial-gradient(circle, rgba(255,255,255,0.12) 1px, transparent 1px);
            background-size: 28px 28px;
            mask-image: radial-gradient(ellipse 80% 80% at 50% 50%, black 40%, transparent 100%);
            -webkit-mask-image: radial-gradient(ellipse 80% 80% at 50% 50%, black 40%, transparent 100%);
            pointer-events: none;
        }
        .hero::after {
            content: ''; position: absolute;
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(29,158,117,0.15) 0%, transparent 70%);
            top: -100px; left: 50%; transform: translateX(-50%);
            pointer-events: none;
            animation: pulse-orb 6s ease-in-out infinite;
        }
        @keyframes pulse-orb {
            0%,100% { opacity: 0.6; transform: translateX(-50%) scale(1); }
            50%      { opacity: 1;   transform: translateX(-50%) scale(1.1); }
        }
        .hero-inner { position: relative; z-index: 1; }
        .hero-tag {
            display: inline-flex; align-items: center; gap: 6px;
            background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15);
            border-radius: 100px; padding: 5px 14px; margin-bottom: 28px;
        }
        .hero-tag span { font-size: 12px; color: #9FE1CB; font-weight: 500; letter-spacing: 0.3px; }
        .hero-tag-dot { width: 6px; height: 6px; background: #1D9E75; border-radius: 50%; }
        .hero h1 {
            font-size: 44px; font-weight: 500; color: #fff;
            line-height: 1.2; margin-bottom: 18px;
            max-width: 620px; margin-left: auto; margin-right: auto;
        }
        .hero h1 em { font-style: normal; color: #5DCAA5; }
        .hero p {
            font-size: 16px; color: rgba(255,255,255,0.6);
            max-width: 480px; margin: 0 auto 36px; line-height: 1.7;
        }
        .hero-btns { display: flex; justify-content: center; gap: 12px; flex-wrap: wrap; }
        .btn-primary {
            background: #1D9E75; color: #fff;
            padding: 11px 24px; border-radius: 8px;
            font-size: 14px; font-weight: 500;
            border: none; cursor: pointer;
            display: inline-flex; align-items: center; gap: 6px;
            transition: background 0.15s;
        }
        .btn-primary:hover { background: #D4A11E; }
        .btn-ghost {
            background: rgba(255,255,255,0.06); color: rgba(255,255,255,0.8);
            padding: 11px 24px; border-radius: 8px;
            font-size: 14px; font-weight: 500; text-decoration: none;
            border: 1px solid rgba(255,255,255,0.15); transition: background 0.15s;
        }
        .btn-ghost:hover { background: #D4A11E; color: #fff; border-color: #D4A11E; }

        /* ── Hero mockup ── */
        .hero-mockup {
            margin: 48px auto 0; max-width: 760px;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 12px; overflow: hidden;
        }
        .mockup-bar {
            background: rgba(255,255,255,0.06); padding: 10px 16px;
            display: flex; align-items: center; gap: 6px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }
        .dot { width: 10px; height: 10px; border-radius: 50%; }
        .mockup-content { padding: 24px; display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; }
        .mock-card {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 8px; padding: 16px;
        }
        .mock-card-label { font-size: 11px; color: rgba(255,255,255,0.4); margin-bottom: 6px; }
        .mock-card-val { font-size: 22px; font-weight: 500; color: #fff; }
        .mock-card-sub { font-size: 11px; margin-top: 4px; }
        .mock-bar-wrap {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 8px; padding: 16px; grid-column: 1 / -1;
        }
        .mock-bar-label { font-size: 11px; color: rgba(255,255,255,0.4); margin-bottom: 10px; }
        .mock-bars { display: flex; align-items: flex-end; gap: 6px; height: 60px; }
        .mock-b-income  { background: #1D9E75; border-radius: 3px 3px 0 0; flex: 1; }
        .mock-b-expense { background: #E24B4A; border-radius: 3px 3px 0 0; flex: 1; opacity: 0.7; }
        .mock-legend { display: flex; gap: 16px; margin-top: 8px; }
        .mock-legend-item { display: flex; align-items: center; gap: 5px; font-size: 11px; color: rgba(255,255,255,0.4); }
        .mock-legend-dot { width: 8px; height: 8px; border-radius: 2px; }

        /* ══════════════════════════════════════
           STATS
        ══════════════════════════════════════ */
        .stats { display: grid; grid-template-columns: repeat(4, 1fr); border-bottom: 1px solid #f0f0ee; }
        .stat { padding: 36px 20px; text-align: center; border-right: 1px solid #f0f0ee; }
        .stat:last-child { border-right: none; }
        .stat-val { font-size: 28px; font-weight: 500; color: #0F6E56; margin-bottom: 4px; }
        .stat-label { font-size: 13px; color: #6b7280; }

        /* ══════════════════════════════════════
           NOTICE
        ══════════════════════════════════════ */
        .notice-wrap { padding: 32px 40px 0; }
        .notice {
            background: #E1F5EE; border: 1px solid #D4A11E;
            border-radius: 10px; padding: 14px 20px;
            display: flex; align-items: flex-start; gap: 12px;
        }
        .notice-icon { width: 18px; height: 18px; stroke: #085041; fill: none; stroke-width: 1.5; flex-shrink: 0; margin-top: 1px; }
        .notice p { font-size: 13px; color: #085041; line-height: 1.5; }
        .notice strong { font-weight: 500; }

        /* ══════════════════════════════════════
           SCROLL REVEAL
        ══════════════════════════════════════ */
        .reveal {
            opacity: 0; transform: translateY(24px);
            transition: opacity 0.55s ease, transform 0.55s ease;
        }
        .reveal.visible { opacity: 1; transform: translateY(0); }
        .reveal-group .reveal { transition-delay: calc(var(--i, 0) * 80ms); }

        /* ══════════════════════════════════════
           FEATURES
        ══════════════════════════════════════ */
        .features { padding: 72px 40px; }
        .section-label {
            font-size: 12px; font-weight: 500; letter-spacing: 0.6px;
            color: #0F6E56; text-transform: uppercase; margin-bottom: 10px;
        }
        .section-title { font-size: 30px; font-weight: 500; color: #111827; max-width: 500px; line-height: 1.3; margin-bottom: 10px; }
        .section-sub { font-size: 15px; color: #6b7280; max-width: 480px; margin-bottom: 48px; }
        .features-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
        .feat {
            background: #f9fafb; border: 1px solid #f0f0ee;
            border-radius: 12px; padding: 24px;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .feat:hover { border-color: #D4A11E; box-shadow: 0 4px 16px rgba(212,161,30,0.1); }
        .feat-icon {
            width: 40px; height: 40px; background: #E1F5EE;
            border-radius: 8px; display: flex; align-items: center;
            justify-content: center; margin-bottom: 16px;
        }
        .feat-icon svg { width: 18px; height: 18px; stroke: #0F6E56; fill: none; stroke-width: 1.5; }
        .feat h3 { font-size: 15px; font-weight: 500; color: #111827; margin-bottom: 8px; }
        .feat p { font-size: 13px; color: #6b7280; line-height: 1.6; }

        /* ══════════════════════════════════════
           ABOUT
        ══════════════════════════════════════ */
        .about {
            padding: 72px 40px; background: #04342C;
            position: relative; overflow: hidden; text-align: center;
        }
        .about::before {
            content: ''; position: absolute; inset: 0;
            background-image: radial-gradient(circle, rgba(255,255,255,0.07) 1px, transparent 1px);
            background-size: 28px 28px; pointer-events: none;
        }
        .about-inner { position: relative; z-index: 1; max-width: 680px; margin: 0 auto; }
        .about-inner .section-label { color: #9FE1CB; }
        .about-inner .section-title { color: #fff; max-width: 100%; margin: 0 auto 16px; }
        .about-inner .section-sub  { color: rgba(255,255,255,0.6); max-width: 100%; margin-bottom: 0; }

        /* ══════════════════════════════════════
           ROLES
        ══════════════════════════════════════ */
        .roles { padding: 72px 40px; background: #f9fafb; }
        .roles-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 48px; align-items: center; }
        .role-list { display: flex; flex-direction: column; gap: 12px; margin-top: 32px; }
        .role-item {
            display: flex; align-items: flex-start; gap: 12px;
            padding: 16px; background: #fff;
            border: 1px solid #f0f0ee; border-radius: 10px; transition: border-color 0.2s;
        }
        .role-item:hover { border-color: #D4A11E; }
        .role-pip { width: 8px; height: 8px; background: #1D9E75; border-radius: 50%; margin-top: 5px; flex-shrink: 0; }
        .role-info h4 { font-size: 14px; font-weight: 500; color: #111827; margin-bottom: 2px; }
        .role-info p  { font-size: 13px; color: #6b7280; }
        .role-visual { background: #fff; border: 1px solid #f0f0ee; border-radius: 12px; overflow: hidden; }
        .rv-head {
            padding: 14px 18px; border-bottom: 1px solid #f0f0ee;
            display: flex; justify-content: space-between; align-items: center;
        }
        .rv-head span { font-size: 13px; font-weight: 500; color: #111827; }
        .rv-badge { font-size: 11px; background: #E1F5EE; color: #085041; padding: 3px 10px; border-radius: 100px; }
        .rv-row { display: flex; align-items: center; gap: 12px; padding: 12px 18px; border-bottom: 1px solid #f0f0ee; }
        .rv-row:last-child { border-bottom: none; }        /* ← Fix: removes stray bottom border */
        .rv-avatar {
            width: 32px; height: 32px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 12px; font-weight: 500; flex-shrink: 0;
        }
        .rv-name { font-size: 13px; font-weight: 500; color: #111827; }
        .rv-pos  { font-size: 12px; color: #6b7280; }
        .rv-tag  { margin-left: auto; font-size: 11px; padding: 3px 10px; border-radius: 100px; white-space: nowrap; }

        /* ══════════════════════════════════════
           CTA
        ══════════════════════════════════════ */
        .cta { padding: 72px 40px; text-align: center; border-top: 1px solid #f0f0ee; }
        .cta h2 { font-size: 30px; font-weight: 500; color: #111827; margin-bottom: 12px; }
        .cta p  { font-size: 15px; color: #6b7280; max-width: 420px; margin: 0 auto 32px; }
        .btn-cta {
            background: #0F6E56; color: #fff;
            padding: 13px 28px; border-radius: 8px;
            font-size: 14px; font-weight: 500;
            border: none; cursor: pointer;
            display: inline-flex; align-items: center; gap: 8px;
            transition: background 0.15s;
        }
        .btn-cta:hover { background: #D4A11E; }

        /* ══════════════════════════════════════
           FOOTER
        ══════════════════════════════════════ */
        footer { background: #f9fafb; border-top: 1px solid #f0f0ee; padding: 48px 40px 24px; }
        .footer-grid { display: grid; grid-template-columns: 1.5fr 1fr 1fr 1fr; gap: 40px; margin-bottom: 36px; }
        footer h4   { font-size: 13px; font-weight: 500; color: #111827; margin-bottom: 14px; }
        footer ul   { list-style: none; }
        footer li   { margin-bottom: 8px; }
        footer a    { font-size: 13px; color: #6b7280; text-decoration: none; transition: color 0.15s; }
        footer a:hover { color: #D4A11E; }
        footer button {
            font-size: 13px; color: #6b7280; background: none;
            border: none; cursor: pointer; padding: 0; transition: color 0.15s;
        }
        footer button:hover { color: #D4A11E; }
        .footer-desc { font-size: 13px; color: #6b7280; line-height: 1.6; margin-top: 10px; }
        .footer-restricted { display: inline-flex; align-items: center; gap: 6px; margin-top: 8px; font-size: 11px; color: #0F6E56; }
        .footer-restricted-dot { width: 6px; height: 6px; background: #1D9E75; border-radius: 50%; }
        .footer-bottom { border-top: 1px solid #f0f0ee; padding-top: 20px; font-size: 12px; color: #9ca3af; text-align: center; }
        .footer-privacy { font-size: 11px; color: #9ca3af; margin-top: 6px; }

        /* ══════════════════════════════════════
           RESPONSIVE
        ══════════════════════════════════════ */
        @media (max-width: 768px) {
            .nav { padding: 14px 20px; }
            .nav-links { display: none; }
            .hamburger { display: flex; }
            .hero { padding: 60px 20px 40px; }
            .hero h1 { font-size: 30px; }
            .mockup-content { grid-template-columns: 1fr 1fr; }
            .stats { grid-template-columns: 1fr 1fr; }
            .stat { border-right: none; border-bottom: 1px solid #f0f0ee; }
            .stat:nth-child(odd) { border-right: 1px solid #f0f0ee; }
            .notice-wrap { padding: 24px 20px 0; }
            .features { padding: 48px 20px; }
            .features-grid { grid-template-columns: 1fr; }
            .about { padding: 48px 20px; }
            .roles { padding: 48px 20px; }
            .roles-grid { grid-template-columns: 1fr; }
            .cta { padding: 48px 20px; }
            footer { padding: 40px 20px 20px; }
            .footer-grid { grid-template-columns: 1fr 1fr; gap: 24px; }
        }
    </style>
</head>
<body>

    {{-- ══ NAV ══ --}}
    <nav class="nav">
        <div class="nav-logo">
            <div class="nav-badge">VSU</div>
            <span class="nav-name">VSULHS SSLG</span>
        </div>
        <div class="nav-links">
            <a href="#features">Features</a>
            <a href="#about">About</a>
            <a href="#contact">Contact</a>
        </div>
        <div class="nav-right">
            {{-- Dark mode toggle — x-cloak prevents flash before Alpine boots --}}
            <button @click="darkMode = !darkMode" class="dark-toggle" aria-label="Toggle dark mode">
                <svg x-show="!darkMode" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                </svg>
                <svg x-show="darkMode" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </button>
            <button @click="loginModalOpen = true" class="nav-cta">Login</button>
            <button class="hamburger" id="hamburger" aria-label="Toggle menu">
                <span></span><span></span><span></span>
            </button>
        </div>
    </nav>

    {{-- ══ MOBILE MENU ══ --}}
    <div class="mobile-menu" id="mobileMenu">
        <a href="#features">Features</a>
        <a href="#about">About</a>
        <a href="#contact">Contact</a>
        <button @click="loginModalOpen = true; document.getElementById('mobileMenu').classList.remove('open'); document.getElementById('hamburger').classList.remove('open');">Login</button>
    </div>

    {{-- ══ HERO ══ --}}
    <section class="hero">
        <div class="hero-inner">
            <div class="hero-tag">
                <div class="hero-tag-dot"></div>
                <span>Exclusively for VSULHS SSLG</span>
            </div>
            <h1>The official portal of<br><em>VSULHS SSLG</em></h1>
            <p>Financial records, documents, and member management — built solely for the Supreme Student Learner Government of VSU Leyte High School.</p>
            <div class="hero-btns">
                <button @click="loginModalOpen = true" class="btn-primary">
                    Login to portal
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </button>
                <a href="#features" class="btn-ghost">Learn more</a>
            </div>

            <div class="hero-mockup">
                <div class="mockup-bar">
                    <div class="dot" style="background:#E24B4A"></div>
                    <div class="dot" style="background:#EF9F27"></div>
                    <div class="dot" style="background:#1D9E75"></div>
                </div>
                <div class="mockup-content">
                    <div class="mock-card">
                        <div class="mock-card-label">Total income</div>
                        <div class="mock-card-val">₱84,500</div>
                        <div class="mock-card-sub" style="color:#5DCAA5;">This term</div>
                    </div>
                    <div class="mock-card">
                        <div class="mock-card-label">Total expenses</div>
                        <div class="mock-card-val">₱52,300</div>
                        <div class="mock-card-sub" style="color:#F09595;">This term</div>
                    </div>
                    <div class="mock-card">
                        <div class="mock-card-label">Net balance</div>
                        <div class="mock-card-val">₱32,200</div>
                        <div class="mock-card-sub" style="color:#5DCAA5;">Remaining</div>
                    </div>
                    <div class="mock-bar-wrap">
                        <div class="mock-bar-label">Income vs expenses — monthly</div>
                        <div class="mock-bars">
                            <div class="mock-b-income"  style="height:40%"></div>
                            <div class="mock-b-expense" style="height:25%"></div>
                            <div class="mock-b-income"  style="height:55%"></div>
                            <div class="mock-b-expense" style="height:38%"></div>
                            <div class="mock-b-income"  style="height:70%"></div>
                            <div class="mock-b-expense" style="height:50%"></div>
                            <div class="mock-b-income"  style="height:85%"></div>
                            <div class="mock-b-expense" style="height:62%"></div>
                        </div>
                        <div class="mock-legend">
                            <div class="mock-legend-item"><div class="mock-legend-dot" style="background:#1D9E75"></div> Income</div>
                            <div class="mock-legend-item"><div class="mock-legend-dot" style="background:#E24B4A"></div> Expenses</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ══ STATS — animated counters ══ --}}
    <div class="stats">
        <div class="stat">
            <div class="stat-val stat-number" data-target="500" data-suffix="+">0</div>
            <div class="stat-label">Active members</div>
        </div>
        <div class="stat">
            <div class="stat-val stat-number" data-target="1200000" data-prefix="₱" data-format="compact">0</div>
            <div class="stat-label">Finances tracked</div>
        </div>
        <div class="stat">
            <div class="stat-val stat-number" data-target="1200" data-suffix="+">0</div>
            <div class="stat-label">Documents uploaded</div>
        </div>
        <div class="stat">
            <div class="stat-val" style="color:#0F6E56;">24/7</div>
            <div class="stat-label">System uptime</div>
        </div>
    </div>

    {{-- ══ NOTICE ══ --}}
    <div class="notice-wrap reveal">
        <div class="notice">
            <svg class="notice-icon" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            <p><strong>Restricted access.</strong> This portal is exclusively for verified members of the VSULHS Supreme Student Learner Government. Accounts are issued by the system administrator — public registration is not available.</p>
        </div>
    </div>

    {{-- ══ FEATURES ══ --}}
    <section id="features" class="features">
        <div class="reveal">
            <div class="section-label">Features</div>
            <div class="section-title">Everything VSULHS SSLG needs</div>
            <div class="section-sub">Purpose-built tools to keep your organisation's finances, documents, and members in one secure place.</div>
        </div>
        <div class="features-grid reveal-group">
            <div class="feat reveal" style="--i:0">
                <div class="feat-icon"><svg viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                <h3>Financial records</h3>
                <p>Log income and expenses with categories, dates, and attachments. Track your net balance and view monthly summaries at a glance.</p>
            </div>
            <div class="feat reveal" style="--i:1">
                <div class="feat-icon"><svg viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
                <h3>Document library</h3>
                <p>Centralised storage with version control, public and private access controls, and easy sharing across teams.</p>
            </div>
            <div class="feat reveal" style="--i:2">
                <div class="feat-icon"><svg viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div>
                <h3>Member management</h3>
                <p>Organise members by role, track positions, and manage your organisational hierarchy with clarity.</p>
            </div>
            <div class="feat reveal" style="--i:3">
                <div class="feat-icon"><svg viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg></div>
                <h3>Reports & analytics</h3>
                <p>Visualise income and expense trends, member activity, and document usage through clean dashboards.</p>
            </div>
            <div class="feat reveal" style="--i:4">
                <div class="feat-icon"><svg viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg></div>
                <h3>Notifications</h3>
                <p>Stay informed with alerts for new financial entries, document updates, and important announcements.</p>
            </div>
            <div class="feat reveal" style="--i:5">
                <div class="feat-icon"><svg viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg></div>
                <h3>Role-based access</h3>
                <p>Fine-grained permissions ensure every member sees only what they need, keeping sensitive records secure.</p>
            </div>
        </div>
    </section>

    {{-- ══ ABOUT ══ --}}
    <section id="about" class="about">
        <div class="about-inner reveal">
            <div class="section-label">About</div>
            <div class="section-title">The VSULHS Supreme Student Learner Government</div>
            <div class="section-sub">The VSULHS SSLG is the official student governing body of the Visayas State University Laboratory High School in Baybay City, Leyte. It represents the student body, promotes student welfare, and upholds transparency and accountability in all organisational activities. This portal was built to support those goals — giving officers and members a single, secure place to manage finances, documents, and records.</div>
        </div>
    </section>

    {{-- ══ ROLES ══ --}}
    <section class="roles">
        <div class="roles-grid">
            <div class="reveal">
                <div class="section-label">Access control</div>
                <div class="section-title">Built around your org structure</div>
                <div class="section-sub">Every role gets a tailored experience — advisers, officers, and members each have the right level of access.</div>
                <div class="role-list">
                    <div class="role-item">
                        <div class="role-pip"></div>
                        <div class="role-info"><h4>Administrator / Adviser</h4><p>Full access to all modules, financial records, audit logs, and system settings</p></div>
                    </div>
                    <div class="role-item">
                        <div class="role-pip"></div>
                        <div class="role-info"><h4>Officer</h4><p>Log income and expenses, upload documents, and manage org members</p></div>
                    </div>
                    <div class="role-item">
                        <div class="role-pip"></div>
                        <div class="role-info"><h4>Member</h4><p>View public documents and org-wide financial summaries</p></div>
                    </div>
                </div>
            </div>
            <div class="role-visual reveal">
                <div class="rv-head">
                    <span>Members</span>
                    <span class="rv-badge">SY 2025–2026</span>
                </div>
                <div class="rv-row">
                    <div class="rv-avatar" style="background:#E1F5EE; color:#085041;">JR</div>
                    <div><div class="rv-name">Juan Reyes</div><div class="rv-pos">Student President</div></div>
                    <div class="rv-tag" style="background:#E1F5EE; color:#085041;">Adviser</div>
                </div>
                <div class="rv-row">
                    <div class="rv-avatar" style="background:#E6F1FB; color:#0C447C;">MA</div>
                    <div><div class="rv-name">Maria Andres</div><div class="rv-pos">Secretary General</div></div>
                    <div class="rv-tag" style="background:#E6F1FB; color:#0C447C;">Officer</div>
                </div>
                <div class="rv-row">
                    <div class="rv-avatar" style="background:#EEEDFE; color:#3C3489;">KC</div>
                    <div><div class="rv-name">Karl Cruz</div><div class="rv-pos">Finance Head</div></div>
                    <div class="rv-tag" style="background:#EEEDFE; color:#3C3489;">Officer</div>
                </div>
                <div class="rv-row">
                    <div class="rv-avatar" style="background:#F1EFE8; color:#444441;">RL</div>
                    <div><div class="rv-name">Rose Lim</div><div class="rv-pos">Grade 11 — STEM</div></div>
                    <div class="rv-tag" style="background:#F1EFE8; color:#444441;">Member</div>
                </div>
            </div>
        </div>
    </section>

    {{-- ══ CTA ══ --}}
    <section class="cta reveal">
        <h2>VSULHS SSLG members only</h2>
        <p>This portal is exclusively for verified VSULHS SSLG members. Contact your administrator to receive access.</p>
        <button @click="loginModalOpen = true" class="btn-cta">
            Login to the portal
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
        </button>
    </section>

    {{-- ══ FOOTER ══ --}}
    <footer id="contact">
        <div class="footer-grid">
            <div>
                <div class="nav-logo">
                    <div class="nav-badge">VSU</div>
                    <span class="nav-name">VSULHS SSLG</span>
                </div>
                <p class="footer-desc">The official management portal of the VSU Laboratory High School Supreme Student Learner Government.</p>
                <div class="footer-restricted">
                    <div class="footer-restricted-dot"></div>
                    Restricted — VSULHS SSLG members only
                </div>
            </div>
            <div>
                <h4>Navigation</h4>
                <ul>
                    <li><a href="#features">Features</a></li>
                    <li><a href="#about">About</a></li>
                    <li><a href="#contact">Contact</a></li>
                    <li><button @click="loginModalOpen = true">Login</button></li>
                </ul>
            </div>
            <div>
                <h4>Resources</h4>
                <ul>
                    <li><a href="#">Help centre</a></li>
                    <li><a href="#">Privacy policy</a></li>
                    <li><a href="#">Terms of service</a></li>
                </ul>
            </div>
            <div>
                <h4>Contact</h4>
                <ul>
                    <li><a href="https://maps.google.com/?q=Visayas+State+University+Integrated+High+School" target="_blank" rel="noopener">VSULHS, Baybay City, Leyte</a></li>
                    <li><a href="/cdn-cgi/l/email-protection#5c2f2f303b1c2a2f2930342f72393829722c34"><span class="__cf_email__" data-cfemail="e596968982a5939690898d96cb808190cb958d">[email&#160;protected]</span></a></li>
                    <li><a href="tel:+639256353456">+63 925 635 3456</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            &copy; {{ date('Y') }} VSULHS Supreme Student Learner Government. All rights reserved.
            <div class="footer-privacy">Personal information collected by this system is protected under Republic Act No. 10173 (Data Privacy Act of 2012).</div>
        </div>
    </footer>

    {{-- ══ LOGIN MODAL ══ --}}
        {{-- ══ LOGIN MODAL ══ --}}
    <div x-show="loginModalOpen"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="background: rgba(0,0,0,0.6); backdrop-filter: blur(8px);"
         @click.self="loginModalOpen = false"
         @keydown.window.escape="loginModalOpen = false">

        <div x-data="loginForm()"
             class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md overflow-hidden border border-gray-200 dark:border-gray-700"
             @click.stop>

            {{-- Modal header — no X button; Cancel button in the form handles closing --}}
            <div class="px-8 py-6 text-center" style="background: linear-gradient(135deg, #0F6E56, #085041);">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl mb-4" style="background: rgba(255,255,255,0.15);">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-white tracking-tight">VSULHS SSLG</h2>
                <p class="text-sm mt-1" style="color: rgba(255,255,255,0.7);">Supreme Student Learner Government</p>
            </div>

            <div class="p-8">

                {{-- Flash: success --}}
                @if (session('success'))
                    <div class="flash-message mb-4 flex items-center gap-2 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-300 px-4 py-3 rounded-lg text-sm">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ session('success') }}
                    </div>
                @endif

                {{-- Flash: errors --}}
                @if ($errors->any())
                    <div class="flash-message mb-4 flex items-start gap-2 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-300 px-4 py-3 rounded-lg text-sm">
                        <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div><span class="font-semibold">Error:</span> {{ $errors->first() }}</div>
                    </div>
                @endif

                <form method="POST" action="{{ route('login.post') }}" @submit="loading = true" class="space-y-5">
                    @csrf

                    {{-- Email --}}
                    <div>
                        <label for="modal_email" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                            Email address
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                </svg>
                            </div>
                            <input type="email"
                                   id="modal_email"
                                   name="email"
                                   value="{{ old('email') }}"
                                   required
                                   autocomplete="email"
                                   placeholder="yourname@gmail.com"
                                   class="w-full pl-10 pr-4 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500 transition dark:bg-gray-700 dark:text-white {{ $errors->has('email') ? 'border-red-400' : 'border-gray-300 dark:border-gray-600' }}">
                        </div>
                    </div>

                    {{-- Password --}}
                    <div>
                        <label for="modal_password" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                            Password
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6-4h12a2 2 0 002-2v-4a2 2 0 00-2-2H6a2 2 0 00-2 2v4a2 2 0 002 2zm10-2V9a2 2 0 00-2-2H8a2 2 0 00-2 2v4"/>
                                </svg>
                            </div>
                            <input :type="showPassword ? 'text' : 'password'"
                                   id="modal_password"
                                   name="password"
                                   required
                                   autocomplete="current-password"
                                   placeholder="••••••••"
                                   class="w-full pl-10 pr-10 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500 transition dark:bg-gray-700 dark:text-white {{ $errors->has('password') ? 'border-red-400' : 'border-gray-300 dark:border-gray-600' }}">
                            <button type="button"
                                    @click="showPassword = !showPassword"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition"
                                    aria-label="Toggle password visibility">
                                <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <svg x-show="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 01-4.243-4.243m4.242 4.242L9.88 9.88"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Remember me + Forgot password --}}
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox"
                                   name="remember"
                                   id="modal_remember"
                                   x-model="rememberMe"
                                   class="rounded border-gray-300 dark:border-gray-600 text-emerald-600 focus:ring-2 focus:ring-gold-500 transition">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Remember me</span>
                        </label>
                        <a href="{{ route('password.request') }}"
                           class="text-sm font-medium text-emerald-700 hover:text-yellow-600 dark:text-emerald-400 transition">
                            Forgot password?
                        </a>
                    </div>

                    {{-- Submit + Cancel --}}
                    <div class="flex gap-3">
                        <button type="submit"
                                :disabled="loading"
                                class="flex-1 text-white font-semibold py-2.5 rounded-lg text-sm transition-all shadow-sm focus:outline-none focus:ring-2 focus:ring-gold-500"
                                :style="loading ? 'background:#0F6E56; opacity:0.8; cursor:not-allowed;' : 'background:#0F6E56;'"
                                onmouseover="if(!this.disabled) this.style.background='#D4A11E'"
                                onmouseout="if(!this.disabled) this.style.background='#0F6E56'">
                            <span x-show="!loading">Sign in</span>
                            <span x-show="loading" class="flex items-center justify-center gap-2">
                                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                                </svg>
                                Signing in…
                            </span>
                        </button>
                        <button type="button"
                                @click="loginModalOpen = false"
                                class="flex-1 rounded-lg text-sm font-semibold py-2.5 transition focus:outline-none focus:ring-2 focus:ring-gold-500"
                                style="background:#dc2626; color:#fff; border:none;"
                                onmouseover="this.style.background='#b91c1c'"
                                onmouseout="this.style.background='#dc2626'">
                            Cancel
                        </button>
                    </div>
                </form>

                {{-- Footer note --}}
                <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <div class="flex items-start gap-2 text-xs text-gray-500 dark:text-gray-400">
                        <svg class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6-4h12a2 2 0 002-2v-4a2 2 0 00-2-2H6a2 2 0 00-2 2v4a2 2 0 002 2zm10-2V9a2 2 0 00-2-2H8a2 2 0 00-2 2v4"/>
                        </svg>
                        <span>Only check "Remember me" on your personal device. Always log out on shared computers.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══ BACK TO TOP ══ --}}
    <button id="backToTop"
            class="fixed bottom-6 right-6 text-white p-3 rounded-full shadow-lg z-50 opacity-0 invisible"
            style="background: #0F6E56;"
            aria-label="Back to top">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
        </svg>
    </button>

    <script data-cfasync="false" src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script><script>
        // ── Alpine component — loginForm ──────────────────────────────────
        document.addEventListener('alpine:init', () => {
            Alpine.data('loginForm', () => ({
                showPassword: false,
                rememberMe:   {{ old('remember') ? 'true' : 'false' }},
                loading:      false,
            }));
        });

        // ── Hamburger menu ────────────────────────────────────────────────
        const hamburger = document.getElementById('hamburger');
        const mobileMenu = document.getElementById('mobileMenu');

        hamburger.addEventListener('click', () => {
            hamburger.classList.toggle('open');
            mobileMenu.classList.toggle('open');
        });
        document.addEventListener('click', (e) => {
            if (!hamburger.contains(e.target) && !mobileMenu.contains(e.target)) {
                hamburger.classList.remove('open');
                mobileMenu.classList.remove('open');
            }
        });

        // ── Smooth scroll ─────────────────────────────────────────────────
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    mobileMenu.classList.remove('open');
                    hamburger.classList.remove('open');
                }
            });
        });

        // ── Scroll reveal ─────────────────────────────────────────────────
        const revealObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    revealObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12 });
        document.querySelectorAll('.reveal').forEach(el => revealObserver.observe(el));

        // ── Counting stats ────────────────────────────────────────────────
        // Handles data-prefix, data-suffix, data-format="compact"
        const countObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (!entry.isIntersecting) return;
                const el       = entry.target;
                const target   = parseInt(el.getAttribute('data-target'));
                const prefix   = el.getAttribute('data-prefix') || '';
                const suffix   = el.getAttribute('data-suffix') || '';
                const fmt      = el.getAttribute('data-format');
                if (isNaN(target)) return;

                let current = 0;
                const steps = 60;
                const increment = Math.ceil(target / steps);

                const format = (n) => {
                    if (fmt === 'compact') {
                        if (n >= 1000000) return prefix + (n / 1000000).toFixed(1) + 'M' + suffix;
                        if (n >= 1000)    return prefix + (n / 1000).toFixed(1) + 'K' + suffix;
                    }
                    return prefix + n.toLocaleString() + suffix;
                };

                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        el.innerText = format(target);
                        clearInterval(timer);
                    } else {
                        el.innerText = format(current);
                    }
                }, 16);

                countObserver.unobserve(el);
            });
        }, { threshold: 0.5 });
        document.querySelectorAll('.stat-number').forEach(el => countObserver.observe(el));

        // ── Back to top ───────────────────────────────────────────────────
        const backToTop = document.getElementById('backToTop');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 300) {
                backToTop.classList.remove('opacity-0', 'invisible');
                backToTop.classList.add('opacity-100', 'visible');
            } else {
                backToTop.classList.add('opacity-0', 'invisible');
                backToTop.classList.remove('opacity-100', 'visible');
            }
        });
        backToTop.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));

        // ── Flash message auto-hide ───────────────────────────────────────
        document.querySelectorAll('.flash-message').forEach(msg => {
            setTimeout(() => {
                msg.classList.add('opacity-0');
                setTimeout(() => msg.remove(), 500);
            }, 4000);
        });
    </script>
</body>
</html>
        