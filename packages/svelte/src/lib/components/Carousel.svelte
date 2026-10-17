<script lang="ts">
    /* Carousel — a scroll-snap track with prev / next chevrons and a dot per
       slide. The active slide is read from the scroll position (the core's
       carousel helpers, as in the Livewire runtime), so a swipe, the arrows
       and the dots agree; the arrows disable at the ends. The dots appear
       once mounted, from the slides in the track. */
    import type { Snippet } from 'svelte';
    import type { HTMLAttributes } from 'svelte/elements';
    import { onMount } from 'svelte';
    import { activeSlide, scrollToSlide } from '@sigta-hq/core';
    import Button from './Button.svelte';
    import Icon from './Icon.svelte';

    interface Props extends HTMLAttributes<HTMLElement> {
        label?: string | null;
        children?: Snippet;
    }

    let { label = null, children, ...rest }: Props = $props();

    let track: HTMLDivElement | undefined = $state();
    let active = $state(0);
    let count = $state(0);
    // The Livewire render has no ends until Alpine runs; neither does this one.
    let mounted = $state(false);

    const sync = () => { if (track) active = activeSlide(track); };
    const go = (index: number) => { if (track) scrollToSlide(track, index); };
    const prev = () => go(Math.max(0, active - 1));
    const next = () => go(Math.min(count - 1, active + 1));

    onMount(() => {
        if (!track) return;
        const el = track;
        const recount = () => { count = el.children.length; };
        recount();
        mounted = true;
        requestAnimationFrame(sync);
        // Slides added or removed later ({#each} over changing data) keep the dots in step.
        const observer = new MutationObserver(() => { recount(); sync(); });
        observer.observe(el, { childList: true });
        return () => observer.disconnect();
    });
</script>

<!-- A <section> is a region only once it is named; the role says it either way. -->
<!-- svelte-ignore a11y_no_redundant_roles -->
<section data-sigta="carousel" role="region" aria-roledescription="carousel" aria-label={label || undefined} {...rest}>
    <div bind:this={track} onscroll={sync} data-scope="carousel" data-part="track">
        {@render children?.()}
    </div>

    <div data-scope="carousel" data-part="controls">
        <Button variant="ghost" aria-label="Previous slide" onclick={prev} disabled={mounted && active <= 0}>
            <Icon name="chevron-left" />
        </Button>

        <div aria-label="Slides" role="group" data-scope="carousel" data-part="dots">
            {#each { length: count }, i}
                <button
                    type="button"
                    onclick={() => go(i)}
                    aria-current={active === i ? 'true' : 'false'}
                    aria-label="Go to slide {i + 1}"
                    data-scope="carousel"
                    data-part="dot"
                ></button>
            {/each}
        </div>

        <Button variant="ghost" aria-label="Next slide" onclick={next} disabled={mounted && active >= count - 1}>
            <Icon name="chevron-right" />
        </Button>
    </div>
</section>
