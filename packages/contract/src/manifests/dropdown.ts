import { defineComponent } from '../schema.ts';
import { c, el } from '../examples.ts';

const open = { click: '[data-part="trigger"] [data-sigta="button"]' };
const positioned = (position: string, wrapper: string) => ({
    label: `Position ${position}`,
    tree: [el('div', { class: wrapper },
        c('dropdown', { position }, { trigger: [c('button', {}, 'Options ', c('icon', { name: 'chevron-down', size: 'sm' }))] },
            c('dropdown.item', { icon: 'pencil' }, 'Edit'),
            c('dropdown.item', { icon: 'copy' }, 'Duplicate'),
        ),
    )],
    interactions: [open],
});

export default defineComponent({
    name: 'dropdown',
    title: 'Dropdown',
    tier: 'components',
    page: 'dropdown',
    svelte: 'Dropdown',
    description: 'A menu button: its trigger opens a menu of dropdown.items on raised paper, below or above it. Handles open, Escape, click outside and roving focus.',
    aria: 'WAI-ARIA menu button: click, Enter / Space or ArrowDown on the trigger opens the menu on its first item (ArrowUp: the last); ArrowUp / ArrowDown wrap, Home / End jump; Escape closes it and returns focus to the trigger; Tab or a click outside closes it.',
    props: {
        position: { type: 'enum', values: ['bottom-start', 'bottom-end', 'top-start', 'top-end'], default: 'bottom-start', description: 'Where the menu opens against the trigger; unknown values act as bottom-start.' },
        open: { type: 'boolean', default: false, bindable: true, blade: false, description: 'bind:open reads and sets whether the menu is open.' },
    },
    parts: {
        trigger: { description: 'Wraps the trigger slot; carries aria-haspopup and aria-expanded.' },
        menu: { description: 'The menu (role="menu"), hidden until open.' },
    },
    states: {
        position: { attribute: 'data-position', values: ['bottom-start', 'bottom-end', 'top-start', 'top-end'], description: 'On the root: where the menu opens.' },
        open: { attribute: 'aria-expanded', description: 'On the trigger wrapper.' },
    },
    slots: {
        trigger: { description: 'What opens the menu, usually a button.', required: true },
        default: { description: 'The dropdown.items and dropdown.separators.', required: true },
    },
    uses: ['dropdown.item', 'dropdown.separator', 'button'],
    notes: {
        shared: ['Use it (with {{tag:dropdown.item}} and {{tag:dropdown.separator}}) instead of a custom menu.'],
        livewire: [
            'The trigger goes in <x-slot:trigger> (required); the items go straight in the default slot. There is no menu slot (that is the context menu\'s).',
            'The sigtaDropdown Alpine component handles open / Escape / click outside / roving focus.',
        ],
        svelte: ['The trigger is a snippet ({#snippet trigger()}<Button>Options</Button>{/snippet}, required); the items are the children. bind:open to open or close it yourself.'],
    },
    examples: [
        {
            label: 'Usage',
            tree: [c('dropdown', {}, { trigger: [c('button', {}, 'Open')] },
                c('dropdown.item', { href: '/profile', icon: 'user' }, 'Profile'),
                c('dropdown.separator'),
                c('dropdown.item', { variant: 'danger', icon: 'log-out' }, 'Sign out'),
            )],
        },
        {
            label: 'Menu',
            tree: [el('div', { class: 'flex gap-6' },
                c('dropdown', {}, { trigger: [c('button', {}, 'Options ', c('icon', { name: 'chevron-down', size: 'sm' }))] },
                    c('dropdown.item', { icon: 'pencil', kbd: '⌘E' }, 'Edit'),
                    c('dropdown.item', { icon: 'copy' }, 'Duplicate'),
                    c('dropdown.separator'),
                    c('dropdown.item', { icon: 'trash-2', variant: 'danger' }, 'Delete'),
                ),
                c('dropdown', { position: 'bottom-end' }, { trigger: [c('button', { variant: 'ghost' }, c('icon', { name: 'ellipsis' }))] },
                    c('dropdown.item', { href: '#top', icon: 'external-link' }, 'Open link'),
                    c('dropdown.item', { icon: 'archive' }, 'Archive'),
                ),
            )],
        },
        {
            label: 'Opened by a click',
            tree: [c('dropdown', {}, { trigger: [c('button', {}, 'Options ', c('icon', { name: 'chevron-down', size: 'sm' }))] },
                c('dropdown.item', { icon: 'pencil', kbd: '⌘E' }, 'Edit'),
                c('dropdown.item', { icon: 'copy' }, 'Duplicate'),
                c('dropdown.separator'),
                c('dropdown.item', { icon: 'trash-2', variant: 'danger' }, 'Delete'),
            )],
            interactions: [open],
        },
        {
            label: 'Keyboard: ArrowDown opens, arrows move, End jumps to the last item',
            tree: [c('dropdown', {}, { trigger: [c('button', {}, 'Options')] },
                c('dropdown.item', { icon: 'pencil' }, 'Edit'),
                c('dropdown.item', { icon: 'copy' }, 'Duplicate'),
                c('dropdown.item', { icon: 'archive' }, 'Archive'),
                c('dropdown.separator'),
                c('dropdown.item', { icon: 'trash-2', variant: 'danger' }, 'Delete'),
            )],
            interactions: [{ focus: '[data-part="trigger"] [data-sigta="button"]', press: 'ArrowDown' }, { press: ['ArrowDown', 'ArrowUp', 'ArrowUp'] }, { press: 'End' }],
        },
        {
            label: 'Escape closes it, focus back on the trigger',
            tree: [c('dropdown', {}, { trigger: [c('button', {}, 'Options')] }, c('dropdown.item', {}, 'Edit'), c('dropdown.item', {}, 'Duplicate'))],
            interactions: [open, { press: ['ArrowDown', 'Escape'] }],
        },
        {
            label: 'Choosing an item closes it',
            tree: [c('dropdown', {}, { trigger: [c('button', {}, 'Options')] }, c('dropdown.item', {}, 'Edit'), c('dropdown.item', {}, 'Duplicate'))],
            interactions: [open, { click: '[role="menuitem"]:nth-of-type(2)' }],
        },
        positioned('bottom-end', 'flex justify-end pb-32'),
        positioned('top-start', 'pt-32'),
        positioned('top-end', 'flex justify-end pt-32'),
    ],
});
