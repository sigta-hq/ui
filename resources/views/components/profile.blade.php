@props([
    'name' => null,
    'meta' => null,
    'src' => null,
    'reverse' => false,
])

@php
$classes = 'inline-flex items-center gap-3';

if ($reverse) {
    $classes .= ' flex-row-reverse text-right';
}
@endphp

<div data-sigta="profile" {{ $attributes->merge(['class' => $classes]) }}>
    <x-sigta::avatar :src="$src" :name="$name" aria-hidden="true" />
    <div class="flex min-w-0 flex-col">
        <span class="font-head font-medium text-[0.98rem] leading-[1.2]">{{ $name }}</span>
        @if ($meta)
            <span class="font-mono text-[0.68rem] tracking-[0.04em] text-graphite mt-0.5">{{ $meta }}</span>
        @endif
    </div>
</div>
