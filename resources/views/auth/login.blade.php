<!DOCTYPE html>
<html lang="en" :class="$store.theme.dark ? 'dark' : ''" x-data>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login — VSULHS_SSLG</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'ui-sans-serif'] },
                    colors: {
                        primary: {
                            50: '#ecfdf5', 100: '#d1fae5', 200: '#a7f3d0', 300: '#6ee7b7',
                            400: '#34d399', 500: '#10b981', 600: '#059669', 700: '#047857',
                            800: '#065f46', 900: '#064e3b',
                        },
                        gold: {
                            50: '#fefce8', 100: '#fef9c3', 200: '#fef08a', 300: '#fde047',
                            400: '#facc15', 500: '#eab308', 600: '#ca8a04', 700: '#a16207',
                            800: '#854d0e', 900: '#713f12',
                        },
                    },
                },
            },
        }
    </script>
    <style>
        .password-toggle-btn { transition: opacity 0.2s ease; }
        .password-toggle-btn:hover { opacity: 0.8; }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in { animation: fadeInUp 0.5s cubic-bezier(0.4, 0, 0.2, 1) forwards; }
        @keyframes spin { to { transform: rotate(360deg); } }
        .animate-spin { animation: spin 0.6s linear infinite; }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-900 dark:to-gray-800 font-sans min-h-screen flex items-center justify-center p-4">

<div x-data="loginForm()" class="w-full max-w-md animate-fade-in">
    
    {{-- Main Card --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gold-200 dark:border-gold-800 overflow-hidden">
        
        {{-- Header with Gradient --}}
        <div class="bg-gradient-to-r from-primary-600 to-primary-700 px-8 py-6 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-white/20 rounded-2xl mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
            </div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight">VSULHS_SSLG</h1>
            <p class="text-primary-200 text-sm mt-1">Supreme Student Learner Government</p>
        </div>
        
        {{-- Form Content --}}
        <div class="p-8">
            
            {{-- Flash Messages --}}
            @if (session('success'))
                <div class="mb-4 flex items-center gap-2 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-300 px-4 py-3 rounded-lg text-sm">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 flex items-start gap-2 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-300 px-4 py-3 rounded-lg text-sm">
                    <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div><span class="font-semibold">Error:</span> {{ $errors->first() }}</div>
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" @submit="submitForm" class="space-y-5">
                @csrf

                {{-- Email Field --}}
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Email Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                            </svg>
                        </div>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required
                               autocomplete="email" placeholder="username@gmail.com"
                               class="w-full pl-10 pr-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-transparent transition {{ $errors->has('email') ? 'border-red-400' : '' }}">
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Use your Gmail account</p>
                </div>

                {{-- Password Field --}}
                <div>
                    <label for="password-field" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6-4h12a2 2 0 002-2v-4a2 2 0 00-2-2H6a2 2 0 00-2 2v4a2 2 0 002 2zm10-2V9a2 2 0 00-2-2H8a2 2 0 00-2 2v4"></path>
                            </svg>
                        </div>
                        <input :type="showPassword ? 'text' : 'password'" 
                               name="password" 
                               id="password-field" 
                               required
                               autocomplete="current-password" 
                               placeholder="••••••••"
                               class="w-full pl-10 pr-10 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-transparent transition {{ $errors->has('password') ? 'border-red-400' : '' }}">
                        <button type="button" 
                                @click="togglePassword"
                                class="password-toggle-btn absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 focus:outline-none"
                                aria-label="Toggle password visibility">
                            <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z" />
                            </svg>
                            <svg x-show="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 0 1-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Remember Me & Forgot Password --}}
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" id="remember" x-model="rememberMe"
                               class="rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-gold-500 transition">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Remember me on this device</span>
                    </label>
                    <a href="{{ route('password.request') }}" class="text-sm text-primary-600 hover:text-primary-800 dark:text-primary-400 transition">Forgot Password?</a>
                </div>

                {{-- Submit Button with Loading State (no pseudo-element) --}}
                <button type="submit" 
                        :class="loading ? 'bg-primary-500' : 'bg-primary-600 hover:bg-gold-500'"
                        class="w-full text-white font-semibold py-2.5 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-[1.02] active:scale-[0.98] text-sm"
                        :disabled="loading">
                    <span x-show="!loading">Sign In</span>
                    <span x-show="loading" class="flex items-center justify-center gap-2">
                        <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Signing in...
                    </span>
                </button>
            </form>

            {{-- Security Note --}}
            <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gold-800">
                <div class="flex items-start gap-2 text-xs text-gray-500 dark:text-gray-400">
                    <svg class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6-4h12a2 2 0 002-2v-4a2 2 0 00-2-2H6a2 2 0 00-2 2v4a2 2 0 002 2zm10-2V9a2 2 0 00-2-2H8a2 2 0 00-2 2v4"></path>
                    </svg>
                    <span>Only check "Remember me" on your personal device. Always logout on shared computers.</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="text-center mt-6">
        <p class="text-xs text-gray-500 dark:text-gray-400">VSULHS Supreme Student Learner Government &copy; {{ date('Y') }}</p>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Version 2.0 | Secure Login System</p>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.store('theme', {
            dark: localStorage.getItem('dark') === 'true',
            toggle() {
                this.dark = !this.dark;
                localStorage.setItem('dark', this.dark);
                document.documentElement.classList.toggle('dark', this.dark);
            },
            init() {
                document.documentElement.classList.toggle('dark', this.dark);
            },
        });
        Alpine.store('theme').init();
    });

    function loginForm() {
        return {
            showPassword: false,
            rememberMe: {{ old('remember') ? 'true' : 'false' }},
            loading: false,
            togglePassword() {
                this.showPassword = !this.showPassword;
            },
            submitForm(e) {
                this.loading = true;
                // The form submits normally; if validation fails, the page reloads and loading resets.
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const flashMessages = document.querySelectorAll('.bg-green-50, .bg-red-50');
        flashMessages.forEach(message => {
            setTimeout(() => {
                message.style.opacity = '0';
                message.style.transition = 'opacity 0.3s ease-out';
                setTimeout(() => {
                    if (message.parentElement) message.remove();
                }, 300);
            }, 5000);
        });
        const emailField = document.getElementById('email');
        if (emailField && !emailField.value) emailField.focus();
    });
</script>
</body>
</html>