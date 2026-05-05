@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@push('styles')
<style>
    /* ══════════════════════════════════════════════
       DASHBOARD TOKENS  (inherits layout CSS vars)
    ══════════════════════════════════════════════ */

    /* ── Entrance animations ── */
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(16px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes scaleIn {
        from { opacity: 0; transform: scale(0.97); }
        to   { opacity: 1; transform: scale(1); }
    }
    .anim-fade-up  { animation: fadeUp  0.45s ease both; }
    .anim-scale-in { animation: scaleIn 0.4s  ease both; }

    .d1 { animation-delay: .04s; }
    .d2 { animation-delay: .08s; }
    .d3 { animation-delay: .12s; }
    .d4 { animation-delay: .16s; }
    .d5 { animation-delay: .20s; }
    .d6 { animation-delay: .24s; }
    .d7 { animation-delay: .28s; }

    /* ── Card base ── */
    .dash-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 1rem;
        box-shadow: var(--shadow-sm);
        transition: box-shadow 0.2s ease, transform 0.2s ease;
        overflow: hidden;
    }
    .dash-card:hover {
        box-shadow: 0 6px 24px rgba(212,175,55,0.12), 0 2px 8px rgba(0,0,0,0.06);
    }

    /* ── Stat card hover lift ── */
    .stat-card {
        cursor: default;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 28px rgba(212,175,55,0.14), 0 2px 8px rgba(0,0,0,0.06);
    }

    /* ── Gold accent border on card hover ── */
    .dash-card-hover:hover {
        border-color: rgba(212,175,55,0.35);
    }

    /* ── Divider ── */
    .dash-divider { border-color: var(--border); }

    /* ── Row hover (transactions/documents) ── */
    .row-hover {
        transition: background 0.15s ease;
        border-bottom: 1px solid var(--border);
    }
    .row-hover:last-child { border-bottom: none; }
    .row-hover:hover { background: rgba(212,175,55,0.04); }

    /* ── Section label ── */
    .section-label {
        font-size: 0.62rem;
        font-weight: 700;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: var(--text-3);
        font-family: 'DM Mono', monospace;
        margin-bottom: 0.75rem;
    }

    /* ── Badge pill ── */
    .badge-emerald {
        background: var(--emerald-pale);
        color: var(--emerald-dark);
        border: 1px solid rgba(5,150,105,0.2);
    }
    html.dark .badge-emerald {
        background: rgba(16,185,129,0.12);
        color: #6EE7B7;
        border-color: rgba(16,185,129,0.25);
    }
    .badge-gold {
        background: var(--gold-pale);
        color: var(--gold-dark);
        border: 1px solid rgba(212,175,55,0.25);
    }
    html.dark .badge-gold {
        background: rgba(212,175,55,0.1);
        color: var(--gold-light);
        border-color: rgba(212,175,55,0.25);
    }
    .badge-rose {
        background: rgba(244,63,94,0.08);
        color: #E11D48;
        border: 1px solid rgba(244,63,94,0.2);
    }
    html.dark .badge-rose {
        background: rgba(244,63,94,0.1);
        color: #FDA4AF;
        border-color: rgba(244,63,94,0.25);
    }
    .badge-amber {
        background: rgba(245,158,11,0.08);
        color: #B45309;
        border: 1px solid rgba(245,158,11,0.2);
    }
    html.dark .badge-amber {
        background: rgba(245,158,11,0.1);
        color: #FCD34D;
        border-color: rgba(245,158,11,0.25);
    }

    /* ── Button Styles ── */
    
    /* Emerald button (Green base → Gold hover) */
    .btn-emerald {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.6rem 1.2rem;
        font-size: 0.8rem;
        font-weight: 700;
        background: linear-gradient(135deg, var(--emerald), var(--emerald-dark));
        color: #fff;
        border: none;
        border-radius: 0.75rem;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        font-family: 'Outfit', sans-serif;
        box-shadow: 0 2px 10px rgba(5,150,105,0.22);
    }
    .btn-emerald:hover {
        background: linear-gradient(135deg, var(--gold), var(--gold-dark));
        color: #0f172a;
        transform: translateY(-1px);
        box-shadow: 0 4px 16px rgba(212,175,55,0.35);
    }

    /* Purple button for Receivable (Purple base → Gold hover) */
    .btn-purple {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.6rem 1.2rem;
        font-size: 0.8rem;
        font-weight: 700;
        background: linear-gradient(135deg, #7c3aed, #6d28d9);
        color: #fff;
        border: none;
        border-radius: 0.75rem;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        font-family: 'Outfit', sans-serif;
        box-shadow: 0 2px 10px rgba(124,58,237,0.22);
    }
    .btn-purple:hover {
        background: linear-gradient(135deg, var(--gold), var(--gold-dark));
        color: #0f172a;
        transform: translateY(-1px);
        box-shadow: 0 4px 16px rgba(212,175,55,0.35);
    }

    /* Blue button for Documents (Blue base → Gold hover) */
    .btn-blue {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.6rem 1.2rem;
        font-size: 0.8rem;
        font-weight: 700;
        background: linear-gradient(135deg, #0EA5E9, #0284C7);
        color: #fff;
        border: none;
        border-radius: 0.75rem;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        font-family: 'Outfit', sans-serif;
        box-shadow: 0 2px 10px rgba(14,165,233,0.22);
    }
    .btn-blue:hover {
        background: linear-gradient(135deg, var(--gold), var(--gold-dark));
        color: #0f172a;
        transform: translateY(-1px);
        box-shadow: 0 4px 16px rgba(212,175,55,0.35);
    }

    /* Red button for Expense (Red base → Gold hover) */
    .btn-red {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.6rem 1.2rem;
        font-size: 0.8rem;
        font-weight: 700;
        background: linear-gradient(135deg, #e11d48, #f43f5e);
        color: #fff;
        border: none;
        border-radius: 0.75rem;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        font-family: 'Outfit', sans-serif;
        box-shadow: 0 2px 10px rgba(225,29,72,0.22);
    }
    .btn-red:hover {
        background: linear-gradient(135deg, var(--gold), var(--gold-dark));
        color: #0f172a;
        transform: translateY(-1px);
        box-shadow: 0 4px 16px rgba(212,175,55,0.35);
    }

    /* Gold button for Edit Profile (Gold base → Emerald hover) */
    .btn-gold {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.6rem 1.2rem;
        font-size: 0.8rem;
        font-weight: 700;
        background: linear-gradient(135deg, var(--gold), var(--gold-dark));
        color: #0f172a;
        border: none;
        border-radius: 0.75rem;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        font-family: 'Outfit', sans-serif;
        box-shadow: 0 2px 10px rgba(212,175,55,0.25);
    }
    .btn-gold:hover {
        background: linear-gradient(135deg, var(--emerald), var(--emerald-dark));
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 4px 16px rgba(5,150,105,0.35);
    }

    /* ── Chart container ── */
    .chart-wrap {
        background: linear-gradient(145deg,
            rgba(5,150,105,0.03) 0%,
            rgba(255,255,255,1) 30%,
            rgba(212,175,55,0.03) 100%);
        border: 1px solid rgba(212,175,55,0.18);
    }
    html.dark .chart-wrap {
        background: linear-gradient(145deg,
            rgba(5,150,105,0.08) 0%,
            rgba(15,23,42,1) 40%,
            rgba(212,175,55,0.06) 100%);
        border-color: rgba(212,175,55,0.12);
    }

    /* ── Range pill ── */
    .range-pill {
        padding: 0.35rem 0.9rem;
        border-radius: 0.5rem;
        font-size: 0.75rem;
        font-weight: 600;
        font-family: 'DM Mono', monospace;
        transition: all 0.2s ease;
        color: var(--text-3);
        text-decoration: none;
    }
    .range-pill:hover {
        background: rgba(212,175,55,0.1);
        color: var(--gold-dark);
    }
    html.dark .range-pill:hover { color: var(--gold-light); }

    .range-pill.active {
        background: linear-gradient(135deg, var(--emerald) 0%, var(--emerald-dark) 100%);
        color: white;
        box-shadow: 0 2px 10px rgba(5,150,105,0.3);
    }

    /* ── Mini progress bar ── */
    .mini-bar-track {
        height: 3px;
        border-radius: 99px;
        background: var(--border);
        overflow: hidden;
        margin-top: 0.75rem;
    }
    .mini-bar-fill {
        height: 100%;
        border-radius: 99px;
        transition: width 1s ease;
    }

    /* ── Profile avatar ring ── */
    .avatar-ring {
        box-shadow: 0 0 0 2px var(--surface), 0 0 0 4px rgba(212,175,55,0.4);
    }

    /* ── View all link ── */
    .view-all {
        font-size: 0.78rem;
        font-weight: 600;
        color: var(--emerald);
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        text-decoration: none;
        transition: color 0.15s ease;
    }
    .view-all:hover { color: var(--gold-dark); }

    /* ── Icon chip ── */
    .icon-chip {
        width: 2.25rem; height: 2.25rem;
        border-radius: 0.625rem;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }

    /* ── Review button ── */
    .review-btn {
        font-size: 0.72rem;
        font-weight: 700;
        padding: 0.3rem 0.75rem;
        border-radius: 0.5rem;
        background: rgba(212,175,55,0.1);
        color: var(--gold-dark);
        border: 1px solid rgba(212,175,55,0.25);
        transition: all 0.15s ease;
        text-decoration: none;
        font-family: 'DM Mono', monospace;
        white-space: nowrap;
    }
    .review-btn:hover {
        background: var(--gold);
        color: white;
        box-shadow: 0 2px 10px rgba(212,175,55,0.35);
    }
    html.dark .review-btn { color: var(--gold-light); border-color: rgba(212,175,55,0.2); }

    /* ── Pending dot ── */
    @keyframes pulseRing {
        0%   { box-shadow: 0 0 0 0 rgba(239,68,68,0.4); }
        70%  { box-shadow: 0 0 0 6px rgba(239,68,68,0); }
        100% { box-shadow: 0 0 0 0 rgba(239,68,68,0); }
    }
    .pulse-ring { animation: pulseRing 2s ease infinite; }

    /* ── Hero gradient ── */
    .hero-gradient {
        background: linear-gradient(135deg,
            #064E3B 0%,
            #065F46 35%,
            #047857 60%,
            #0A3A28 100%);
    }
    /* ── Stat bar (bottom of chart) ── */
    .stat-bar-cell {
        padding: 0.75rem 1rem;
        text-align: center;
        border-right: 1px solid var(--border);
        flex: 1;
    }
    .stat-bar-cell:last-child { border-right: none; }
</style>
@endpush

@section('content')
@php
    $hour = now()->format('H');
    $greeting = match(true) {
        $hour < 12 => 'Good morning',
        $hour < 18 => 'Good afternoon',
        default    => 'Good evening'
    };
@endphp

<div style="font-family:'Outfit',sans-serif;" class="space-y-5">

    {{-- ══════════════════════════════════════
         HERO HEADER — emerald green base
    ══════════════════════════════════════ --}}
    <div class="hero-gradient relative overflow-hidden rounded-2xl anim-fade-up" style="min-height:156px;">
        {{-- Decorative elements --}}
        <div class="absolute inset-0 opacity-[0.05]"
             style="background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,1) 1px, transparent 0); background-size: 28px 28px;"></div>
        <div class="absolute -top-16 right-0 w-72 h-72 rounded-full opacity-20"
             style="background: radial-gradient(circle, #D4AF37, transparent 65%); filter: blur(48px);"></div>
        <div class="absolute bottom-0 left-1/2 w-48 h-48 rounded-full opacity-10"
             style="background: radial-gradient(circle, #6EE7B7, transparent 65%); filter: blur(36px);"></div>

        <div class="relative z-10 p-6 md:p-7">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                {{-- Greeting --}}
                <div>
                    <p class="text-emerald-300 text-[11px] font-semibold tracking-[0.2em] uppercase mb-1"
                       style="font-family:'DM Mono',monospace;">
                        {{ now()->format('l · F j, Y') }}
                    </p>
                    <h1 class="text-2xl md:text-3xl font-bold text-white tracking-tight leading-snug">
                        {{ $greeting }},
                        <span style="background: linear-gradient(90deg, #fff 0%, #D4AF37 100%);
                                     -webkit-background-clip: text; background-clip: text; color: transparent;">
                            {{ $user->first_name ?: $user->email }}
                        </span>
                    </h1>
                    <p class="text-emerald-200/70 text-sm mt-1.5 max-w-lg">{{ $roleDescription }}</p>
                </div>

                {{-- Right: badges + pending alert --}}
                <div class="flex flex-col items-start md:items-end gap-2.5">
                    {{-- Badges --}}
                    <div class="flex flex-wrap gap-2">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold"
                              style="background: rgba(255,255,255,0.12); color:#D1FAE5; border:1px solid rgba(255,255,255,0.15);">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>
                            {{ $user->role->name }}
                        </span>
                        @if($user->role->abbreviation)
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold"
                              style="background: rgba(212,175,55,0.2); color:#F0CC55; border:1px solid rgba(212,175,55,0.3); font-family:'DM Mono',monospace;">
                            {{ $user->role->abbreviation }}
                        </span>
                        @endif
                        @if($user->position)
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs"
                              style="background: rgba(255,255,255,0.1); color:#E2E8F0; border:1px solid rgba(255,255,255,0.12);">
                            {{ $user->position }}
                        </span>
                        @endif
                        @if(isset($userBadges))
                            @foreach($userBadges as $badge)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold"
                                  style="background: rgba(139,92,246,0.2); color:#DDD6FE; border:1px solid rgba(139,92,246,0.3);">
                                ✦ {{ $badge['text'] }}
                            </span>
                            @endforeach
                        @endif
                    </div>

                    @if($pendingTasksCount > 0)
                    <div class="pulse-ring inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold"
                         style="background:rgba(239,68,68,0.15); color:#FCA5A5; border:1px solid rgba(239,68,68,0.3);">
                        <span class="w-1.5 h-1.5 rounded-full bg-red-400 animate-pulse"></span>
                        {{ $pendingTasksCount }} pending task{{ $pendingTasksCount > 1 ? 's' : '' }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════
         SUMMARY CARDS
    ══════════════════════════════════════ --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        {{-- Income --}}
        <div class="dash-card dash-card-hover stat-card anim-fade-up d1 p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="icon-chip" style="background: rgba(5,150,105,0.1);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                         style="color:var(--emerald);">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                    </svg>
                </div>
                <span class="section-label" style="margin:0;">Income</span>
            </div>
            <p class="text-2xl md:text-3xl font-bold leading-none break-all"
               style="color:var(--emerald-dark); font-family:'DM Mono',monospace;">
                ₱{{ number_format($incomeTotal, 0) }}
            </p>
            <p class="text-[11px] mt-1" style="color:var(--text-3);">Approved · all time</p>
            <div class="mini-bar-track">
                <div class="mini-bar-fill"
                     style="width:{{ $incomeTotal > 0 ? min(100,($incomeTotal/max($incomeTotal,$expenseTotal))*100) : 0 }}%;
                            background:linear-gradient(90deg,var(--emerald),var(--emerald-light));"></div>
            </div>
        </div>

        {{-- Expenses --}}
        <div class="dash-card dash-card-hover stat-card anim-fade-up d2 p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="icon-chip" style="background:rgba(244,63,94,0.08);">
                    <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                    </svg>
                </div>
                <span class="section-label" style="margin:0; color:#E11D48;">Expenses</span>
            </div>
            <p class="text-2xl md:text-3xl font-bold leading-none break-all text-rose-600 dark:text-rose-400"
               style="font-family:'DM Mono',monospace;">
                ₱{{ number_format($expenseTotal, 0) }}
            </p>
            <p class="text-[11px] mt-1" style="color:var(--text-3);">Approved · all time</p>
            <div class="mini-bar-track">
                <div class="mini-bar-fill"
                     style="width:{{ $expenseTotal > 0 ? min(100,($expenseTotal/max($incomeTotal,$expenseTotal))*100) : 0 }}%;
                            background:linear-gradient(90deg,#F43F5E,#FDA4AF);"></div>
            </div>
        </div>

        {{-- Balance --}}
        <div class="dash-card dash-card-hover stat-card anim-fade-up d3 p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="icon-chip" style="background:rgba(212,175,55,0.1);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                         style="color:var(--gold-dark);">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
                    </svg>
                </div>
                <span class="section-label" style="margin:0; color:var(--gold-dark);">Balance</span>
            </div>
            <p class="text-2xl md:text-3xl font-bold leading-none break-all"
               style="color:{{ $balance >= 0 ? 'var(--emerald-dark)' : '#E11D48' }}; font-family:'DM Mono',monospace;">
                {{ $balance >= 0 ? '' : '-' }}₱{{ number_format(abs($balance), 0) }}
            </p>
            <p class="text-[11px] mt-1" style="color:var(--text-3);">
                Net · {{ $balance >= 0 ? 'surplus' : 'deficit' }}
            </p>
            <div class="mini-bar-track">
                <div class="mini-bar-fill"
                     style="width:{{ $incomeTotal > 0 ? min(100,(abs($balance)/max($incomeTotal,1))*100) : 0 }}%;
                            background:{{ $balance >= 0 ? 'linear-gradient(90deg,var(--gold),var(--gold-light))' : 'linear-gradient(90deg,#F43F5E,#FDA4AF)' }};"></div>
            </div>
        </div>

        {{-- Pending --}}
        <div class="dash-card dash-card-hover stat-card anim-fade-up d4 p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="icon-chip" style="background:rgba(245,158,11,0.1);">
                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="section-label" style="margin:0; color:#B45309;">Pending</span>
            </div>
            <p class="text-2xl md:text-3xl font-bold leading-none text-amber-600 dark:text-amber-400"
               style="font-family:'DM Mono',monospace;">
                {{ $pendingTransactions }}
            </p>
            <p class="text-[11px] mt-1" style="color:var(--text-3);">Awaiting action</p>
            <div class="mini-bar-track">
                @if($pendingTransactions > 0)
                <div class="mini-bar-fill animate-pulse"
                     style="width:65%; background:linear-gradient(90deg,#F59E0B,#FCD34D);"></div>
                @endif
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════
         FINANCIAL CHART
    ══════════════════════════════════════ --}}
    <div class="chart-wrap rounded-2xl anim-fade-up d3" style="overflow:hidden;">
        <div class="p-5 md:p-6">
            {{-- Header --}}
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-5">
                {{-- Title --}}
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <div class="icon-chip" style="background:rgba(5,150,105,0.1); width:2rem; height:2rem; border-radius:0.5rem;">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                 style="color:var(--emerald);">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <h3 class="font-bold text-base" style="color:var(--text);">Revenue Overview</h3>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold"
                              style="background:rgba(5,150,105,0.1); color:var(--emerald-dark); border:1px solid rgba(5,150,105,0.2); font-family:'DM Mono',monospace;">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            LIVE
                        </span>
                    </div>
                    <p class="text-[11px]" style="color:var(--text-3); font-family:'DM Mono',monospace; padding-left:2.25rem;">
                        Income vs Expenses · {{ now()->year }}
                    </p>
                </div>

                {{-- Right: legend + range --}}
                <div class="flex flex-col items-start sm:items-end gap-2.5">
                    {{-- Legend pills --}}
                    <div class="flex flex-wrap items-center gap-3">
                        <div class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-sm" style="background:var(--emerald);"></span>
                            <span class="text-[11px] font-medium" style="color:var(--text-3);">Income</span>
                            <span class="text-[11px] font-bold" style="color:var(--emerald-dark); font-family:'DM Mono',monospace;">
                                ₱{{ number_format($incomeTotal, 0) }}
                            </span>
                        </div>
                        <div class="w-px h-3" style="background:var(--border);"></div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-sm" style="background:#F43F5E;"></span>
                            <span class="text-[11px] font-medium" style="color:var(--text-3);">Expenses</span>
                            <span class="text-[11px] font-bold text-rose-600 dark:text-rose-400"
                                  style="font-family:'DM Mono',monospace;">
                                ₱{{ number_format($expenseTotal, 0) }}
                            </span>
                        </div>
                        <div class="w-px h-3" style="background:var(--border);"></div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-sm" style="background:var(--gold);"></span>
                            <span class="text-[11px] font-medium" style="color:var(--text-3);">Net</span>
                            <span class="text-[11px] font-bold"
                                  style="color:{{ $balance >= 0 ? 'var(--emerald-dark)' : '#E11D48' }}; font-family:'DM Mono',monospace;">
                                {{ $balance >= 0 ? '+' : '' }}₱{{ number_format($balance, 0) }}
                            </span>
                        </div>
                    </div>

                    {{-- Range toggle --}}
                    <div class="flex items-center gap-0.5 p-1 rounded-xl"
                         style="background:var(--surface-3); border:1px solid var(--border);">
                        @foreach(['weekly' => 'Week', 'monthly' => 'Month', 'yearly' => 'Year'] as $val => $lbl)
                        <a href="{{ route('dashboard', ['range' => $val]) }}"
                           class="range-pill {{ $range === $val ? 'active' : '' }}">
                            {{ $lbl }}
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Canvas --}}
            <div style="height:260px; position:relative;">
                <canvas id="financialChart"></canvas>
            </div>

            {{-- Bottom stat bar --}}
            <div class="flex mt-4 rounded-xl overflow-hidden"
                 style="border:1px solid var(--border); background:var(--surface-2);">
                <div class="stat-bar-cell">
                    <p class="section-label" style="margin:0 0 0.25rem;">Avg / Period</p>
                    <p class="text-sm font-bold" style="color:var(--emerald-dark); font-family:'DM Mono',monospace;">
                        ₱{{ number_format($incomeTotal / max(count($chartData['income']), 1), 0) }}
                    </p>
                    <p class="text-[10px] mt-0.5" style="color:var(--text-3);">income</p>
                </div>
                <div class="stat-bar-cell">
                    <p class="section-label" style="margin:0 0 0.25rem;">Avg / Period</p>
                    <p class="text-sm font-bold text-rose-600 dark:text-rose-400" style="font-family:'DM Mono',monospace;">
                        ₱{{ number_format($expenseTotal / max(count($chartData['expense']), 1), 0) }}
                    </p>
                    <p class="text-[10px] mt-0.5" style="color:var(--text-3);">expense</p>
                </div>
                <div class="stat-bar-cell" style="border-right:none;">
                    <p class="section-label" style="margin:0 0 0.25rem;">Savings Rate</p>
                    <p class="text-sm font-bold" style="color:{{ $balance >= 0 ? 'var(--gold-dark)' : '#E11D48' }}; font-family:'DM Mono',monospace;">
                        @if($incomeTotal > 0) {{ number_format(($balance / $incomeTotal) * 100, 1) }}%
                        @else —
                        @endif
                    </p>
                    <p class="text-[10px] mt-0.5" style="color:var(--text-3);">net / income</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════
         MEMBER STATS
    ══════════════════════════════════════ --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @php
        $memberStats = [
            ['label'=>'Total Members',  'value'=>number_format($totalMembers),       'sub'=>'All registered',       'iconColor'=>'#6366F1', 'iconBg'=>'rgba(99,102,241,0.1)',  'icon'=>'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
            ['label'=>'Active Members', 'value'=>number_format($activeMembers),       'sub'=>'Currently active',    'iconColor'=>'var(--emerald)', 'iconBg'=>'rgba(5,150,105,0.1)', 'icon'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['label'=>'Leaders',        'value'=>number_format($officersCount),        'sub'=>'Officers & advisers', 'iconColor'=>'var(--gold-dark)', 'iconBg'=>'rgba(212,175,55,0.1)', 'icon'=>'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z'],
            ['label'=>'New This Month', 'value'=>number_format($newMembersThisMonth), 'sub'=>now()->format('F Y'), 'iconColor'=>'#EC4899', 'iconBg'=>'rgba(236,72,153,0.1)',  'icon'=>'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z'],
        ];
        @endphp

        @foreach($memberStats as $i => $stat)
        <div class="dash-card dash-card-hover stat-card anim-fade-up d{{ $i+2 }} p-5">
            <div class="flex items-start justify-between mb-4">
                <div class="icon-chip" style="background:{{ $stat['iconBg'] }};">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                         style="color:{{ $stat['iconColor'] }};">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $stat['icon'] }}"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl md:text-3xl font-bold leading-none mb-1"
               style="color:var(--text); font-family:'DM Mono',monospace;">{{ $stat['value'] }}</p>
            <p class="text-sm font-semibold" style="color:var(--text-2);">{{ $stat['label'] }}</p>
            <p class="text-[11px] mt-0.5" style="color:var(--text-3);">{{ $stat['sub'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- ══════════════════════════════════════
         QUICK ACTIONS — All buttons with proper colors
    ══════════════════════════════════════════ --}}
    @if($user->hasPermission('members.create') || $user->hasPermission('documents.create') || $user->hasPermission('financial.create'))
    <div class="anim-fade-up d5">
        <p class="section-label mb-3">Quick Actions</p>
        <div class="flex flex-wrap gap-3">
            {{-- Add Member — Emerald (Green → Gold) --}}
            @if($user->hasPermission('members.create'))
            <a href="{{ route('members.create') }}" class="btn-emerald">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Add Member
            </a>
            @endif
            
            {{-- Upload Document — Blue (Blue → Gold) --}}
            @if($user->hasPermission('documents.create'))
            <a href="{{ route('documents.create') }}" class="btn-blue">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Upload Document
            </a>
            @endif
            
            {{-- Add Income — Emerald (Green → Gold) --}}
            @if($user->hasPermission('financial.create'))
            <a href="{{ route('financial.income.create') }}" class="btn-emerald">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Add Income
            </a>
            @endif
            
            {{-- Add Expense — Red (Red → Gold) --}}
            @if($user->hasPermission('financial.create'))
            <a href="{{ route('financial.expense.create') }}" class="btn-red">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/>
                </svg>
                Add Expense
            </a>
            @endif
            
            {{-- Add Receivable — Purple (Purple → Gold) --}}
            @if($user->hasPermission('financial.create'))
            <a href="{{ route('financial.receivable.create') }}" class="btn-purple">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Add Receivable
            </a>
            @endif
        </div>
    </div>
    @endif

    {{-- ══════════════════════════════════════
         MAIN GRID — Profile + Content
    ══════════════════════════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Profile Card --}}
        <div class="lg:col-span-1 anim-fade-up d4">
             @if($user->hasPermission('profile.index') && $user->role->name !== 'Guest')
            <div class="dash-card dash-card-hover lg:sticky lg:top-6" style="overflow:visible;">
                {{-- Card header --}}
                <div class="hero-gradient px-5 py-5 relative overflow-hidden rounded-t-2xl">
                    <div class="absolute -top-8 -right-8 w-28 h-28 rounded-full opacity-15"
                         style="background:radial-gradient(circle,var(--gold),transparent); filter:blur(20px);"></div>
                    <div class="flex items-center gap-3 relative z-10">
                        <div class="relative">
                            <div class="w-12 h-12 rounded-2xl overflow-hidden avatar-ring"
                                 style="border:2px solid rgba(212,175,55,0.5);">
                                @if($user->avatar_url)
                                    <img src="{{ $user->avatar_url }}" alt="{{ $user->full_name }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center font-bold text-base text-white"
                                         style="background:linear-gradient(135deg,var(--gold),var(--emerald)); font-family:'DM Mono',monospace;">
                                        {{ strtoupper(substr($user->full_name,0,2)) }}
                                    </div>
                                @endif
                            </div>
                            <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full bg-emerald-400 border-2 border-white dark:border-gray-900"></span>
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-white font-bold text-base truncate">{{ $user->full_name }}</h3>
                            <p class="text-emerald-200/70 text-xs truncate">{{ $user->email }}</p>
                            <div class="flex gap-1.5 mt-1.5">
                                <span class="badge-emerald text-[10px] font-semibold px-2 py-0.5 rounded-full">Active</span>
                                @if($user->hasVerifiedEmail())
                                <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full"
                                      style="background:rgba(59,130,246,0.2); color:#93C5FD;">Verified</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Fields --}}
                <div class="p-5">
                        @php
                        $profileRows = [
                            ['Role',         $user->role->name],
                            ['Position',     $user->member?->position ?? $user->position ?? '—'],
                            ['Member Since', optional($user->member?->joined_at ?? $user->member?->term_start ?? $user->created_at)->format('M d, Y') ?? '—'],
                            ['Last Login',   optional($user->last_login_at)->format('M d · H:i') ?? 'Never'],
                            ['Last Updated', optional($user->updated_at)->format('M d, Y')],
                        ];
                        @endphp
                        <div class="space-y-0">
                            @foreach($profileRows as $row)
                            <div class="flex justify-between items-center py-2.5 border-b dash-divider last:border-0">
                                <span class="text-xs" style="color:var(--text-3);">{{ $row[0] }}</span>
                                <span class="text-xs font-semibold text-right max-w-[56%] truncate" style="color:var(--text);">{{ $row[1] }}</span>
                            </div>
                            @endforeach
                        </div>
                       
                        {{-- Edit Profile Button — Gold (Gold → Emerald) --}}
                        <a href="{{ route('profile.index') }}" class="btn-emerald w-full justify-center mt-4">
                            Edit Profile
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                        
                </div>
            </div>
            @endif
        </div>

        {{-- Right Column --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Recent Documents --}}
            @if($user->hasPermission('documents.view') && isset($recentDocuments) && count($recentDocuments) > 0)
            <div class="dash-card dash-card-hover anim-fade-up d5">
                <div class="flex items-center justify-between px-5 py-4 border-b dash-divider">
                    <div class="flex items-center gap-2.5">
                        <div class="icon-chip" style="background:rgba(245,158,11,0.1); width:2rem; height:2rem; border-radius:0.5rem;">
                            <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-sm" style="color:var(--text);">Recent Documents</h3>
                    </div>
                    <a href="{{ route('documents.index') }}" class="view-all">
                        View all
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
                <ul>
                    @forelse($recentDocuments as $doc)
                    <li class="row-hover flex items-center gap-3 px-5 py-3.5">
                        <div class="icon-chip" style="background:rgba(245,158,11,0.08); flex-shrink:0;">
                            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                      d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium truncate" style="color:var(--text);">{{ $doc->title }}</p>
                            <p class="text-[11px] mt-0.5" style="color:var(--text-3);">
                                {{ $doc->uploader->full_name ?? 'Unknown' }} · {{ optional($doc->created_at)->diffForHumans() }}
                            </p>
                        </div>
                    </li>
                    @empty
                    <li class="px-5 py-8 text-center text-sm" style="color:var(--text-3);">No documents yet.</li>
                    @endforelse
                </ul>
            </div>
            @endif

            {{-- Recent Transactions --}}
            @if($user->hasPermission('financial.view') && isset($recentTransactions) && count($recentTransactions) > 0)
            <div class="dash-card dash-card-hover anim-fade-up d6">
                <div class="flex items-center justify-between px-5 py-4 border-b dash-divider">
                    <div class="flex items-center gap-2.5">
                        <div class="icon-chip" style="background:rgba(5,150,105,0.1); width:2rem; height:2rem; border-radius:0.5rem;">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                 style="color:var(--emerald);">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-sm" style="color:var(--text);">Recent Transactions</h3>
                    </div>
                    <a href="{{ route('financial.index') }}" class="view-all">
                        View all
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
                <ul>
                    @forelse($recentTransactions as $tx)
                    <li class="row-hover flex items-center justify-between px-5 py-3.5">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="icon-chip flex-shrink-0"
                                 style="background:{{ $tx->type === 'income' ? 'rgba(5,150,105,0.1)' : ($tx->type === 'expense' ? 'rgba(244,63,94,0.08)' : 'rgba(124,58,237,0.1)') }};">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                     style="color:{{ $tx->type === 'income' ? 'var(--emerald)' : ($tx->type === 'expense' ? '#F43F5E' : '#7c3aed') }};">
                                    @if($tx->type === 'income')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                                    @elseif($tx->type === 'expense')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                                    @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    @endif
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-medium truncate" style="color:var(--text);">{{ $tx->description }}</p>
                                <p class="text-[11px] mt-0.5" style="color:var(--text-3); font-family:'DM Mono',monospace;">
                                    {{ ucfirst($tx->type) }} · {{ optional($tx->date ?? $tx->created_at)->format('M d, Y') }}
                                </p>
                            </div>
                        </div>
                        <span class="text-xs font-bold px-2.5 py-1 rounded-lg flex-shrink-0 ml-3"
                              style="font-family:'DM Mono',monospace;
                                     background:{{ $tx->type === 'income' ? 'rgba(5,150,105,0.1)' : ($tx->type === 'expense' ? 'rgba(244,63,94,0.08)' : 'rgba(124,58,237,0.1)') }};
                                     color:{{ $tx->type === 'income' ? 'var(--emerald-dark)' : ($tx->type === 'expense' ? '#E11D48' : '#6d28d9') }};">
                            {{ $tx->type === 'income' ? '+' : ($tx->type === 'expense' ? '−' : '↻') }}₱{{ number_format($tx->amount, 2) }}
                        </span>
                    </li>
                    @empty
                    <li class="px-5 py-8 text-center text-sm" style="color:var(--text-3);">No transactions yet.</li>
                    @endforelse
                </ul>
            </div>
            @endif

            {{-- Pending Approvals --}}
            @if($user->hasPermission('financial.approve') && isset($pendingApprovals) && count($pendingApprovals) > 0)
            <div class="dash-card anim-fade-up d7" style="border-color:rgba(212,175,55,0.3);">
                <div class="flex items-center justify-between px-5 py-4 border-b dash-divider">
                    <div class="flex items-center gap-2.5">
                        <div class="icon-chip" style="background:rgba(212,175,55,0.1); width:2rem; height:2rem; border-radius:0.5rem;">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                 style="color:var(--gold-dark);">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-sm" style="color:var(--text);">Pending Approvals</h3>
                        <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold text-white"
                              style="background:var(--gold-dark);">{{ count($pendingApprovals) }}</span>
                    </div>
                    <a href="{{ route('financial.index') }}" class="view-all" style="color:var(--gold-dark);">
                        Review all
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
                <ul>
                    @foreach($pendingApprovals as $item)
                    <li class="row-hover flex items-center justify-between px-5 py-3.5">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium truncate" style="color:var(--text);">{{ $item['title'] }}</p>
                            <p class="text-[11px] mt-0.5" style="color:var(--text-3);">
                                {{ $item['type'] }} · by {{ $item['submitter'] }}
                            </p>
                        </div>
                        <a href="{{ $item['link'] }}" class="review-btn ml-3 flex-shrink-0">Review →</a>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Financial Summary Banner --}}
            @if($user->hasPermission('financial.view') && isset($totalIncome))
            <div class="hero-gradient relative overflow-hidden rounded-2xl p-5 anim-fade-up d7">
                <div class="absolute -top-10 -right-10 w-40 h-40 rounded-full opacity-20"
                     style="background:radial-gradient(circle,var(--gold),transparent); filter:blur(28px);"></div>
                <div class="relative z-10 flex items-center justify-between gap-4">
                    <div class="min-w-0">
                        <p class="text-emerald-300 text-[10px] font-bold tracking-widest uppercase mb-1"
                           style="font-family:'DM Mono',monospace;">All-Time Financial Summary</p>
                        <p class="text-2xl md:text-3xl font-bold text-white break-all leading-tight"
                           style="font-family:'DM Mono',monospace;">
                            ₱{{ number_format($totalIncome, 2) }}
                        </p>
                        <p class="text-emerald-200/60 text-xs mt-1.5" style="font-family:'DM Mono',monospace;">
                            Expenses: ₱{{ number_format($totalExpense, 2) }}
                            <span class="mx-1.5 opacity-40">·</span>
                            Net:
                            <span class="{{ $netBalance >= 0 ? 'text-emerald-300' : 'text-rose-300' }}">
                                ₱{{ number_format($netBalance, 2) }}
                            </span>
                        </p>
                    </div>
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center flex-shrink-0"
                         style="background:rgba(212,175,55,0.2); border:1px solid rgba(212,175,55,0.3);">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                             style="color:var(--gold-light);">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                  d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
            @endif

        </div>{{-- end right col --}}
    </div>{{-- end main grid --}}

