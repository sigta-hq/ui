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
    {{ $attributes }}
    data-sigta="command.item"
>
    @if ($icon)<x-sigta::icon :name="$icon" size="sm" data-scope="command.item" data-part="icon" />@endif
    <span data-scope="command.item" data-part="label">{{ $slot }}</span>
    @if ($kbd)<kbd data-scope="command.item" data-part="kbd">{{ $kbd }}</kbd>@endif
@if ($attributes->has('href'))
    </a>
@else
    </button>
@endif
