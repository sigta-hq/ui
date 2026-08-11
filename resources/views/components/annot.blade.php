{{-- Handwritten margin annotation (Caveat). Decorative by default. --}}
@props([
    'rotate' => -6,
    'tone' => null,
    'muted' => false,
    'decorative' => true,
])

@php
// `tone` (ink | muted | accent) is the forward-looking API; the `muted`
// boolean is kept for back-compat. `accent` is for annotations on dark bands.
$tone ??= $muted ? 'muted' : 'ink';
$toneClass = match ($tone) {
    'muted' => 'text-graphite',
    'accent' => 'text-accent',
    default => 'text-ink',
};
@endphp

<span
    {{ $attributes->merge(['class' => 'font-hand font-semibold leading-none text-[1.35rem] inline-block '.$toneClass]) }}
    style="transform: rotate({{ (float) $rotate }}deg)"
    @if ($decorative) aria-hidden="true" @endif
    data-sigta="annot"
>{{ $slot }}</span>