</div>

{{-- ── Chart.js ── --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const canvas = document.getElementById('financialChart');
    if (!canvas) return;

    const isDark  = document.documentElement.classList.contains('dark');
    const ctx     = canvas.getContext('2d');
    const data    = @json($chartData);
    const h       = 260;

    // Emerald gradient bars
    const ig = ctx.createLinearGradient(0, 0, 0, h);
    ig.addColorStop(0, 'rgba(5,150,105,0.85)');
    ig.addColorStop(1, 'rgba(5,150,105,0.25)');

    // Rose gradient bars
    const eg = ctx.createLinearGradient(0, 0, 0, h);
    eg.addColorStop(0, 'rgba(244,63,94,0.8)');
    eg.addColorStop(1, 'rgba(244,63,94,0.2)');

    const gridColor  = isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)';
    const tickColor  = isDark ? '#475569' : '#94A3B8';

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.labels,
            datasets: [
                {
                    label: 'Income',
                    data: data.income,
                    backgroundColor: ig,
                    borderWidth: 0,
                    borderRadius: { topLeft: 6, topRight: 6 },
                    borderSkipped: false,
                    barPercentage: 0.52,
                    categoryPercentage: 0.72,
                    order: 2,
                },
                {
                    label: 'Expenses',
                    data: data.expense,
                    backgroundColor: eg,
                    borderWidth: 0,
                    borderRadius: { topLeft: 6, topRight: 6 },
                    borderSkipped: false,
                    barPercentage: 0.52,
                    categoryPercentage: 0.72,
                    order: 2,
                },
                {
                    label: 'Net',
                    data: data.income.map((v, i) => v - data.expense[i]),
                    type: 'line',
                    borderColor: 'rgba(212,175,55,0.9)',
                    borderWidth: 2,
                    borderDash: [5, 4],
                    pointBackgroundColor: '#D4AF37',
                    pointBorderColor: isDark ? '#0F172A' : '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: false,
                    tension: 0.42,
                    order: 1,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            animation: { duration: 650, easing: 'easeInOutCubic' },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: isDark ? 'rgba(15,23,42,0.97)' : 'rgba(15,23,42,0.93)',
                    borderColor: 'rgba(212,175,55,0.2)',
                    borderWidth: 1,
                    padding: { top:10, bottom:10, left:14, right:14 },
                    titleColor: '#94A3B8',
                    titleFont: { size: 10, family: "'DM Mono',monospace" },
                    bodyFont:  { size: 12, family: "'DM Mono',monospace", weight: '600' },
                    bodySpacing: 5,
                    cornerRadius: 10,
                    boxWidth: 8, boxHeight: 8, boxPadding: 4,
                    callbacks: {
                        label: c => {
                            const v = c.raw ?? 0;
                            const s = c.dataset.label === 'Net' && v > 0 ? '+' : '';
                            return `  ${c.dataset.label}: ${s}₱${v.toLocaleString('en-PH',{minimumFractionDigits:0})}`;
                        }
                    }
                }
            },
            scales: {
                x: {
                    border: { display: false },
                    grid:   { display: false },
                    ticks: { color: tickColor, font: { size: 10, family:"'DM Mono',monospace" }, maxRotation: 0 }
                },
                y: {
                    border: { display: false, dash: [4,4] },
                    grid:   { color: gridColor, drawTicks: false },
                    ticks: {
                        color: tickColor,
                        font:  { size: 10, family:"'DM Mono',monospace" },
                        padding: 10,
                        callback: v => {
                            if (v >= 1_000_000) return '₱'+(v/1_000_000).toFixed(1)+'M';
                            if (v >= 1_000)     return '₱'+(v/1_000).toFixed(0)+'k';
                            return '₱'+v;
                        }
                    },
                    beginAtZero: true,
                }
            }
        }
    });
});
</script>
@endsection