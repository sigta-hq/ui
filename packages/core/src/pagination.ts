/* ---------------------------------------------------------------------------
   Pagination — the page model both runtimes render: which page links show
   (1 … around the current page … last), and the hrefs of the pages and the
   prev / next arrows. The Blade view computes the same in PHP (it also reads
   a Laravel paginator); this is the neutral version. Pure: no DOM.
--------------------------------------------------------------------------- */

/** The neutral paginator shape. Laravel's paginator JSON (current_page, last_page, next_page_url) reads too. */
export interface PageState {
    /** The page shown, 1-based. */
    currentPage: number;
    /** The last page; null or absent when the total is unknown (prev / next only). */
    lastPage?: number | null;
    /** With no lastPage: whether a page follows the current one. */
    hasMorePages?: boolean;
}

/** A href pattern ("?page=%d", or a prefix the number is appended to: "?page="), or a function of the page. */
export type PageUrl = string | ((page: number) => string) | null | undefined;

export type PageItem = number | '…';

export interface PaginationModel {
    current: number;
    /** null when the total is unknown. */
    last: number | null;
    items: PageItem[];
    /** null: the pages are buttons (no url to link them to). */
    hrefFor: ((page: number) => string) | null;
    prevHref: string | null;
    nextHref: string | null;
    atStart: boolean;
    atEnd: boolean;
}

/** PHP's range(): inclusive, and counting down when from > to. */
function range(from: number, to: number): number[] {
    const out: number[] = [];
    if (from <= to) for (let i = from; i <= to; i++) out.push(i);
    else for (let i = from; i >= to; i--) out.push(i);
    return out;
}

/** The page links: every page up to 7, else 1 … the pages around the current one … last. */
export function pageWindow(current: number, last: number): PageItem[] {
    if (last <= 7) return range(1, last);
    const from = Math.max(2, current - 1);
    const to = Math.min(last - 1, current + 1);
    const items: PageItem[] = [1];
    if (from > 2) items.push('…');
    items.push(...range(from, to));
    if (to < last - 1) items.push('…');
    items.push(last);
    return items;
}

/** The href builder a url pattern or function describes (sprintf's %d, or an appended number). */
export function pageHref(url: PageUrl): ((page: number) => string) | null {
    if (url === null || url === undefined) return null;
    if (typeof url === 'function') return url;
    return (page) => (url.includes('%d') ? url.replace('%d', String(Math.trunc(page))) : url + page);
}

const int = (value: unknown, fallback: number): number => {
    const n = Math.trunc(Number(value));
    return Number.isFinite(n) ? n : fallback;
};

export interface PaginateOptions {
    /** The neutral paginator object (not clamped, like a server paginator). */
    paginator?: PageState | Record<string, unknown> | null;
    /** Manual mode: the number of pages. */
    pages?: number | null;
    /** Manual mode: the current page (clamped to 1…pages). */
    current?: number | null;
    url?: PageUrl;
}

export function paginate({ paginator = null, pages = null, current = 1, url = null }: PaginateOptions = {}): PaginationModel {
    const hrefFor = pageHref(url);
    let page: number;
    let last: number | null;
    let hasMore: boolean;
    if (paginator && Object.keys(paginator).length) {
        const p = paginator as Record<string, unknown>;
        page = int(p.currentPage ?? p.current_page ?? 1, 1);
        const lastValue = p.lastPage ?? p.last_page ?? null;
        last = lastValue === null || lastValue === undefined ? null : int(lastValue, 1);
        hasMore = Boolean(p.hasMorePages ?? ('next_page_url' in p ? p.next_page_url !== null : last !== null && page < last));
    } else {
        last = Math.max(1, int(pages ?? 1, 0));
        page = Math.min(last, Math.max(1, int(current ?? 1, 0)));
        hasMore = page < last;
    }
    const prevHref = hrefFor && page > 1 ? hrefFor(page - 1) : null;
    const nextHref = hrefFor && (last !== null ? page < last : hasMore) ? hrefFor(page + 1) : null;
    return {
        current: page,
        last,
        items: last === null ? [] : pageWindow(page, last),
        hrefFor,
        prevHref,
        nextHref,
        atStart: page <= 1,
        atEnd: last !== null ? page >= last : !hasMore,
    };
}
