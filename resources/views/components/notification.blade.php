@props(['type' => 'success', 'title' => null, 'message' => null])

@php
    $config = [
        'success' => [
            'border' => 'border-green-500',
            'bg_from' => 'from-green-50',
            'bg_to' => 'to-white',
            'dark_bg_from' => 'dark:from-green-900/30',
            'dark_bg_to' => 'dark:to-gray-800',
            'icon_bg' => 'bg-green-100 dark:bg-green-900/50',
            'icon_color' => 'text-green-600 dark:text-green-400',
            'title_color' => 'text-green-800 dark:text-green-200',
            'message_color' => 'text-green-700 dark:text-green-300',
            'button_color' => 'text-green-500 hover:text-green-700 dark:text-green-400 dark:hover:text-green-300',
            'progress_color' => 'bg-green-500',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>',
            'default_title' => 'Success!'
        ],
        'error' => [
            'border' => 'border-red-500',
            'bg_from' => 'from-red-50',
            'bg_to' => 'to-white',
            'dark_bg_from' => 'dark:from-red-900/30',
            'dark_bg_to' => 'dark:to-gray-800',
            'icon_bg' => 'bg-red-100 dark:bg-red-900/50',
            'icon_color' => 'text-red-600 dark:text-red-400',
            'title_color' => 'text-red-800 dark:text-red-200',
            'message_color' => 'text-red-700 dark:text-red-300',
            'button_color' => 'text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300',
            'progress_color' => 'bg-red-500',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>',
            'default_title' => 'Error!'
        ],
        'warning' => [
            'border' => 'border-yellow-500',
            'bg_from' => 'from-yellow-50',
            'bg_to' => 'to-white',
            'dark_bg_from' => 'dark:from-yellow-900/30',
            'dark_bg_to' => 'dark:to-gray-800',
            'icon_bg' => 'bg-yellow-100 dark:bg-yellow-900/50',
            'icon_color' => 'text-yellow-600 dark:text-yellow-400',
            'title_color' => 'text-yellow-800 dark:text-yellow-200',
            'message_color' => 'text-yellow-700 dark:text-yellow-300',
            'button_color' => 'text-yellow-500 hover:text-yellow-700 dark:text-yellow-400 dark:hover:text-yellow-300',
            'progress_color' => 'bg-yellow-500',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>',
            'default_title' => 'Warning!'
        ],
        'info' => [
            'border' => 'border-blue-500',
            'bg_from' => 'from-blue-50',
            'bg_to' => 'to-white',
            'dark_bg_from' => 'dark:from-blue-900/30',
            'dark_bg_to' => 'dark:to-gray-800',
            'icon_bg' => 'bg-blue-100 dark:bg-blue-900/50',
            'icon_color' => 'text-blue-600 dark:text-blue-400',
            'title_color' => 'text-blue-800 dark:text-blue-200',
            'message_color' => 'text-blue-700 dark:text-blue-300',
            'button_color' => 'text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300',
            'progress_color' => 'bg-blue-500',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
            'default_title' => 'Information'
        ]
    ];
    
    $cfg = $config[$type];
    $title = $title ?? $cfg['default_title'];
@endphp

<div x-data="{ show: true }" 
     x-init="setTimeout(() => show = false, 5000)"
     x-show="show"
     x-transition:enter="transform transition-all duration-300 ease-out"
     x-transition:enter-start="translate-y-2 opacity-0 scale-95"
     x-transition:enter-end="translate-y-0 opacity-100 scale-100"
     x-transition:leave="transform transition-all duration-200 ease-in"
     x-transition:leave-start="translate-y-0 opacity-100 scale-100"
     x-transition:leave-end="translate-y-2 opacity-0 scale-95"
     class="rounded-xl shadow-xl overflow-hidden border-l-4 {{ $cfg['border'] }} bg-gradient-to-r {{ $cfg['bg_from'] }} {{ $cfg['bg_to'] }} {{ $cfg['dark_bg_from'] }} {{ $cfg['dark_bg_to'] }} backdrop-blur-sm">
    <div class="p-4">
        <div class="flex items-start gap-3">
            <div class="flex-shrink-0">
                <div class="w-8 h-8 rounded-full {{ $cfg['icon_bg'] }} flex items-center justify-center">
                    <svg class="w-4 h-4 {{ $cfg['icon_color'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        {!! $cfg['icon'] !!}
                    </svg>
                </div>
            </div>
            <div class="flex-1">
                <p class="text-sm font-semibold {{ $cfg['title_color'] }}">{{ $title }}</p>
                <p class="text-sm {{ $cfg['message_color'] }} mt-0.5">{{ $message }}</p>
            </div>
            <div class="flex-shrink-0">
                <button @click="show = false" class="{{ $cfg['button_color'] }} transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
    <div class="h-1 {{ $cfg['progress_color'] }} animate-progress" style="width: 100%; animation: progress-shrink 5s linear forwards;"></div>
</div>

<style>
    @keyframes progress-shrink {
        from { width: 100%; }
        to { width: 0%; }
    }
    .animate-progress {
        animation: progress-shrink 5s linear forwards;
    }
</style>