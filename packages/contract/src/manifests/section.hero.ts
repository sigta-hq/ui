import { defineComponent } from '../schema.ts';
import { c, el } from '../examples.ts';

export default defineComponent({
    name: 'section.hero',
    title: 'Section · Hero',
    tier: 'sections',
    page: 'hero',
    svelte: 'Section.Hero',
    description: 'The page opener: eyebrow, a display heading (with a highlighter mark), a lede, two calls to action and a row of inline figures, beside a visual (default: a hand-drawn sea chart). Two columns from lg; stacked below.',
    aria: 'A <section>; the heading is the page\'s <h1>. The default visual is an <svg role="img"> with a label.',
    props: {
        eyebrow: { type: 'string', default: 'Tech-partner · Hjælp, rådgivning og udvikling', description: 'Mono kicker above the heading; empty to leave it out.' },
        heading: { type: 'string', default: null, description: 'The heading as a string (HTML allowed). Without it (and without the default slot) the brand line renders: "Teknologi, oversat til forretning. Og tilbage igen." with a mark.' },
        lede: { type: 'string', default: 'Sigta er den tech-partner, du kan få: hjælp, rådgivning og udvikling til virksomheder, der ikke har deres egen tekniske ledelse. Vi taler begge sprog, forretning og teknik.', description: 'The lede; empty to leave it out.' },
        primaryLabel: { type: 'string', default: 'Book en intro-samtale', description: 'The ink call to action (with a trailing arrow); empty to leave it out.' },
        primaryHref: { type: 'string', default: 'https://calendly.com/sf-sigta/30min', description: 'Where the primary call to action goes.' },
        target: { type: 'string', default: '_blank', description: 'The primary link\'s target: a new tab by default (with rel="noopener").' },
        secondaryLabel: { type: 'string', default: 'Se hvordan vi arbejder', description: 'The outline call to action; empty to leave it out.' },
        secondaryHref: { type: 'string', default: '#hjaelp', description: 'Where the secondary call to action goes.' },
        secondaryTarget: { type: 'string', default: null, description: 'The secondary link\'s target.' },
        stats: { type: 'array', default: null, shape: '{ value: string; label: string }[]', description: 'The inline figures under the actions, a list of { value, label }. Without stats: the three brand figures; an empty list leaves the row out.' },
    },
    parts: {
        grid: { description: 'The copy column and the visual (1.15fr / 0.85fr from lg).' },
        eyebrow: { description: 'The eyebrow.' },
        lede: { description: 'The lede.' },
        actions: { description: 'The call-to-action row.' },
        arrow: { description: 'The primary call to action\'s arrow; it nudges forward on hover.' },
        stats: { description: 'The row of inline figures.' },
        visual: { description: 'The visual column.' },
    },
    slots: {
        default: { description: 'The heading as rich content (author a mark in it); wins over the heading prop.' },
        visual: { description: 'Your own visual; replaces the sea chart.' },
    },
    uses: ['eyebrow', 'heading', 'mark', 'text', 'button', 'stat'],
    usage: {
        livewire: `<sigta:section.hero />
<sigta:section.hero heading="We build software that ships." />
<sigta:section.hero :stats="[['value' => '40+', 'label' => 'projekter']]">
    Software, der <sigta:mark>holder</sigta:mark>.
    <x-slot:visual><img src="/hero.png" alt=""></x-slot:visual>
</sigta:section.hero>`,
        svelte: `<Section.Hero />
<Section.Hero heading="We build software that ships." />
<Section.Hero stats={[{ value: '40+', label: 'projekter' }]}>
    Software, der <Mark>holder</Mark>.
    {#snippet visual()}<img src="/hero.png" alt="" />{/snippet}
</Section.Hero>`,
    },
    notes: {
        shared: [
            'Brand-default Danish copy: it renders complete with no props, and every prop is optional and overrides one piece.',
            'The heading comes from the default slot (rich content, author a mark there), else the heading prop (a string, HTML allowed), else the brand line.',
        ],
        livewire: ['The visual is a named slot: <x-slot:visual>…</x-slot:visual>; the heading is the default slot.'],
        svelte: ['The visual is a snippet: {#snippet visual()}…{/snippet}; the heading is the children.'],
    },
    examples: [
        {
            label: 'Zero-config (brand defaults)',
            frame: 'wide',
            tree: [c('section.hero')],
        },
        {
            label: 'Own heading, copy, figures and visual',
            frame: 'wide',
            tree: [c('section.hero', {
                eyebrow: 'Studio · Software',
                heading: 'We build software that ships.',
                lede: 'A small studio for teams without their own technical lead.',
                primaryLabel: 'Book a call',
                secondaryLabel: 'See the work',
                secondaryHref: '#work',
                stats: [{ value: '40+', label: 'products shipped' }, { value: '1', label: 'flat hourly rate' }],
            }, {
                visual: [el('div', { class: 'aspect-[6/5] rounded-frame border border-line bg-surface-raised grid place-items-center font-mono text-[0.8rem] tracking-[0.14em] uppercase text-graphite' }, 'Your visual')],
            })],
        },
        {
            label: 'Rich heading through the default slot',
            frame: 'wide',
            tree: [c('section.hero', { stats: [] }, 'Software, der', c('mark', {}, 'holder'))],
        },
    ],
});
