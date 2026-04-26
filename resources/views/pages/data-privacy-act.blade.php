@extends('layouts.app')

@section('title', 'Data Privacy — VSULHS SSLG')

@section('content')
<div class="relative overflow-hidden">
    {{-- Hero Section with Back button --}}
    <div class="relative bg-gradient-to-r from-emerald-700 to-emerald-900 dark:from-emerald-900 dark:to-emerald-950 py-20 px-6 md:px-12 text-center overflow-hidden">
        {{-- Back Button --}}
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
                Legal
            </div>
            <h1 class="font-serif text-4xl md:text-5xl lg:text-6xl font-bold text-white leading-tight tracking-tight">
                Data Privacy Notice
            </h1>
            <p class="text-emerald-100 text-lg mt-4 max-w-2xl mx-auto opacity-90">
                How we collect, use, and protect your personal information under the Data Privacy Act of 2012.
            </p>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="max-w-4xl mx-auto px-4 sm:px-6 py-12 md:py-16">

        {{-- Compliance Callout --}}
        <div class="pub-callout flex items-start gap-4 bg-emerald-50 dark:bg-emerald-900/20 border-l-4 border-emerald-600 dark:border-emerald-400 rounded-r-lg p-5 mb-10">
            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 flex-shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            <p class="text-sm text-gray-700 dark:text-gray-300">
                This notice is issued in compliance with
                <a href="https://www.privacy.gov.ph/data-privacy-act/"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="inline-flex items-center gap-1 font-semibold text-emerald-700 dark:text-emerald-400 hover:underline underline-offset-2">
                    Republic Act No. 10173
                    <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                </a>,
                the Data Privacy Act of 2012, and its
                <a href="https://www.privacy.gov.ph/implementing-rules-and-regulations/"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="inline-flex items-center gap-1 font-semibold text-emerald-700 dark:text-emerald-400 hover:underline underline-offset-2">
                    Implementing Rules and Regulations
                    <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                </a>.
                Last updated: <strong>{{ date('F Y') }}</strong>.
            </p>
        </div>

        {{-- NPC reference banner --}}
        <div class="flex items-center gap-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl px-5 py-3.5 mb-10 text-sm text-blue-800 dark:text-blue-300">
            <svg class="w-4 h-4 flex-shrink-0 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>
                This portal's data practices are aligned with guidelines issued by the
                <a href="https://www.privacy.gov.ph/"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="inline-flex items-center gap-1 font-semibold text-blue-700 dark:text-blue-400 hover:underline underline-offset-2">
                    National Privacy Commission (NPC)
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                </a>
                of the Philippines.
            </span>
        </div>

        {{-- Identity --}}
        <div class="pub-section-badge inline-block px-3 py-1 text-xs font-semibold uppercase tracking-wider text-emerald-700 dark:text-emerald-400 bg-emerald-100 dark:bg-emerald-900/40 rounded-full mb-3">
            Identity
        </div>
        <h2 class="font-serif text-3xl font-bold text-gray-900 dark:text-white mb-4">Who We Are</h2>
        <p class="text-gray-700 dark:text-gray-300 mb-4">
            The <strong>VSULHS Supreme Student Learner Government (SSLG)</strong> is the official student governing body of the Visayas State University Laboratory High School, Baybay City, Leyte, Philippines. This portal is the official digital management system used by verified SSLG members to manage financial records, documents, and membership information.
        </p>
        <p class="text-gray-700 dark:text-gray-300 mb-10">
            For the purposes of the
            <a href="https://www.privacy.gov.ph/data-privacy-act/"
               target="_blank"
               rel="noopener noreferrer"
               class="inline-flex items-center gap-0.5 font-medium text-emerald-600 dark:text-emerald-400 hover:underline underline-offset-2">
                Data Privacy Act of 2012
                <svg class="w-3 h-3 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
            </a>,
            the VSULHS SSLG acts as the <strong>Personal Information Controller (PIC)</strong> for data collected through this portal.
        </p>

        {{-- Collection --}}
        <div class="pub-section-badge inline-block px-3 py-1 text-xs font-semibold uppercase tracking-wider text-emerald-700 dark:text-emerald-400 bg-emerald-100 dark:bg-emerald-900/40 rounded-full mb-3">
            Collection
        </div>
        <h2 class="font-serif text-3xl font-bold text-gray-900 dark:text-white mb-4">What Information We Collect</h2>
        <p class="text-gray-700 dark:text-gray-300 mb-4">
            We collect only the personal information necessary for operating this portal and fulfilling organizational responsibilities.
        </p>
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mt-6 mb-2">Account information</h3>
        <ul class="list-disc list-inside space-y-1 text-gray-700 dark:text-gray-300 mb-4 ml-4">
            <li>Full name</li>
            <li>Email address</li>
            <li>Password (stored in encrypted, hashed form — never in plain text)</li>
            <li>Role and position within the organization</li>
            <li>Account status (active/inactive)</li>
        </ul>
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mt-6 mb-2">Activity information</h3>
        <ul class="list-disc list-inside space-y-1 text-gray-700 dark:text-gray-300 mb-4 ml-4">
            <li>Login timestamps and session data</li>
            <li>Document uploads and downloads</li>
            <li>Financial record entries and approvals</li>
            <li>Audit log entries (actions performed within the system)</li>
        </ul>
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mt-6 mb-2">Technical information</h3>
        <ul class="list-disc list-inside space-y-1 text-gray-700 dark:text-gray-300 mb-4 ml-4">
            <li>Browser type and operating system (for session security)</li>
            <li>IP address (logged for audit and security purposes)</li>
        </ul>
        <p class="text-gray-700 dark:text-gray-300 mb-10">
            We do <strong>not</strong> collect sensitive personal information such as government IDs, biometric data, health records, or financial account details.
        </p>

        {{-- Purpose --}}
        <div class="pub-section-badge inline-block px-3 py-1 text-xs font-semibold uppercase tracking-wider text-emerald-700 dark:text-emerald-400 bg-emerald-100 dark:bg-emerald-900/40 rounded-full mb-3">
            Purpose
        </div>
        <h2 class="font-serif text-3xl font-bold text-gray-900 dark:text-white mb-4">How We Use Your Information</h2>
        <p class="text-gray-700 dark:text-gray-300 mb-4">
            Your personal information is used solely for the following purposes:
        </p>
        <div class="overflow-x-auto mb-10">
            <table class="pub-table min-w-full border-collapse text-sm" aria-label="Data usage purposes">
                <thead>
                    <tr class="border-b border-emerald-200 dark:border-emerald-800">
                        <th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Purpose</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Legal Basis</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <tr><td class="py-3 px-4">Authenticating your identity when you log in</td><td class="py-3 px-4">Contractual necessity</td></tr>
                    <tr><td class="py-3 px-4">Displaying your name and role within the portal</td><td class="py-3 px-4">Legitimate interest</td></tr>
                    <tr><td class="py-3 px-4">Maintaining audit logs of system actions</td><td class="py-3 px-4">Legal obligation / transparency</td></tr>
                    <tr><td class="py-3 px-4">Sending password reset and verification emails</td><td class="py-3 px-4">Contractual necessity</td></tr>
                    <tr><td class="py-3 px-4">Generating organizational reports and dashboards</td><td class="py-3 px-4">Legitimate interest</td></tr>
                </tbody>
            </table>
        </div>
        <p class="text-gray-700 dark:text-gray-300 mb-10">
            Your information is <strong>never sold, rented, or shared</strong> with third parties for marketing or commercial purposes.
        </p>

        {{-- Access --}}
        <div class="pub-section-badge inline-block px-3 py-1 text-xs font-semibold uppercase tracking-wider text-emerald-700 dark:text-emerald-400 bg-emerald-100 dark:bg-emerald-900/40 rounded-full mb-3">
            Access
        </div>
        <h2 class="font-serif text-3xl font-bold text-gray-900 dark:text-white mb-4">Who Can Access Your Information</h2>
        <p class="text-gray-700 dark:text-gray-300 mb-4">
            Access to personal information within this portal is strictly role-based:
        </p>
        <ul class="list-disc list-inside space-y-2 text-gray-700 dark:text-gray-300 mb-4 ml-4">
            <li><strong>System Administrator</strong> — Full access to all user records and audit logs for system management</li>
            <li><strong>Supreme Admin / Club Adviser</strong> — Access to member profiles and organizational records within their scope</li>
            <li><strong>Officers and Members</strong> — Access to their own profile and shared organizational content only</li>
            <li><strong>Guest users</strong> — No access to any personal member information</li>
        </ul>
        <p class="text-gray-700 dark:text-gray-300 mb-10">
            All access is logged in the system audit trail. Unauthorized access attempts are recorded and may be reported to the appropriate school authority.
        </p>

        {{-- Retention --}}
        <div class="pub-section-badge inline-block px-3 py-1 text-xs font-semibold uppercase tracking-wider text-emerald-700 dark:text-emerald-400 bg-emerald-100 dark:bg-emerald-900/40 rounded-full mb-3">
            Retention
        </div>
        <h2 class="font-serif text-3xl font-bold text-gray-900 dark:text-white mb-4">How Long We Keep Your Data</h2>
        <p class="text-gray-700 dark:text-gray-300 mb-4">
            We retain personal information for as long as your membership in the VSULHS SSLG is active, and for a reasonable period afterward as required for audit and accountability purposes.
        </p>
        <ul class="list-disc list-inside space-y-2 text-gray-700 dark:text-gray-300 mb-10 ml-4">
            <li><strong>Active accounts</strong> — Retained for the duration of membership</li>
            <li><strong>Deactivated accounts</strong> — Retained for up to 1 year for audit trail completeness, then permanently deleted upon request</li>
            <li><strong>Audit logs</strong> — Retained for a minimum of 2 years per good governance standards</li>
        </ul>

        {{-- Rights --}}
        <div class="pub-section-badge inline-block px-3 py-1 text-xs font-semibold uppercase tracking-wider text-emerald-700 dark:text-emerald-400 bg-emerald-100 dark:bg-emerald-900/40 rounded-full mb-3">
            Your Rights
        </div>
        <h2 class="font-serif text-3xl font-bold text-gray-900 dark:text-white mb-4">Your Rights Under RA 10173</h2>
        <p class="text-gray-700 dark:text-gray-300 mb-4">
            As a data subject under the
            <a href="https://www.privacy.gov.ph/data-privacy-act/"
               target="_blank"
               rel="noopener noreferrer"
               class="inline-flex items-center gap-0.5 font-medium text-emerald-600 dark:text-emerald-400 hover:underline underline-offset-2">
                Data Privacy Act of 2012
                <svg class="w-3 h-3 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
            </a>,
            you have the following rights:
        </p>
        <ul class="list-disc list-inside space-y-2 text-gray-700 dark:text-gray-300 mb-4 ml-4">
            <li><strong>Right to be informed</strong> — You have the right to know what personal data is being collected and how it is used</li>
            <li><strong>Right to access</strong> — You may request a copy of the personal data we hold about you</li>
            <li><strong>Right to rectification</strong> — You may request corrections to inaccurate or incomplete data</li>
            <li><strong>Right to erasure</strong> — You may request deletion of your data, subject to legal and audit retention requirements</li>
            <li><strong>Right to object</strong> — You may object to processing of your data in certain circumstances</li>
            <li><strong>Right to data portability</strong> — You may request a copy of your data in a structured, commonly used format</li>
            <li><strong>Right to damages</strong> — You may claim compensation if you suffer damage due to inaccurate, incomplete, or unlawfully processed data</li>
        </ul>
        <p class="text-gray-700 dark:text-gray-300 mb-4">
            To exercise any of these rights, contact us at
            <a href="mailto:sslg@vsulhs.edu.ph" class="font-medium text-emerald-600 dark:text-emerald-400 hover:underline underline-offset-2">sslg@vsulhs.edu.ph</a>.
        </p>
        <p class="text-gray-700 dark:text-gray-300 mb-10">
            You may also file a complaint directly with the
            <a href="https://www.privacy.gov.ph/complaints-assistance/"
               target="_blank"
               rel="noopener noreferrer"
               class="inline-flex items-center gap-0.5 font-medium text-emerald-600 dark:text-emerald-400 hover:underline underline-offset-2">
                National Privacy Commission
                <svg class="w-3 h-3 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
            </a>
            if you believe your rights as a data subject have been violated.
        </p>

        {{-- Security --}}
        <div class="pub-section-badge inline-block px-3 py-1 text-xs font-semibold uppercase tracking-wider text-emerald-700 dark:text-emerald-400 bg-emerald-100 dark:bg-emerald-900/40 rounded-full mb-3">
            Security
        </div>
        <h2 class="font-serif text-3xl font-bold text-gray-900 dark:text-white mb-4">How We Protect Your Data</h2>
        <ul class="list-disc list-inside space-y-2 text-gray-700 dark:text-gray-300 mb-10 ml-4">
            <li>All passwords are hashed using <strong>bcrypt</strong> — they are never stored in plain text</li>
            <li>All data transmissions are encrypted via <strong>HTTPS/TLS</strong></li>
            <li>Access is restricted by role-based permissions and authentication requirements</li>
            <li>Sessions are protected with CSRF tokens on all form submissions</li>
            <li>The system maintains a full audit log of all significant actions</li>
            <li>Inactive sessions are automatically terminated</li>
        </ul>

        {{-- Updates --}}
        <div class="pub-section-badge inline-block px-3 py-1 text-xs font-semibold uppercase tracking-wider text-emerald-700 dark:text-emerald-400 bg-emerald-100 dark:bg-emerald-900/40 rounded-full mb-3">
            Updates
        </div>
        <h2 class="font-serif text-3xl font-bold text-gray-900 dark:text-white mb-4">Changes to This Notice</h2>
        <p class="text-gray-700 dark:text-gray-300 mb-4">
            We may update this Data Privacy Notice from time to time to reflect changes in the system or applicable law. The date at the top of this page indicates when it was last revised. Continued use of the portal after changes are posted constitutes your acceptance of the updated notice.
        </p>
        <p class="text-gray-700 dark:text-gray-300 mb-10">
            For questions or concerns about this notice or our data practices, contact
            <a href="mailto:sslg@vsulhs.edu.ph" class="font-medium text-emerald-600 dark:text-emerald-400 hover:underline underline-offset-2">sslg@vsulhs.edu.ph</a>.
        </p>

        {{-- Official References Footer --}}
        <div class="mt-4 border-t border-gray-200 dark:border-gray-700 pt-8">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-4">Official References</p>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <a href="https://www.privacy.gov.ph/data-privacy-act/"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="flex items-start gap-3 p-4 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-emerald-400 dark:hover:border-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition group">
                    <svg class="w-5 h-5 text-emerald-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <div>
                        <p class="text-xs font-semibold text-gray-800 dark:text-gray-200 group-hover:text-emerald-700 dark:group-hover:text-emerald-400 transition">Republic Act No. 10173</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Data Privacy Act of 2012</p>
                        <p class="text-xs text-emerald-600 dark:text-emerald-500 mt-1 flex items-center gap-1">
                            privacy.gov.ph
                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                        </p>
                    </div>
                </a>

                <a href="https://www.privacy.gov.ph/implementing-rules-and-regulations/"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="flex items-start gap-3 p-4 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-emerald-400 dark:hover:border-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition group">
                    <svg class="w-5 h-5 text-emerald-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l4 4v10a2 2 0 01-2 2z"/>
                    </svg>
                    <div>
                        <p class="text-xs font-semibold text-gray-800 dark:text-gray-200 group-hover:text-emerald-700 dark:group-hover:text-emerald-400 transition">IRR of RA 10173</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Implementing Rules & Regulations</p>
                        <p class="text-xs text-emerald-600 dark:text-emerald-500 mt-1 flex items-center gap-1">
                            privacy.gov.ph
                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                        </p>
                    </div>
                </a>

                <a href="https://www.privacy.gov.ph/"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="flex items-start gap-3 p-4 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-emerald-400 dark:hover:border-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition group">
                    <svg class="w-5 h-5 text-emerald-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="text-xs font-semibold text-gray-800 dark:text-gray-200 group-hover:text-emerald-700 dark:group-hover:text-emerald-400 transition">National Privacy Commission</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Official NPC website</p>
                        <p class="text-xs text-emerald-600 dark:text-emerald-500 mt-1 flex items-center gap-1">
                            privacy.gov.ph
                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                        </p>
                    </div>
                </a>
            </div>
        </div>

    </div>
</div>

<style>
    .pub-callout {
        transition: all 0.2s ease;
    }
    .pub-table th, .pub-table td {
        border-bottom: 1px solid var(--border, #e2e8f0);
    }
    .dark .pub-table th, .dark .pub-table td {
        border-bottom-color: var(--border-dark, #334155);
    }
    .pub-table tr:last-child td {
        border-bottom: none;
    }
</style>
@endsection