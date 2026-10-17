{{-- The tab row: arrow keys move between tabs (wrapping), Home and End jump to the ends. --}}
@props([
    'label' => 'Tabs',
])

<div
    role="tablist"
    aria-label="{{ $label }}"
    x-on:keydown.right.prevent="move(1)"
    x-on:keydown.left.prevent="move(-1)"
    x-on:keydown.home.prevent="moveTo(0)"
    x-on:keydown.end.prevent="moveTo(-1)"
    {{ $attributes }}
    data-sigta="tabs.list"
>
    {{ $slot }}
</div>
