import { defineComponent } from '../schema.ts';
import { c, el } from '../examples.ts';

export default defineComponent({
    name: 'pagination',
    title: 'Pagination',
    tier: 'components',
    page: 'pagination',
    svelte: 'Pagination',
    description: 'Page navigation in small mono squares: prev / next arrows that dim at either end, and the pages around the current one (1 … 4 5 6 … 10) with the current page in ink.',
    aria: 'A <nav aria-label="Pagination"> landmark; the current page carries aria-current="page", the others "Page n" labels.',
    props: {
        paginator: {
            type: 'object',
            default: null,
            shape: '{ currentPage: number; lastPage?: number | null; hasMorePages?: boolean } — lastPage null (or absent) = total unknown: prev / next only, next while hasMorePages. A Laravel paginator\'s JSON (current_page, last_page, next_page_url) reads too.',
            description: 'The page state, from a server paginator. Its links follow url.',
            blade: { accepts: 'a Laravel paginator (LengthAwarePaginator, or a simple Paginator: prev / next only) — its own urls are used', note: 'Pass the paginator itself: :paginator="$users".' },
        },
        pages: { type: 'number', default: null, description: 'Manual mode: the number of pages.' },
        current: { type: 'number', default: 1, description: 'Manual mode: the current page (clamped to 1…pages).' },
        url: {
            type: 'string',
            default: null,
            description: 'The page href pattern: "?page=%d", or a prefix the page number is appended to ("?page="). Without it (and without a server paginator) the pages are buttons for you to wire up.',
            blade: { accepts: 'a Closure(int $page): string' },
            svelte: { type: 'string | ((page: number) => string)', note: 'Or a function of the page.' },
        },
    },
    parts: {
        list: { description: 'The <ul> row.' },
        prev: { description: 'The previous-page arrow: a link, a button, or a dimmed span (aria-disabled) on the first page.' },
        next: { description: 'The next-page arrow, dimmed on the last page.' },
        page: { description: 'A page: a link, a button, or the current page\'s span.' },
        gap: { description: 'An ellipsis between page runs.' },
    },
    states: {
        current: { attribute: 'aria-current', values: ['page'], description: 'On the current page.' },
        disabled: { attribute: 'aria-disabled', description: 'On an arrow at either end.' },
    },
    usage: {
        livewire: '<sigta:pagination :paginator="$users" />\n<sigta:pagination :pages="10" :current="$page" url="?page=%d" />',
        svelte: '<Pagination paginator={{ currentPage: users.current_page, lastPage: users.last_page }} url="?page=%d" />\n<Pagination pages={10} current={page} url={(n) => `/users/${n}`} />',
    },
    notes: {
        shared: [
            'Accepts a paginator or manual pages / current / url. Up to seven pages are all listed; beyond that, the first, the last and the pages either side of the current one, with ellipses between.',
        ],
        livewire: ['A Laravel paginator brings its own urls (query string included); url only applies to manual mode and the neutral shape.'],
        svelte: ['Pass a plain object — a Laravel paginator serialized by Inertia works as is (paginator={users}) — and url.'],
    },
    examples: [
        {
            label: 'Windowed (10 pages)',
            tree: [c('pagination', { pages: 10, current: 5, url: '?page=%d' })],
        },
        {
            label: 'Few pages (no ellipsis)',
            tree: [c('pagination', { pages: 5, current: 2, url: '?page=%d' })],
        },
        {
            label: 'Bounds — first & last',
            tree: [el('div', { class: 'flex flex-col gap-4' },
                c('pagination', { pages: 6, current: 1, url: '?page=%d' }),
                c('pagination', { pages: 6, current: 6, url: '?page=%d' }),
            )],
        },
        {
            label: 'From a paginator object',
            tree: [c('pagination', { paginator: { currentPage: 3, lastPage: 12 }, url: '/invoices?page=' })],
        },
        {
            label: 'Simple paginator — prev / next only',
            tree: [c('pagination', { paginator: { currentPage: 2, hasMorePages: true }, url: '?page=%d' })],
        },
        {
            label: 'Buttons (no url) to wire up yourself',
            tree: [c('pagination', { pages: 4, current: 2 })],
        },
        {
            label: 'A Laravel paginator in Blade, the same page state in Svelte',
            conformance: 'browser',
            blade: `@php($paginator = new \\Illuminate\\Pagination\\LengthAwarePaginator(range(1, 10), 50, 10, 3, ['path' => '/things']))
<x-sigta::pagination :paginator="$paginator" />`,
            svelte: `<script>
    import { Pagination } from '@sigta-hq/svelte';
</script>

<Pagination paginator={{ currentPage: 3, lastPage: 5 }} url="/things?page=%d" />`,
        },
        {
            label: 'Hovering a page',
            tree: [c('pagination', { pages: 10, current: 5, url: '?page=%d' })],
            interactions: [{ hover: '[aria-label="Page 6"]' }],
        },
    ],
});
