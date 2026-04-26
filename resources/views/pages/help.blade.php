@extends('layouts.app')

@section('title', 'Help Centre — VSULHS SSLG')

@section('content')

@php
$faqs = [
    [
        'question' => 'How do I log in to the portal?',
        'answer'   => 'You can log in using your registered email address and password. The login form is available on the landing page. If you haven\'t received your credentials, please contact your SSLG adviser.',
        'category' => 'Account',
    ],
    [
        'question' => 'I forgot my password. What should I do?',
        'answer'   => 'Click the "Forgot password?" link on the login modal. You will receive an email with instructions to reset your password. If you don\'t see the email, check your spam folder.',
        'category' => 'Account',
    ],
    [
        'question' => 'How do I add an income or expense record?',
        'answer'   => 'Go to the "Financial Records" section in the sidebar, click "Add Record", select whether it is Income or Expense, fill in the details (amount, description, date), and save. You can later view, edit, or delete entries.',
        'category' => 'Financial',
    ],
    [
        'question' => 'How can I update my profile information?',
        'answer'   => 'Click on your profile icon in the top bar, then select "Profile". You can edit your personal details, change your password, or upload an avatar.',
        'category' => 'Account',
    ],
    [
        'question' => 'Who can access the portal?',
        'answer'   => 'Only verified members of the VSULHS Supreme Student Learner Government (SSLG) are permitted to have accounts. Access is granted by the SSLG adviser or system administrator.',
        'category' => 'General',
    ],
    [
        'question' => 'How do I report a technical issue?',
        'answer'   => 'Use the "Email Support" button at the bottom of this page to reach the SSLG technical team. Please describe the issue, include screenshots if possible, and note the date and time it occurred.',
        'category' => 'Support',
    ],
    [
        'question' => 'Can I export financial records?',
        'answer'   => 'Yes. In the Financial Records section, look for the export button at the top of the table. You can download records as a CSV or PDF file for reporting purposes.',
        'category' => 'Financial',
    ],
    [
        'question' => 'How do I change my password?',
        'answer'   => 'Go to your Profile page from the top navigation, scroll to the Security section, and fill in your current password followed by your new password. Click Save to apply the changes.',
        'category' => 'Account',
    ],
    [
        'question' => 'What browsers are supported?',
        'answer'   => 'The portal works best on modern browsers: Google Chrome, Mozilla Firefox, Microsoft Edge, and Safari (latest versions). Internet Explorer is not supported.',
        'category' => 'General',
    ],
    [
        'question' => 'How are document categories managed?',
        'answer'   => 'Document categories are managed by the System Administrator under the Administration > Doc Categories menu. If you need a new category, contact your administrator.',
        'category' => 'Documents',
    ],
];
@endphp

{{--
    Single Alpine component wrapping the entire page.
    All search state lives here — both the hero search bar and
    the FAQ panel bind to the same `query` and `activeCategory`.
--}}
<div
    x-data="{
        query: '',
        activeCategory: 'All',
        openIndex: null,
        faqs: {{ json_encode(array_values($faqs)) }},

        get categories() {
            const cats = ['All', ...new Set(this.faqs.map(f => f.category))];
            return cats;
        },

        get filtered() {
            const q = this.query.trim().toLowerCase();
            return this.faqs
                .map((faq, i) => ({ ...faq, originalIndex: i }))
                .filter(faq => {
                    const matchesCategory =
                        this.activeCategory === 'All' ||
                        faq.category === this.activeCategory;

                    if (!q) return matchesCategory;

                    const haystack = (faq.question + ' ' + faq.answer).toLowerCase();
                    return matchesCategory && haystack.includes(q);
                });
        },

        get hasResults() {
            return this.filtered.length > 0;
        },

        highlight(text) {
            const q = this.query.trim();
            if (!q) return text;
            const escaped = q.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            return text.replace(
                new RegExp('(' + escaped + ')', 'gi'),
                '<mark class=\'bg-emerald-200 dark:bg-emerald-700/60 text-emerald-900 dark:text-emerald-100 rounded px-0.5\'>$1</mark>'
            );
        },

        toggle(index) {
            this.openIndex = this.openIndex === index ? null : index;
        },

        clearSearch() {
            this.query = '';
            this.activeCategory = 'All';
            this.openIndex = null;
        },
    }"
    class="relative overflow-hidden"
