import { defineComponent } from '../schema.ts';
import { c } from '../examples.ts';

export default defineComponent({
    name: 'breadcrumbs',
    title: 'Breadcrumbs',
    tier: 'components',
    page: 'breadcrumbs',
    svelte: 'Breadcrumbs',
    description: 'A breadcrumb trail: a <nav> around an ordered list of breadcrumbs.items in small mono type, separated by graphite slashes.',
    aria: 'A <nav aria-label="Breadcrumb"> landmark around an <ol>; the current page carries aria-current="page".',
    props: {},
    parts: {
        list: { description: 'The <ol> of crumbs.' },
    },
    slots: { default: { description: 'The breadcrumbs.items, first to current.', required: true } },
    uses: ['breadcrumbs.item'],
    notes: {
        shared: ['Container only (<nav><ol>); attributes land on the <nav>, and an aria-label replaces "Breadcrumb".'],
    },
    examples: [
        {
            label: 'Trail — last item is the current page',
            tree: [c('breadcrumbs', {},
                c('breadcrumbs.item', { href: '#home' }, 'Home'),
                c('breadcrumbs.item', { href: '#projects' }, 'Projects'),
                c('breadcrumbs.item', {}, 'Marginalia v3'),
            )],
        },
        {
            label: 'Two levels',
            tree: [c('breadcrumbs', {},
                c('breadcrumbs.item', { href: '#studio' }, 'Studio'),
                c('breadcrumbs.item', {}, 'Practices'),
            )],
        },
        {
            label: 'Explicit current prop (href kept for context, rendered as text)',
            tree: [c('breadcrumbs', {},
                c('breadcrumbs.item', { href: '#home' }, 'Home'),
                c('breadcrumbs.item', { href: '#settings', current: true }, 'Settings'),
            )],
        },
        {
            label: 'Hovering a crumb',
            tree: [c('breadcrumbs', {},
                c('breadcrumbs.item', { href: '#home' }, 'Home'),
                c('breadcrumbs.item', { href: '#projects' }, 'Projects'),
                c('breadcrumbs.item', {}, 'Marginalia v3'),
            )],
            interactions: [{ hover: 'a[href="#projects"]' }],
        },
    ],
});
