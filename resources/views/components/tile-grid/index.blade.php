{{-- Hairline-gap grid: 1px gap over a bg-line ground reads as thin dividers
     between tiles. Covers the brand .recog and .svc grids. Columns collapse
     per brand: 4 → 2 → 1, 3 → 1, 2 → 1 (any other count reads as 3). --}}
@props([
    'cols' => 3,
])

<div data-sigta="tile-grid" {{ $attributes->merge(['data-cols' => (int) $cols]) }}>{{ $slot }}</div>
