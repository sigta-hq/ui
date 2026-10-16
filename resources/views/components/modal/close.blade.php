@props([
    'name' => null,
])

<span
    x-data
    x-on:click="$dispatch('sigta-modal-hide', { name: @js($name) })"
    {{ $attributes }}
    data-sigta="modal.close"
>{{ $slot }}</span>
