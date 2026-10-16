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
    aria-selected="false"
    x-bind:aria-selected="isSelected($el.dataset.value ?? $el.textContent.trim()) ? 'true' : 'false'"
    {{ $attributes }}
    data-sigta="autocomplete.option"
>{{ $slot }}</div>
