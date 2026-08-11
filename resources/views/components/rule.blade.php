{{-- Hand-drawn horizontal divider — the brand's squiggle rule. --}}
@props([
    'variant' => 1,
])

@php
$paths = [
    1 => 'M0 8 C140 4 260 11 420 7 S720 3 880 9 1120 6 1180 8',
    2 => 'M0 7 C160 11 280 4 460 9 S760 12 920 6 1100 9 1180 7',
    3 => 'M0 8 C200 4 300 11 480 7 S780 3 940 9 1120 6 1180 8',
];
$d = $paths[((int) $variant - 1) % 3 + 1] ?? $paths[1];
@endphp

<div {{ $attributes->merge(['class' => 'block w-full h-3.5 text-line']) }} aria-hidden="true" data-sigta="rule">
    <svg class="w-full h-full" viewBox="0 0 1180 14" preserveAspectRatio="none" fill="none">
        <path d="{{ $d }}" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
    </svg>
</div>
