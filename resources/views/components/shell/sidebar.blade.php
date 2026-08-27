{{-- Shell sidebar (brand .app-side): nav column on paper. On desktop it is a
     vertical rail; on mobile it collapses under a burger toggle that reveals
     the nav. `brand` slot renders the lockup up top. --}}
@php
$panelId = 'sigta-shell-sidebar-'.\Illuminate\Support\Str::random(8);
@endphp

<aside
    data-sigta="shell.sidebar"
    x-data="{ open: false }"
    @keydown.escape.window="open = false"
    {{ $attributes->merge([
        'class' => 'bg-surface border-line px-3.5 py-[18px] flex flex-col border-b md:border-b-0 md:border-r',
        'aria-label' => 'App navigation',
    ]) }}
>
    {{-- Header row: brand + burger. The burger only shows on mobile; on desktop
         the row is just the brand lockup. --}}
    <div class="flex items-center justify-between gap-2 w-full md:mb-4">
        @isset($brand)
            <span class="flex items-center gap-2 min-w-0 font-head font-semibold text-[1.05rem] text-ink">{{ $brand }}</span>
        @endisset

        <button
            type="button"
            class="md:hidden inline-flex shrink-0 items-center justify-center ms-auto -me-1 p-2 rounded-base text-ink cursor-pointer hover:bg-ink/5 transition duration-150 ease-out"
            aria-expanded="false"
            :aria-expanded="open ? 'true' : 'false'"
            aria-controls="{{ $panelId }}"
            aria-label="{{ __('Toggle navigation') }}"
            @click="open = ! open"
        >
            <span x-show="! open"><x-sigta::icon name="menu" /></span>
            <span x-show="open" x-cloak><x-sigta::icon name="x" /></span>
        </button>
    </div>

    {{-- Nav body: always visible on desktop (md:!flex overrides the collapse);
         on mobile it is hidden until the burger toggles `open`. --}}
    <div
        id="{{ $panelId }}"
        x-show="open"
        x-cloak
        class="flex md:!flex flex-1 min-h-0 w-full flex-col gap-1 mt-4 md:mt-0"
    >
        {{ $slot }}
    </div>
</aside>
