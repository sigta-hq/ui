{{-- Shell header: a full-width row above the sidebar and main, with the brand
     lockup on the left and `actions` (account menu, quick actions) on the right.
     Pass it in the shell's `header` slot. Below the tablet breakpoint it is the
     phone's top bar, so it carries the burger that opens the sidebar drawer. --}}
<header
    data-sigta="shell.header"
    x-data="{ navOpen: false }"
    x-on:sigta-shell-nav.window="navOpen = $event.detail.open"
    {{ $attributes->merge(['class' => 'md:col-span-2 flex items-center gap-2 min-h-14 bg-surface border-b border-line px-3.5 py-2']) }}
>
    @isset($brand)
        <span class="flex items-center gap-2 min-w-0 font-head font-semibold text-[1.05rem] text-ink">{{ $brand }}</span>
    @endisset

    <div class="flex shrink-0 items-center gap-1 ms-auto -me-1">
        @isset($actions)
            {{ $actions }}
        @endisset

        {{-- The sidebar owns the drawer's state, so the burger asks it to toggle
             and mirrors what it reports back rather than keeping a second copy. --}}
        <button
            type="button"
            class="md:hidden inline-flex shrink-0 items-center justify-center p-2 rounded-base text-ink cursor-pointer hover:bg-ink/5 transition duration-150 ease-out"
            aria-expanded="false"
            x-bind:aria-expanded="navOpen ? 'true' : 'false'"
            aria-label="{{ __('Toggle navigation') }}"
            x-on:click="$dispatch('sigta-shell-toggle')"
        >
            <x-sigta::icon name="menu" />
        </button>
    </div>
</header>
