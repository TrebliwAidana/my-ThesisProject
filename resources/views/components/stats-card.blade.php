@props(['title', 'value', 'bgColor' => 'bg-white'])

<div {{ $attributes->merge(['class' => "$bgColor p-5 rounded-xl shadow flex items-center gap-4"]) }}>
    {{-- Icon --}}
    <div class="flex-shrink-0 w-12 h-12 flex items-center justify-center rounded-full bg-white/20 text-2xl">
        {{ $icon ?? '' }}
    </div>

    {{-- Text Content --}}
    <div class="flex-1">
        <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">{{ $title }}</p>
        <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ $value }}</h3>
        <p class="text-xs text-gray-400 mt-1">{{ $slot }}</p>
    </div>
</div>