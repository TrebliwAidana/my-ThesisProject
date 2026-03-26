<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — VSULHS_SSLG</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script>tailwind.config = { theme: { extend: { fontFamily: { sans: ['Inter', 'ui-sans-serif'] } } } }</script>
</head>
<body class="bg-gray-100 font-sans min-h-screen flex items-center justify-center">

<div class="w-full max-w-md bg-white border border-gray-200 rounded-2xl shadow-sm p-8">

    <div class="text-center mb-8">
        <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">VSULHS_SSLG</h1>
        <p class="text-sm text-gray-500 mt-1">Supreme Student Leadership Government</p>
    </div>

    @if (session('success'))
        <div class="mb-4 flex items-center gap-2 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('login.post') }}">
        @csrf

        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Email Address</label>
            <input type="email" name="email" value="{{ old('email') }}" required
                   autocomplete="email" placeholder="you@vsulhs-sslg.com"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-black {{ $errors->has('email') ? 'border-red-400' : '' }}">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Password</label>
            <input type="password" name="password" required
                   autocomplete="current-password" placeholder="••••••••"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-black {{ $errors->has('password') ? 'border-red-400' : '' }}">
        </div>

        <div class="flex items-center gap-2 mb-6">
            <input type="checkbox" name="remember" id="remember" class="rounded border-gray-300">
            <label for="remember" class="text-sm text-gray-600 cursor-pointer">Remember me</label>
        </div>

        <button type="submit"
                class="w-full bg-black text-white font-semibold py-2.5 rounded-lg hover:bg-gray-800 transition text-sm">
            Sign In
        </button>
    </form>

    <p class="text-center text-xs text-gray-400 mt-6">VSULHS Supreme Student Leadership Government &copy; {{ date('Y') }}</p>

</div>

</body>
</html>
