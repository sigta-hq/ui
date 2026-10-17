{{-- Shell sidebar (brand .app-side): nav column on paper. On desktop it is a
     vertical rail; below the tablet breakpoint it becomes a top bar whose burger
     slides the nav in as a drawer over the page. `brand` slot renders the lockup
     up top; `actions` sits at the far end of that row, beside the burger, for the
     one or two controls that must stay reachable without opening the nav.
     Inside a shell with a `header`, the header carries the brand, actions and
     burger instead, so this row is dropped and the sidebar is nav alone. --}}
@php
$panelId = 'sigta-shell-sidebar-'.\Illuminate\Support\Str::random(8);
$navLabel = $attributes->get('aria-label') ?? 'App navigation';
@endphp

<aside
    data-sigta="shell.sidebar"
    x-data="sigtaShellSidebar"
    x-on:keydown.escape.window="close()"
    x-on:livewire:navigated.window="close()"
    x-on:sigta-shell-toggle.window="toggle()"
    {{ $attributes->merge(['aria-label' => 'App navigation']) }}
>
    {{-- Header row: brand, then the actions + burger cluster. The burger only shows
         on mobile; on desktop the row is the brand lockup and any actions. --}}
    <div data-scope="shell.sidebar" data-part="top">
        @isset($brand)
            <span data-scope="shell.sidebar" data-part="brand">{{ $brand }}</span>
        @endisset

        <div data-scope="shell.sidebar" data-part="end">
            @isset($actions)
                {{ $actions }}
            @endisset

            <button
                type="button"
                aria-expanded="false"
                :aria-expanded="open ? 'true' : 'false'"
                aria-controls="{{ $panelId }}"
                aria-label="{{ __('Toggle navigation') }}"
                x-on:click="toggle()"
                data-scope="shell.sidebar"
                data-part="toggle"
            >
                <x-sigta::icon name="menu" />
            </button>
        </div>
    </div>

    {{-- Scrim behind the drawer: tapping it is the expected way back out. --}}
    <div
        x-show="open"
        x-cloak
        x-on:click="close()"
        x-transition:enter="sigta-shell-enter"
        x-transition:enter-start="sigta-shell-away"
        x-transition:leave="sigta-shell-leave"
        x-transition:leave-end="sigta-shell-away"
        aria-hidden="true"
        data-scope="shell.sidebar"
        data-part="scrim"
    ></div>

    {{-- Nav body: the rail's column on desktop (the stylesheet overrides the
         collapse there), a drawer sliding in from the rail's own edge below the
         breakpoint. The trap and scroll lock are bound to `drawer` as well as
         `open`, so the rail is never trapped. --}}
    <div
        id="{{ $panelId }}"
        x-show="open"
        x-cloak
        x-trap.noscroll="open && drawer"
        x-bind:role="drawer ? 'dialog' : null"
        x-bind:aria-modal="drawer && open ? 'true' : null"
        aria-label="{{ $navLabel }}"
        x-transition:enter="sigta-shell-enter"
        x-transition:enter-start="sigta-shell-away"
        x-transition:leave="sigta-shell-leave"
        x-transition:leave-end="sigta-shell-away"
        data-scope="shell.sidebar"
        data-part="nav"
    >
        {{-- The burger is under the scrim once the drawer is over the page, so the
             drawer carries its own way out, roughly where the burger was. --}}
        <div data-scope="shell.sidebar" data-part="dismiss">
            <button
                type="button"
                x-on:click="close()"
                aria-label="{{ __('Close navigation') }}"
                data-scope="shell.sidebar"
                data-part="close"
            >
                <x-sigta::icon name="x" />
            </button>
        </div>

        {{ $slot }}
    </div>
</aside>
