@props([
    'variant' => 'outline',
])

@php
$classes = 'inline-flex items-center leading-none font-mono text-[0.68rem] tracking-[0.05em] px-[0.7em] py-[0.32em] rounded-full border-[1.5px]';

$classes .= ' '.match ($variant) {
    'solid' => 'bg-ink text-surface border-ink',
    'accent' => 'bg-accent border-accent-line text-ink',
    default => 'bg-transparent text-ink border-ink',
};
@endphp

<span data-sigta="badge" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</span>
