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
@endphp

<span
    {{ $attributes->merge(['data-tone' => $tone]) }}
    style="transform: rotate({{ (float) $rotate }}deg)"
    @if ($decorative) aria-hidden="true" @endif
    data-sigta="annot"
>{{ $slot }}</span>
