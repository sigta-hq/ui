{{-- Shell header: a full-width row above the sidebar and main, with the brand
     lockup on the left and `actions` (account menu, quick actions) on the right.
     Pass it in the shell's `header` slot. Below the tablet breakpoint it is the
     phone's top bar, so it carries the burger that opens the sidebar drawer. --}}
<header
    data-sigta="shell.header"
    x-data="{ navOpen: false }"
    x-on:sigta-shell-nav.window="navOpen = $event.detail.open"
    {{ $attributes }}
>
    @isset($brand)
        <span data-scope="shell.header" data-part="brand">{{ $brand }}</span>
    @endisset

    <div data-scope="shell.header" data-part="end">
        @isset($actions)
            {{ $actions }}
        @endisset

        {{-- The sidebar owns the drawer's state, so the burger asks it to toggle
             and mirrors what it reports back rather than keeping a second copy. --}}
        <button
            type="button"
            aria-expanded="false"
            x-bind:aria-expanded="navOpen ? 'true' : 'false'"
            aria-label="{{ __('Toggle navigation') }}"
            x-on:click="$dispatch('sigta-shell-toggle')"
            data-scope="shell.header"
            data-part="toggle"
        >
            <x-sigta::icon name="menu" />
        </button>
    </div>
</header>