>

    {{-- ── Hero Section ──────────────────────────────────────────────── --}}
    <div class="relative bg-gradient-to-r from-emerald-700 to-emerald-900 dark:from-emerald-900 dark:to-emerald-950 py-20 px-6 md:px-12 text-center overflow-hidden">

        {{-- Back Button --}}
        <div class="absolute top-6 left-6 z-20">
            <a href="{{ route('landing') }}"
               class="inline-flex items-center gap-2 text-white/70 hover:text-white text-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Home
            </a>
        </div>

        <div class="absolute inset-0 opacity-20">
            <div class="absolute inset-0 bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:32px_32px]"></div>
        </div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[400px] bg-emerald-500/20 rounded-full blur-3xl"></div>

        <div class="relative z-10 max-w-3xl mx-auto">
            <div class="inline-block mb-4 px-4 py-1 rounded-full bg-emerald-500/20 text-emerald-100 text-xs font-semibold tracking-wide border border-emerald-400/30 backdrop-blur-sm">
                Help & Support
            </div>
            <h1 class="font-serif text-4xl md:text-5xl lg:text-6xl font-bold text-white leading-tight tracking-tight">
                How can we help you?
            </h1>
            <p class="text-emerald-100 text-lg mt-4 max-w-2xl mx-auto opacity-90">
                Find answers, guides, and support for using the VSULHS SSLG portal.
            </p>

            {{-- Hero Search Bar — bound to shared `query` --}}
            <div class="mt-8 max-w-lg mx-auto">
                <div class="relative">
                    <input
                        type="text"
                        placeholder="Search for help… e.g. password, financial, login"
                        x-model="query"
                        @input="openIndex = null"
                        class="w-full px-5 py-3.5 pl-12 pr-10 rounded-xl bg-white/10 backdrop-blur-sm border border-white/20 text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:bg-white/20 transition"
                    >
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    {{-- Clear button --}}
                    <button
                        x-show="query.length > 0"
                        @click="clearSearch()"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-white/50 hover:text-white transition"
                        aria-label="Clear search"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Live result count pill --}}
                <p
                    x-show="query.trim().length > 0"
                    x-transition
                    class="mt-2 text-xs text-white/60 text-center"
                >
                    <span x-text="filtered.length"></span>
                    <span x-text="filtered.length === 1 ? 'result' : 'results'"></span>
                    found for "<span x-text="query" class="italic"></span>"
                </p>
            </div>
        </div>
    </div>

    {{-- ── Main Content ──────────────────────────────────────────────── --}}
    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-12 md:py-16">

        {{-- Quick Links --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-12">
            <button
                @click="activeCategory = 'General'; query = ''; openIndex = null; $nextTick(() => $el.closest('.relative').querySelector('#faq-section').scrollIntoView({ behavior: 'smooth' }))"
                class="bg-white dark:bg-gray-800 rounded-xl border border-emerald-200 dark:border-emerald-800 p-5 text-center hover:shadow-lg hover:border-emerald-400 transition text-left group"
            >
                <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900/30 rounded-full flex items-center justify-center mx-auto mb-3 group-hover:bg-emerald-200 dark:group-hover:bg-emerald-800/50 transition">
                    <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-900 dark:text-white text-center">Getting Started</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 text-center">New to the portal? Learn the basics.</p>
            </button>

            <button
                @click="activeCategory = 'Financial'; query = ''; openIndex = null; $nextTick(() => $el.closest('.relative').querySelector('#faq-section').scrollIntoView({ behavior: 'smooth' }))"
                class="bg-white dark:bg-gray-800 rounded-xl border border-emerald-200 dark:border-emerald-800 p-5 text-center hover:shadow-lg hover:border-emerald-400 transition group"
            >
                <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900/30 rounded-full flex items-center justify-center mx-auto mb-3 group-hover:bg-emerald-200 dark:group-hover:bg-emerald-800/50 transition">
                    <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-900 dark:text-white">Financial Records</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">How to add income and expense entries.</p>
            </button>

            <a href="mailto:sslg@vsulhs.edu.ph"
               class="bg-white dark:bg-gray-800 rounded-xl border border-emerald-200 dark:border-emerald-800 p-5 text-center hover:shadow-lg hover:border-emerald-400 transition group">
                <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900/30 rounded-full flex items-center justify-center mx-auto mb-3 group-hover:bg-emerald-200 dark:group-hover:bg-emerald-800/50 transition">
                    <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-900 dark:text-white">Contact Support</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Get in touch with the SSLG team.</p>
            </a>
        </div>

        {{-- ── FAQ Section ──────────────────────────────────────────── --}}
        <div
            id="faq-section"
            class="bg-white dark:bg-gray-800 rounded-2xl border border-emerald-200 dark:border-emerald-800 overflow-hidden shadow-sm"
        >
            {{-- FAQ Header with inline search + category filters --}}
            <div class="px-6 py-4 bg-emerald-50 dark:bg-emerald-900/20 border-b border-emerald-200 dark:border-emerald-800">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-serif font-semibold text-emerald-800 dark:text-emerald-300">
                            Frequently Asked Questions
                        </h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-0.5">
                            <span x-text="filtered.length"></span> of {{ count($faqs) }} questions shown
                        </p>
                    </div>

                    {{-- Inline search --}}
                    <div class="relative w-full sm:w-56">
                        <input
                            type="text"
                            placeholder="Filter questions…"
                            x-model="query"
                            @input="openIndex = null"
                            class="w-full px-3 py-1.5 pl-8 pr-7 text-sm rounded-lg border border-emerald-200 dark:border-emerald-700 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-400 transition"
                        >
                        <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <button
                            x-show="query.length > 0"
                            @click="query = ''; openIndex = null"
                            class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition"
                            aria-label="Clear"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Category filter pills --}}
                <div class="flex flex-wrap gap-2 mt-3">
                    <template x-for="cat in categories" :key="cat">
                        <button
                            @click="activeCategory = cat; openIndex = null"
                            :class="activeCategory === cat
                                ? 'bg-emerald-600 text-white border-emerald-600'
                                : 'bg-white dark:bg-gray-700 text-gray-600 dark:text-gray-300 border-gray-200 dark:border-gray-600 hover:border-emerald-400 hover:text-emerald-600 dark:hover:text-emerald-400'"
                            class="px-3 py-1 text-xs font-medium rounded-full border transition"
                            x-text="cat"
                        ></button>
                    </template>
                </div>
            </div>

            {{-- FAQ Items --}}
            <div class="divide-y divide-gray-100 dark:divide-gray-700">

                {{-- Results --}}
                <template x-for="(faq, i) in filtered" :key="faq.originalIndex">
                    <div class="group">
                        <button
                            @click="toggle(faq.originalIndex)"
                            class="flex justify-between items-start w-full text-left gap-4 px-6 py-4 hover:bg-emerald-50/50 dark:hover:bg-emerald-900/10 transition"
                            :aria-expanded="openIndex === faq.originalIndex"
                        >
                            <div class="flex-1 min-w-0">
                                {{-- Category badge --}}
                                <span class="inline-block text-xs font-medium px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400 mb-1.5"
                                      x-text="faq.category"></span>
                                <p class="font-medium text-gray-900 dark:text-white text-sm leading-snug"
                                   x-html="highlight(faq.question)"></p>
                            </div>
                            <div class="flex-shrink-0 mt-1">
                                <svg class="w-4 h-4 text-emerald-500 transition-transform duration-200"
                                     :class="openIndex === faq.originalIndex ? 'rotate-180' : ''"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </button>

                        {{-- Answer --}}
                        <div
                            x-show="openIndex === faq.originalIndex"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 -translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0"
                            class="px-6 pb-4"
                        >
                            <div class="pl-0 pt-1 border-l-2 border-emerald-300 dark:border-emerald-700 pl-3">
                                <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed"
                                   x-html="highlight(faq.answer)"></p>
                            </div>
                        </div>
                    </div>
                </template>

                {{-- Empty state --}}
                <div
                    x-show="!hasResults"
                    x-transition
                    class="py-16 px-6 text-center"
                >
                    <div class="w-14 h-14 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">No results found</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-500 mb-4">
                        No questions match
                        "<span class="italic font-medium text-gray-700 dark:text-gray-300" x-text="query"></span>"
                        <template x-if="activeCategory !== 'All'">
                            <span> in <span class="font-medium" x-text="activeCategory"></span></span>
                        </template>
                    </p>
                    <div class="flex flex-wrap justify-center gap-2">
                        <button
                            x-show="query.trim().length > 0"
                            @click="query = ''"
                            class="text-xs px-3 py-1.5 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 hover:bg-emerald-200 dark:hover:bg-emerald-800/50 transition"
                        >
                            Clear search
                        </button>
                        <button
                            x-show="activeCategory !== 'All'"
                            @click="activeCategory = 'All'"
                            class="text-xs px-3 py-1.5 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition"
                        >
                            Show all categories
                        </button>
                        <a href="mailto:sslg@vsulhs.edu.ph"
                           class="text-xs px-3 py-1.5 rounded-lg border border-emerald-300 dark:border-emerald-700 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 transition">
                            Contact support
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Contact Section ───────────────────────────────────────── --}}
        <div class="mt-12 bg-gradient-to-r from-emerald-50 to-teal-50 dark:from-emerald-900/20 dark:to-teal-900/20 rounded-2xl p-6 border border-emerald-200 dark:border-emerald-800">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex-1 text-center md:text-left">
                    <h3 class="text-lg font-serif font-semibold text-emerald-800 dark:text-emerald-300">Still need help?</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Our SSLG support team is ready to assist you.</p>
                </div>
                <div class="flex gap-4">
                    <a href="mailto:sslg@vsulhs.edu.ph"
                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-xl transition shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Email Support
                    </a>
                    <a href="{{ route('landing') }}#contact"
                       class="inline-flex items-center gap-2 px-5 py-2.5 border border-emerald-300 dark:border-emerald-700 text-emerald-700 dark:text-emerald-300 text-sm font-medium rounded-xl hover:bg-emerald-50 dark:hover:bg-emerald-900/30 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Contact Officer
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection