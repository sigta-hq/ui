{{-- Carousel: a scroll-snap track with prev/next chevrons and clickable dots.
     Active slide is derived from scroll position (so swipe + buttons stay in
     sync); dots are generated from the slide count. Buttons disable at the ends.
     Smooth scrolling is skipped under prefers-reduced-motion. The behavior is
     sigtaCarousel in resources/js/sigta.js, on the core's carousel helpers. --}}
@props([
    'label' => null,
])

<section
    data-sigta="carousel"
    role="region"
    aria-roledescription="carousel"
    @if ($label) aria-label="{{ $label }}" @endif
    x-data="sigtaCarousel"
    {{ $attributes }}
>
    <div x-ref="track" x-on:scroll.passive="sync()" data-scope="carousel" data-part="track">
        {{ $slot }}
    </div>

    <div data-scope="carousel" data-part="controls">
        <x-sigta::button
            variant="ghost"
            aria-label="Previous slide"
            x-on:click="prev()"
            x-bind:disabled="atStart()"
        >
            <x-sigta::icon name="chevron-left" />
        </x-sigta::button>

        <div aria-label="Slides" role="group" data-scope="carousel" data-part="dots">
            <template x-for="i in count" :key="i">
                <button
                    type="button"
                    x-on:click="go(i - 1)"
                    x-bind:aria-current="active === (i - 1) ? 'true' : 'false'"
                    x-bind:aria-label="'Go to slide ' + i"
                    data-scope="carousel"
                    data-part="dot"
                ></button>
            </template>
        </div>

        <x-sigta::button
            variant="ghost"
            aria-label="Next slide"
            x-on:click="next()"
            x-bind:disabled="atEnd()"
        >
            <x-sigta::icon name="chevron-right" />
        </x-sigta::button>
    </div>
</section>
