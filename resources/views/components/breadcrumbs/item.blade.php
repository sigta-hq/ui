{{-- One crumb. Link when href given; current page (no href or current) is ink + aria-current.
     Attributes land on the link (or the current-page span), not the <li>. --}}
@props([
    'href' => null,
    'current' => false,
])

@php
$isCurrent = $current || $href === null;
@endphp

<li data-sigta="breadcrumbs.item">
    @if ($isCurrent)
        <span aria-current="page" data-scope="breadcrumbs.item" data-part="current" {{ $attributes }}>{{ $slot }}</span>
    @else
        <a href="{{ $href }}" data-scope="breadcrumbs.item" data-part="link" {{ $attributes }}>{{ $slot }}</a>
    @endif
</li>
