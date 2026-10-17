{{-- Generic popover — dropdown positioning without menu semantics. Free-form content.
     The panel takes focus when clicked (tabindex -1), so clicking its text or padding
     keeps it open; focus leaving it, a click outside or Escape closes it. --}}
@props([
    'position' => 'bottom-start',
])

<div
    x-data="sigtaDropdown"
    x-on:keydown.escape.prevent.stop="close(true)"
    x-on:focusout="if (! $el.contains($event.relatedTarget)) close()"
    {{ $attributes->merge(['data-position' => $position]) }}
    data-sigta="popover"
>
    <div
        x-ref="trigger"
        x-on:click="toggle()"
        aria-expanded="false"
        x-bind:aria-expanded="open"
        aria-haspopup="dialog"
        data-scope="popover"
        data-part="trigger"
    >
        {{ $trigger }}
    </div>

    <div
        x-cloak
        x-show="open"
        x-on:click.outside="close()"
        role="dialog"
        tabindex="-1"
        data-scope="popover"
        data-part="panel"
    >
        {{ $slot }}
    </div>
</div>
