{{-- Page-ground overlay: dot grid + paper grain. Place once, directly inside <body>. --}}
@props([
    'dots' => true,
    'grain' => true,
])

<div aria-hidden="true" class="pointer-events-none fixed inset-0 z-0" data-sigta="paper">
    @if ($dots)
        <div class="absolute inset-0 opacity-55 [background-image:radial-gradient(var(--sigta-line)_1px,transparent_1.4px)] [background-size:26px_26px] [background-position:-13px_-13px]"></div>
    @endif
    @if ($grain)
        <div class="absolute inset-0 opacity-5 mix-blend-multiply">
            <svg class="w-full h-full">
                <filter id="sigta-grain"><feTurbulence type="fractalNoise" baseFrequency="0.9" numOctaves="2" stitchTiles="stitch"/></filter>
                <rect width="100%" height="100%" filter="url(#sigta-grain)"/>
            </svg>
        </div>
    @endif
</div>
