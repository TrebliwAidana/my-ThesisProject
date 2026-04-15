@extends('layouts.app')

@section('title', 'Data Privacy Act of 2012 (Philippines)')

@section('content')
<div class="relative overflow-hidden">
    {{-- Hero Section with Back button --}}
    <div class="relative bg-gradient-to-r from-emerald-700 to-emerald-900 dark:from-emerald-900 dark:to-emerald-950 py-20 px-6 md:px-12 text-center overflow-hidden">
        {{-- Back button – now a button, not a link --}}
        <div class="absolute top-6 left-6 z-20">
            <button onclick="if (document.referrer && document.referrer !== window.location.href) { window.history.back(); } else { window.location.href = '{{ route('landing') }}'; }" 
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/10 backdrop-blur-sm text-white text-sm font-medium hover:bg-white/20 transition-all duration-200 border border-white/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back
            </button>
        </div>

        <div class="absolute inset-0 opacity-20">
            <div class="absolute inset-0 bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:32px_32px]"></div>
        </div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[400px] bg-emerald-500/20 rounded-full blur-3xl"></div>
        <div class="relative z-10 max-w-3xl mx-auto">
            <div class="inline-block mb-4 px-4 py-1 rounded-full bg-emerald-500/20 text-emerald-100 text-xs font-semibold tracking-wide border border-emerald-400/30 backdrop-blur-sm">
                Republic Act No. 10173
            </div>
            <h1 class="font-serif text-4xl md:text-5xl lg:text-6xl font-bold text-white leading-tight tracking-tight">
                Data Privacy Act of 2012
            </h1>
            <p class="text-emerald-100 text-lg mt-4 max-w-2xl mx-auto opacity-90">
                Protecting your personal information in the digital age — transparency, security, and your rights.
            </p>
        </div>
    </div>

    {{-- Content Card --}}
    <div class="max-w-4xl mx-auto px-4 sm:px-6 py-12 md:py-16">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gold-200 dark:border-gold-800 overflow-hidden">
            <div class="p-6 md:p-8 space-y-6 text-gray-700 dark:text-gray-300">
                <div class="prose dark:prose-invert max-w-none">
                    <p class="text-sm text-gray-500 dark:text-gray-400 border-b border-gold-200 dark:border-gold-800 pb-3">
                        Last updated: {{ now()->format('F d, Y') }}
                    </p>

                    <h2 class="text-2xl font-serif font-semibold text-emerald-700 dark:text-emerald-400 mt-8">Overview</h2>
                    <p>The Data Privacy Act of 2012 (Republic Act No. 10173) is the Philippines' comprehensive privacy law that protects personal information in both government and private sector. It establishes the National Privacy Commission (NPC) and sets rules for collection, processing, storage, and sharing of personal data.</p>

                    <h2 class="text-2xl font-serif font-semibold text-emerald-700 dark:text-emerald-400 mt-8">Key Principles</h2>
                    <ul class="list-disc pl-6 space-y-2">
                        <li><strong>Transparency</strong> – Data subjects must be informed of the purpose and extent of data collection.</li>
                        <li><strong>Legitimate Purpose</strong> – Data must be processed only for declared, specified, and legitimate purposes.</li>
                        <li><strong>Proportionality</strong> – Data collected must be relevant and not excessive for the intended purpose.</li>
                    </ul>

                    <h2 class="text-2xl font-serif font-semibold text-emerald-700 dark:text-emerald-400 mt-8">How This Portal Complies</h2>
                    <ul class="list-disc pl-6 space-y-2">
                        <li><strong>Limited collection</strong> – We only collect information necessary for student government operations (name, email, student ID, role, position).</li>
                        <li><strong>Secure storage</strong> – All personal data is encrypted and access is restricted to authorised personnel only.</li>
                        <li><strong>No unauthorised sharing</strong> – Personal data is never sold or shared with third parties without explicit consent.</li>
                        <li><strong>User rights</strong> – Members may request access, correction, or deletion of their personal information.</li>
                    </ul>

                    <h2 class="text-2xl font-serif font-semibold text-emerald-700 dark:text-emerald-400 mt-8">Your Rights as a Data Subject</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-2">
                        @foreach([
                            'Right to be informed',
                            'Right to access',
                            'Right to object',
                            'Right to erasure or blocking',
                            'Right to rectify',
                            'Right to data portability',
                            'Right to damages'
                        ] as $right)
                            <div class="flex items-center gap-2 text-sm">
                                <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>{{ $right }}</span>
                            </div>
                        @endforeach
                    </div>

                    <h2 class="text-2xl font-serif font-semibold text-emerald-700 dark:text-emerald-400 mt-8">Contact the Data Protection Officer (DPO)</h2>
                    <p>For any privacy concerns, inquiries, or to exercise your rights, please contact:</p>
                    <div class="bg-gray-50 dark:bg-gray-700/50 p-5 rounded-xl border-l-4 border-emerald-500 mt-2">
                        <p class="font-semibold">Data Protection Officer</p>
                        <p class="text-sm mt-1">VSULHS Supreme Student Learner Government<br>Baybay City, Leyte</p>
                        <p class="text-sm mt-1">
                            Email: <a href="mailto:privacy@vsulhs-sslg.edu.ph" class="text-emerald-600 dark:text-emerald-400 hover:underline">privacy@vsulhs-sslg.edu.ph</a>
                        </p>
                    </div>

                    <div class="mt-8 p-5 bg-amber-50 dark:bg-amber-900/20 border-l-4 border-amber-500 rounded-r-xl">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-sm text-amber-700 dark:text-amber-400">
                                <strong>Note:</strong> This portal is exclusively for official VSULHS SSLG business. By using this system, you acknowledge that your personal information will be processed in accordance with the Data Privacy Act of 2012.
                            </p>
                        </div>
                    </div>

                    <p class="text-xs text-gray-400 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                        For the full text of RA 10173, please visit the 
                        <a href="https://www.privacy.gov.ph" target="_blank" rel="noopener noreferrer" class="text-emerald-600 hover:underline">National Privacy Commission website</a>.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection