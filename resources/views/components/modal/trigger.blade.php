@props([
    'name' => null,
])

<span
    x-data
    x-on:click="$dispatch('sigta-modal-show', { name: @js($name) })"
    {{ $attributes->merge(['class' => 'inline-flex']) }}
    data-sigta="modal.trigger"
>{{ $slot }}</span>
