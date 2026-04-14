<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VSULHS SSLG — Student Government Portal</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;
            color: #111827;
            background: #ffffff;
            font-size: 15px;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        /* ── Nav ── */
        .nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 18px 40px;
            border-bottom: 1px solid #f0f0ee;
            background: #ffffff;
            position: sticky;
            top: 0;
            z-index: 50;
        }
        .nav-logo { display: flex; align-items: center; gap: 10px; }
        .nav-badge {
            width: 32px; height: 32px;
            background: #0F6E56;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 10px; font-weight: 500; letter-spacing: 0.3px;
        }
        .nav-name { font-weight: 500; font-size: 15px; color: #111827; }
        .nav-links { display: flex; gap: 28px; }
        .nav-links a { font-size: 14px; color: #6b7280; text-decoration: none; transition: color 0.15s; }
        .nav-links a:hover { color: #111827; }
        .nav-right { display: flex; align-items: center; gap: 12px; }
        .nav-cta {
            background: #0F6E56; color: #fff;
            padding: 8px 18px; border-radius: 8px;
            font-size: 13px; font-weight: 500; text-decoration: none;
            transition: background 0.15s;
        }
        .nav-cta:hover { background: #085041; }

        /* ── Hamburger ── */
        .hamburger {
            display: none;
            flex-direction: column;
            justify-content: center;
            gap: 5px;
            width: 36px; height: 36px;
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

        /* Mobile drawer */
        .mobile-menu {
            display: none;
            position: fixed;
            top: 65px; left: 0; right: 0;
            background: #fff;
            border-bottom: 1px solid #f0f0ee;
            padding: 16px 24px 20px;
            z-index: 49;
            flex-direction: column;
            gap: 4px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.06);
        }
        .mobile-menu.open { display: flex; }
        .mobile-menu a {
            font-size: 15px; color: #374151; text-decoration: none;
            padding: 10px 4px;
            border-bottom: 1px solid #f9fafb;
        }
        .mobile-menu a:last-child { border-bottom: none; }
        .mobile-menu a:hover { color: #0F6E56; }

        /* ── Hero ── */
        .hero {
            position: relative;
            background: #04342C;
            padding: 80px 40px 60px;
            text-align: center;
            overflow: hidden;
        }
        /* Dot grid pattern */
        .hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(circle, rgba(255,255,255,0.12) 1px, transparent 1px);
            background-size: 28px 28px;
            mask-image: radial-gradient(ellipse 80% 80% at 50% 50%, black 40%, transparent 100%);
            -webkit-mask-image: radial-gradient(ellipse 80% 80% at 50% 50%, black 40%, transparent 100%);
            pointer-events: none;
        }
        /* Subtle animated glow orb */
        .hero::after {
            content: '';
            position: absolute;
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(29,158,117,0.15) 0%, transparent 70%);
            top: -100px; left: 50%;
            transform: translateX(-50%);
            pointer-events: none;
            animation: pulse-orb 6s ease-in-out infinite;
        }
        @keyframes pulse-orb {
            0%, 100% { opacity: 0.6; transform: translateX(-50%) scale(1); }
            50%       { opacity: 1;   transform: translateX(-50%) scale(1.1); }
        }
        .hero-inner { position: relative; z-index: 1; }
        .hero-tag {
            display: inline-flex; align-items: center; gap: 6px;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 100px;
            padding: 5px 14px;
            margin-bottom: 28px;
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
            font-size: 14px; font-weight: 500; text-decoration: none;
            display: inline-flex; align-items: center; gap: 6px;
            transition: background 0.15s;
        }
        .btn-primary:hover { background: #0F6E56; }
        .btn-ghost {
            background: rgba(255,255,255,0.06); color: rgba(255,255,255,0.8);
            padding: 11px 24px; border-radius: 8px;
            font-size: 14px; font-weight: 500; text-decoration: none;
            border: 1px solid rgba(255,255,255,0.15);
            transition: background 0.15s;
        }
        .btn-ghost:hover { background: rgba(255,255,255,0.1); }

        /* ── Hero Mockup ── */
        .hero-mockup {
            margin: 48px auto 0; max-width: 760px;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 12px; overflow: hidden;
        }
        .mockup-bar {
            background: rgba(255,255,255,0.06);
            padding: 10px 16px;
            display: flex; align-items: center; gap: 6px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }
        .dot { width: 10px; height: 10px; border-radius: 50%; }
        .mockup-content {
            padding: 24px;
            display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px;
        }
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
            border-radius: 8px; padding: 16px;
            grid-column: 1 / -1;
        }
        .mock-bar-label { font-size: 11px; color: rgba(255,255,255,0.4); margin-bottom: 10px; }
        .mock-bars { display: flex; align-items: flex-end; gap: 6px; height: 60px; }
        .mock-b-income  { background: #1D9E75; border-radius: 3px 3px 0 0; flex: 1; }
        .mock-b-expense { background: #E24B4A; border-radius: 3px 3px 0 0; flex: 1; opacity: 0.7; }
        .mock-legend { display: flex; gap: 16px; margin-top: 8px; }
        .mock-legend-item { display: flex; align-items: center; gap: 5px; font-size: 11px; color: rgba(255,255,255,0.4); }
        .mock-legend-dot { width: 8px; height: 8px; border-radius: 2px; }

        /* ── Stats ── */
        .stats { display: grid; grid-template-columns: repeat(4, 1fr); border-bottom: 1px solid #f0f0ee; }
        .stat { padding: 36px 20px; text-align: center; border-right: 1px solid #f0f0ee; }
        .stat:last-child { border-right: none; }
        .stat-val { font-size: 28px; font-weight: 500; color: #0F6E56; margin-bottom: 4px; }
        .stat-label { font-size: 13px; color: #6b7280; }

        /* ── Notice ── */
        .notice-wrap { padding: 32px 40px 0; }
        .notice {
            background: #E1F5EE;
            border: 1px solid #5DCAA5;
            border-radius: 10px; padding: 14px 20px;
            display: flex; align-items: flex-start; gap: 12px;
        }
        .notice-icon { width: 18px; height: 18px; stroke: #085041; fill: none; stroke-width: 1.5; flex-shrink: 0; margin-top: 1px; }
        .notice p { font-size: 13px; color: #085041; line-height: 1.5; }
        .notice strong { font-weight: 500; }

        /* ── Scroll fade-in ── */
        .reveal {
            opacity: 0;
            transform: translateY(24px);
            transition: opacity 0.55s ease, transform 0.55s ease;
        }
        .reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }
        .reveal-group .reveal {
            transition-delay: calc(var(--i, 0) * 80ms);
        }

        /* ── Features ── */
        .features { padding: 72px 40px; }
        .section-label {
            font-size: 12px; font-weight: 500;
            letter-spacing: 0.6px; color: #0F6E56;
            text-transform: uppercase; margin-bottom: 10px;
        }
        .section-title { font-size: 30px; font-weight: 500; color: #111827; max-width: 500px; line-height: 1.3; margin-bottom: 10px; }
        .section-sub { font-size: 15px; color: #6b7280; max-width: 480px; margin-bottom: 48px; }
        .features-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
        .feat {
            background: #f9fafb;
            border: 1px solid #f0f0ee;
            border-radius: 12px; padding: 24px;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .feat:hover {
            border-color: #5DCAA5;
            box-shadow: 0 4px 16px rgba(15,110,86,0.07);
        }
        .feat-icon {
            width: 40px; height: 40px;
            background: #E1F5EE; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 16px;
        }
        .feat-icon svg { width: 18px; height: 18px; stroke: #0F6E56; fill: none; stroke-width: 1.5; }
        .feat h3 { font-size: 15px; font-weight: 500; color: #111827; margin-bottom: 8px; }
        .feat p { font-size: 13px; color: #6b7280; line-height: 1.6; }

        /* ── About ── */
        .about {
            padding: 72px 40px;
            background: #04342C;
            position: relative;
            overflow: hidden;
            text-align: center;
        }
        .about::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(circle, rgba(255,255,255,0.07) 1px, transparent 1px);
            background-size: 28px 28px;
            pointer-events: none;
        }
        .about-inner { position: relative; z-index: 1; max-width: 680px; margin: 0 auto; }
        .about-inner .section-label { color: #9FE1CB; }
        .about-inner .section-title { color: #fff; max-width: 100%; margin: 0 auto 16px; }
        .about-inner .section-sub  { color: rgba(255,255,255,0.6); max-width: 100%; margin-bottom: 0; }

        /* ── Roles ── */
        .roles { padding: 72px 40px; background: #f9fafb; }
        .roles-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 48px; align-items: center; }
        .role-list { display: flex; flex-direction: column; gap: 12px; margin-top: 32px; }
        .role-item {
            display: flex; align-items: flex-start; gap: 12px;
            padding: 16px; background: #fff;
            border: 1px solid #f0f0ee; border-radius: 10px;
            transition: border-color 0.2s;
        }
        .role-item:hover { border-color: #5DCAA5; }
        .role-pip { width: 8px; height: 8px; background: #1D9E75; border-radius: 50%; margin-top: 5px; flex-shrink: 0; }
        .role-info h4 { font-size: 14px; font-weight: 500; color: #111827; margin-bottom: 2px; }
        .role-info p { font-size: 13px; color: #6b7280; }
        .role-visual { background: #fff; border: 1px solid #f0f0ee; border-radius: 12px; overflow: hidden; }
        .rv-head {
            padding: 14px 18px; border-bottom: 1px solid #f0f0ee;
            display: flex; justify-content: space-between; align-items: center;
        }
        .rv-head span { font-size: 13px; font-weight: 500; color: #111827; }
        .rv-badge { font-size: 11px; background: #E1F5EE; color: #085041; padding: 3px 10px; border-radius: 100px; }
        .rv-row { display: flex; align-items: center; gap: 12px; padding: 12px 18px; border-bottom: 1px solid #f0f0ee; }
        .rv-row:last-child { border-bottom: none; }
        .rv-avatar {
            width: 32px; height: 32px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 12px; font-weight: 500; flex-shrink: 0;
        }
        .rv-name { font-size: 13px; font-weight: 500; color: #111827; }
        .rv-pos { font-size: 12px; color: #6b7280; }
        .rv-tag { margin-left: auto; font-size: 11px; padding: 3px 10px; border-radius: 100px; white-space: nowrap; }

        /* ── CTA ── */
        .cta { padding: 72px 40px; text-align: center; border-top: 1px solid #f0f0ee; }
        .cta h2 { font-size: 30px; font-weight: 500; color: #111827; margin-bottom: 12px; }
        .cta p { font-size: 15px; color: #6b7280; max-width: 420px; margin: 0 auto 32px; }
        .btn-cta {
            background: #0F6E56; color: #fff;
            padding: 13px 28px; border-radius: 8px;
            font-size: 14px; font-weight: 500; text-decoration: none;
            display: inline-flex; align-items: center; gap: 8px;
            transition: background 0.15s;
        }
        .btn-cta:hover { background: #085041; }

        /* ── Footer ── */
        footer {
            background: #f9fafb;
            border-top: 1px solid #f0f0ee;
            padding: 48px 40px 24px;
        }
        .footer-grid { display: grid; grid-template-columns: 1.5fr 1fr 1fr 1fr; gap: 40px; margin-bottom: 36px; }
        footer h4 { font-size: 13px; font-weight: 500; color: #111827; margin-bottom: 14px; }
        footer ul { list-style: none; }
        footer li { margin-bottom: 8px; }
        footer a { font-size: 13px; color: #6b7280; text-decoration: none; transition: color 0.15s; }
        footer a:hover { color: #0F6E56; }
        .footer-desc { font-size: 13px; color: #6b7280; line-height: 1.6; margin-top: 10px; }
        .footer-restricted { display: inline-flex; align-items: center; gap: 6px; margin-top: 8px; font-size: 11px; color: #0F6E56; }
        .footer-restricted-dot { width: 6px; height: 6px; background: #1D9E75; border-radius: 50%; }
        .footer-bottom {
            border-top: 1px solid #f0f0ee; padding-top: 20px;
            font-size: 12px; color: #9ca3af; text-align: center;
        }
        .footer-privacy { font-size: 11px; color: #9ca3af; margin-top: 6px; }

        /* ── Responsive ── */
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

    {{-- Navigation --}}
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
            <a href="{{ route('login') }}" class="nav-cta">Login</a>
            <button class="hamburger" id="hamburger" aria-label="Toggle menu">
                <span></span><span></span><span></span>
            </button>
        </div>
    </nav>

    {{-- Mobile Menu --}}
    <div class="mobile-menu" id="mobileMenu">
        <a href="#features">Features</a>
        <a href="#about">About</a>
        <a href="#contact">Contact</a>
        <a href="{{ route('login') }}">Login</a>
    </div>

    {{-- Hero --}}
    <section class="hero">
        <div class="hero-inner">
            <div class="hero-tag">
                <div class="hero-tag-dot"></div>
                <span>Exclusively for VSULHS SSLG</span>
            </div>
            <h1>The official portal of<br><em>VSULHS SSLG</em></h1>
            <p>Financial records, documents, and member management — built solely for the Supreme Student Leadership Government of VSU Leyte High School.</p>
            <div class="hero-btns">
                <a href="{{ route('login') }}" class="btn-primary">
                    Login to portal
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </a>
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

    {{-- Stats --}}
    <div class="stats">
        <div class="stat"><div class="stat-val">500+</div><div class="stat-label">Active members</div></div>
        <div class="stat"><div class="stat-val">₱1.2M</div><div class="stat-label">Finances tracked</div></div>
        <div class="stat"><div class="stat-val">1,200+</div><div class="stat-label">Documents uploaded</div></div>
        <div class="stat"><div class="stat-val">24/7</div><div class="stat-label">System uptime</div></div>
    </div>

    {{-- Access Notice --}}
    <div class="notice-wrap reveal">
        <div class="notice">
            <svg class="notice-icon" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            <p><strong>Restricted access.</strong> This portal is exclusively for verified members of the VSULHS Supreme Student Leadership Government. Accounts are issued by the system administrator — public registration is not available.</p>
        </div>
    </div>

    {{-- Features --}}
    <section id="features" class="features">
        <div class="reveal">
            <div class="section-label">Features</div>
            <div class="section-title">Everything VSULHS SSLG needs</div>
            <div class="section-sub">Purpose-built tools to keep your organisation's finances, documents, and members in one secure place.</div>
        </div>
        <div class="features-grid reveal-group">
            <div class="feat reveal" style="--i:0">
                <div class="feat-icon">
                    <svg viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3>Financial records</h3>
                <p>Log income and expenses with categories, dates, and attachments. Track your net balance and view monthly summaries at a glance.</p>
            </div>
            <div class="feat reveal" style="--i:1">
                <div class="feat-icon">
                    <svg viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <h3>Document library</h3>
                <p>Centralised storage with version control, public and private access controls, and easy sharing across teams.</p>
            </div>
            <div class="feat reveal" style="--i:2">
                <div class="feat-icon">
                    <svg viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <h3>Member management</h3>
                <p>Organise members by role, track positions, and manage your organisational hierarchy with clarity.</p>
            </div>
            <div class="feat reveal" style="--i:3">
                <div class="feat-icon">
                    <svg viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <h3>Reports & analytics</h3>
                <p>Visualise income and expense trends, member activity, and document usage through clean dashboards.</p>
            </div>
            <div class="feat reveal" style="--i:4">
                <div class="feat-icon">
                    <svg viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                </div>
                <h3>Notifications</h3>
                <p>Stay informed with alerts for new financial entries, document updates, and important announcements.</p>
            </div>
            <div class="feat reveal" style="--i:5">
                <div class="feat-icon">
                    <svg viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <h3>Role-based access</h3>
                <p>Fine-grained permissions ensure every member sees only what they need, keeping sensitive records secure.</p>
            </div>
        </div>
    </section>

    {{-- About --}}
    <section id="about" class="about">
        <div class="about-inner reveal">
            <div class="section-label">About</div>
            <div class="section-title">The VSULHS Supreme Student Leadership Government</div>
            <div class="section-sub">The VSULHS SSLG is the official student governing body of the Visayas State University Leyte High School in Villaba, Leyte. It represents the student body, promotes student welfare, and upholds transparency and accountability in all organisational activities. This portal was built to support those goals — giving officers and members a single, secure place to manage finances, documents, and records.</div>
        </div>
    </section>

    {{-- Roles --}}
    <section class="roles">
        <div class="roles-grid">
            <div class="reveal">
                <div class="section-label">Access control</div>
                <div class="section-title">Built around your org structure</div>
                <div class="section-sub">Every role gets a tailored experience — advisers, officers, and members each have the right level of access.</div>
                <div class="role-list">
                    <div class="role-item">
                        <div class="role-pip"></div>
                        <div class="role-info">
                            <h4>Administrator / Adviser</h4>
                            <p>Full access to all modules, financial records, audit logs, and system settings</p>
                        </div>
                    </div>
                    <div class="role-item">
                        <div class="role-pip"></div>
                        <div class="role-info">
                            <h4>Officer</h4>
                            <p>Log income and expenses, upload documents, and manage org members</p>
                        </div>
                    </div>
                    <div class="role-item">
                        <div class="role-pip"></div>
                        <div class="role-info">
                            <h4>Member</h4>
                            <p>View public documents and org-wide financial summaries</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="role-visual reveal">
                <div class="rv-head">
                    <span>Members</span>
                    <span class="rv-badge">SY 2024–2025</span>
                </div>
                <div class="rv-row">
                    <div class="rv-avatar" style="background:#E1F5EE; color:#085041;">JR</div>
                    <div>
                        <div class="rv-name">Juan Reyes</div>
                        <div class="rv-pos">Student President</div>
                    </div>
                    <div class="rv-tag" style="background:#E1F5EE; color:#085041;">Adviser</div>
                </div>
                <div class="rv-row">
                    <div class="rv-avatar" style="background:#E6F1FB; color:#0C447C;">MA</div>
                    <div>
                        <div class="rv-name">Maria Andres</div>
                        <div class="rv-pos">Secretary General</div>
                    </div>
                    <div class="rv-tag" style="background:#E6F1FB; color:#0C447C;">Officer</div>
                </div>
                <div class="rv-row">
                    <div class="rv-avatar" style="background:#EEEDFE; color:#3C3489;">KC</div>
                    <div>
                        <div class="rv-name">Karl Cruz</div>
                        <div class="rv-pos">Finance Head</div>
                    </div>
                    <div class="rv-tag" style="background:#EEEDFE; color:#3C3489;">Officer</div>
                </div>
                <div class="rv-row">
                    <div class="rv-avatar" style="background:#F1EFE8; color:#444441;">RL</div>
                    <div>
                        <div class="rv-name">Rose Lim</div>
                        <div class="rv-pos">Grade 11 — STEM</div>
                    </div>
                    <div class="rv-tag" style="background:#F1EFE8; color:#444441;">Member</div>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="cta reveal">
        <h2>VSULHS SSLG members only</h2>
        <p>This portal is exclusively for verified VSULHS SSLG members. Contact your administrator to receive access.</p>
        <a href="{{ route('login') }}" class="btn-cta">
            Login to the portal
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
        </a>
    </section>

    {{-- Footer --}}
    <footer id="contact">
        <div class="footer-grid">
            <div>
                <div class="nav-logo">
                    <div class="nav-badge">VSU</div>
                    <span class="nav-name">VSULHS SSLG</span>
                </div>
                <p class="footer-desc">The official management portal of the VSU Leyte High School Supreme Student Leadership Government.</p>
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
                    <li><a href="{{ route('login') }}">Login</a></li>
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
                    <li><a href="#">VSULHS, Villaba, Leyte</a></li>
                    <li><a href="mailto:sslg@vsulhs.edu.ph">sslg@vsulhs.edu.ph</a></li>
                    <li><a href="#">+63 912 345 6789</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            &copy; {{ date('Y') }} VSULHS Supreme Student Leadership Government. All rights reserved.
            <div class="footer-privacy">Personal information collected by this system is protected under Republic Act No. 10173 (Data Privacy Act of 2012).</div>
        </div>
    </footer>

    <script>
        // ── Smooth scroll ──
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    // close mobile menu if open
                    document.getElementById('mobileMenu').classList.remove('open');
                    document.getElementById('hamburger').classList.remove('open');
                }
            });
        });

        // ── Hamburger menu (Alpine-safe: pure vanilla, no shared DOM state) ──
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

        // ── Intersection Observer scroll fade-in ──
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12 });

        document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
    </script>

</body>
</html>