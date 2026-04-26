@extends('layouts.app')

@section('title', 'Terms of Service — VSULHS SSLG')

@section('content')
<div class="relative overflow-hidden" x-data="{ activeSection: 'acceptance' }">

    {{-- ── Hero ────────────────────────────────────────────────────────── --}}
    <div class="relative bg-gradient-to-r from-emerald-700 to-emerald-900 dark:from-emerald-900 dark:to-emerald-950 py-20 px-6 md:px-12 text-center overflow-hidden">
        <div class="absolute top-6 left-6 z-20">
            <a href="{{ route('landing') }}" class="inline-flex items-center gap-2 text-white/70 hover:text-white text-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Home
            </a>
        </div>

        {{-- Print button --}}
        <div class="absolute top-6 right-6 z-20">
            <button onclick="window.print()"
                    class="inline-flex items-center gap-2 text-white/70 hover:text-white text-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Print
            </button>
        </div>

        <div class="absolute inset-0 opacity-20">
            <div class="absolute inset-0 bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:32px_32px]"></div>
        </div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[400px] bg-emerald-500/20 rounded-full blur-3xl"></div>

        <div class="relative z-10 max-w-3xl mx-auto">
            <div class="inline-block mb-4 px-4 py-1 rounded-full bg-emerald-500/20 text-emerald-100 text-xs font-semibold tracking-wide border border-emerald-400/30 backdrop-blur-sm">
                Legal
            </div>
            <h1 class="font-serif text-4xl md:text-5xl lg:text-6xl font-bold text-white leading-tight tracking-tight">
                Terms of Service
            </h1>
            <p class="text-emerald-100 text-lg mt-4 max-w-2xl mx-auto opacity-90">
                Please read these terms carefully before using the VSULHS SSLG portal.
            </p>
            {{-- Last updated badge --}}
            <div class="mt-6 inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 border border-white/20 text-white/70 text-xs backdrop-blur-sm">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Last updated: {{ now()->format('F d, Y') }}
            </div>
        </div>
    </div>

    {{-- ── TL;DR Summary Cards ─────────────────────────────────────────── --}}
    <div class="max-w-5xl mx-auto px-4 sm:px-6 -mt-6 relative z-10 mb-10">
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-emerald-200 dark:border-emerald-800 shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-emerald-600 to-emerald-700 dark:from-emerald-800 dark:to-emerald-900 px-6 py-3 flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-xs font-semibold text-emerald-100 uppercase tracking-wider">Quick Summary — The Key Points</span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 divide-y sm:divide-y-0 sm:divide-x divide-emerald-100 dark:divide-emerald-800/50">
                <div class="p-5 flex flex-col gap-2">
                    <div class="w-9 h-9 rounded-xl bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-semibold text-gray-800 dark:text-white">Verified Members Only</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">Only official VSULHS SSLG members may hold accounts. Keep your credentials private.</p>
                </div>
                <div class="p-5 flex flex-col gap-2">
                    <div class="w-9 h-9 rounded-xl bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-semibold text-gray-800 dark:text-white">Use Responsibly</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">No illegal activity, impersonation, or disruption. All financial records must be truthful.</p>
                </div>
                <div class="p-5 flex flex-col gap-2">
                    <div class="w-9 h-9 rounded-xl bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-semibold text-gray-800 dark:text-white">Your Data is Protected</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">We comply with RA 10173. Your data is never sold or shared for commercial purposes.</p>
                </div>
                <div class="p-5 flex flex-col gap-2">
                    <div class="w-9 h-9 rounded-xl bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
                        </svg>
                    </div>
                    <p class="text-sm font-semibold text-gray-800 dark:text-white">Philippine Law Governs</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">These terms are governed by Philippine law. Disputes are handled in Baybay City, Leyte.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Main Content: TOC + Sections ───────────────────────────────── --}}
    <div class="max-w-5xl mx-auto px-4 sm:px-6 pb-16">
        <div class="flex flex-col lg:flex-row gap-8 items-start">

            {{-- Sticky Table of Contents --}}
            <aside class="lg:w-56 flex-shrink-0 lg:sticky lg:top-24">
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-emerald-200 dark:border-emerald-800 shadow-sm overflow-hidden">
                    <div class="bg-emerald-50 dark:bg-emerald-900/20 px-4 py-3 border-b border-emerald-200 dark:border-emerald-800">
                        <p class="text-xs font-semibold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider">Contents</p>
                    </div>
                    <nav class="p-2">
                        @php
                            $toc = [
                                ['id' => 'acceptance',    'label' => '1. Acceptance'],
                                ['id' => 'accounts',      'label' => '2. User Accounts'],
                                ['id' => 'acceptable',    'label' => '3. Acceptable Use'],
                                ['id' => 'financial',     'label' => '4. Financial Records'],
                                ['id' => 'ip',            'label' => '5. Intellectual Property'],
                                ['id' => 'privacy',       'label' => '6. Privacy & Data'],
                                ['id' => 'termination',   'label' => '7. Termination'],
                                ['id' => 'liability',     'label' => '8. Liability'],
                                ['id' => 'modifications', 'label' => '9. Modifications'],
                                ['id' => 'governing',     'label' => '10. Governing Law'],
                                ['id' => 'contact',       'label' => '11. Contact'],
                            ];
                        @endphp
                        @foreach($toc as $item)
                            <a href="#{{ $item['id'] }}"
                               class="block px-3 py-1.5 text-xs rounded-lg transition-all
                                      text-gray-500 dark:text-gray-400
                                      hover:bg-emerald-50 dark:hover:bg-emerald-900/20
                                      hover:text-emerald-700 dark:hover:text-emerald-400">
                                {{ $item['label'] }}
                            </a>
                        @endforeach
                    </nav>
                </div>
            </aside>

            {{-- Sections --}}
            <div class="flex-1 min-w-0 space-y-5">

                {{-- Section: Acceptance --}}
                <div id="acceptance" class="bg-white dark:bg-gray-800 rounded-2xl border border-emerald-200 dark:border-emerald-800 shadow-sm overflow-hidden scroll-mt-28">
                    <div class="flex items-center gap-3 px-6 py-4 border-b border-emerald-100 dark:border-emerald-800/60 bg-emerald-50/50 dark:bg-emerald-900/10">
                        <span class="w-7 h-7 rounded-lg bg-emerald-600 text-white text-xs font-bold flex items-center justify-center flex-shrink-0">1</span>
                        <div class="inline-block px-3 py-0.5 text-xs font-semibold uppercase tracking-wider text-emerald-700 dark:text-emerald-400 bg-emerald-100 dark:bg-emerald-900/40 rounded-full">Acceptance</div>
                        <h2 class="font-serif text-lg font-semibold text-gray-900 dark:text-white">Acceptance of Terms</h2>
                    </div>
                    <div class="px-6 py-5 text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                        <p>By accessing or using the VSULHS Supreme Student Learner Government (SSLG) portal, you agree to be bound by these Terms of Service. If you do not agree, please do not use the portal.</p>
                    </div>
                </div>

                {{-- Section: User Accounts --}}
                <div id="accounts" class="bg-white dark:bg-gray-800 rounded-2xl border border-emerald-200 dark:border-emerald-800 shadow-sm overflow-hidden scroll-mt-28">
                    <div class="flex items-center gap-3 px-6 py-4 border-b border-emerald-100 dark:border-emerald-800/60 bg-emerald-50/50 dark:bg-emerald-900/10">
                        <span class="w-7 h-7 rounded-lg bg-emerald-600 text-white text-xs font-bold flex items-center justify-center flex-shrink-0">2</span>
                        <div class="inline-block px-3 py-0.5 text-xs font-semibold uppercase tracking-wider text-emerald-700 dark:text-emerald-400 bg-emerald-100 dark:bg-emerald-900/40 rounded-full">Accounts</div>
                        <h2 class="font-serif text-lg font-semibold text-gray-900 dark:text-white">User Accounts</h2>
                    </div>
                    <div class="px-6 py-5 space-y-3">
                        @php
                            $accountItems = [
                                ['label' => 'Eligibility',           'text' => 'Only verified members of VSULHS SSLG are permitted to have accounts.'],
                                ['label' => 'Accuracy',              'text' => 'You must provide accurate, complete, and up‑to‑date information.'],
                                ['label' => 'Security',              'text' => 'You are responsible for maintaining the confidentiality of your password and for all activities under your account.'],
                                ['label' => 'Unauthorised access',   'text' => 'You must notify the SSLG immediately of any suspected breach.'],
                            ];
                        @endphp
                        @foreach($accountItems as $item)
                            <div class="flex items-start gap-3 p-3 rounded-xl bg-gray-50 dark:bg-gray-700/30 border border-gray-100 dark:border-gray-700">
                                <svg class="w-4 h-4 text-emerald-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/>
                                </svg>
                                <p class="text-sm text-gray-700 dark:text-gray-300">
                                    <strong class="text-gray-900 dark:text-white">{{ $item['label'] }}:</strong>
                                    {{ $item['text'] }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Section: Acceptable Use --}}
                <div id="acceptable" class="bg-white dark:bg-gray-800 rounded-2xl border border-emerald-200 dark:border-emerald-800 shadow-sm overflow-hidden scroll-mt-28">
                    <div class="flex items-center gap-3 px-6 py-4 border-b border-emerald-100 dark:border-emerald-800/60 bg-emerald-50/50 dark:bg-emerald-900/10">
                        <span class="w-7 h-7 rounded-lg bg-emerald-600 text-white text-xs font-bold flex items-center justify-center flex-shrink-0">3</span>
                        <div class="inline-block px-3 py-0.5 text-xs font-semibold uppercase tracking-wider text-emerald-700 dark:text-emerald-400 bg-emerald-100 dark:bg-emerald-900/40 rounded-full">Usage</div>
                        <h2 class="font-serif text-lg font-semibold text-gray-900 dark:text-white">Acceptable Use</h2>
                    </div>
                    <div class="px-6 py-5">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">You agree <strong class="text-gray-800 dark:text-gray-200">not</strong> to:</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @php
                                $dontItems = [
                                    'Use the portal for any illegal purpose or in violation of any laws.',
                                    'Attempt to gain unauthorised access to other accounts or systems.',
                                    'Upload malicious code, viruses, or harmful content.',
                                    'Impersonate another member or officer.',
                                    'Disrupt the normal operation of the portal.',
                                    'Share your account credentials with others.',
                                ];
                            @endphp
                            @foreach($dontItems as $item)
                                <div class="flex items-start gap-2.5 p-3 rounded-xl bg-red-50 dark:bg-red-900/10 border border-red-100 dark:border-red-900/30">
                                    <svg class="w-4 h-4 text-red-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    <p class="text-xs text-gray-700 dark:text-gray-300 leading-relaxed">{{ $item }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Section: Financial Records --}}
                <div id="financial" class="bg-white dark:bg-gray-800 rounded-2xl border border-emerald-200 dark:border-emerald-800 shadow-sm overflow-hidden scroll-mt-28">
                    <div class="flex items-center gap-3 px-6 py-4 border-b border-emerald-100 dark:border-emerald-800/60 bg-emerald-50/50 dark:bg-emerald-900/10">
                        <span class="w-7 h-7 rounded-lg bg-emerald-600 text-white text-xs font-bold flex items-center justify-center flex-shrink-0">4</span>
                        <div class="inline-block px-3 py-0.5 text-xs font-semibold uppercase tracking-wider text-emerald-700 dark:text-emerald-400 bg-emerald-100 dark:bg-emerald-900/40 rounded-full">Financial</div>
                        <h2 class="font-serif text-lg font-semibold text-gray-900 dark:text-white">Financial Records & Documents</h2>
                    </div>
                    <div class="px-6 py-5 space-y-3">
                        @php
                            $financialItems = [
                                ['label' => 'Accuracy',        'text' => 'All financial entries and documents uploaded must be truthful and authorised.'],
                                ['label' => 'Confidentiality', 'text' => 'Private documents and financial data must not be shared outside the organisation.'],
                                ['label' => 'Retention',       'text' => 'Records will be kept in accordance with the organisation\'s data retention policy.'],
                            ];
                        @endphp
                        @foreach($financialItems as $item)
                            <div class="flex items-start gap-3 p-3 rounded-xl bg-gray-50 dark:bg-gray-700/30 border border-gray-100 dark:border-gray-700">
                                <svg class="w-4 h-4 text-emerald-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/>
                                </svg>
                                <p class="text-sm text-gray-700 dark:text-gray-300">
                                    <strong class="text-gray-900 dark:text-white">{{ $item['label'] }}:</strong>
                                    {{ $item['text'] }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Section: Intellectual Property --}}
                <div id="ip" class="bg-white dark:bg-gray-800 rounded-2xl border border-emerald-200 dark:border-emerald-800 shadow-sm overflow-hidden scroll-mt-28">
                    <div class="flex items-center gap-3 px-6 py-4 border-b border-emerald-100 dark:border-emerald-800/60 bg-emerald-50/50 dark:bg-emerald-900/10">
                        <span class="w-7 h-7 rounded-lg bg-emerald-600 text-white text-xs font-bold flex items-center justify-center flex-shrink-0">5</span>
                        <div class="inline-block px-3 py-0.5 text-xs font-semibold uppercase tracking-wider text-emerald-700 dark:text-emerald-400 bg-emerald-100 dark:bg-emerald-900/40 rounded-full">IP</div>
                        <h2 class="font-serif text-lg font-semibold text-gray-900 dark:text-white">Intellectual Property</h2>
                    </div>
                    <div class="px-6 py-5 text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                        <p>All content, design, logos, and code of this portal are the property of VSULHS SSLG. You may not copy, modify, or redistribute any part without written permission.</p>
                    </div>
                </div>

                {{-- Section: Privacy --}}
                <div id="privacy" class="bg-white dark:bg-gray-800 rounded-2xl border border-emerald-200 dark:border-emerald-800 shadow-sm overflow-hidden scroll-mt-28">
                    <div class="flex items-center gap-3 px-6 py-4 border-b border-emerald-100 dark:border-emerald-800/60 bg-emerald-50/50 dark:bg-emerald-900/10">
                        <span class="w-7 h-7 rounded-lg bg-emerald-600 text-white text-xs font-bold flex items-center justify-center flex-shrink-0">6</span>
                        <div class="inline-block px-3 py-0.5 text-xs font-semibold uppercase tracking-wider text-emerald-700 dark:text-emerald-400 bg-emerald-100 dark:bg-emerald-900/40 rounded-full">Privacy</div>
                        <h2 class="font-serif text-lg font-semibold text-gray-900 dark:text-white">Privacy & Data Protection</h2>
                    </div>
                    <div class="px-6 py-5 text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                        <p>
                            Your use of the portal is also governed by our
                            <a href="{{ route('data-privacy-act') }}"
                               class="inline-flex items-center gap-0.5 font-medium text-emerald-600 dark:text-emerald-400 hover:underline underline-offset-2">
                                Data Privacy Policy
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                            </a>,
                            which complies with the
                            <a href="https://www.privacy.gov.ph/data-privacy-act/"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="inline-flex items-center gap-0.5 font-medium text-emerald-600 dark:text-emerald-400 hover:underline underline-offset-2">
                                Data Privacy Act of 2012 (Republic Act No. 10173)
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                            </a>.
                        </p>
                    </div>
                </div>

                {{-- Section: Termination --}}
                <div id="termination" class="bg-white dark:bg-gray-800 rounded-2xl border border-emerald-200 dark:border-emerald-800 shadow-sm overflow-hidden scroll-mt-28">
                    <div class="flex items-center gap-3 px-6 py-4 border-b border-emerald-100 dark:border-emerald-800/60 bg-emerald-50/50 dark:bg-emerald-900/10">
                        <span class="w-7 h-7 rounded-lg bg-emerald-600 text-white text-xs font-bold flex items-center justify-center flex-shrink-0">7</span>
                        <div class="inline-block px-3 py-0.5 text-xs font-semibold uppercase tracking-wider text-emerald-700 dark:text-emerald-400 bg-emerald-100 dark:bg-emerald-900/40 rounded-full">Termination</div>
                        <h2 class="font-serif text-lg font-semibold text-gray-900 dark:text-white">Termination</h2>
                    </div>
                    <div class="px-6 py-5 text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                        <p>The SSLG reserves the right to suspend or terminate your account if you violate these Terms. Upon termination, your access to the portal will be revoked, and any pending data may be archived or deleted in accordance with applicable data retention policies.</p>
                    </div>
                </div>

                {{-- Section: Liability --}}
                <div id="liability" class="bg-white dark:bg-gray-800 rounded-2xl border border-amber-200 dark:border-amber-800/50 shadow-sm overflow-hidden scroll-mt-28">
                    <div class="flex items-center gap-3 px-6 py-4 border-b border-amber-100 dark:border-amber-800/40 bg-amber-50/60 dark:bg-amber-900/10">
                        <span class="w-7 h-7 rounded-lg bg-amber-500 text-white text-xs font-bold flex items-center justify-center flex-shrink-0">8</span>
                        <div class="inline-block px-3 py-0.5 text-xs font-semibold uppercase tracking-wider text-amber-700 dark:text-amber-400 bg-amber-100 dark:bg-amber-900/40 rounded-full">Important</div>
                        <h2 class="font-serif text-lg font-semibold text-gray-900 dark:text-white">Limitation of Liability</h2>
                    </div>
                    <div class="px-6 py-5 text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                        <p>The portal is provided on an "as is" and "as available" basis without warranties of any kind, whether express or implied. VSULHS SSLG, its officers, and its advisers shall not be liable for any indirect, incidental, special, or consequential damages — including but not limited to loss of data or service interruption — arising from your use of or inability to use the portal. We do not guarantee uninterrupted, timely, or error‑free operation of the system.</p>
                    </div>
                </div>

                {{-- Section: Modifications --}}
                <div id="modifications" class="bg-white dark:bg-gray-800 rounded-2xl border border-emerald-200 dark:border-emerald-800 shadow-sm overflow-hidden scroll-mt-28">
                    <div class="flex items-center gap-3 px-6 py-4 border-b border-emerald-100 dark:border-emerald-800/60 bg-emerald-50/50 dark:bg-emerald-900/10">
                        <span class="w-7 h-7 rounded-lg bg-emerald-600 text-white text-xs font-bold flex items-center justify-center flex-shrink-0">9</span>
                        <div class="inline-block px-3 py-0.5 text-xs font-semibold uppercase tracking-wider text-emerald-700 dark:text-emerald-400 bg-emerald-100 dark:bg-emerald-900/40 rounded-full">Updates</div>
                        <h2 class="font-serif text-lg font-semibold text-gray-900 dark:text-white">Modifications to Terms</h2>
                    </div>
                    <div class="px-6 py-5 text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                        <p>We may update these Terms from time to time. Continued use of the portal after changes constitutes acceptance of the revised Terms. Significant changes will be communicated via an in-portal announcement or email notification.</p>
                    </div>
                </div>

                {{-- Section: Governing Law --}}
                <div id="governing" class="bg-white dark:bg-gray-800 rounded-2xl border border-emerald-200 dark:border-emerald-800 shadow-sm overflow-hidden scroll-mt-28">
                    <div class="flex items-center gap-3 px-6 py-4 border-b border-emerald-100 dark:border-emerald-800/60 bg-emerald-50/50 dark:bg-emerald-900/10">
                        <span class="w-7 h-7 rounded-lg bg-emerald-600 text-white text-xs font-bold flex items-center justify-center flex-shrink-0">10</span>
                        <div class="inline-block px-3 py-0.5 text-xs font-semibold uppercase tracking-wider text-emerald-700 dark:text-emerald-400 bg-emerald-100 dark:bg-emerald-900/40 rounded-full">Legal</div>
                        <h2 class="font-serif text-lg font-semibold text-gray-900 dark:text-white">Governing Law</h2>
                    </div>
                    <div class="px-6 py-5 text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                        <p>These Terms shall be governed by and construed in accordance with the laws of the <strong class="text-gray-900 dark:text-white">Republic of the Philippines</strong>. Any disputes arising from these Terms or your use of the portal shall be brought exclusively before the appropriate courts of Baybay City, Leyte.</p>
                    </div>
                </div>

                {{-- Section: Contact --}}
                <div id="contact" class="bg-white dark:bg-gray-800 rounded-2xl border border-emerald-200 dark:border-emerald-800 shadow-sm overflow-hidden scroll-mt-28">
                    <div class="flex items-center gap-3 px-6 py-4 border-b border-emerald-100 dark:border-emerald-800/60 bg-emerald-50/50 dark:bg-emerald-900/10">
                        <span class="w-7 h-7 rounded-lg bg-emerald-600 text-white text-xs font-bold flex items-center justify-center flex-shrink-0">11</span>
                        <div class="inline-block px-3 py-0.5 text-xs font-semibold uppercase tracking-wider text-emerald-700 dark:text-emerald-400 bg-emerald-100 dark:bg-emerald-900/40 rounded-full">Contact</div>
                        <h2 class="font-serif text-lg font-semibold text-gray-900 dark:text-white">Contact Information</h2>
                    </div>
                    <div class="px-6 py-5 text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                        <p>If you have any questions about these Terms, please contact the SSLG adviser or email
                            <a href="mailto:sslg@vsulhs.edu.ph"
                               class="font-medium text-emerald-600 dark:text-emerald-400 hover:underline underline-offset-2">
                                sslg@vsulhs.edu.ph
                            </a>.
                        </p>
                    </div>
                </div>

                {{-- ── Acknowledgment Banner ──────────────────────────────── --}}
                <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-emerald-600 to-emerald-700 dark:from-emerald-800 dark:to-emerald-900 p-6 border border-emerald-500 dark:border-emerald-700">
                    <div class="absolute top-0 right-0 -mt-6 -mr-6 w-32 h-32 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
                    <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-white/15 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-white mb-0.5">Acknowledgment</p>
                            <p class="text-xs text-emerald-100 leading-relaxed">
                                By using this portal, you confirm that you have read, understood, and agree to be bound by these Terms of Service and our
                                <a href="{{ route('data-privacy-act') }}"
                                   class="text-emerald-200 hover:text-white underline underline-offset-2 transition">
                                    Data Privacy Policy
                                </a>.
                            </p>
                        </div>
                        <a href="{{ route('data-privacy-act') }}"
                           class="flex-shrink-0 inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/15 hover:bg-white/25 text-white text-xs font-semibold transition border border-white/20">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            View Privacy Policy
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>

<style>
    @media print {
        aside, .absolute.top-6 { display: none !important; }
        .scroll-mt-28 { scroll-margin-top: 0 !important; }
        .shadow-xl, .shadow-sm { box-shadow: none !important; }
    }
</style>

@endsection