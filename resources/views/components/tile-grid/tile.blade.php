{{-- One cell of a tile-grid. Optional mono number on top (brand .svc/.rec .num). --}}
@props([
    'num' => null,
])

<article data-sigta="tile" {{ $attributes }}>
    @if ($num !== null)
        <span data-scope="tile" data-part="num">{{ $num }}</span>
    @endif
    {{ $slot }}
</article>
