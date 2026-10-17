import { defineComponent } from '../schema.ts';
import { c, el } from '../examples.ts';

export default defineComponent({
    name: 'shell.link',
    title: 'Shell link',
    tier: 'layout',
    page: 'shell',
    svelte: 'Shell.Link',
    description: 'A nav link in the shell sidebar: mono, quiet graphite with an optional leading icon; inverts to ink when current.',
    aria: 'An <a>; the current one carries aria-current="page".',
    props: {
        icon: { type: 'string', default: null, description: 'Leading icon name.' },
        current: { type: 'boolean', default: false, description: 'The current page: inverts to ink and sets aria-current="page".' },
        href: { type: 'string', default: null, native: true, description: 'The link target.' },
    },
    parts: {
        icon: { description: 'The leading icon.' },
    },
    states: {
        current: { attribute: 'aria-current', values: ['page'], description: 'The current page.' },
    },
    slots: { default: { description: 'The label.', required: true } },
    usage: {
        livewire: `<sigta:shell.link href="/" icon="layout-grid" :current="request()->is('/')" wire:navigate>Overview</sigta:shell.link>`,
        svelte: `<Shell.Link href="/" icon="layout-grid" current={page.url === '/'}>Overview</Shell.Link>`,
    },
    notes: {
        shared: ['Put them in a shell.sidebar; mark the page you are on current.'],
        livewire: ['Attributes (href, wire:navigate, …) forward onto the <a>.'],
        svelte: ['Attributes (href, data-*, onclick) pass straight through to the <a>.'],
    },
    examples: [
        {
            label: 'Current, plain and without an icon',
            tree: [
                el('nav', { class: 'flex max-w-56 flex-col gap-1 bg-surface p-3.5' },
                    c('shell.link', { href: '#', icon: 'layout-grid', current: true }, 'Overview'),
                    c('shell.link', { href: '#', icon: 'trending-up' }, 'Trends'),
                    c('shell.link', { href: '#' }, 'Settings'),
                ),
            ],
        },
        {
            label: 'Hover',
            tree: [
                el('nav', { class: 'flex max-w-56 flex-col gap-1 bg-surface p-3.5' },
                    c('shell.link', { href: '#', icon: 'layout-grid', current: true }, 'Overview'),
                    c('shell.link', { href: '#', icon: 'clock' }, 'Reports'),
                ),
            ],
            interactions: [{ hover: '[data-sigta="shell.link"]:not([aria-current])' }],
        },
    ],
});
