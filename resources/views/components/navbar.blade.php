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
@endphp

{{-- data-collapse picks the breakpoint in the contract stylesheet: a site with longer
     links and a wide CTA folds them away up to `lg`, rather than wrapping each link
     onto two lines. --}}
<header
    data-sigta="navbar"
    data-collapse="{{ $collapse }}"
    x-data="{ open: false }"
    {{ $attributes }}
>
    <div data-scope="navbar" data-part="bar">
        @isset ($brand)
            <div {{ $brand->attributes->merge(['data-scope' => 'navbar', 'data-part' => 'brand']) }}>{{ $brand }}</div>
        @endisset

        <nav data-scope="navbar" data-part="links" aria-label="Primary">
            {{ $slot }}
        </nav>

        <div data-scope="navbar" data-part="end">
            @isset ($actions)
                <div {{ $actions->attributes->merge(['data-scope' => 'navbar', 'data-part' => 'actions']) }}>{{ $actions }}</div>
            @elseif ($ctaLabel)
                {{-- Without an href the CTA is a plain button, never a link to nowhere. --}}
                @if ($ctaHref !== null)
                    <x-sigta::button variant="cta" size="sm" :href="$ctaHref" :target="$target">{{ $ctaLabel }}</x-sigta::button>
                @else
                    <x-sigta::button variant="cta" size="sm">{{ $ctaLabel }}</x-sigta::button>
                @endif
            @endisset

            <button
                type="button"
                aria-expanded="false"
                :aria-expanded="open ? 'true' : 'false'"
                aria-controls="{{ $panelId }}"
                aria-label="Menu"
                @click="open = !open"
                data-scope="navbar"
                data-part="toggle"
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
        aria-label="Primary"
        data-scope="navbar"
        data-part="panel"
    >
        {{ $slot }}
    </nav>
</header>
