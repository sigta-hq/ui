{{-- Bordered surface like Card, but WITHOUT overflow-hidden so overhanging
     annotations / badges show. Static (no hover lift). Brand .price-panel. --}}
<div data-sigta="panel" {{ $attributes->merge(['class' => 'relative flex flex-col bg-surface-raised border border-line rounded-panel p-[clamp(24px,3vw,40px)]']) }}>{{ $slot }}</div>
