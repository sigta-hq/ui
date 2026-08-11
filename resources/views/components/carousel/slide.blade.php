{{-- Carousel slide. Full-width by default; pass a width utility via `basis`
     for multi-per-view layouts, e.g. basis="basis-4/5" or basis="w-1/2". --}}
@props([
    'basis' => 'w-full',
])

<div
    data-sigta="carousel.slide"
    role="group"
    aria-roledescription="slide"
    {{ $attributes->merge(['class' => 'snap-start shrink-0 '.$basis]) }}
>
    {{ $slot }}
</div>
