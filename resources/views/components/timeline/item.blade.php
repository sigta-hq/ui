@props([
    'heading' => null,
    'meta' => null,
    'variant' => 'default',
])

<li data-sigta="timeline.item" {{ $attributes->merge(['data-variant' => $variant]) }}>
    <span aria-hidden="true" data-scope="timeline.item" data-part="dot"></span>

    @if ($meta)
        <p data-scope="timeline.item" data-part="meta">{{ $meta }}</p>
    @endif

    @if ($heading)
        <h3 data-scope="timeline.item" data-part="heading">{{ $heading }}</h3>
    @endif

    @if ($slot->isNotEmpty())
        <div data-scope="timeline.item" data-part="body">{{ $slot }}</div>
    @endif
</li>
