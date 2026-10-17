import { defineComponent } from '../schema.ts';
import { c, el } from '../examples.ts';

// Both runtimes listen for the contextmenu event; the browser check fires one at a fixed point.
const rightClick = (selector: string, x: number, y: number) => ({
    script: `document.querySelector(${JSON.stringify(selector)}).dispatchEvent(new MouseEvent('contextmenu', { bubbles: true, cancelable: true, clientX: ${x}, clientY: ${y} }))`,
});
const surface = el('div', { class: 'grid place-items-center h-40 bg-surface border-[1.5px] border-dashed border-line rounded-panel font-mono text-[0.76rem] text-graphite select-none' }, 'Right-click anywhere in here');
const items = [
    c('dropdown.item', { icon: 'pencil', kbd: '⌘R' }, 'Rename'),
    c('dropdown.item', { icon: 'copy' }, 'Duplicate'),
    c('dropdown.item', { icon: 'external-link', href: '#top' }, 'Open in new tab'),
    c('dropdown.separator'),
    c('dropdown.item', { icon: 'trash-2', variant: 'danger' }, 'Delete'),
];

export default defineComponent({
    name: 'context-menu',
    title: 'Context menu',
    tier: 'components',
    page: 'context-menu',
    svelte: 'ContextMenu',
    description: 'A right-click surface that opens a dropdown-styled menu at the pointer, kept clear of the viewport edges. Reuses dropdown.item and dropdown.separator.',
    aria: 'role="menu" opened on contextmenu with focus on its first item; ArrowUp / ArrowDown wrap, Home / End jump; Escape or a click outside closes it.',
    props: {},
    parts: {
        menu: { description: 'The fixed menu (role="menu") at the pointer; left / top are set inline.' },
    },
    slots: {
        default: { description: 'The surface that listens for the right-click.', required: true },
        menu: { description: 'The dropdown.items.', required: true },
    },
    uses: ['dropdown.item', 'dropdown.separator'],
    notes: {
        shared: ['The items are dropdown.item and dropdown.separator; an item closes the menu when chosen.'],
        livewire: ['The default slot is the surface; <x-slot:menu> holds the items.'],
        svelte: ['children is the surface; {#snippet menu()} holds the items.'],
    },
    examples: [
        {
            label: 'Right-click the surface',
            tree: [c('context-menu', {}, { menu: items }, surface)],
        },
        {
            label: 'Opened at the pointer',
            tree: [c('context-menu', {}, { menu: items }, surface)],
            interactions: [rightClick('[data-sigta="context-menu"] > div', 220, 90)],
        },
        {
            label: 'Keyboard: arrows move, End jumps, Escape closes',
            tree: [c('context-menu', {}, { menu: items }, surface)],
            interactions: [rightClick('[data-sigta="context-menu"] > div', 220, 90), { press: ['ArrowDown', 'ArrowDown'] }, { press: 'End' }],
        },
        {
            label: 'Closed again with Escape',
            tree: [c('context-menu', {}, { menu: items }, surface)],
            interactions: [rightClick('[data-sigta="context-menu"] > div', 220, 90), { press: 'Escape' }],
        },
        {
            label: 'On a table row',
            tree: [c('context-menu', {}, {
                menu: [
                    c('dropdown.item', { icon: 'download' }, 'Download'),
                    c('dropdown.item', { icon: 'archive' }, 'Archive'),
                ],
            }, el('div', { class: 'flex items-center justify-between bg-surface border border-line rounded-panel px-4 py-3' },
                el('span', { class: 'font-body text-[0.95rem]' }, 'Q2 revenue report.pdf'),
                el('span', { class: 'font-mono text-[0.68rem] text-graphite' }, '2.4 MB'),
            ))],
        },
        {
            label: 'Near the viewport edge',
            tree: [c('context-menu', {}, { menu: items }, surface)],
            interactions: [rightClick('[data-sigta="context-menu"] > div', 1090, 790)],
        },
    ],
});
