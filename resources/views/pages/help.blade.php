@extends('layouts.app')

@section('title', 'Help Centre — VSULHS SSLG')

@section('content')
<div class="relative overflow-hidden">
    {{-- Hero Section with Back button and Search --}}
    <div class="relative bg-gradient-to-r from-emerald-700 to-emerald-900 dark:from-emerald-900 dark:to-emerald-950 py-20 px-6 md:px-12 text-center overflow-hidden">
        {{-- Back Button (always goes to landing) --}}
        <div class="absolute top-6 left-6 z-20">
            <a href="{{ route('landing') }}" class="inline-flex items-center gap-2 text-white/70 hover:text-white text-sm transition">
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

            {{-- Search bar — filters FAQ items via Alpine.js --}}
            <div class="mt-8 max-w-lg mx-auto" x-data="faqSearch()">
                <div class="relative">
                    <input
                        type="text"
                        placeholder="Search for help..."
                        x-model="query"
                        @input="filterFaqs()"
                        class="w-full px-5 py-3 pl-12 rounded-xl bg-white/10 backdrop-blur-sm border border-white/20 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-emerald-400"
                    >
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-12 md:py-16">

        {{-- Quick Links (Cards) --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-12">
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-emerald-200 dark:border-emerald-800 p-5 text-center hover:shadow-lg transition">
                <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900/30 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-900 dark:text-white">Getting Started</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">New to the portal? Learn the basics.</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-emerald-200 dark:border-emerald-800 p-5 text-center hover:shadow-lg transition">
                <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900/30 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-900 dark:text-white">Financial Records</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">How to add income and expense entries.</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-emerald-200 dark:border-emerald-800 p-5 text-center hover:shadow-lg transition">
                <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900/30 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-900 dark:text-white">Contact Support</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Get in touch with the SSLG team.</p>
            </div>
        </div>

        {{-- FAQ Section — driven by Alpine.js with search filtering --}}
        <div
            x-data="faqSearch()"
            class="bg-white dark:bg-gray-800 rounded-2xl border border-emerald-200 dark:border-emerald-800 overflow-hidden shadow-sm"
        >
            <div class="px-6 py-4 bg-emerald-50 dark:bg-emerald-900/20 border-b border-emerald-200 dark:border-emerald-800 flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl font-serif font-semibold text-emerald-800 dark:text-emerald-300">Frequently Asked Questions</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Common questions about using the portal.</p>
                </div>
                {{-- Inline search for the FAQ panel --}}
                <div class="relative hidden sm:block w-56">
                    <input
                        type="text"
                        placeholder="Filter questions…"
                        x-model="query"
                        class="w-full px-3 py-1.5 pl-8 text-sm rounded-lg border border-emerald-200 dark:border-emerald-700 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-400"
                    >
                    <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>

            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @php
                    $faqs = [
                        [
                            'question' => 'How do I log in to the portal?',
                            'answer'   => 'You can log in using your registered email address and password. The login form is available on the landing page. If you haven\'t received your credentials, please contact your SSLG adviser.',
                        ],
                        [
                            'question' => 'I forgot my password. What should I do?',
                            'answer'   => 'Click the "Forgot password?" link on the login modal. You will receive an email with instructions to reset your password. If you don\'t see the email, check your spam folder.',
                        ],
                        [
                            'question' => 'How do I add an income or expense record?',
                            'answer'   => 'Go to the "Financial Records" section in the sidebar, click "Add Record", select whether it is Income or Expense, fill in the details (amount, description, date), and save. You can later view, edit, or delete entries.',
                        ],
                        [
                            'question' => 'How can I update my profile information?',
                            'answer'   => 'Click on your profile icon in the top bar, then select "Profile". You can edit your personal details, change your password, or upload an avatar.',
                        ],
                        [
                            'question' => 'Who can access the portal?',
                            'answer'   => 'Only verified members of the VSULHS Supreme Student Learner Government (SSLG) are permitted to have accounts. Access is granted by the SSLG adviser or system administrator.',
                        ],
                        [
                            'question' => 'How do I report a technical issue?',
                            'answer'   => 'Use the "Email Support" button at the bottom of this page to reach the SSLG technical team. Please describe the issue, include screenshots if possible, and note the date and time it occurred.',
                        ],
                    ];
                @endphp

                @foreach($faqs as $index => $faq)
                    <div
                        x-show="matches({{ $index }})"
                        x-transition
                        x-data="{ open: false }"
                        class="p-5"
                        data-question="{{ strtolower($faq['question']) }} {{ strtolower($faq['answer']) }}"
                    >
                        <button
                            @click="open = !open"
                            class="flex justify-between items-center w-full text-left gap-4"
                            :aria-expanded="open"
                        >
                            <span class="font-medium text-gray-900 dark:text-white">{{ $faq['question'] }}</span>
                            <svg class="w-5 h-5 text-gray-500 flex-shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open" x-collapse class="mt-3 text-sm text-gray-600 dark:text-gray-400">
                            <p>{{ $faq['answer'] }}</p>
                        </div>
                    </div>
                @endforeach

                {{-- Empty state when no FAQs match the search --}}
                <div x-show="noResults()" class="p-8 text-center text-sm text-gray-400 dark:text-gray-500">
                    No questions match your search. Try different keywords or
                    <a href="mailto:sslg@vsulhs.edu.ph" class="text-emerald-600 dark:text-emerald-400 hover:underline">contact us directly</a>.
                </div>
            </div>
        </div>

        {{-- Contact Section --}}
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

@push('scripts')
<script>
    /**
     * faqSearch — Alpine.js data function for FAQ filtering.
     * Used by both the hero search bar and the inline FAQ filter.
     * Items are matched against their data-question attribute.
     */
    function faqSearch() {
        return {
            query: '',
            items: [],

            init() {
                this.items = Array.from(
                    this.$el.querySelectorAll('[data-question]')
                );
            },

            matches(index) {
                if (!this.query.trim()) return true;
                const q = this.query.toLowerCase();
                const el = this.$el.querySelectorAll('[data-question]')[index];
                return el ? el.dataset.question.includes(q) : true;
            },

            noResults() {
                if (!this.query.trim()) return false;
                const q = this.query.toLowerCase();
                return Array.from(
                    this.$el.querySelectorAll('[data-question]')
                ).every(el => !el.dataset.question.includes(q));
            },
        };
    }
</script>
@endpush
@endsection