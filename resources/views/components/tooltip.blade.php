{{-- Ink bubble on hover/focus with a 150ms open delay. Escape dismisses. Slot is the trigger. --}}
@props([
    'text',
    'position' => 'top',
])

@php
$bubbleId = 'sigta-tooltip-'.\Illuminate\Support\Str::random(8);

$bubblePosition = match ($position) {
    'bottom' => 'top-full left-1/2 -translate-x-1/2 mt-2',
    'left' => 'right-full top-1/2 -translate-y-1/2 mr-2',
    'right' => 'left-full top-1/2 -translate-y-1/2 ml-2',
    default => 'bottom-full left-1/2 -translate-x-1/2 mb-2',
};
@endphp

<span
    data-sigta="tooltip"
    x-data="{ show: false, timer: null }"
    @mouseenter="timer = setTimeout(() => show = true, 150)"
    @mouseleave="clearTimeout(timer); show = false"
    @focusin="timer = setTimeout(() => show = true, 150)"
    @focusout="clearTimeout(timer); show = false"
    @keydown.escape.window="clearTimeout(timer); show = false"
    {{ $attributes->merge(['class' => 'relative inline-flex']) }}
>
    <span class="inline-flex" aria-describedby="{{ $bubbleId }}">{{ $slot }}</span>

    <span
        id="{{ $bubbleId }}"
        role="tooltip"
        x-show="show"
        x-cloak
        class="absolute z-20 {{ $bubblePosition }} pointer-events-none bg-ink text-surface font-mono text-[0.7rem] tracking-[0.04em] px-2 py-1 rounded-base whitespace-nowrap shadow-paper"
    >{{ $text }}</span>
</span>
