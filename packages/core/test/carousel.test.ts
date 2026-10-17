// @vitest-environment jsdom
// The carousel's arithmetic: which slide is active for a scroll position,
// and where the track scrolls to reach a slide.
import { afterEach, describe, expect, it, vi } from 'vitest';
import { activeSlide, nearestSlide, scrollToSlide, slideOffsets } from '../src/carousel.ts';
import { autosize } from '../src/autosize.ts';

// jsdom does no layout: give each slide the offset a real track would.
function track(offsets: number[], scrollLeft = 0): HTMLElement {
    const el = document.createElement('div');
    offsets.forEach((left) => {
        const slide = document.createElement('div');
        Object.defineProperty(slide, 'offsetLeft', { value: left });
        el.append(slide);
    });
    el.scrollLeft = scrollLeft;
    Object.defineProperty(el, 'scrollLeft', { value: scrollLeft, writable: true });
    el.scrollTo = vi.fn() as unknown as HTMLElement['scrollTo'];
    return el;
}

afterEach(() => vi.unstubAllGlobals());

describe('nearestSlide', () => {
    it('picks the slide whose start is nearest the scroll position', () => {
        expect(nearestSlide([0, 400, 800], 0)).toBe(0);
        expect(nearestSlide([0, 400, 800], 190)).toBe(0);
        expect(nearestSlide([0, 400, 800], 210)).toBe(1);
        expect(nearestSlide([0, 400, 800], 1200)).toBe(2);
    });

    it('takes the first of a tie, and 0 without slides', () => {
        expect(nearestSlide([0, 400], 200)).toBe(0);
        expect(nearestSlide([], 120)).toBe(0);
    });
});

describe('slideOffsets / activeSlide', () => {
    it('measures from the first slide, so the track padding does not count', () => {
        expect(slideOffsets(track([24, 424, 824]))).toEqual([0, 400, 800]);
        expect(activeSlide(track([24, 424, 824], 400))).toBe(1);
        expect(slideOffsets(document.createElement('div'))).toEqual([]);
    });
});

describe('scrollToSlide', () => {
    it('scrolls to the slide start, without smoothing under reduced motion', () => {
        vi.stubGlobal('matchMedia', (query: string) => ({ matches: query.includes('reduce'), media: query }));
        const el = track([24, 424, 824]);
        scrollToSlide(el, 2);
        expect(el.scrollTo).toHaveBeenCalledWith({ left: 800, behavior: 'auto' });
    });

    it('ignores a slide that is not there', () => {
        const el = track([0, 400]);
        scrollToSlide(el, 5);
        scrollToSlide(el, -1);
        expect(el.scrollTo).not.toHaveBeenCalled();
    });
});

describe('autosize', () => {
    it('fits the height to the text up to the row limit, then scrolls', () => {
        const el = document.createElement('textarea');
        el.style.lineHeight = '24px';
        document.body.append(el);
        Object.defineProperty(el, 'scrollHeight', { value: 72, configurable: true });
        autosize(el, 6);
        expect(el.style.height).toBe('72px');
        expect(el.style.overflowY).toBe('hidden');

        Object.defineProperty(el, 'scrollHeight', { value: 400, configurable: true });
        autosize(el, 6);
        expect(el.style.height).toBe('144px');
        expect(el.style.overflowY).toBe('auto');
        el.remove();
    });
});
