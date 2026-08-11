@props([
    'variant' => 'neutral',
])

@php
$classes = 'inline-flex items-center gap-[0.45em] font-mono text-[0.68rem] px-[0.7em] py-[0.32em] rounded-full';

$classes .= ' '.match ($variant) {
    'success' => 'bg-success/10 text-success',
    'danger' => 'bg-danger/10 text-danger',
    default => 'bg-graphite/10 text-graphite',
};
@endphp

<span data-sigta="pill" {{ $attributes->merge(['class' => $classes]) }}>
    <span class="size-[7px] rounded-full bg-current shrink-0" aria-hidden="true"></span>{{ $slot }}
</span>
