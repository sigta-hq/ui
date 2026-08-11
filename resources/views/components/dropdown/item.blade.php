@props([
    'icon' => null,
    'variant' => 'default',
    'kbd' => null,
])

@php
$classes = 'w-full text-left flex items-center gap-2.5 px-3.5 py-2 font-mono text-[0.78rem] cursor-pointer focus:outline-none focus:bg-ink focus:text-surface hover:bg-ink/5 aria-disabled:opacity-40 aria-disabled:pointer-events-none '
    .($variant === 'danger' ? 'text-danger focus:bg-danger' : 'text-ink');
@endphp

@if ($attributes->has('href'))
    <a role="menuitem" tabindex="-1" data-sigta="dropdown.item" {{ $attributes->merge(['class' => $classes]) }}>
@else
    <button type="button" role="menuitem" tabindex="-1" data-sigta="dropdown.item" x-on:click="close(true)" {{ $attributes->merge(['class' => $classes]) }}>
@endif
        @if ($icon)<x-sigta::icon :name="$icon" size="sm" class="shrink-0" />@endif
        <span class="grow">{{ $slot }}</span>
        @if ($kbd)<kbd class="font-mono text-[0.62rem] text-graphite">{{ $kbd }}</kbd>@endif
@if ($attributes->has('href'))
    </a>
@else
    </button>
@endif
