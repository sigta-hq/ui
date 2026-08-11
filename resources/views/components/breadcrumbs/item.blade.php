{{-- One crumb. Link when href given; current page (no href or current) is ink + aria-current. --}}
@props([
    'href' => null,
    'current' => false,
])

@php
$isCurrent = $current || $href === null;
@endphp

<li
    data-sigta="breadcrumbs.item"
    class="flex items-center gap-[0.6em] before:content-['/'] before:text-graphite first:before:hidden"
>
    @if ($isCurrent)
        <span aria-current="page" {{ $attributes->merge(['class' => 'text-ink']) }}>{{ $slot }}</span>
    @else
        <a href="{{ $href }}" {{ $attributes->merge(['class' => 'text-graphite no-underline hover:text-ink transition-colors duration-200 ease-out']) }}>{{ $slot }}</a>
    @endif
</li>
