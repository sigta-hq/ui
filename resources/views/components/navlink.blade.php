{{-- Brand .nav-links a: mono link, graphite → ink, underline grows left → right on hover. --}}
@props([
    'current' => false,
])

@php
$classes = 'relative inline-block font-mono text-[0.8rem] tracking-[0.05em] no-underline py-[0.2rem] transition-colors duration-200 ease-out'
    ." after:content-[''] after:absolute after:left-0 after:-bottom-[2px] after:h-[1.5px] after:bg-ink after:transition-[right] after:duration-[220ms] after:ease-out";

$classes .= ' '.($current
    ? 'text-ink after:right-0'
    : 'text-graphite hover:text-ink after:right-full hover:after:right-0');
@endphp

<a
    data-sigta="navlink"
    @if ($current) aria-current="page" @endif
    {{ $attributes->merge(['class' => $classes]) }}
>{{ $slot }}</a>
