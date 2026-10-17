@props([
    'name' => null,
    'meta' => null,
    'src' => null,
    'reverse' => false,
])

<div data-sigta="profile" {{ $attributes->merge($reverse ? ['data-reverse' => ''] : []) }}>
    <x-sigta::avatar :src="$src" :name="$name" aria-hidden="true" />
    <div data-scope="profile" data-part="text">
        <span data-scope="profile" data-part="name">{{ $name }}</span>
        @if ($meta)
            <span data-scope="profile" data-part="meta">{{ $meta }}</span>
        @endif
    </div>
</div>
