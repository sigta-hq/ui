{{-- Page-ground overlay: dot grid + paper grain. Place once, directly inside <body>. --}}
@props([
    'dots' => true,
    'grain' => true,
])

<div aria-hidden="true" data-sigta="paper">
    @if ($dots)
        <div data-scope="paper" data-part="dots"></div>
    @endif
    @if ($grain)
        <div data-scope="paper" data-part="grain">
            <svg data-scope="paper" data-part="noise">
                <filter id="sigta-grain"><feTurbulence type="fractalNoise" baseFrequency="0.9" numOctaves="2" stitchTiles="stitch"/></filter>
                <rect width="100%" height="100%" filter="url(#sigta-grain)"/>
            </svg>
        </div>
    @endif
</div>
