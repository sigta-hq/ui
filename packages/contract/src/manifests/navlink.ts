import { defineComponent } from '../schema.ts';
import { c, el } from '../examples.ts';

export default defineComponent({
    name: 'navlink',
    title: 'Navlink',
    tier: 'components',
    page: 'navbar',
    svelte: 'Navlink',
    description: 'A navigation link for the navbar: mono, graphite to ink, with an underline that grows left to right on hover; the current page keeps it drawn (aria-current="page").',
    aria: 'A link; aria-current="page" when current.',
    props: {
        current: { type: 'boolean', default: false, description: 'The current page: ink, underline drawn, aria-current="page".' },
        href: { type: 'string', default: null, native: true, description: 'The link target.' },
    },
    states: {
        current: { attribute: 'aria-current', values: ['page'], description: 'The current page.' },
    },
    slots: { default: { description: 'The link text.', required: true } },
    uses: ['navbar'],
    notes: {
        livewire: ['Attributes (wire:navigate, data-*) land on the <a>.'],
    },
    examples: [
        {
            label: 'Navlinks — hover underline, current state',
            tree: [el('div', { class: 'flex items-center gap-8' },
                c('navlink', { href: '#work', current: true }, 'Current page'),
                c('navlink', { href: '#studio' }, 'Hover me'),
                c('navlink', { href: '#journal' }, 'Another link'),
            )],
        },
        {
            label: 'Hovered',
            tree: [el('div', { class: 'flex items-center gap-8' },
                c('navlink', { href: '#work', current: true }, 'Work'),
                c('navlink', { href: '#studio' }, 'Studio'),
            )],
            interactions: [{ hover: 'a[href="#studio"]', wait: 450 }],
        },
    ],
});
