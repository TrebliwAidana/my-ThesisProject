<!DOCTYPE html>
<html lang="en"
      x-data="{
          darkMode: localStorage.getItem('dark') === 'true' ? true : (localStorage.getItem('dark') === null ? window.matchMedia('(prefers-color-scheme: dark)').matches : false),
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
    <title>VSULHS SSLG — Student Government Portal</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;1,9..40,300&display=swap" rel="stylesheet">

    <style>
        [x-cloak] { display: none !important; }
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            transition: background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease;
        }

        :root {
            --forest:   #0A4A38;
            --jade:     #12745A;
            --mint:     #1DB384;
            --pale:     #D6F0E7;
            --gold:     #C89B2A;
            --gold-lt:  #F5D98B;
            --ink:      #0D1510;
            --mist:     #F3F5F2;
            --cloud:    #E8EDE9;
            --slate:    #5C6B61;
            --serif:    'DM Serif Display', Georgia, serif;
            --sans:     'DM Sans', ui-sans-serif, system-ui, sans-serif;
            --r:        .6s cubic-bezier(.22,.68,0,1.2);
            --brilliant-gold: #D4AF37;
            --emerald:  #059669;
            --emerald-dark: #047857;
            --emerald-hover: #10B981;
        }

        /* Dark tokens – improved contrast for small text */
        .dark {
            --forest:   #1DB384;
            --jade:     #25D49A;
            --mint:     #4EEDB5;
            --pale:     #0D2B22;
            --gold:     #F5D98B;
            --gold-lt:  #FFF3C4;
            --ink:      #EBF0EC;
            --mist:     #0C1510;
            --cloud:    #162319;
            --slate:    #B8CFC2;    /* lighter for better contrast */
            --emerald:  #10B981;
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: var(--sans);
            background: #FAFBF9;
            color: var(--ink);
            font-size: 16px;
            line-height: 1.65;
            -webkit-font-smoothing: antialiased;
            transition: background .3s, color .3s;
        }
        .dark body { background: #0B110E; }
        body.modal-open { overflow: hidden; }

        /* Focus outlines for all interactive elements */
        a:focus-visible, button:focus-visible,
        .btn-toggle:focus-visible, .mobile-dark-toggle:focus-visible,
        .hamburger:focus-visible {
            outline: 2px solid var(--brilliant-gold);
            outline-offset: 3px;
            border-radius: 4px;
        }

        /* ════════════════════════════════════
           NAV
        ════════════════════════════════════ */
        .nav {
            position: sticky; top: 0; z-index: 100;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 48px;
            height: 64px;
            background: rgba(250,251,249,.85);
            backdrop-filter: blur(16px) saturate(1.4);
            -webkit-backdrop-filter: blur(16px) saturate(1.4);
            border-bottom: 1px solid rgba(12,45,30,.08);
        }
        .dark .nav {
            background: rgba(11,17,14,.85);
            border-bottom-color: rgba(255,255,255,.07);
        }

        .nav-brand { display: flex; align-items: center; gap: 12px; text-decoration: none; }

        .nav-crest {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            background: transparent;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            overflow: hidden;
        }
        .nav-crest img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        /* No filter on logo in dark mode */
        .dark .nav-crest img {
            filter: none;
        }

        .nav-wordmark {
            font-family: var(--serif);
            font-size: 17px;
            color: var(--ink);
            letter-spacing: -.01em;
        }

        .nav-links {
            display: flex; gap: 32px; list-style: none;
        }
        .nav-links a {
            font-size: 14px; font-weight: 400;
            color: var(--slate);
            text-decoration: none;
            letter-spacing: .01em;
            transition: color .2s;
        }
        .nav-links a:hover { color: var(--brilliant-gold); }

        .nav-actions { display: flex; align-items: center; gap: 10px; }

        .btn-toggle {
            width: 36px; height: 36px;
            background: none; border: none; cursor: pointer;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            color: var(--slate);
            transition: background .15s, color .15s;
        }
        .btn-toggle:hover { background: var(--cloud); color: var(--ink); }
        .dark .btn-toggle:hover { background: rgba(255,255,255,.08); color: #fff; }
        .btn-toggle svg { width: 18px; height: 18px; stroke: currentColor; fill: none; stroke-width: 1.8; }

        .btn-login {
            font-family: var(--sans);
            font-size: 13px; font-weight: 500;
            padding: 8px 20px;
            border-radius: 8px;
            background: var(--emerald);
            color: #fff;
            border: none; cursor: pointer;
            letter-spacing: .02em;
            transition: background .2s, transform .15s;
        }
        .btn-login:hover { background: var(--brilliant-gold); transform: translateY(-1px); }
        .dark .btn-login { background: var(--emerald); }
        .dark .btn-login:hover { background: var(--brilliant-gold); }

        /* Hamburger */
        .hamburger {
            display: none;
            flex-direction: column; gap: 5px;
            width: 36px; height: 36px;
            background: none; border: none; cursor: pointer;
            padding: 8px; border-radius: 8px;
        }
        .hamburger span {
            display: block; height: 1.5px;
            background: var(--ink);
            border-radius: 2px;
            transition: transform .25s, opacity .2s;
            transform-origin: center;
        }
        .dark .hamburger span { background: #e2e8f0; }
        .hamburger.open span:nth-child(1) { transform: translateY(6.5px) rotate(45deg); }
        .hamburger.open span:nth-child(2) { opacity: 0; }
        .hamburger.open span:nth-child(3) { transform: translateY(-6.5px) rotate(-45deg); }

        /* Mobile drawer */
        .mobile-menu {
            display: none;
            position: fixed; top: 64px; left: 0; right: 0;
            background: #FAFBF9;
            border-bottom: 1px solid rgba(12,45,30,.08);
            padding: 12px 24px 20px;
            flex-direction: column; gap: 2px;
            z-index: 99;
            box-shadow: 0 16px 48px rgba(0,0,0,.1);
        }
        .dark .mobile-menu {
            background: #0B110E;
            border-bottom-color: rgba(255,255,255,.07);
        }
        .mobile-menu.open { display: flex; }
        .mobile-menu a, .mobile-menu button {
            font-size: 15px; color: var(--slate);
            text-decoration: none; padding: 12px 4px;
            border-bottom: 1px solid var(--cloud);
            background: none; border-left: none; border-right: none; border-top: none;
            text-align: left; cursor: pointer; width: 100%;
            transition: color .2s;
        }
        .dark .mobile-menu a, .dark .mobile-menu button { border-bottom-color: rgba(255,255,255,.06); }
        .mobile-menu a:hover, .mobile-menu button:hover { color: var(--brilliant-gold); }

        /* Dark mode row inside mobile menu — ensures no cutoff on small screens */
        .mobile-dark-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 12px 4px;
            border-bottom: 1px solid var(--cloud);
            font-size: 15px;
            color: var(--slate);
            width: 100%;
            overflow: visible;
        }
        .dark .mobile-dark-row { border-bottom-color: rgba(255,255,255,.06); }
        .mobile-dark-row span {
            font-size: 15px;
            white-space: nowrap;      /* prevents wrapping and keeps button on same line */
            flex-shrink: 1;
        }
        .mobile-dark-toggle {
            width: 36px;
            height: 36px;
            background: var(--cloud);
            border: none;
            cursor: pointer;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--slate);
            transition: background .15s, color .15s;
            flex-shrink: 0;
            margin-right: 0;          /* ensure no extra margin pushes it out */
        }
        .dark .mobile-dark-toggle { background: rgba(255,255,255,.08); }
        .mobile-dark-toggle svg { width: 18px; height: 18px; stroke: currentColor; fill: none; stroke-width: 1.8; }

        /* ════════════════════════════════════
           HERO
        ════════════════════════════════════ */
        .hero {
            position: relative;
            min-height: 92vh;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            padding: 80px 48px 64px;
            text-align: center;
            overflow: hidden;
            background-image: url('/images/VSU_Malakas@Maganda.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        /* Overlay reduced from 0.50 to 0.35 */
        .hero::after {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0, 0, 0, 0.35);
            z-index: 0;
        }
        .hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(rgba(29,179,132,.18) 1px, transparent 1px);
            background-size: 32px 32px;
            mask-image: radial-gradient(ellipse 75% 75% at 50% 40%, black 30%, transparent 100%);
            -webkit-mask-image: radial-gradient(ellipse 75% 75% at 50% 40%, black 30%, transparent 100%);
            z-index: 0;
            pointer-events: none;
        }
        .hero-glow {
            position: absolute;
            top: -10%; left: 50%; transform: translateX(-50%);
            width: 900px; height: 600px;
            background: radial-gradient(ellipse at center, rgba(18,116,90,.35) 0%, transparent 65%);
            pointer-events: none;
            animation: breathe 8s ease-in-out infinite;
            z-index: 0;
        }
        @keyframes breathe {
            0%, 100% { opacity: .6; transform: translateX(-50%) scale(1); }
            50%       { opacity: 1; transform: translateX(-50%) scale(1.08); }
        }

        .hero-inner { position: relative; z-index: 1; max-width: 720px; }

        .hero-eyebrow {
            display: inline-flex; align-items: center; gap: 8px;
            background: rgba(29,179,132,.12);
            border: 1px solid rgba(29,179,132,.28);
            border-radius: 100px;
            padding: 5px 16px 5px 8px;
            margin-bottom: 36px;
        }
        .eyebrow-dot {
            width: 22px; height: 22px; border-radius: 50%;
            background: rgba(29,179,132,.15);
            display: flex; align-items: center; justify-content: center;
        }
        .eyebrow-dot::after {
            content: '';
            width: 8px; height: 8px; border-radius: 50%;
            background: #1DB384;
            animation: ping 2s ease-in-out infinite;
        }
        @keyframes ping {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: .6; transform: scale(.8); }
        }
        .hero-eyebrow span {
            font-size: 12px; color: #6BE8BF;
            font-weight: 500; letter-spacing: .04em;
        }

        .hero h1 {
            font-family: var(--serif);
            font-size: clamp(42px, 6vw, 72px);
            color: #FFFFFF;
            line-height: 1.08;
            letter-spacing: -.02em;
            margin-bottom: 24px;
        }
        .hero h1 em {
            font-style: italic;
            color: #5DDCAA;
        }

        .hero-sub {
            font-size: 17px;
            color: rgba(255,255,255,.6);
            max-width: 460px;
            margin: 0 auto 44px;
            line-height: 1.7;
            font-weight: 300;
        }

        .hero-btns {
            display: flex; justify-content: center; gap: 12px; flex-wrap: wrap;
        }

        .btn-primary {
            display: inline-flex; align-items: center; gap: 8px;
            font-family: var(--sans);
            font-size: 14px; font-weight: 500;
            padding: 13px 28px;
            background: var(--emerald);
            color: #fff;
            border: none; border-radius: 10px; cursor: pointer;
            letter-spacing: .02em;
            transition: background .2s, transform .15s, box-shadow .2s;
            box-shadow: 0 0 0 0 rgba(5,150,105,0);
        }
        .btn-primary:hover {
            background: var(--brilliant-gold);
            transform: translateY(-2px);
            box-shadow: 0 8px 32px rgba(212,175,55,.35);
        }
        .btn-primary svg { width: 14px; height: 14px; stroke: currentColor; fill: none; stroke-width: 2.2; transition: transform .2s; }
        .btn-primary:hover svg { transform: translateX(3px); }

        .btn-outline {
            display: inline-flex; align-items: center; gap: 8px;
            font-size: 14px; font-weight: 400;
            padding: 13px 28px;
            background: rgba(255,255,255,.05);
            color: rgba(255,255,255,.7);
            border: 1px solid rgba(255,255,255,.14);
            border-radius: 10px;
            text-decoration: none;
            transition: background .2s, color .2s, border-color .2s;
        }
        .btn-outline:hover {
            background: rgba(255,255,255,.1);
            color: #fff;
            border-color: rgba(255,255,255,.25);
        }

        /* ════════════════════════════════════
           STATS BAND
        ════════════════════════════════════ */
        .stats-band {
            display: grid; grid-template-columns: repeat(4, 1fr);
            border-bottom: 1px solid var(--cloud);
            background: #FAFBF9;
        }
        .dark .stats-band { background: #0B110E; border-bottom-color: rgba(255,255,255,.07); }
        .stat-cell {
            padding: 40px 24px; text-align: center;
            border-right: 1px solid var(--cloud);
        }
        .dark .stat-cell { border-right-color: rgba(255,255,255,.07); }
        .stat-cell:last-child { border-right: none; }
        .stat-num {
            font-family: var(--serif);
            font-size: 34px; color: #0A4A38;
            line-height: 1;
            margin-bottom: 6px;
        }
        .dark .stat-num { color: #1DB384; }
        .stat-lbl { font-size: 13px; color: var(--slate); font-weight: 300; letter-spacing: .02em; }

        /* ════════════════════════════════════
           NOTICE
        ════════════════════════════════════ */
        .notice-wrap { padding: 40px 48px 0; }
        .notice {
            display: flex; align-items: flex-start; gap: 14px;
            background: #EBF7F2;
            border: 1px solid rgba(10,74,56,.15);
            border-left: 3px solid var(--emerald);
            border-radius: 10px;
            padding: 16px 20px;
        }
        .dark .notice { background: rgba(13,43,34,.6); border-left-color: var(--emerald); border-color: rgba(5,150,105,.2); }
        .notice-ico { flex-shrink: 0; margin-top: 1px; }
        .notice-ico svg { width: 16px; height: 16px; stroke: var(--emerald); fill: none; stroke-width: 1.8; }
        .dark .notice-ico svg { stroke: #5DDCAA; }
        .notice p { font-size: 13.5px; color: #0A4A38; line-height: 1.6; font-weight: 300; }
        .dark .notice p { color: #9FE1CB; }
        .notice strong { font-weight: 500; }

        /* ════════════════════════════════════
           REVEAL
        ════════════════════════════════════ */
        .reveal {
            opacity: 0; transform: translateY(28px);
            transition: opacity .6s ease, transform .6s ease;
        }
        .reveal.visible { opacity: 1; transform: translateY(0); }
        .reveal-group .reveal { transition-delay: calc(var(--i, 0) * 90ms); }

        /* ════════════════════════════════════
           SECTION SHARED
        ════════════════════════════════════ */
        .section-eyebrow {
            font-size: 11px; font-weight: 500; letter-spacing: .1em;
            text-transform: uppercase; color: var(--emerald);
            margin-bottom: 12px;
        }
        .dark .section-eyebrow { color: #1DB384; }
        .section-hed {
            font-family: var(--serif);
            font-size: clamp(28px, 3.5vw, 40px);
            color: var(--ink);
            line-height: 1.15;
            letter-spacing: -.02em;
            margin-bottom: 14px;
        }
        .section-sub { font-size: 15.5px; color: var(--slate); font-weight: 300; line-height: 1.7; max-width: 500px; }

        /* ════════════════════════════════════
           FEATURES
        ════════════════════════════════════ */
        .features { padding: 80px 48px; }
        .features-intro { margin-bottom: 56px; }
        .features-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 2px; background: var(--cloud); border: 1px solid var(--cloud); border-radius: 14px; overflow: hidden; }
        .dark .features-grid { background: rgba(255,255,255,.06); border-color: rgba(255,255,255,.06); }
        .feat {
            background: #FAFBF9;
            padding: 32px 28px;
            transition: background .2s;
        }
        .dark .feat { background: #0B110E; }
        .feat:hover { background: #fff; }
        .dark .feat:hover { background: #0F1A14; }
        .feat-ico {
            width: 44px; height: 44px;
            background: var(--pale);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 20px;
        }
        .dark .feat-ico { background: rgba(5,150,105,.12); }
        .feat-ico svg { width: 20px; height: 20px; stroke: var(--emerald); fill: none; stroke-width: 1.7; }
        .dark .feat-ico svg { stroke: #1DB384; }
        .feat h3 { font-family: var(--serif); font-size: 18px; color: var(--ink); margin-bottom: 10px; letter-spacing: -.01em; }
        .feat p { font-size: 14px; color: var(--slate); line-height: 1.65; font-weight: 300; }

        /* ════════════════════════════════════
           ABOUT SECTION
        ════════════════════════════════════ */
        .about {
            position: relative;
            background: #051009;
            padding: 96px 48px;
            text-align: center;
            overflow: hidden;
        }
        .about::before {
            content: ''; position: absolute; inset: 0;
            background-image: radial-gradient(rgba(5,150,105,.12) 1px, transparent 1px);
            background-size: 28px 28px;
        }
        .about::after {
            content: ''; position: absolute;
            top: -150px; left: 50%; transform: translateX(-50%);
            width: 700px; height: 500px;
            background: radial-gradient(ellipse, rgba(5,150,105,.22) 0%, transparent 70%);
            pointer-events: none;
        }
        .about-inner { position: relative; z-index: 1; max-width: 660px; margin: 0 auto; }
        .about .section-eyebrow { color: #4EEDB5; }
        .about .section-hed { color: #fff; max-width: 100%; }
        .about .section-sub { color: rgba(255,255,255,.48); max-width: 100%; margin: 0 auto; }

        /* ════════════════════════════════════
           ROLES
        ════════════════════════════════════ */
        .roles {
            padding: 80px 48px;
            background: var(--mist);
        }
        .dark .roles { background: #060D09; }
        .roles-grid { display: grid; grid-template-columns: 1fr 1.2fr; gap: 72px; align-items: center; }

        .role-list { display: flex; flex-direction: column; gap: 10px; margin-top: 36px; }
        .role-card {
            display: flex; align-items: flex-start; gap: 16px;
            padding: 20px 20px;
            background: #fff;
            border: 1px solid var(--cloud);
            border-radius: 12px;
            transition: border-color .2s, box-shadow .2s;
        }
        .dark .role-card { background: #0D1B12; border-color: rgba(255,255,255,.07); }
        .role-card:hover { border-color: var(--brilliant-gold); box-shadow: 0 4px 20px rgba(212,175,55,.1); }
        .role-pip {
            width: 36px; height: 36px; border-radius: 8px;
            background: var(--pale); flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
        }
        .dark .role-pip { background: rgba(5,150,105,.12); }
        .role-pip svg { width: 16px; height: 16px; stroke: var(--emerald); fill: none; stroke-width: 1.8; }
        .dark .role-pip svg { stroke: #1DB384; }
        .role-info h4 { font-size: 14.5px; font-weight: 500; color: var(--ink); margin-bottom: 3px; }
        .role-info p  { font-size: 13px; color: var(--slate); font-weight: 300; line-height: 1.5; }

        /* Member table visual */
        .members-panel {
            background: #fff;
            border: 1px solid var(--cloud);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(0,0,0,.06);
        }
        .dark .members-panel { background: #0D1B12; border-color: rgba(255,255,255,.07); }
        .mp-header {
            padding: 16px 20px;
            border-bottom: 1px solid var(--cloud);
            display: flex; justify-content: space-between; align-items: center;
        }
        .dark .mp-header { border-bottom-color: rgba(255,255,255,.07); }
        .mp-title { font-family: var(--serif); font-size: 15px; color: var(--ink); }
        .mp-badge {
            font-size: 11px; font-weight: 500;
            padding: 4px 12px; border-radius: 100px;
            background: var(--pale); color: var(--emerald);
        }
        .dark .mp-badge { background: rgba(5,150,105,.15); color: #5DDCAA; }
        .mp-row {
            display: flex; align-items: center; gap: 14px;
            padding: 14px 20px;
            border-bottom: 1px solid var(--cloud);
            transition: background .15s;
        }
        .dark .mp-row { border-bottom-color: rgba(255,255,255,.06); }
        .mp-row:last-child { border-bottom: none; }
        .mp-row:hover { background: var(--mist); }
        .dark .mp-row:hover { background: rgba(255,255,255,.03); }
        .mp-avatar {
            width: 34px; height: 34px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 12px; font-weight: 500; flex-shrink: 0;
        }
        .mp-name  { font-size: 13.5px; font-weight: 500; color: var(--ink); }
        .mp-pos   { font-size: 12px; color: var(--slate); font-weight: 300; }
        .mp-role  {
            margin-left: auto; font-size: 11px; font-weight: 500;
            padding: 3px 12px; border-radius: 100px; white-space: nowrap;
        }

        /* ════════════════════════════════════
           CTA
        ════════════════════════════════════ */
        .cta-section {
            padding: 96px 48px;
            text-align: center;
            border-top: 1px solid var(--cloud);
        }
        .dark .cta-section { border-top-color: rgba(255,255,255,.07); }
        .cta-section h2 {
            font-family: var(--serif);
            font-size: clamp(30px, 4vw, 48px);
            color: var(--ink);
            letter-spacing: -.02em;
            margin-bottom: 16px;
        }
        .cta-section p { font-size: 16px; color: var(--slate); font-weight: 300; max-width: 400px; margin: 0 auto 40px; }
        .btn-cta {
            display: inline-flex; align-items: center; gap: 8px;
            font-size: 15px; font-weight: 500;
            padding: 15px 36px;
            background: var(--emerald);
            color: #fff;
            border: none; border-radius: 12px; cursor: pointer;
            letter-spacing: .02em;
            transition: background .2s, transform .15s, box-shadow .2s;
        }
        .btn-cta:hover {
            background: var(--brilliant-gold);
            transform: translateY(-2px);
            box-shadow: 0 12px 40px rgba(212,175,55,.3);
        }
        .dark .btn-cta { background: var(--emerald); }
        .dark .btn-cta:hover { background: var(--brilliant-gold); }

        /* ════════════════════════════════════
           FOOTER
        ════════════════════════════════════ */
        footer {
            background: var(--mist);
            border-top: 1px solid var(--cloud);
            padding: 56px 48px 28px;
        }
        .dark footer { background: #060D09; border-top-color: rgba(255,255,255,.07); }

        .footer-grid {
            display: grid; grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 48px; margin-bottom: 48px;
        }
        .footer-about-name {
            font-family: var(--serif);
            font-size: 16px; color: var(--ink);
            margin-bottom: 12px;
        }
        .footer-about-desc { font-size: 13px; color: var(--slate); line-height: 1.65; font-weight: 300; margin-bottom: 16px; }
        .footer-tag {
            display: inline-flex; align-items: center; gap: 6px;
            font-size: 11px; color: var(--emerald);
            font-weight: 500;
        }
        .dark .footer-tag { color: #1DB384; }
        .footer-tag-dot { width: 6px; height: 6px; background: var(--emerald); border-radius: 50%; }

        footer h4 {
            font-size: 11px; font-weight: 500; letter-spacing: .08em;
            text-transform: uppercase; color: var(--slate);
            margin-bottom: 18px;
        }
        footer ul { list-style: none; }
        footer li { margin-bottom: 10px; }
        footer a, footer button {
            font-size: 13.5px; color: var(--slate); font-weight: 300;
            text-decoration: none; background: none; border: none; cursor: pointer; padding: 0;
            transition: color .2s;
        }
        footer a:hover, footer button:hover { color: var(--brilliant-gold); }

        .footer-bottom {
            border-top: 1px solid var(--cloud);
            padding-top: 24px;
            font-size: 12px; color: var(--slate); font-weight: 300;
            text-align: center; line-height: 1.6;
        }
        .dark .footer-bottom { border-top-color: rgba(255,255,255,.07); }

        /* ════════════════════════════════════
           MODAL
        ════════════════════════════════════ */
        .modal-overlay {
            position: fixed; inset: 0; z-index: 200;
            display: flex; align-items: center; justify-content: center;
            padding: 16px;
            background: rgba(4,8,6,.7);
            backdrop-filter: blur(12px) saturate(1.3);
            -webkit-backdrop-filter: blur(12px) saturate(1.3);
        }
        .modal-box {
            background: #fff;
            border-radius: 20px;
            width: 100%; max-width: 360px;
            overflow: hidden;
            border: 1px solid rgba(0,0,0,.06);
        }
        .dark .modal-box { background: #0D1B12; border-color: rgba(255,255,255,.09); }
        .modal-head {
            padding: 24px 24px 18px;
            text-align: center;
            border-bottom: 1px solid var(--cloud);
        }
        .dark .modal-head { border-bottom-color: rgba(255,255,255,.07); }
        .modal-crest {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: transparent;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
            overflow: hidden;
        }
        .modal-crest img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        .modal-head h2 {
            font-family: var(--serif);
            font-size: 20px; color: var(--ink);
            letter-spacing: -.01em; margin-bottom: 4px;
        }
        .modal-head p { font-size: 12px; color: var(--slate); font-weight: 300; }
        .modal-body { padding: 20px 24px 24px; }

        /* flash */
        .flash {
            display: flex; align-items: flex-start; gap: 10px;
            padding: 10px 14px; border-radius: 10px;
            font-size: 12px; margin-bottom: 16px;
            transition: opacity .4s;
        }
        .flash-ok { background: #EBF7F2; border: 1px solid rgba(5,150,105,.15); color: var(--emerald); }
        .flash-err { background: #FEF2F2; border: 1px solid rgba(220,38,38,.15); color: #991B1B; }
        .dark .flash-ok { background: rgba(13,43,34,.6); color: #9FE1CB; }
        .dark .flash-err { background: rgba(127,29,29,.3); color: #FCA5A5; }
        .flash svg { width: 14px; height: 14px; stroke: currentColor; fill: none; stroke-width: 1.8; flex-shrink: 0; margin-top: 1px; }

        /* form fields */
        .field { margin-bottom: 14px; }
        .field label {
            display: block; font-size: 12px; font-weight: 500;
            color: var(--ink); margin-bottom: 5px;
        }
        .field-wrap { position: relative; }
        .field-ico {
            position: absolute; left: 10px; top: 50%; transform: translateY(-50%);
            pointer-events: none;
        }
        .field-ico svg { width: 14px; height: 14px; stroke: var(--slate); fill: none; stroke-width: 1.8; }
        .field input[type="email"],
        .field input[type="password"],
        .field input[type="text"] {
            width: 100%; padding: 8px 10px 8px 34px;
            font-family: var(--sans); font-size: 13px;
            background: #F5F7F5; border: 1px solid var(--cloud);
            border-radius: 8px; color: var(--ink);
            outline: none; transition: border-color .2s, box-shadow .2s, background .2s;
        }
        .dark .field input { background: rgba(255,255,255,.06); border-color: rgba(255,255,255,.1); color: #e2efe8; }
        .field input:focus {
            border-color: var(--emerald);
            box-shadow: 0 0 0 3px rgba(5,150,105,.1);
            background: #fff;
        }
        .dark .field input:focus { border-color: #1DB384; box-shadow: 0 0 0 3px rgba(29,179,132,.15); background: rgba(255,255,255,.08); }

        /* pw toggle */
        .pw-toggle {
            position: absolute; right: 8px; top: 50%; transform: translateY(-50%);
            background: none; border: none; cursor: pointer;
            color: var(--slate); padding: 4px;
            display: flex; align-items: center;
            transition: color .15s;
        }
        .pw-toggle:hover { color: var(--ink); }
        .pw-toggle svg { width: 14px; height: 14px; stroke: currentColor; fill: none; stroke-width: 1.8; }

        .form-opts {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 18px; font-size: 12px;
        }
        .form-opts label { display: flex; align-items: center; gap: 6px; cursor: pointer; color: var(--slate); font-weight: 300; }
        .form-opts input[type="checkbox"] { width: 13px; height: 13px; accent-color: var(--emerald); }
        .form-opts a { color: var(--emerald); text-decoration: none; font-weight: 500; transition: color .2s; }
        .dark .form-opts a { color: #1DB384; }
        .form-opts a:hover { color: var(--brilliant-gold); }

        .btn-submit {
            width: 100%; padding: 10px;
            font-family: var(--sans); font-size: 13px; font-weight: 500;
            background: var(--emerald);
            color: #fff;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background .2s, transform .15s, box-shadow .2s;
            margin-bottom: 8px;
        }
        .btn-submit:hover:not(:disabled) {
            background: var(--brilliant-gold);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(212,175,55,0.3);
        }
        .btn-submit:focus-visible {
            outline: 2px solid var(--brilliant-gold);
            outline-offset: 2px;
            border-radius: 8px;
        }
        .btn-submit:disabled { opacity: .7; cursor: not-allowed; }
        .dark .btn-submit { background: var(--emerald); }
        .dark .btn-submit:hover:not(:disabled) {
            background: #E6C358;
            box-shadow: 0 4px 14px rgba(230,195,88,0.4);
        }

        .btn-cancel {
            width: 100%; padding: 10px;
            font-family: var(--sans); font-size: 13px; font-weight: 500;
            background: #C0392B;
            color: #fff;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background .2s, transform .15s;
        }
        .btn-cancel:hover { background: #A93226; transform: translateY(-1px); }
        .btn-cancel:focus-visible { outline: 2px solid var(--brilliant-gold); outline-offset: 2px; }
        .dark .btn-cancel { background: #B03A2E; }
        .dark .btn-cancel:hover { background: #962D22; }

        .modal-note {
            margin-top: 16px; padding-top: 16px;
            border-top: 1px solid var(--cloud);
            display: flex; align-items: flex-start; gap: 8px;
            font-size: 11px; color: var(--slate); font-weight: 300; line-height: 1.5;
        }
        .dark .modal-note { border-top-color: rgba(255,255,255,.07); }
        .modal-note svg { width: 12px; height: 12px; stroke: var(--slate); fill: none; stroke-width: 1.8; flex-shrink: 0; margin-top: 1px; }

        /* Back to top */
        #backToTop {
            position: fixed; bottom: 24px; right: 24px; z-index: 50;
            width: 42px; height: 42px; border-radius: 10px;
            background: var(--emerald);
            color: #fff;
            border: none; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            opacity: 0; visibility: hidden;
            transition: opacity .3s, visibility .3s, background .2s, transform .2s;
        }
        #backToTop.show { opacity: 1; visibility: visible; }
        #backToTop:hover { background: var(--brilliant-gold); transform: translateY(-2px); }
        #backToTop svg { width: 18px; height: 18px; stroke: #fff; fill: none; stroke-width: 2; }

        /* ════════════════════════════════════
           RESPONSIVE
        ════════════════════════════════════ */
        @media (max-width: 768px) {
            .nav { padding: 0 20px; }
            .nav-links { display: none; }
            .hamburger { display: flex; }
            .btn-toggle { display: none; }  /* hide dark mode icon in top bar on mobile */
            .hero { padding: 60px 24px 48px; min-height: auto; }
            .hero h1 { font-size: 36px; }
            .hero-sub { font-size: 15px; }
            .stats-band { grid-template-columns: 1fr 1fr; }
            .stat-cell { border-right: none; border-bottom: 1px solid var(--cloud); }
            .stat-cell:nth-child(odd) { border-right: 1px solid var(--cloud); }
            .stat-cell:last-child { border-bottom: none; }
            .notice-wrap { padding: 32px 24px 0; }
            .features { padding: 56px 24px; }
            .features-grid { grid-template-columns: 1fr; }
            .about { padding: 64px 24px; }
            .roles { padding: 56px 24px; }
            .roles-grid { grid-template-columns: 1fr; gap: 40px; }
            .cta-section { padding: 64px 24px; }
            footer { padding: 48px 24px 24px; }
            .footer-grid { grid-template-columns: 1fr 1fr; gap: 32px; }
        }

        /* Extra small screens (<= 400px): reduce padding to avoid button cutoff */
        @media (max-width: 400px) {
            .mobile-menu {
                padding-left: 16px;
                padding-right: 16px;
            }
            .mobile-dark-row {
                gap: 8px;
            }
            .mobile-dark-row span {
                white-space: nowrap;
            }
        }
    </style>
</head>
<body>

{{-- ═══ MAIN CONTENT LANDMARK ═══ --}}
<main>

{{-- ═══ NAV ═══ --}}
<nav class="nav">
    <a href="#" class="nav-brand">
        <div class="nav-crest">
            <img src="{{ asset('images/vsulhs_logo.png') }}" alt="VSULHS Logo">
        </div>
        <span class="nav-wordmark">VSULHS SSLG</span>
    </a>

    <ul class="nav-links">
        <li><a href="#features">Features</a></li>
        <li><a href="#about">About</a></li>
        <li><a href="#contact">Contact</a></li>
    </ul>

    <div class="nav-actions">
        <button @click="darkMode = !darkMode" class="btn-toggle" aria-label="Toggle dark mode">
            <svg x-show="!darkMode" x-cloak viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
            <svg x-show="darkMode" x-cloak viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
        </button>
        <button @click="loginModalOpen = true" class="btn-login">Sign in</button>
        <button class="hamburger" id="hamburger" aria-label="Toggle menu" aria-expanded="false">
            <span></span><span></span><span></span>
        </button>
    </div>
</nav>

{{-- Mobile drawer --}}
<div class="mobile-menu" id="mobileMenu">
    <a href="#features">Features</a>
    <a href="#about">About</a>
    <a href="#contact">Contact</a>
    <button @click="loginModalOpen = true; closeMobileMenu()">Sign in</button>
    <div class="mobile-dark-row">
        <span>Dark mode</span>
        <button
            class="mobile-dark-toggle"
            @click="darkMode = !darkMode"
            aria-label="Toggle dark mode">
            <svg x-show="!darkMode" x-cloak viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
            <svg x-show="darkMode" x-cloak viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
        </button>
    </div>
</div>

{{-- ═══ HERO (with reveal) ═══ --}}
<section class="hero">
    <div class="hero-glow"></div>
    <div class="hero-inner reveal">
        <div class="hero-eyebrow">
            <div class="eyebrow-dot"></div>
            <span>Exclusively for VSULHS SSLG members</span>
        </div>
        <h1>The official portal of<br><em>VSULHS SSLG</em></h1>
        <p class="hero-sub">Financial records, documents, and member management — purpose-built for the Supreme Student Learner Government of VSU Laboratory High School.</p>
        <div class="hero-btns">
            <button @click="loginModalOpen = true" class="btn-primary">
                Sign in to portal
                <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </button>
            <a href="#features" class="btn-outline">Explore features</a>
        </div>
    </div>
</section>

{{-- ═══ STATS (label updated) ═══ --}}
<div class="stats-band">
    <div class="stat-cell">
        <div class="stat-num stat-number" data-target="500" data-suffix="+">0</div>
        <div class="stat-lbl">Active members</div>
    </div>
    <div class="stat-cell">
        <div class="stat-num stat-number" data-target="1200000" data-format="compact" data-prefix="₱">0</div>
        <div class="stat-lbl">Total funds (₱)</div>
    </div>
    <div class="stat-cell">
        <div class="stat-num stat-number" data-target="1200" data-suffix="+">0</div>
        <div class="stat-lbl">Documents uploaded</div>
    </div>
    <div class="stat-cell">
        <div class="stat-num">24/7</div>
        <div class="stat-lbl">System uptime</div>
    </div>
</div>

{{-- ═══ NOTICE ═══ --}}
<div class="notice-wrap reveal">
    <div class="notice">
        <div class="notice-ico">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
        </div>
        <p><strong>Restricted access.</strong> This portal is exclusively for verified members of the VSULHS Supreme Student Learner Government. Accounts are issued by the system administrator — public registration is not available.</p>
    </div>
</div>

{{-- ═══ FEATURES ═══ --}}
<section id="features" class="features">
    <div class="features-intro reveal">
        <div class="section-eyebrow">Features</div>
        <div class="section-hed">Everything SSLG needs,<br>in one secure place</div>
        <p class="section-sub">Purpose-built tools to keep your organisation's finances, documents, and members managed with clarity and control.</p>
    </div>
    <div class="features-grid reveal-group">
        <div class="feat reveal" style="--i:0">
            <div class="feat-ico"><svg viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
            <h3>Financial records</h3>
            <p>Log income and expenses with categories, dates, and attachments. Track net balance and view monthly summaries at a glance.</p>
        </div>
        <div class="feat reveal" style="--i:1">
            <div class="feat-ico"><svg viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
            <h3>Document library</h3>
            <p>Centralised storage with version control, public and private access controls, and easy sharing across committees.</p>
        </div>
        <div class="feat reveal" style="--i:2">
            <div class="feat-ico"><svg viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div>
            <h3>Member management</h3>
            <p>Organise members by role, track positions, and manage your organisational hierarchy with clarity.</p>
        </div>
        <div class="feat reveal" style="--i:3">
            <div class="feat-ico"><svg viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg></div>
            <h3>Reports & analytics</h3>
            <p>Visualise income and expense trends, member activity, and document usage through clean dashboards.</p>
        </div>
        <div class="feat reveal" style="--i:4">
            <div class="feat-ico"><svg viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg></div>
            <h3>Notifications</h3>
            <p>Stay informed with alerts for new financial entries, document updates, and important org announcements.</p>
        </div>
        <div class="feat reveal" style="--i:5">
            <div class="feat-ico"><svg viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg></div>
            <h3>Role-based access</h3>
            <p>Fine-grained permissions ensure every member sees only what they need, keeping sensitive records secure.</p>
        </div>
    </div>
</section>

{{-- ═══ ABOUT ═══ --}}
<section id="about" class="about">
    <div class="about-inner reveal">
        <div class="section-eyebrow">About</div>
        <div class="section-hed">The VSULHS Supreme Student<br>Learner Government</div>
        <p class="section-sub" style="margin-top:16px">The VSULHS SSLG is the official student governing body of the Visayas State University Laboratory High School in Baybay City, Leyte. It represents the student body, promotes student welfare, and upholds transparency and accountability in all organisational activities. This portal was built to support those goals — giving officers and members a single, secure place to manage finances, documents, and records.</p>
    </div>
</section>

{{-- ═══ ROLES ═══ --}}
<section class="roles">
    <div class="roles-grid">
        <div class="reveal">
            <div class="section-eyebrow">Access control</div>
            <div class="section-hed">Built around your<br>org structure</div>
            <p class="section-sub">Every role gets a tailored experience. Advisers, officers, and members each have exactly the right level of access.</p>
            <div class="role-list">
                <div class="role-card"><div class="role-pip"><svg viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg></div><div class="role-info"><h4>Administrator / Adviser</h4><p>Full access to all modules, financial records, audit logs, and system settings</p></div></div>
                <div class="role-card"><div class="role-pip"><svg viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg></div><div class="role-info"><h4>Officer</h4><p>Log income and expenses, upload documents, and manage org members</p></div></div>
                <div class="role-card"><div class="role-pip"><svg viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div><div class="role-info"><h4>Member</h4><p>View public documents and org-wide financial summaries</p></div></div>
            </div>
        </div>
        <div class="members-panel reveal">
            <div class="mp-header"><span class="mp-title">Members</span><span class="mp-badge">SY 2025–2026</span></div>
            <div class="mp-row"><div class="mp-avatar" style="background:#EBF7F2; color:#0A4A38;">JR</div><div><div class="mp-name">Juan Reyes</div><div class="mp-pos">Student President</div></div><div class="mp-role" style="background:#EBF7F2; color:#0A4A38;">Adviser</div></div>
            <div class="mp-row"><div class="mp-avatar" style="background:#E6F1FB; color:#0C447C;">MA</div><div><div class="mp-name">Maria Andres</div><div class="mp-pos">Secretary General</div></div><div class="mp-role" style="background:#E6F1FB; color:#0C447C;">Officer</div></div>
            <div class="mp-row"><div class="mp-avatar" style="background:#EEEDFE; color:#3C3489;">KC</div><div><div class="mp-name">Karl Cruz</div><div class="mp-pos">Finance Head</div></div><div class="mp-role" style="background:#EEEDFE; color:#3C3489;">Officer</div></div>
            <div class="mp-row"><div class="mp-avatar" style="background:#F1EFE8; color:#444441;">RL</div><div><div class="mp-name">Rose Lim</div><div class="mp-pos">Grade 11 — STEM</div></div><div class="mp-role" style="background:#F1EFE8; color:#444441;">Member</div></div>
        </div>
    </div>
</section>

{{-- ═══ CTA ═══ --}}
<section class="cta-section reveal">
    <div class="section-eyebrow" style="margin-bottom:16px">Access portal</div>
    <h2>VSULHS SSLG members only</h2>
    <p>This portal is exclusively for verified VSULHS SSLG members. Contact your administrator to receive access credentials.</p>
    <button @click="loginModalOpen = true" class="btn-cta">Sign in to the portal<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg></button>
</section>

</main> {{-- end main --}}

{{-- ═══ FOOTER ═══ --}}
<footer id="contact">
    <div class="footer-grid">
        <div><div class="footer-about-name">VSULHS SSLG</div><p class="footer-about-desc">The official management portal of the VSU Laboratory High School Supreme Student Learner Government, Baybay City, Leyte.</p><div class="footer-tag"><div class="footer-tag-dot"></div>Restricted — VSULHS SSLG members only</div></div>
        <div><h4>Navigation</h4><ul><li><a href="#features">Features</a></li><li><a href="#about">About</a></li><li><a href="#contact">Contact</a></li><li><button @click="loginModalOpen = true">Sign in</button></li></ul></div>
        <div><h4>Resources</h4><ul><li><a href="{{ route('help') }}">Help Centre</a></li><li><a href="{{ route('data-privacy-act') }}">Data Privacy Act of 2012 (Philippines)</a></li><li><a href="{{ route('terms-of-service') }}">Terms of Service</a></li></ul></div>
        <div><h4>Contact</h4><ul><li><a href="https://maps.google.com/?q=Visayas+State+University+Integrated+High+School" target="_blank" rel="noopener">VSULHS, Baybay City, Leyte</a></li><li><a href="mailto:sslg@vsulhs.edu.ph">sslg@vsulhs.edu.ph</a></li><li><a href="tel:+639256353456">+63 925 635 3456</a></li></ul></div>
    </div>
    <div class="footer-bottom">&copy; {{ date('Y') }} VSULHS Supreme Student Learner Government. All rights reserved.<br><span style="font-size:11px; opacity:.7;">Personal information collected by this system is protected under Republic Act No. 10173 (Data Privacy Act of 2012).</span></div>
</footer>

{{-- ═══ LOGIN MODAL ═══ --}}
<div x-show="loginModalOpen" x-cloak class="modal-overlay" @click.self="loginModalOpen = false" @keydown.window.escape="loginModalOpen = false">
    <div x-data="loginForm()" class="modal-box" @click.stop>
        <div class="modal-head">
            <div class="modal-crest"><img src="{{ asset('images/vsulhs_logo.png') }}" alt="VSULHS Logo"></div>
            <h2>Welcome back</h2>
            <p>Sign in to the VSULHS SSLG portal</p>
        </div>
        <div class="modal-body">
            @if (session('success'))
                <div class="flash flash-ok"><svg viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="flash flash-err" role="alert"><svg viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><div><strong>Error:</strong> {{ $errors->first() }}</div></div>
            @endif
            <form method="POST" action="{{ route('login.post') }}" @submit="loading = true">
                @csrf
                <div class="field"><label for="m_email">Email address</label><div class="field-wrap"><div class="field-ico"><svg viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/></svg></div><input type="email" id="m_email" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="yourname@gmail.com"></div></div>
                <div class="field"><label for="m_password">Password</label><div class="field-wrap"><div class="field-ico"><svg viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6-4h12a2 2 0 002-2v-4a2 2 0 00-2-2H6a2 2 0 00-2 2v4a2 2 0 002 2zm10-2V9a2 2 0 00-2-2H8a2 2 0 00-2 2v4"/></svg></div><input :type="showPassword ? 'text' : 'password'" id="m_password" name="password" required autocomplete="current-password" placeholder="••••••••"><button type="button" class="pw-toggle" @click="showPassword = !showPassword" aria-label="Toggle password visibility"><svg x-show="!showPassword" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg><svg x-show="showPassword" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 01-4.243-4.243m4.242 4.242L9.88 9.88"/></svg></button></div></div>
                <div class="form-opts"><label><input type="checkbox" name="remember"> Remember me</label><a href="{{ route('password.request') }}">Forgot password?</a></div>
                <button type="submit" class="btn-submit" :disabled="loading"><span x-show="!loading">Sign in</span><span x-show="loading" style="display:flex;align-items:center;justify-content:center;gap:8px"><svg class="animate-spin" style="width:15px;height:15px" fill="none" viewBox="0 0 24 24"><circle style="opacity:.25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path style="opacity:.75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>Signing in…</span></button>
                <button type="button" class="btn-cancel" @click="loginModalOpen = false">Cancel</button>
            </form>
            <div class="modal-note"><svg viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6-4h12a2 2 0 002-2v-4a2 2 0 00-2-2H6a2 2 0 00-2 2v4a2 2 0 002 2zm10-2V9a2 2 0 00-2-2H8a2 2 0 00-2 2v4"/></svg>Only check "Remember me" on your personal device. Always log out on shared computers.</div>
        </div>
    </div>
</div>

{{-- Back to top --}}
<button id="backToTop" aria-label="Back to top"><svg viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg></button>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('loginForm', () => ({
            showPassword: false,
            loading: false,
        }));
    });

    // Helper to close mobile menu
    window.closeMobileMenu = function() {
        const hbg = document.getElementById('hamburger');
        const mob = document.getElementById('mobileMenu');
        if (hbg && mob) {
            hbg.classList.remove('open');
            mob.classList.remove('open');
            hbg.setAttribute('aria-expanded', 'false');
        }
    };

    // Hamburger with ARIA
    const hbg = document.getElementById('hamburger');
    const mob = document.getElementById('mobileMenu');
    if (hbg && mob) {
        hbg.addEventListener('click', () => {
            const isOpen = hbg.classList.toggle('open');
            mob.classList.toggle('open');
            hbg.setAttribute('aria-expanded', isOpen);
        });
        document.addEventListener('click', e => {
            if (!hbg.contains(e.target) && !mob.contains(e.target)) {
                hbg.classList.remove('open');
                mob.classList.remove('open');
                hbg.setAttribute('aria-expanded', 'false');
            }
        });
    }

    // Smooth scroll
    document.querySelectorAll('a[href^="#"]').forEach(a => {
        a.addEventListener('click', function(e) {
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                closeMobileMenu();
            }
        });
    });

    // Scroll reveal
    const ro = new IntersectionObserver(entries => {
        entries.forEach(en => {
            if (en.isIntersecting) {
                en.target.classList.add('visible');
                ro.unobserve(en.target);
            }
        });
    }, { threshold: 0.1 });
    document.querySelectorAll('.reveal').forEach(el => ro.observe(el));

    // Counting numbers
    const co = new IntersectionObserver(entries => {
        entries.forEach(en => {
            if (!en.isIntersecting) return;
            const el = en.target;
            const target = parseInt(el.dataset.target);
            const prefix = el.dataset.prefix || '';
            const suffix = el.dataset.suffix || '';
            const fmt = el.dataset.format;
            if (isNaN(target)) return;
            let current = 0;
            const steps = 60, inc = Math.ceil(target / steps);
            const format = n => {
                if (fmt === 'compact') {
                    if (n >= 1e6) return prefix + (n / 1e6).toFixed(1) + 'M' + suffix;
                    if (n >= 1e3) return prefix + (n / 1e3).toFixed(1) + 'K' + suffix;
                }
                return prefix + n.toLocaleString() + suffix;
            };
            const interval = setInterval(() => {
                current += inc;
                if (current >= target) {
                    el.textContent = format(target);
                    clearInterval(interval);
                } else {
                    el.textContent = format(current);
                }
            }, 16);
            co.unobserve(el);
        });
    }, { threshold: 0.5 });
    document.querySelectorAll('.stat-number').forEach(el => co.observe(el));

    // Back to top
    const btt = document.getElementById('backToTop');
    window.addEventListener('scroll', () => btt.classList.toggle('show', window.scrollY > 300));
    btt.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));

    // Flash auto-hide
    document.querySelectorAll('.flash').forEach(el => {
        setTimeout(() => { el.style.opacity = '0'; setTimeout(() => el.remove(), 400); }, 4000);
    });
</script>
</body>
</html>