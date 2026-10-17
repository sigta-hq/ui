import { defineComponent } from '../schema.ts';
import { c, el } from '../examples.ts';

const brand = { brand: [el('a', { href: '#top', class: 'no-underline' }, c('brand', { size: 'sm' }))] };
const calendly = 'https://calendly.com/sf-sigta/30min';
const frame = (...children: ReturnType<typeof c>[]) =>
    el('div', { class: '-m-6 overflow-hidden rounded-panel' }, ...children, el('div', { class: 'h-24 bg-surface' }));

export default defineComponent({
    name: 'navbar',
    title: 'Navbar',
    tier: 'components',
    page: 'navbar',
    svelte: 'Navbar',
    description: 'The sticky, translucent site header on a hairline: brand, links and actions. Below the collapse breakpoint the links fold behind a burger into a panel under the bar.',
    aria: 'A <header> with a "Primary" <nav>; the burger is a button with aria-expanded and aria-controls pointing at the folded-out panel; Escape folds it away.',
    props: {
        ctaLabel: { type: 'string', default: null, description: 'With no actions slot, renders a call-to-action button (cta, sm) with this label.' },
        ctaHref: { type: 'string', default: null, description: 'The CTA\'s link; without it the CTA is a plain button.' },
        target: { type: 'string', default: null, description: 'Forwards onto the CTA like on a button (target="_blank" gets rel="noopener").' },
        collapse: {
            type: 'enum',
            values: ['md', 'lg'],
            default: 'md',
            description: 'The breakpoint below which the links fold behind the burger. Use lg when the links and a wide CTA do not fit on one line at tablet widths, so no link wraps onto two lines; the actions stay visible either way.',
        },
    },
    parts: {
        bar: { description: 'The centred row (site max width and gutter, 68px tall).' },
        brand: { description: 'Wraps the brand slot.' },
        links: { description: 'The "Primary" <nav> of links in the bar (hidden below the collapse breakpoint).' },
        end: { description: 'The right-hand group: actions (or the CTA) and the burger.' },
        actions: { description: 'Wraps the actions slot.' },
        toggle: { description: 'The burger button (shown below the collapse breakpoint).' },
        panel: { description: 'The folded-out "Primary" <nav> under the bar, a column of the same links.' },
    },
    states: {
        collapse: { attribute: 'data-collapse', values: ['md', 'lg'], description: 'On the header: the breakpoint the links fold below (unknown values act as md).' },
        open: { attribute: 'aria-expanded', description: 'On the burger: the panel is folded out.' },
    },
    slots: {
        default: { description: 'The links (navlinks); rendered in the bar and again in the folded-out panel.', required: true },
        brand: { description: 'The brand, at the start of the bar.' },
        actions: { description: 'Actions at the end of the bar (replaces the CTA props); stays visible on every width.' },
    },
    uses: ['navlink', 'button', 'icon'],
    notes: {
        shared: ['Put navlinks in the default slot and mark the current page with current.'],
        livewire: ['Sticky translucent header; the burger menu is a small Alpine toggle. Slots: <x-slot:brand>, default (links), <x-slot:actions>; attributes on a slot tag land on its wrapper.'],
        svelte: ['Snippets: brand, children (the links), actions.'],
    },
    examples: [
        {
            label: 'Usage — CTA from props',
            tree: [c('navbar', { ctaLabel: 'Book a call', ctaHref: calendly, target: '_blank' }, c('navlink', { href: '#work' }, 'Work'))],
        },
        {
            label: 'Brand header — brand / links / actions',
            tree: [frame(c('navbar', {}, {
                ...brand,
                actions: [c('button', { variant: 'cta', size: 'sm', href: calendly, target: '_blank' }, 'Book a project')],
            },
            c('navlink', { href: '#work', current: true }, 'Work'),
            c('navlink', { href: '#studio' }, 'Studio'),
            c('navlink', { href: '#journal' }, 'Journal'),
            c('navlink', { href: '#contact' }, 'Contact'),
            ))],
            frame: 'wide',
        },
        {
            label: 'CTA via props — target like a button',
            tree: [frame(c('navbar', { ctaLabel: 'Book a project', ctaHref: calendly, target: '_blank' }, brand,
                c('navlink', { href: '#work' }, 'Work'),
                c('navlink', { href: '#studio' }, 'Studio'),
            ))],
            frame: 'wide',
        },
        {
            label: 'collapse="lg": longer links fold behind the burger up to lg',
            tree: [frame(c('navbar', { collapse: 'lg', ctaLabel: 'Book en intro-samtale', ctaHref: calendly, target: '_blank' }, brand,
                c('navlink', { href: '#raadgivning', current: true }, 'Rådgivning'),
                c('navlink', { href: '#hjaelp' }, 'Sådan hjælper vi'),
                c('navlink', { href: '#priser' }, 'Priser'),
                c('navlink', { href: '#om' }, 'Om Sigta'),
            ))],
            frame: 'wide',
        },
        {
            label: 'Burger opened (the panel shows below the collapse breakpoint)',
            tree: [frame(c('navbar', { ctaLabel: 'Book a project', ctaHref: calendly }, brand,
                c('navlink', { href: '#work', current: true }, 'Work'),
                c('navlink', { href: '#studio' }, 'Studio'),
            ))],
            interactions: [{ script: 'document.querySelector(\'[data-part="toggle"]\').click()' }],
        },
        {
            label: 'Escape folds the panel away',
            tree: [frame(c('navbar', {}, c('navlink', { href: '#work' }, 'Work')))],
            interactions: [{ script: 'document.querySelector(\'[data-part="toggle"]\').click()' }, { press: 'Escape' }],
        },
    ],
});
