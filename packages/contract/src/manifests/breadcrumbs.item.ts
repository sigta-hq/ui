import { defineComponent } from '../schema.ts';
import { c } from '../examples.ts';

export default defineComponent({
    name: 'breadcrumbs.item',
    title: 'Breadcrumbs item',
    tier: 'components',
    page: 'breadcrumbs',
    svelte: 'Breadcrumbs.Item',
    description: 'One crumb: a graphite link that inks in on hover, or — with no href, or current — the current page as ink text with aria-current="page".',
    aria: 'A link, or a span with aria-current="page".',
    props: {
        href: { type: 'string', default: null, description: 'The crumb\'s link; without it the crumb is the current page.' },
        current: { type: 'boolean', default: false, description: 'Render as the current page even with an href.' },
    },
    parts: {
        link: { description: 'The <a> of a crumb with an href.' },
        current: { description: 'The <span aria-current="page"> of the current page.' },
    },
    states: {
        current: { attribute: 'aria-current', values: ['page'], description: 'On the current page\'s span.' },
    },
    slots: { default: { description: 'The crumb text.', required: true } },
    uses: ['breadcrumbs'],
    notes: {
        livewire: ['Attributes (wire:navigate, data-*) land on the link or the current span, not the <li>.'],
        svelte: ['Attributes land on the link or the current span, not the <li>.'],
    },
    examples: [
        {
            label: 'A link and the current page',
            tree: [c('breadcrumbs', {},
                c('breadcrumbs.item', { href: '#home' }, 'Home'),
                c('breadcrumbs.item', {}, 'Settings'),
            )],
        },
    ],
});
