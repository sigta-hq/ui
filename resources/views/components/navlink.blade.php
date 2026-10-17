{{-- Brand .nav-links a: mono link, graphite → ink, underline grows left → right on hover. --}}
@props([
    'current' => false,
])

<a
    data-sigta="navlink"
    @if ($current) aria-current="page" @endif
    {{ $attributes }}
>{{ $slot }}</a>
