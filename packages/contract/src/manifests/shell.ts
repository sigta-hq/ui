import { defineComponent } from '../schema.ts';
import { c, el } from '../examples.ts';

const brand = [c('brand', { size: 'sm', glyphOnly: true }), 'Plumb'];

export default defineComponent({
    name: 'shell',
    title: 'Shell',
    tier: 'layout',
    page: 'shell',
    svelte: 'Shell',
    description: 'App shell grid: sidebar + main. An embeddable rounded frame on raised paper by default; full for a full-viewport app. An optional header row spans both columns above them.',
    aria: 'A <div> grid; the landmarks are its children (shell.sidebar is an <aside>, shell.header a <header>).',
    props: {
        full: { type: 'boolean', default: false, description: 'true → full-viewport app (no frame); default → embeddable rounded frame with a hairline border.' },
    },
    states: {
        full: { attribute: 'data-full', description: 'Fills the viewport, no frame.' },
        header: { attribute: 'data-header', description: 'A header row is given: it spans both columns, and the sidebar drops its own brand row and burger.' },
    },
    slots: {
        header: { description: 'Optional: a shell.header, the full-width row above the sidebar and main.' },
        sidebar: { description: 'The shell.sidebar (the nav rail / drawer).' },
        default: { description: 'The main column: a shell.main.', required: true },
    },
    uses: ['shell.header', 'shell.sidebar', 'shell.main', 'shell.bar', 'shell.link'],
    usage: {
        livewire: `<sigta:shell full>
    <x-slot:header>
        <sigta:shell.header>
            <x-slot:brand><sigta:brand /></x-slot:brand>
            <x-slot:actions><sigta:avatar name="Ada Lovelace" size="sm" /></x-slot:actions>
        </sigta:shell.header>
    </x-slot:header>
    <x-slot:sidebar><sigta:shell.sidebar>…nav…</sigta:shell.sidebar></x-slot:sidebar>
    <sigta:shell.main>…</sigta:shell.main>
</sigta:shell>`,
        svelte: `<Shell full>
    {#snippet header()}
        <Shell.Header>
            {#snippet brand()}<Brand />{/snippet}
            {#snippet actions()}<Avatar name="Ada Lovelace" size="sm" />{/snippet}
        </Shell.Header>
    {/snippet}
    {#snippet sidebar()}<Shell.Sidebar>…nav…</Shell.Sidebar>{/snippet}
    <Shell.Main>…</Shell.Main>
</Shell>`,
    },
    notes: {
        shared: [
            'full=true → full-viewport app; default → embeddable rounded frame (min 440px tall, overflow clipped).',
            'header is optional. With it the header row spans both columns above the sidebar and main, carries the brand, actions and (on a phone) the burger, and the sidebar is navigation alone. A shell without header is unchanged.',
        ],
        livewire: ['Slots: <x-slot:header> (optional, a <sigta:shell.header>), <x-slot:sidebar> (a <sigta:shell.sidebar>), and the default slot for the main (a <sigta:shell.main>).'],
        svelte: ['Snippets: header (optional, a <Shell.Header>), sidebar (a <Shell.Sidebar>), and children for the main (a <Shell.Main>).'],
    },
    examples: [
        {
            label: 'Embedded app frame — sidebar, bar, main (narrow the viewport: the burger slides the nav in as a drawer)',
            tree: [
                c('shell', {},
                    { sidebar: [c('shell.sidebar', {},
                        { brand },
                        c('shell.link', { href: '#', icon: 'layout-grid', current: true }, 'Overview'),
                        c('shell.link', { href: '#', icon: 'trending-up' }, 'Trends'),
                        c('shell.link', { href: '#', icon: 'clock' }, 'Reports'),
                        c('shell.link', { href: '#', icon: 'settings' }, 'Settings'),
                    )] },
                    c('shell.main', {},
                        c('shell.bar', { heading: 'Overview', sub: 'Q2 2026 · updated 6 min ago' },
                            { actions: [
                                c('segmented', { name: 'shell-range', label: 'Range', options: { '7d': '7d', '30d': '30d', qtd: 'QTD' }, checked: '30d' }),
                                c('pill', { variant: 'success' }, 'On track'),
                            ] },
                        ),
                        el('div', { class: 'grid gap-3 sm:grid-cols-3' },
                            c('stat', { label: 'Active accounts', value: '2,418', delta: '6.2% vs last 30d', trend: 'up' }),
                            c('stat', { label: 'Net retention', value: '112%', delta: '1.4 pts', trend: 'up' }),
                            c('stat', { label: 'Time to value', value: '3.1d', delta: '0.4d slower', trend: 'down' }),
                        ),
                        el('div', { class: 'bg-surface border border-line rounded-panel p-4' },
                            c('chart.line', { values: [22, 26, 25, 30, 33, 31, 38, 36, 42, 45], height: 140 }),
                        ),
                    ),
                ),
            ],
        },
        {
            label: 'Sidebar actions (stay reachable while the nav is collapsed)',
            tree: [
                c('shell', {},
                    { sidebar: [c('shell.sidebar', {},
                        {
                            brand,
                            actions: [c('button', { variant: 'ghost', size: 'sm', 'aria-label': 'Quick actions' }, c('icon', { name: 'zap', size: 'sm' }))],
                        },
                        c('shell.link', { href: '#', icon: 'layout-grid', current: true }, 'Overview'),
                        c('shell.link', { href: '#', icon: 'clock' }, 'Reports'),
                    )] },
                    c('shell.main', {},
                        c('text', { variant: 'muted' }, 'Narrow the viewport: the nav slides in as a drawer over this content, and the action stays beside the burger rather than going with it.'),
                    ),
                ),
            ],
        },
        {
            label: 'Header row (brand and account across the top; the sidebar is navigation alone, and on a phone the burger lives in the header)',
            tree: [
                c('shell', {},
                    {
                        header: [c('shell.header', {},
                            {
                                brand,
                                actions: [
                                    c('button', { variant: 'ghost', size: 'sm', 'aria-label': 'Quick actions' }, c('icon', { name: 'zap', size: 'sm' })),
                                    c('avatar', { name: 'Ada Lovelace', size: 'sm' }),
                                ],
                            },
                        )],
                        sidebar: [c('shell.sidebar', {},
                            c('shell.link', { href: '#', icon: 'layout-grid', current: true }, 'Overview'),
                            c('shell.link', { href: '#', icon: 'clock' }, 'Reports'),
                        )],
                    },
                    c('shell.main', {},
                        c('text', { variant: 'muted' }, 'The page starts straight with its content.'),
                    ),
                ),
            ],
        },
        {
            label: 'Full viewport',
            tree: [
                el('div', { class: 'h-96 overflow-hidden rounded-frame border border-dashed border-line' },
                    c('shell', { full: true },
                        { sidebar: [c('shell.sidebar', {},
                            { brand },
                            c('shell.link', { href: '#', icon: 'layout-grid', current: true }, 'Overview'),
                            c('shell.link', { href: '#', icon: 'clock' }, 'Reports'),
                        )] },
                        c('shell.main', {}, c('text', { variant: 'muted' }, 'No frame: the app is the page.')),
                    ),
                ),
            ],
        },
    ],
});
