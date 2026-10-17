{{-- Menu button: the `trigger` slot opens a menu of <x-sigta::dropdown.item>s (the default
     slot). Click or ArrowDown / ArrowUp on the trigger opens it on the first / last item;
     arrows, Home and End move, Escape closes and returns focus to the trigger; a click
     outside or focus leaving closes it. --}}
@props([
    'position' => 'bottom-start',
])

<div
    x-data="sigtaDropdown"
    x-on:keydown.escape.prevent.stop="close(true)"
    x-on:focusout="if (! $el.contains($event.relatedTarget)) close()"
    {{ $attributes->merge(['data-position' => $position]) }}
    data-sigta="dropdown"
>
    <div
        x-ref="trigger"
        x-on:click="toggle()"
        x-on:keydown.down.prevent="openAt(0)"
        x-on:keydown.up.prevent="openAt(-1)"
        aria-expanded="false"
        x-bind:aria-expanded="open"
        aria-haspopup="menu"
        data-scope="dropdown"
        data-part="trigger"
    >
        {{ $trigger }}
    </div>

    <div
        x-cloak
        x-show="open"
        x-on:click.outside="close()"
        x-on:keydown.down.prevent="move(1)"
        x-on:keydown.up.prevent="move(-1)"
        x-on:keydown.home.prevent="focusItem(0)"
        x-on:keydown.end.prevent="focusItem(-1)"
        role="menu"
        data-scope="dropdown"
        data-part="menu"
    >
        {{ $slot }}
    </div>
</div>
