{{-- Brand header.nav: sticky translucent paper bar with hairline. Slots: brand / default (links) / actions.
     Optional CTA props render a button in the actions area when the actions slot is empty —
     `target` forwards onto that button like a normal <sigta:button>. `collapse` is the breakpoint
     below which the links fold behind the burger: `md` (default) or `lg`. --}}
@props([
    'ctaLabel' => null,
    'ctaHref' => null,
    'target' => null,
    'collapse' => 'md',
])

@php
$panelId = 'sigta-navbar-panel-'.\Illuminate\Support\Str::random(8);

// Written out whole so Tailwind finds each class. A site with longer links and a
// wide CTA folds them away up to `lg`, rather than wrapping each link onto two lines.
[$barLinks, $belowOnly] = match ($collapse) {
    'lg' => ['hidden lg:flex', 'lg:hidden'],
    default => ['hidden md:flex', 'md:hidden'],
};
@endphp

<header
    data-sigta="navbar"
    x-data="{ open: false }"
    {{ $attributes->merge(['class' => 'sticky top-0 z-50 bg-surface/85 backdrop-blur-md border-b border-line']) }}
>
    <div class="max-w-(--sigta-maxw) mx-auto px-(--sigta-gutter) flex items-center justify-between gap-4 h-[68px]">
        @isset ($brand)
            <div {{ $brand->attributes->merge(['class' => 'flex items-center shrink-0']) }}>{{ $brand }}</div>
        @endisset

        <nav class="{{ $barLinks }} items-center gap-8" aria-label="Primary">
            {{ $slot }}
        </nav>

        <div class="flex items-center gap-3">
            @isset ($actions)
                <div {{ $actions->attributes->merge(['class' => 'flex items-center gap-3']) }}>{{ $actions }}</div>
            @elseif ($ctaLabel)
                <x-sigta::button variant="cta" size="sm" :href="$ctaHref" :target="$target">{{ $ctaLabel }}</x-sigta::button>
            @endisset

            <button
                type="button"
                class="{{ $belowOnly }} inline-flex items-center justify-center p-2 -mr-2 text-ink rounded-base cursor-pointer hover:bg-ink/5 transition duration-150 ease-out"
                aria-expanded="false"
                :aria-expanded="open ? 'true' : 'false'"
                aria-controls="{{ $panelId }}"
                aria-label="Menu"
                @click="open = !open"
            >
                <span x-show="!open"><x-sigta::icon name="menu" /></span>
                <span x-show="open" x-cloak><x-sigta::icon name="x" /></span>
            </button>
        </div>
    </div>

    <nav
        id="{{ $panelId }}"
        x-show="open"
        x-cloak
        @keydown.escape.window="open = false"
        class="{{ $belowOnly }} border-t border-line px-(--sigta-gutter) py-4 flex flex-col gap-3"
        aria-label="Primary"
    >
        {{ $slot }}
    </nav>
</header>
