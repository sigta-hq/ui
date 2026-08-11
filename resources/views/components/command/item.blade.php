@props([
    'icon' => null,
    'kbd' => null,
    'value' => null,
])

@php
$id = 'sigta-command-'.\Illuminate\Support\Str::random(8);
@endphp

@if ($attributes->has('href'))
    <a
@else
    <button type="button"
@endif
    id="{{ $id }}"
    role="option"
    @if ($value !== null) data-value="{{ $value }}" @endif
    x-on:mousemove="setActive($el)"
    x-on:click="hide()"
    {{ $attributes->merge(['class' => 'w-full text-left flex items-center gap-3 px-4 py-2.5 font-mono text-[0.8rem] text-ink cursor-pointer data-active:bg-ink data-active:text-surface']) }}
    data-sigta="command.item"
>
    @if ($icon)<x-sigta::icon :name="$icon" size="sm" class="shrink-0 opacity-70" />@endif
    <span class="grow">{{ $slot }}</span>
    @if ($kbd)<kbd class="font-mono text-[0.62rem] opacity-60">{{ $kbd }}</kbd>@endif
@if ($attributes->has('href'))
    </a>
@else
    </button>
@endif
