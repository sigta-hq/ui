import { defineComponent } from '../schema.ts';
import { c, el } from '../examples.ts';

const faq = [
    ['Hvad koster det?', 'Én fast timepris: 800 kr./time ekskl. moms. Ingen minimumskøb, og intro-samtalen er gratis. For skræddersyede værktøjer aftaler vi en fast månedspris, før vi går i gang.'],
    ['Hvor meget tid skal vi bruge?', 'Det afhænger af opgaven. Nogle kunder bruger et par timer om måneden til sparring, andre mere i perioder. Vi skalerer op og ned efter behov.'],
    ['Hvad er en tech-partner?', 'En fast partner, der dækker hele spektret fra rådgivning og sparring til hands-on udvikling. For mange virksomheder fungerer det som en teknisk direktør på deltid, internationalt kaldet en fractional CTO, men samarbejdet former sig efter jeres behov.'],
];

export default defineComponent({
    name: 'accordion',
    title: 'Accordion',
    tier: 'components',
    page: 'accordion',
    svelte: 'Accordion',
    description: 'A group of disclosure items (the FAQ look) on native <details>/<summary>. Its name forwards to every item, so the browser keeps one open at a time. No JS.',
    aria: 'Native <details>/<summary> disclosure: Enter / Space toggle, announced as expanded / collapsed.',
    props: {
        name: { type: 'string', default: null, description: 'Forwards to every item\'s <details name> for native exclusive-open; omit it for independent items.' },
    },
    slots: { default: { description: 'The accordion.items.', required: true } },
    uses: ['accordion.item'],
    notes: {
        shared: ['Use it (with accordion.item) for an accordion or FAQ instead of styling <details> by hand.'],
        livewire: ['Items read the group\'s name with @aware: an item\'s own name wins, else the nearest enclosing component that was given a name.'],
        svelte: ['Items read the group\'s name from context; an item\'s own name wins.'],
    },
    examples: [
        {
            label: 'Usage',
            tree: [c('accordion', { name: 'faq' }, c('accordion.item', { heading: 'How does billing work?' }, 'Monthly, cancel anytime.'))],
        },
        {
            label: 'FAQ — native details/summary, exclusive open via name',
            tree: [c('accordion', { name: 'faq' }, ...faq.map(([q, a]) => c('accordion.item', { heading: q }, el('p', {}, a))))],
        },
        {
            label: 'Independent items (no name) — one open by default',
            tree: [c('accordion', {},
                c('accordion.item', { heading: 'Open by default', open: true },
                    el('p', {}, 'This item was rendered with the native ', el('code', { class: 'font-mono text-[0.8rem]' }, 'open'), ' attribute forwarded onto the details element.'),
                ),
                c('accordion.item', { heading: 'Stays independent' }, el('p', {}, 'Without a group name, opening one item does not close the others.')),
            )],
        },
        {
            label: 'Opening one closes the other (exclusive)',
            tree: [c('accordion', { name: 'kit-exclusive' },
                c('accordion.item', { heading: 'First question', open: true }, el('p', {}, 'Open on load.')),
                c('accordion.item', { heading: 'Second question' }, el('p', {}, 'Opening this one closes the first.')),
            )],
            interactions: [{ click: '[data-sigta="accordion.item"]:nth-of-type(2) > summary' }],
        },
    ],
});
