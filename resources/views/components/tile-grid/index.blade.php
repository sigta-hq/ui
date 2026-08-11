{{-- Hairline-gap grid: 1px gap over a bg-line ground reads as thin dividers
     between tiles. Covers the brand .recog and .svc grids. --}}
@props([
    'cols' => 3,
])

@php
// Responsive collapse per brand: 4 → 2 → 1, 3 → 1, 2 → 1.
$colClasses = match ((int) $cols) {
    2 => 'grid-cols-1 md:grid-cols-2',
    4 => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-4',
    default => 'grid-cols-1 md:grid-cols-3',
};
@endphp

<div data-sigta="tile-grid" {{ $attributes->merge(['class' => 'grid gap-px overflow-hidden rounded-base border border-line bg-line '.$colClasses]) }}>{{ $slot }}</div>
