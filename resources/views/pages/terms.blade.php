@extends('layouts.app')

@section('title', 'Terms of Service — VSULHS SSLG')

@section('content')
<div class="relative overflow-hidden">
    {{-- Hero Section with Back button --}}
    <div class="relative bg-gradient-to-r from-emerald-700 to-emerald-900 dark:from-emerald-900 dark:to-emerald-950 py-16 px-6 md:px-12 text-center overflow-hidden">

        {{-- Back Button Component --}}
        <x-back-button />

        <div class="absolute inset-0 opacity-20">
            <div class="absolute inset-0 bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:32px_32px]"></div>
        </div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[400px] bg-emerald-500/20 rounded-full blur-3xl"></div>

        <div class="relative z-10 max-w-3xl mx-auto">
            <div class="inline-block mb-4 px-4 py-1 rounded-full bg-emerald-500/20 text-emerald-100 text-xs font-semibold tracking-wide border border-emerald-400/30 backdrop-blur-sm">
                Terms of Service
            </div>
            <h1 class="font-serif text-4xl md:text-5xl lg:text-6xl font-bold text-white leading-tight tracking-tight">
                Terms of Service
            </h1>
            <p class="text-emerald-100 text-lg mt-4 max-w-2xl mx-auto opacity-90">
                Please read these terms carefully before using the VSULHS SSLG portal.
            </p>
        </div>
    </div>

    {{-- Content Card --}}
    <div class="max-w-4xl mx-auto px-4 sm:px-6 py-12 md:py-16">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-emerald-200 dark:border-emerald-800 overflow-hidden">
            <div class="p-6 md:p-8 space-y-6 text-gray-700 dark:text-gray-300">
                <div class="prose dark:prose-invert max-w-none">
                    <p class="text-sm text-gray-500 dark:text-gray-400 border-b border-emerald-200 dark:border-emerald-800 pb-3">
                        Last updated: {{ now()->format('F d, Y') }}
                    </p>

                    <h2 class="text-2xl font-serif font-semibold text-emerald-700 dark:text-emerald-400 mt-8">1. Acceptance of Terms</h2>
                    <p>By accessing or using the VSULHS Supreme Student Learner Government (SSLG) portal, you agree to be bound by these Terms of Service. If you do not agree, please do not use the portal.</p>

                    <h2 class="text-2xl font-serif font-semibold text-emerald-700 dark:text-emerald-400 mt-8">2. User Accounts</h2>
                    <ul class="list-disc pl-6 space-y-2">
                        <li><strong>Eligibility:</strong> Only verified members of VSULHS SSLG are permitted to have accounts.</li>
                        <li><strong>Accuracy:</strong> You must provide accurate, complete, and up‑to‑date information.</li>
                        <li><strong>Security:</strong> You are responsible for maintaining the confidentiality of your password and for all activities under your account.</li>
                        <li><strong>Unauthorised access:</strong> You must notify the SSLG immediately of any suspected breach.</li>
                    </ul>

                    <h2 class="text-2xl font-serif font-semibold text-emerald-700 dark:text-emerald-400 mt-8">3. Acceptable Use</h2>
                    <p>You agree not to:</p>
                    <ul class="list-disc pl-6 space-y-2">
                        <li>Use the portal for any illegal purpose or in violation of any laws.</li>
                        <li>Attempt to gain unauthorised access to other accounts or systems.</li>
                        <li>Upload malicious code, viruses, or harmful content.</li>
                        <li>Impersonate another member or officer.</li>
                        <li>Disrupt the normal operation of the portal.</li>
                        <li>Share your account credentials with others.</li>
                    </ul>

                    <h2 class="text-2xl font-serif font-semibold text-emerald-700 dark:text-emerald-400 mt-8">4. Financial Records & Documents</h2>
                    <ul class="list-disc pl-6 space-y-2">
                        <li><strong>Accuracy:</strong> All financial entries and documents uploaded must be truthful and authorised.</li>
                        <li><strong>Confidentiality:</strong> Private documents and financial data must not be shared outside the organisation.</li>
                        <li><strong>Retention:</strong> Records will be kept in accordance with the organisation's data retention policy.</li>
                    </ul>

                    <h2 class="text-2xl font-serif font-semibold text-emerald-700 dark:text-emerald-400 mt-8">5. Intellectual Property</h2>
                    <p>All content, design, logos, and code of this portal are the property of VSULHS SSLG. You may not copy, modify, or redistribute any part without written permission.</p>

                    <h2 class="text-2xl font-serif font-semibold text-emerald-700 dark:text-emerald-400 mt-8">6. Privacy & Data Protection</h2>
                    <p>Your use of the portal is also governed by our <a href="{{ route('data-privacy-act') }}" class="text-emerald-600 dark:text-emerald-400 hover:underline">Data Privacy Policy</a>, which complies with the Data Privacy Act of 2012 (Republic Act No. 10173).</p>

                    <h2 class="text-2xl font-serif font-semibold text-emerald-700 dark:text-emerald-400 mt-8">7. Termination</h2>
                    <p>The SSLG reserves the right to suspend or terminate your account if you violate these Terms. Upon termination, your access to the portal will be revoked, and any pending data may be archived or deleted in accordance with applicable data retention policies.</p>

                    <h2 class="text-2xl font-serif font-semibold text-emerald-700 dark:text-emerald-400 mt-8">8. Limitation of Liability</h2>
                    <p>The portal is provided on an "as is" and "as available" basis without warranties of any kind, whether express or implied. VSULHS SSLG, its officers, and its advisers shall not be liable for any indirect, incidental, special, or consequential damages — including but not limited to loss of data or service interruption — arising from your use of or inability to use the portal. We do not guarantee uninterrupted, timely, or error‑free operation of the system.</p>

                    <h2 class="text-2xl font-serif font-semibold text-emerald-700 dark:text-emerald-400 mt-8">9. Modifications to Terms</h2>
                    <p>We may update these Terms from time to time. Continued use of the portal after changes constitutes acceptance of the revised Terms. Significant changes will be communicated via an in-portal announcement or email notification.</p>

                    <h2 class="text-2xl font-serif font-semibold text-emerald-700 dark:text-emerald-400 mt-8">10. Governing Law</h2>
                    <p>These Terms shall be governed by and construed in accordance with the laws of the Republic of the Philippines. Any disputes arising from these Terms or your use of the portal shall be brought exclusively before the appropriate courts of Baybay City, Leyte.</p>

                    <h2 class="text-2xl font-serif font-semibold text-emerald-700 dark:text-emerald-400 mt-8">11. Contact Information</h2>
                    <p>If you have any questions about these Terms, please contact the SSLG adviser or email <a href="mailto:sslg@vsulhs.edu.ph" class="text-emerald-600 dark:text-emerald-400 hover:underline">sslg@vsulhs.edu.ph</a>.</p>

                    <div class="mt-8 p-4 bg-amber-50 dark:bg-amber-900/20 border-l-4 border-amber-500 rounded-r-lg">
                        <p class="text-sm text-amber-700 dark:text-amber-400">
                            <strong>Acknowledgment:</strong> By using this portal, you confirm that you have read, understood, and agree to be bound by these Terms of Service.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection