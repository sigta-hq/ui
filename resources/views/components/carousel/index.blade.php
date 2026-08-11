{{-- Carousel: a scroll-snap track with prev/next chevrons and clickable dots.
     Active slide is derived from scroll position (so swipe + buttons stay in
     sync); dots are generated from the slide count. Buttons disable at the ends.
     Smooth scrolling is skipped under prefers-reduced-motion. --}}
@props([
    'label' => null,
])

<section
    data-sigta="carousel"
    role="region"
    aria-roledescription="carousel"
    @if ($label) aria-label="{{ $label }}" @endif
    x-data="{
        active: 0,
        count: 0,

        init() {
            this.count = this.$refs.track.children.length;
            this.$nextTick(() => this.sync());
        },

        base() {
            return this.$refs.track.children[0] ? this.$refs.track.children[0].offsetLeft : 0;
        },

        sync() {
            const track = this.$refs.track;
            const left = track.scrollLeft, base = this.base();
            let best = 0, min = Infinity;
            Array.from(track.children).forEach((slide, i) => {
                const d = Math.abs((slide.offsetLeft - base) - left);
                if (d < min) { min = d; best = i; }
            });
            this.active = best;
        },

        go(i) {
            const target = this.$refs.track.children[i];
            if (! target) return;
            const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            this.$refs.track.scrollTo({ left: target.offsetLeft - this.base(), behavior: reduce ? 'auto' : 'smooth' });
        },

        prev() { this.go(Math.max(0, this.active - 1)); },
        next() { this.go(Math.min(this.count - 1, this.active + 1)); },
        atStart() { return this.active <= 0; },
        atEnd() { return this.active >= this.count - 1; },
    }"
    {{ $attributes->merge(['class' => 'w-full']) }}
>
    <div
        x-ref="track"
        x-on:scroll.passive="sync()"
        class="flex gap-4 overflow-x-auto snap-x snap-mandatory [scrollbar-width:none] [&::-webkit-scrollbar]:hidden"
    >
        {{ $slot }}
    </div>

    <div class="mt-4 flex items-center justify-center gap-4">
        <x-sigta::button
            variant="ghost"
            aria-label="Previous slide"
            x-on:click="prev()"
            x-bind:disabled="atStart()"
        >
            <x-sigta::icon name="chevron-left" />
        </x-sigta::button>

        <div class="flex items-center gap-2" aria-label="Slides" role="group">
            <template x-for="i in count" :key="i">
                <button
                    type="button"
                    x-on:click="go(i - 1)"
                    x-bind:aria-current="active === (i - 1) ? 'true' : 'false'"
                    x-bind:aria-label="'Go to slide ' + i"
                    class="size-2.5 rounded-full border-[1.5px] border-ink transition-colors duration-150 ease-out cursor-pointer"
                    x-bind:class="active === (i - 1) ? 'bg-ink' : 'bg-transparent hover:bg-ink/20'"
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
