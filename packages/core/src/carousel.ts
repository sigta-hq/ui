/* ---------------------------------------------------------------------------
   Carousel: a scroll-snap track whose active slide is read from the scroll
   position, so a swipe, the arrows and the dots all agree on it. Both
   runtimes' carousels (Alpine's sigtaCarousel, the Svelte Carousel) measure
   and move the track with these. SSR-safe: nothing runs at import time.
--------------------------------------------------------------------------- */
import { reduced } from './env.ts';

/** Where each slide starts along the track, measured from the first slide. */
export function slideOffsets(track: HTMLElement): number[] {
    const slides = Array.from(track.children) as HTMLElement[];
    const base = slides[0]?.offsetLeft ?? 0;
    return slides.map((slide) => slide.offsetLeft - base);
}

/** The slide whose start is nearest the scroll position (the first of a tie). */
export function nearestSlide(offsets: readonly number[], scrollLeft: number): number {
    let best = 0;
    let min = Infinity;
    offsets.forEach((offset, index) => {
        const distance = Math.abs(offset - scrollLeft);
        if (distance < min) {
            min = distance;
            best = index;
        }
    });
    return best;
}

/** The active slide of a track, from where it is scrolled to. */
export function activeSlide(track: HTMLElement): number {
    return nearestSlide(slideOffsets(track), track.scrollLeft);
}

/** Scrolls the track to a slide: smoothly, unless the visitor asked for reduced motion. */
export function scrollToSlide(track: HTMLElement, index: number): void {
    const target = track.children[index] as HTMLElement | undefined;
    if (!target) return;
    track.scrollTo({ left: slideOffsets(track)[index], behavior: reduced() ? 'auto' : 'smooth' });
}
