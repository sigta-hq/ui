import { defineComponent } from '../schema.ts';
import { c, el } from '../examples.ts';

export default defineComponent({
    name: 'section.pricing',
    title: 'Section · Pricing',
    tier: 'sections',
    page: 'pricing',
    svelte: 'Section.Pricing',
    description: 'How you charge: a section header over tiers side by side (stacked on a phone). Each tier is a non-clipping panel — so a hand-note can overhang it — with a mono label, a title, a big figure (optionally marked) with its suffix, and body paragraphs. Ships the two brand tracks.',
    aria: 'A <section>; each tier\'s title is an <h3>.',
    props: {
        heading: { type: 'string', default: 'To måder at samarbejde på.', description: 'The section heading.' },
        lede: { type: 'string', default: 'Én filosofi bag begge: du skal altid vide, hvad du betaler for.', description: 'The lede beside the heading.' },
        tiers: {
            type: 'array',
            default: null,
            shape: '{ label?: string; title?: string; figure?: string; figureSuffix?: string; mark?: boolean; annot?: string; body?: string[] }[]',
            description: 'The tiers, a list of { label, title, figure, figureSuffix, mark, annot, body } (each optional; body is a list of paragraphs). mark highlights the figure; annot hangs a hand-note over the panel\'s top edge (hidden on a phone). Without tiers: the two brand tracks.',
        },
    },
    parts: {
        header: { description: 'The section header.' },
        tiers: { description: 'The grid of tiers (two columns from md).' },
        note: { description: 'A tier\'s overhanging hand-note (an annot).' },
        label: { description: 'A tier\'s mono label.' },
        title: { description: 'A tier\'s title (an h3).' },
        figure: { description: 'The figure row: the figure and its suffix, wrapping as whole blocks.' },
        suffix: { description: 'The mono suffix after the figure.' },
        body: { description: 'Each body paragraph.' },
        aside: { description: 'The aside beside the tiers.' },
    },
    slots: {
        aside: { description: 'Fills the second column beside a single tier, e.g. a call to book for a reader still deciding.' },
    },
    uses: ['section-header', 'panel', 'heading', 'mark', 'annot'],
    usage: {
        livewire: `<sigta:section.pricing />
<sigta:section.pricing heading="Én fast pris." :tiers="[
    ['title' => 'Teknisk vurdering', 'figure' => '60.000 kr.', 'figureSuffix' => 'ekskl. moms', 'mark' => true, 'body' => ['Halvdelen ved opstart.']],
]">
    <x-slot:aside>
        <p>I tvivl? Det finder vi ud af på 30 minutter.</p>
        <sigta:button variant="cta" href="/book" class="self-start">Book en samtale</sigta:button>
    </x-slot:aside>
</sigta:section.pricing>`,
        svelte: `<Section.Pricing />
<Section.Pricing heading="Én fast pris." tiers={[
    { title: 'Teknisk vurdering', figure: '60.000 kr.', figureSuffix: 'ekskl. moms', mark: true, body: ['Halvdelen ved opstart.'] },
]}>
    {#snippet aside()}
        <p>I tvivl? Det finder vi ud af på 30 minutter.</p>
        <Button variant="cta" href="/book" class="self-start">Book en samtale</Button>
    {/snippet}
</Section.Pricing>`,
    },
    notes: {
        shared: [
            'Brand-default Danish copy: it renders complete with no props, and every prop is optional and overrides one piece.',
            'A long figure suffix drops below the figure as one block rather than breaking inside the figure\'s line.',
        ],
        livewire: ['The aside is a named slot: <x-slot:aside>…</x-slot:aside>. Attributes on the slot tag (class, id) land on the aside wrapper, a centred column.'],
        svelte: ['The aside is a snippet: {#snippet aside()}…{/snippet}, rendered in a centred column beside the tiers.'],
    },
    examples: [
        {
            label: 'Zero-config (brand defaults)',
            frame: 'wide',
            tree: [c('section.pricing')],
        },
        {
            label: 'A long figure suffix drops below the figure as one block',
            frame: 'wide',
            tree: [c('section.pricing', {
                heading: 'Én fast pris.',
                tiers: [
                    { label: 'Hele forløbet', title: 'Teknisk vurderingsplan', figure: '60.000 kr.', figureSuffix: 'ekskl. moms', mark: true, body: ['Halvdelen ved opstart, resten ved gennemgang.'] },
                    { label: 'Hvis vi ikke er de rette', title: 'Anbefaling og overdragelse', figure: '40.000 kr.', figureSuffix: '+ 20.000 kr. til Leverandør-sparring, ekskl. moms', body: ['Resten bliver til sparring, eller du får dem tilbage.'] },
                ],
            })],
        },
        {
            label: 'One tier with the aside slot beside it',
            frame: 'wide',
            tree: [c('section.pricing', {
                heading: 'Én fast pris.',
                tiers: [
                    { title: 'Teknisk vurderingsplan', figure: '60.000 kr.', figureSuffix: 'ekskl. moms', mark: true, body: ['Halvdelen ved opstart, resten ved gennemgang.'] },
                ],
            }, {
                aside: [
                    el('p', { class: 'text-[1.1rem]' }, 'I tvivl om, det passer til dig? Det finder vi ud af på 30 minutter.'),
                    c('button', { variant: 'cta', href: 'https://calendly.com/sf-sigta/30min', target: '_blank', class: 'self-start' }, 'Book en gratis intro-samtale'),
                ],
            })],
        },
    ],
});
