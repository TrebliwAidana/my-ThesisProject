<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email - VSULHS_SSLG</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8">
        <div class="text-center">
            <div class="mx-auto h-12 w-12 text-yellow-500">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
            </div>
            <h2 class="mt-4 text-2xl font-bold text-gray-900">Verify Your Email</h2>
            <p class="mt-2 text-gray-600">Please verify your email address to continue.</p>
        </div>

        @if(session('warning'))
            <div class="mt-4 p-3 bg-yellow-100 border border-yellow-400 text-yellow-700 rounded">
                {{ session('warning') }}
            </div>
        @endif

        @if(session('success'))
            <div class="mt-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <p class="text-sm text-yellow-800">
                A verification link has been sent to <strong>{{ session('verification_email', 'your email address') }}</strong>.
                Please check your inbox and click the link to verify your account.
            </p>
        </div>

        <form method="POST" action="{{ route('verification.resend') }}" class="mt-6">
            @csrf
            <input type="hidden" name="email" value="{{ session('verification_email') }}">
            <button type="submit" 
                    class="w-full bg-primary-600 hover:bg-gold-500 text-white font-semibold py-2 rounded-lg transition">
                Resend Verification Email
            </button>
        </form>

        <div class="mt-4 text-center">
            <a href="{{ route('login') }}" class="text-sm text-gray-500 hover:text-gray-700">
                Back to Login
            </a>
        </div>
    </div>
</body>
</html>