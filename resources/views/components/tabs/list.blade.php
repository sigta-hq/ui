@props([
    'label' => 'Tabs',
])

<div
    role="tablist"
    aria-label="{{ $label }}"
    x-on:keydown.right.prevent="move(1)"
    x-on:keydown.left.prevent="move(-1)"
    {{ $attributes->merge(['class' => 'flex items-end gap-1 border-b border-line']) }}
    data-sigta="tabs.list"
>
    {{ $slot }}
</div>
