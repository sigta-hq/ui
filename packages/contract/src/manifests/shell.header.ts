import { defineComponent } from '../schema.ts';
import { c } from '../examples.ts';

const headed = c('shell', {},
    {
        header: [c('shell.header', {},
            {
                brand: [c('brand', { size: 'sm', glyphOnly: true }), 'Plumb'],
                actions: [c('avatar', { name: 'Ada Lovelace', size: 'sm' })],
            },
        )],
        sidebar: [c('shell.sidebar', {},
            c('shell.link', { href: '#', icon: 'layout-grid', current: true }, 'Overview'),
            c('shell.link', { href: '#', icon: 'clock' }, 'Reports'),
        )],
    },
    c('shell.main', {}, c('text', { variant: 'muted' }, 'The page starts straight with its content.')),
);

export default defineComponent({
    name: 'shell.header',
    title: 'Shell header',
    tier: 'layout',
    page: 'shell',
    svelte: 'Shell.Header',
    description: 'A full-width row above the shell\'s sidebar and main: brand on the left, actions on the right. Below the tablet breakpoint it is the phone\'s top bar and carries the burger that opens the sidebar drawer.',
    aria: 'A <header>; the burger is labelled "Toggle navigation" and mirrors the drawer in aria-expanded.',
    props: {},
    parts: {
        brand: { description: 'The brand lockup.' },
        end: { description: 'The actions + burger cluster on the right.' },
        toggle: { description: 'The burger (phones only).' },
    },
    states: {
        expanded: { attribute: 'aria-expanded', description: 'On the burger: the sidebar drawer is open.' },
    },
    slots: {
        brand: { description: 'The lockup on the left.' },
        actions: { description: 'Account menu, quick actions — on the right, before the burger.' },
    },
    events: {
        toggle: { description: 'The burger asks the sidebar to toggle its drawer.', blade: 'sigta-shell-toggle', svelte: false, detail: '{}' },
    },
    uses: ['shell', 'shell.sidebar', 'icon'],
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
            'Pass it in the shell\'s optional header slot: a full-width row above the sidebar and main, brand on the left, actions on the right. Below the tablet breakpoint it is the phone\'s top bar and carries the burger that opens the sidebar drawer (the sidebar then drops its own brand row, actions and burger, and is navigation alone). A shell without header is unchanged.',
            'The sidebar owns the drawer\'s state: the burger sends a sigta-shell-toggle window event and mirrors the sigta-shell-nav { open } the sidebar reports back, so header and sidebar talk wherever they sit.',
        ],
        livewire: ['Slots: <x-slot:brand> and <x-slot:actions>.'],
        svelte: ['Snippets: brand and actions.'],
    },
    examples: [
        {
            label: 'Header row above the sidebar and main',
            tree: [headed],
        },
        {
            label: 'Its burger opens the sidebar drawer and mirrors it',
            tree: [headed],
            interactions: [{ script: 'document.querySelector(\'[data-scope="shell.header"][data-part="toggle"]\').click()' }],
        },
    ],
});
