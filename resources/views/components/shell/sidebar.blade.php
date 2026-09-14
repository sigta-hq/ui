{{-- Shell sidebar (brand .app-side): nav column on paper. On desktop it is a
     vertical rail; below the tablet breakpoint it becomes a top bar whose burger
     slides the nav in as a drawer over the page. `brand` slot renders the lockup
     up top; `actions` sits at the far end of that row, beside the burger, for the
     one or two controls that must stay reachable without opening the nav. --}}
@php
$panelId = 'sigta-shell-sidebar-'.\Illuminate\Support\Str::random(8);
$navLabel = $attributes->get('aria-label') ?? 'App navigation';
@endphp

<aside
    data-sigta="shell.sidebar"
    x-data="sigtaShellSidebar"
    x-on:keydown.escape.window="close()"
    x-on:livewire:navigated.window="close()"
    {{ $attributes->merge([
        'class' => 'bg-surface border-line px-3.5 py-[18px] flex flex-col border-b md:border-b-0 md:border-r',
        'aria-label' => 'App navigation',
    ]) }}
>
    {{-- Header row: brand, then the actions + burger cluster. The burger only shows
         on mobile; on desktop the row is the brand lockup and any actions. One
         auto margin on the cluster keeps actions hard against the burger, rather
         than the free space being split between them. --}}
    <div class="flex items-center gap-2 w-full md:mb-4">
        @isset($brand)
            <span class="flex items-center gap-2 min-w-0 font-head font-semibold text-[1.05rem] text-ink">{{ $brand }}</span>
        @endisset

        <div class="flex shrink-0 items-center gap-1 ms-auto -me-1">
            @isset($actions)
                {{ $actions }}
            @endisset

            <button
                type="button"
                class="md:hidden inline-flex shrink-0 items-center justify-center p-2 rounded-base text-ink cursor-pointer hover:bg-ink/5 transition duration-150 ease-out"
                aria-expanded="false"
                :aria-expanded="open ? 'true' : 'false'"
                aria-controls="{{ $panelId }}"
                aria-label="{{ __('Toggle navigation') }}"
                x-on:click="toggle()"
            >
                <x-sigta::icon name="menu" />
            </button>
        </div>
    </div>

    {{-- Scrim behind the drawer. Dimming the page is what tells a reader the nav
         has taken over, and tapping it is the expected way back out. --}}
    <div
        x-show="open"
        x-cloak
        x-on:click="close()"
        x-transition:enter="transition-opacity ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="md:hidden fixed inset-0 z-40 bg-ink/45 backdrop-blur-[2px]"
        aria-hidden="true"
    ></div>

    {{-- Nav body: the rail's column on desktop (md:!flex overrides the collapse),
         a drawer sliding in from the rail's own edge below the breakpoint. The
         trap and scroll lock are bound to `drawer` as well as `open`, so the rail
         is never trapped. --}}
    <div
        id="{{ $panelId }}"
        x-show="open"
        x-cloak
        x-trap.noscroll="open && drawer"
        x-bind:role="drawer ? 'dialog' : null"
        x-bind:aria-modal="drawer && open ? 'true' : null"
        aria-label="{{ $navLabel }}"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="max-md:ltr:-translate-x-full max-md:rtl:translate-x-full"
        x-transition:enter-end="max-md:translate-x-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="max-md:translate-x-0"
        x-transition:leave-end="max-md:ltr:-translate-x-full max-md:rtl:translate-x-full"
        class="flex md:!flex flex-1 min-h-0 w-full flex-col gap-1 mt-4 md:mt-0
               max-md:fixed max-md:inset-y-0 max-md:start-0 max-md:z-50 max-md:mt-0
               max-md:w-[min(84vw,19rem)] max-md:overflow-y-auto max-md:overscroll-contain
               max-md:bg-surface max-md:border-e max-md:border-line max-md:shadow-paper
               max-md:px-3.5 max-md:py-[18px]"
    >
        {{-- The burger is under the scrim once the drawer is over the page, so the
             drawer carries its own way out, roughly where the burger was. --}}
        <div class="md:hidden flex justify-end -mt-1 -me-1 mb-1">
            <button
                type="button"
                x-on:click="close()"
                aria-label="{{ __('Close navigation') }}"
                class="inline-flex items-center justify-center p-2 rounded-base text-ink cursor-pointer hover:bg-ink/5 transition duration-150 ease-out"
            >
                <x-sigta::icon name="x" />
            </button>
        </div>

        {{ $slot }}
    </div>
</aside>
