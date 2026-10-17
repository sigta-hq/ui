// The shared page model: each case is what the pagination Blade view computes
// in PHP, so the Svelte twin renders the same links.
import { describe, expect, it } from 'vitest';
import { pageHref, pageWindow, paginate } from '../src/pagination.ts';

describe('pageWindow', () => {
    it('lists every page up to seven', () => {
        expect(pageWindow(2, 5)).toEqual([1, 2, 3, 4, 5]);
        expect(pageWindow(1, 7)).toEqual([1, 2, 3, 4, 5, 6, 7]);
    });

    it('windows around the current page with gaps', () => {
        expect(pageWindow(5, 10)).toEqual([1, '…', 4, 5, 6, '…', 10]);
        expect(pageWindow(1, 10)).toEqual([1, 2, '…', 10]);
        expect(pageWindow(10, 10)).toEqual([1, '…', 9, 10]);
        expect(pageWindow(3, 10)).toEqual([1, 2, 3, 4, '…', 10]);
    });
});

describe('pageHref', () => {
    it('fills a %d pattern, or appends the page to a prefix', () => {
        expect(pageHref('?page=%d')!(4)).toBe('?page=4');
        expect(pageHref('/things?page=')!(4)).toBe('/things?page=4');
        expect(pageHref((page) => `/p/${page}`)!(4)).toBe('/p/4');
        expect(pageHref(null)).toBeNull();
    });
});

describe('paginate', () => {
    it('clamps manual mode and links prev / next', () => {
        const model = paginate({ pages: 10, current: 5, url: '?page=%d' });
        expect(model).toMatchObject({ current: 5, last: 10, prevHref: '?page=4', nextHref: '?page=6', atStart: false, atEnd: false });
        expect(paginate({ pages: 6, current: 9, url: '?page=%d' })).toMatchObject({ current: 6, atEnd: true, nextHref: null });
        expect(paginate({ pages: 0, current: 0 })).toMatchObject({ current: 1, last: 1, items: [1] });
    });

    it('renders buttons (no hrefs) without a url', () => {
        expect(paginate({ pages: 3, current: 2 })).toMatchObject({ hrefFor: null, prevHref: null, nextHref: null, atStart: false, atEnd: false });
    });

    it('reads the neutral paginator shape', () => {
        expect(paginate({ paginator: { currentPage: 3, lastPage: 5 }, url: '/things?page=%d' }))
            .toMatchObject({ current: 3, last: 5, prevHref: '/things?page=2', nextHref: '/things?page=4', items: [1, 2, 3, 4, 5] });
    });

    it('reads a simple paginator (no last page) from hasMorePages', () => {
        const more = paginate({ paginator: { currentPage: 2, hasMorePages: true }, url: '?page=%d' });
        expect(more).toMatchObject({ last: null, items: [], prevHref: '?page=1', nextHref: '?page=3', atEnd: false });
        expect(paginate({ paginator: { currentPage: 2, hasMorePages: false }, url: '?page=%d' })).toMatchObject({ nextHref: null, atEnd: true });
    });

    it("reads Laravel's paginator JSON", () => {
        expect(paginate({ paginator: { current_page: 2, last_page: 4, next_page_url: '/x?page=3' }, url: '?page=%d' }))
            .toMatchObject({ current: 2, last: 4, nextHref: '?page=3' });
        expect(paginate({ paginator: { current_page: 2, next_page_url: null }, url: '?page=%d' })).toMatchObject({ last: null, atEnd: true });
    });
});
