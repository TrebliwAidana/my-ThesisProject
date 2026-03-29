@props(['title' => null])

<div x-data="{ show: false }" 
     x-init="setTimeout(() => show = true, 10)"
     class="page-transition"
     :class="show ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-5'"
     style="transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);">
    
    @if($title)
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $title }}</h1>
            @if(isset($subtitle))
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $subtitle }}</p>
            @endif
        </div>
    @endif
    
    {{ $slot }}
</div>