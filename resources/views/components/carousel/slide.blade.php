{{-- Carousel slide. Full-width by default; pass a width utility via `basis`
     for multi-per-view layouts, e.g. basis="basis-4/5" or basis="w-1/2". A
     basis other than the default is your class, and the slide drops its own
     full width for it (data-basis="custom"). --}}
@props([
    'basis' => 'w-full',
])

@php
$custom = $basis !== 'w-full';
@endphp

<div
    data-sigta="carousel.slide"
    role="group"
    aria-roledescription="slide"
    @if ($custom) data-basis="custom" @endif
    {{ $custom ? $attributes->merge(['class' => $basis]) : $attributes }}
>
    {{ $slot }}
</div>
