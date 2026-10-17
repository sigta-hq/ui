{{-- Ink bubble on hover/focus with a 150ms open delay. Escape dismisses. Slot is the trigger. --}}
@props([
    'text',
    'position' => 'top',
])

@php
$bubbleId = 'sigta-tooltip-'.\Illuminate\Support\Str::random(8);
@endphp

<span
    data-sigta="tooltip"
    data-position="{{ $position }}"
    x-data="{ show: false, timer: null }"
    @mouseenter="timer = setTimeout(() => show = true, 150)"
    @mouseleave="clearTimeout(timer); show = false"
    @focusin="timer = setTimeout(() => show = true, 150)"
    @focusout="clearTimeout(timer); show = false"
    @keydown.escape.window="clearTimeout(timer); show = false"
    {{ $attributes }}
>
    <span data-scope="tooltip" data-part="trigger" aria-describedby="{{ $bubbleId }}">{{ $slot }}</span>

    <span
        id="{{ $bubbleId }}"
        role="tooltip"
        x-show="show"
        x-cloak
        data-scope="tooltip"
        data-part="bubble"
    >{{ $text }}</span>
</span>
