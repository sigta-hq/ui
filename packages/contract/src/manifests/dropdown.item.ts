import { defineComponent } from '../schema.ts';
import { c } from '../examples.ts';

const menu = (...items: ReturnType<typeof c>[]) =>
    c('dropdown', {}, { trigger: [c('button', {}, 'Options')] }, ...items);

export default defineComponent({
    name: 'dropdown.item',
    title: 'Dropdown item',
    tier: 'components',
    page: 'dropdown',
    svelte: 'Dropdown.Item',
    description: 'One menu row in mono: an optional icon, the label and a keyboard hint. Renders an <a> with an href, else a button that closes its menu when clicked. Focused, it fills with ink.',
    aria: 'role="menuitem", out of the tab order (tabindex -1): the menu moves focus. aria-disabled="true" dims it and skips it.',
    props: {
        icon: { type: 'string', default: null, description: 'A leading icon name.' },
        variant: { type: 'enum', values: ['default', 'danger'], default: 'default', description: 'danger = red text, for destructive actions.' },
        kbd: { type: 'string', default: null, description: 'A keyboard hint at the end of the row (⌘E).' },
        href: { type: 'string', default: null, native: true, description: 'Renders an <a role="menuitem">; else a <button role="menuitem">.' },
    },
    parts: {
        icon: { description: 'The leading icon.' },
        label: { description: 'The label (grows).' },
        kbd: { description: 'The keyboard hint.' },
    },
    states: {
        variant: { attribute: 'data-variant', values: ['default', 'danger'], description: 'On the item.' },
        disabled: { attribute: 'aria-disabled', description: 'Dimmed, not clickable, skipped by the arrow keys.' },
    },
    slots: { default: { description: 'The label.', required: true } },
    uses: ['dropdown', 'icon'],
    notes: {
        shared: ['Also the item of popover and context-menu: a button item closes whichever of them holds it.'],
        livewire: ['A button item runs your x-on:click / wire:click first, then closes the menu.'],
        svelte: ['A button item calls your onclick, then closes the menu.'],
    },
    examples: [
        {
            label: 'Icons, hints, a link, danger and disabled',
            tree: [menu(
                c('dropdown.item', { icon: 'pencil', kbd: '⌘E' }, 'Edit'),
                c('dropdown.item', { href: '#top', icon: 'external-link' }, 'Open link'),
                c('dropdown.item', { icon: 'archive', 'aria-disabled': 'true' }, 'Archive (disabled)'),
                c('dropdown.item', { icon: 'trash-2', variant: 'danger' }, 'Delete'),
            )],
            interactions: [{ click: '[data-part="trigger"] [data-sigta="button"]' }],
        },
        {
            label: 'Hovered, and a focused danger item',
            tree: [menu(
                c('dropdown.item', { icon: 'pencil' }, 'Edit'),
                c('dropdown.item', { icon: 'copy' }, 'Duplicate'),
                c('dropdown.item', { icon: 'trash-2', variant: 'danger' }, 'Delete'),
            )],
            interactions: [{ click: '[data-part="trigger"] [data-sigta="button"]' }, { press: 'End' }, { hover: '[role="menuitem"]:nth-of-type(2)' }],
        },
    ],
});
