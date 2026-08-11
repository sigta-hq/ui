@props([
    'variant' => 'body',
])

@php
$classes = match ($variant) {
    'lede' => 'text-[clamp(1.05rem,1.6vw,1.3rem)] text-graphite leading-[1.55]',
    'muted' => 'text-graphite',
    'caption' => 'font-mono text-[0.74rem] tracking-[0.04em] text-graphite',
    'hand' => 'font-hand font-semibold text-[1.15rem] leading-none',
    default => '',
};
@endphp

<p {{ $attributes->merge(['class' => trim($classes)]) }}>{{ $slot }}</p>
