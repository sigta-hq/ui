@props([
    'value' => null,
    'label' => null,
])

@php
$id = 'sigta-option-'.\Illuminate\Support\Str::random(8);
@endphp

<div
    id="{{ $id }}"
    role="option"
    @if ($value !== null) data-value="{{ $value }}" @endif
    @if ($label !== null) data-label="{{ $label }}" @endif
    {{-- Prevent the default mousedown so the field doesn't blur before this
         option's click fires — otherwise focusout closes the panel first and
         the selection never registers. --}}
    x-on:mousedown.prevent=""
    x-on:click="choose($el)"
    x-on:mousemove="setActive($el)"
    x-bind:aria-selected="isSelected($el.dataset.value ?? $el.textContent.trim()) ? 'true' : 'false'"
    {{ $attributes->merge(['class' => 'px-3.5 py-2 font-mono text-[0.78rem] text-ink cursor-pointer data-active:bg-ink data-active:text-surface aria-selected:font-medium']) }}
    data-sigta="autocomplete.option"
>{{ $slot }}</div>
