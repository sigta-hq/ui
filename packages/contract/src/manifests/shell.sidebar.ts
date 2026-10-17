import { defineComponent } from '../schema.ts';
import { c } from '../examples.ts';

const brand = [c('brand', { size: 'sm', glyphOnly: true }), 'Plumb'];
const links = [
    c('shell.link', { href: '#', icon: 'layout-grid', current: true }, 'Overview'),
    c('shell.link', { href: '#', icon: 'trending-up' }, 'Trends'),
    c('shell.link', { href: '#', icon: 'clock' }, 'Reports'),
];
const frame = (...sidebarSlots: Parameters<typeof c>[2][]) => c('shell', {},
    { sidebar: [c('shell.sidebar', {}, ...sidebarSlots)] },
    c('shell.main', {}, c('text', { variant: 'muted' }, 'Main content.')),
);

export default defineComponent({
    name: 'shell.sidebar',
    title: 'Shell sidebar',
    tier: 'layout',
    page: 'shell',
    svelte: 'Shell.Sidebar',
    description: 'The shell\'s nav column on paper. A full rail on desktop; below the tablet breakpoint it becomes a top bar whose burger slides the nav in as a drawer over the page, with a scrim behind it.',
    aria: 'An <aside> landmark named by its aria-label ("App navigation" by default). The open drawer is a modal dialog (role="dialog", aria-modal="true") with a focus trap; the burger carries aria-expanded and aria-controls.',
    props: {
        open: { type: 'boolean', default: false, bindable: true, blade: false, description: 'Svelte: bind:open — whether the drawer is open (below the tablet breakpoint; the rail ignores it).' },
    },
    parts: {
        top: { description: 'The top row: brand, then the actions + burger cluster (dropped under a shell header).' },
        brand: { description: 'The brand lockup.' },
        end: { description: 'The actions + burger cluster at the far end of the top row.' },
        toggle: { description: 'The burger (phones only).' },
        scrim: { description: 'Dims the page behind the open drawer; tapping it closes the drawer.' },
        nav: { description: 'The nav body: the rail\'s column on desktop, the drawer below the breakpoint.' },
        dismiss: { description: 'The drawer\'s own close row (phones only).' },
        close: { description: 'The drawer\'s close button.' },
    },
    states: {
        expanded: { attribute: 'aria-expanded', description: 'On the burger: the drawer is open.' },
        dialog: { attribute: 'aria-modal', description: 'On the nav while it is an open drawer (with role="dialog").' },
    },
    slots: {
        default: { description: 'The nav: shell.links (and anything else that belongs in it).', required: true },
        brand: { description: 'The lockup at the top of the rail.' },
        actions: { description: 'One or two icon-sized controls at the far end of the top row, hard against the burger; they stay visible while the nav is collapsed.' },
    },
    events: {
        toggle: { description: 'Toggles the drawer from anywhere (the shell header\'s burger sends it).', blade: 'sigta-shell-toggle', svelte: false, detail: '{}' },
        nav: { description: 'The drawer opened or closed (the shell header\'s burger mirrors it).', blade: 'sigta-shell-nav', svelte: false, detail: '{ open: boolean }' },
    },
    uses: ['shell.link', 'icon'],
    usage: {
        livewire: `<sigta:shell.sidebar aria-label="Main menu">
    <x-slot:brand><sigta:brand size="sm" glyph-only /> Plumb</x-slot:brand>
    <x-slot:actions>
        <sigta:button variant="ghost" size="sm" aria-label="Quick actions"><sigta:icon name="zap" size="sm" /></sigta:button>
    </x-slot:actions>
    <sigta:shell.link href="/" icon="layout-grid" current>Overview</sigta:shell.link>
    <sigta:shell.link href="/reports" icon="clock">Reports</sigta:shell.link>
</sigta:shell.sidebar>`,
        svelte: `<Shell.Sidebar aria-label="Main menu" bind:open={navOpen}>
    {#snippet brand()}<Brand size="sm" glyphOnly /> Plumb{/snippet}
    {#snippet actions()}
        <Button variant="ghost" size="sm" aria-label="Quick actions"><Icon name="zap" size="sm" /></Button>
    {/snippet}
    <Shell.Link href="/" icon="layout-grid" current>Overview</Shell.Link>
    <Shell.Link href="/reports" icon="clock">Reports</Shell.Link>
</Shell.Sidebar>`,
    },
    notes: {
        shared: [
            'A full rail on desktop; below the tablet breakpoint it becomes a top bar whose burger slides the nav in as a drawer over the page, with a scrim behind it.',
            'The open drawer is a modal dialog: focus is trapped in it, the page behind stops scrolling, and it closes on the scrim, on Escape and on its own close button. None of that applies to the desktop rail. Give the sidebar an aria-label to name both the landmark and the drawer (defaults to "App navigation").',
            'actions sits at the far end of the top row, hard against the burger, and stays visible when the nav is collapsed. Use it for the one or two controls that must be reachable without opening the nav (a search or quick-action trigger); everything else belongs in the nav. Keep it to icon-sized controls: the row is as narrow as the rail on desktop.',
            'Inside a shell with a header, the header carries the brand, actions and burger, so this row is dropped and the sidebar is navigation alone.',
            'A sigta-shell-toggle window event toggles the drawer; the sidebar reports each change as a sigta-shell-nav window event { open }. The shell header\'s burger talks to it that way, so the two can sit anywhere on the page.',
        ],
        livewire: [
            'Alpine-driven, no wiring needed. The drawer also closes after a wire:navigate visit.',
            'Slots: <x-slot:brand>, <x-slot:actions>, and the default slot for the nav.',
        ],
        svelte: [
            'Works by itself; bind:open when your code opens or closes the drawer. It closes after an Inertia visit (inertia:navigate); in another router, set open = false after navigating.',
            'Snippets: brand, actions, and children for the nav.',
        ],
    },
    examples: [
        {
            label: 'Brand, actions and nav',
            tree: [frame(
                { brand, actions: [c('button', { variant: 'ghost', size: 'sm', 'aria-label': 'Quick actions' }, c('icon', { name: 'zap', size: 'sm' }))] },
                ...links,
            )],
        },
        {
            label: 'Named by its aria-label',
            tree: [c('shell', {},
                { sidebar: [c('shell.sidebar', { 'aria-label': 'Main menu' }, { brand }, ...links)] },
                c('shell.main', {}, c('text', { variant: 'muted' }, 'The landmark and the drawer are both "Main menu".')),
            )],
        },
        {
            label: 'Drawer opened by the toggle event',
            tree: [frame({ brand }, ...links)],
            interactions: [{ script: 'window.dispatchEvent(new CustomEvent("sigta-shell-toggle"))' }],
        },
        {
            label: 'Burger toggles it, Escape closes it',
            tree: [frame({ brand }, ...links)],
            interactions: [
                { script: 'document.querySelector(\'[data-scope="shell.sidebar"][data-part="toggle"]\').click()' },
                { press: 'Escape' },
            ],
        },
        {
            label: 'Closed from the drawer\'s own close button',
            tree: [frame({ brand }, ...links)],
            interactions: [
                { script: 'document.querySelector(\'[data-scope="shell.sidebar"][data-part="toggle"]\').click()' },
                { script: 'document.querySelector(\'[data-scope="shell.sidebar"][data-part="close"]\').click()' },
            ],
        },
    ],
});
