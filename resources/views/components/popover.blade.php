{{-- Generic popover — dropdown positioning without menu semantics. Free-form content. --}}
@props([
    'position' => 'bottom-start',
])

@php
$panelPosition = match ($position) {
    'bottom-end' => 'top-full right-0 mt-2',
    'top-start' => 'bottom-full left-0 mb-2',
    'top-end' => 'bottom-full right-0 mb-2',
    default => 'top-full left-0 mt-2',
};
@endphp

<div
    x-data="sigtaDropdown"
    x-on:keydown.escape.prevent.stop="close(true)"
    x-on:focusout="if (! $el.contains($event.relatedTarget)) close()"
    {{ $attributes->merge(['class' => 'relative inline-block']) }}
    data-sigta="popover"
>
    <div x-ref="trigger" x-on:click="toggle()" x-bind:aria-expanded="open" aria-haspopup="dialog">
        {{ $trigger }}
    </div>

    <div
        x-cloak
        x-show="open"
        x-on:click.outside="close()"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 -translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        role="dialog"
        class="absolute {{ $panelPosition }} z-50 min-w-56 bg-surface-raised border border-line rounded-panel shadow-paper p-4"
    >
        {{ $slot }}
    </div>
</div>
