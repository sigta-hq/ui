import { defineComponent } from '../schema.ts';
import { c, el } from '../examples.ts';
import { sections } from '../sections.ts';

export default defineComponent({
    name: 'section.about',
    title: 'Section · About',
    tier: 'sections',
    page: 'about',
    svelte: 'Section.About',
    description: 'The "Om Sigta" block: a device-framed portrait beside a text column (eyebrow, heading, graphite paragraphs); the columns stack on a phone. Without a portrait it shows an SVG placeholder with a hand-note.',
    aria: 'A <section>; the heading is an <h2>, the placeholder portrait an <svg role="img"> with a label.',
    props: {
        eyebrow: { type: 'string', default: 'Om Sigta · Manden bag', description: 'Mono kicker above the heading; empty to leave it out.' },
        heading: { type: 'string', default: 'Jeg hedder Søren, og jeg taler begge sprog.', description: 'The heading (an h2 at the xl size).' },
        paragraphs: { type: 'array', default: sections.about.paragraphs, shape: 'string[]', description: 'The body paragraphs, in graphite at a 52ch measure. Defaults to the four brand paragraphs.' },
    },
    parts: {
        wrap: { description: 'The centred page-width wrap.' },
        grid: { description: 'The two columns (portrait 0.8fr, text 1.2fr from md; stacked below).' },
        portrait: { description: 'The portrait column (positions the hand-note).' },
        frame: { description: 'The default device frame around the placeholder.' },
        image: { description: 'The placeholder SVG.' },
        note: { description: 'The hand-note under the placeholder (an annot).' },
        eyebrow: { description: 'The eyebrow.' },
        heading: { description: 'The heading.' },
        paragraph: { description: 'Each body paragraph.' },
    },
    slots: {
        portrait: { description: 'Your own framed portrait; replaces the placeholder and its hand-note.' },
    },
    uses: ['eyebrow', 'heading', 'annot'],
    usage: {
        livewire: `<sigta:section.about />
<sigta:section.about eyebrow="Om studiet · Holdet bag" heading="Vi er et lille studie med store idéer." :paragraphs="['Vi bygger software, der holder.']">
    <x-slot:portrait><img src="/team.jpg" alt="Holdet" class="rounded-frame border border-line shadow-paper"></x-slot:portrait>
</sigta:section.about>`,
        svelte: `<Section.About />
<Section.About eyebrow="Om studiet · Holdet bag" heading="Vi er et lille studie med store idéer." paragraphs={['Vi bygger software, der holder.']}>
    {#snippet portrait()}<img src="/team.jpg" alt="Holdet" class="rounded-frame border border-line shadow-paper" />{/snippet}
</Section.About>`,
    },
    notes: {
        shared: [
            'Brand-default Danish copy: it renders complete with no props, and every prop is optional and overrides one piece. Key props: eyebrow, heading, paragraphs; the portrait slot.',
            'The default portrait is an SVG placeholder (the real photo lives in the site, not the package); pass the portrait slot to drop in a real framed image.',
        ],
        livewire: ['The portrait is a named slot: <x-slot:portrait>…</x-slot:portrait>.'],
        svelte: ['The portrait is a snippet: {#snippet portrait()}…{/snippet}.'],
    },
    examples: [
        {
            label: 'Om Sigta — zero-config (SVG portrait placeholder)',
            frame: 'wide',
            tree: [c('section.about')],
        },
        {
            label: 'Custom copy + your own framed portrait',
            frame: 'wide',
            tree: [c('section.about', { eyebrow: 'Om studiet · Holdet bag', heading: 'Vi er et lille studie med store idéer.' }, {
                portrait: [el('div', { class: 'overflow-hidden rounded-frame border border-line bg-surface-raised shadow-paper aspect-[686/800] grid place-items-center text-graphite font-mono text-[0.8rem] tracking-[0.14em] uppercase' }, 'Dit portræt her')],
            })],
        },
    ],
});
