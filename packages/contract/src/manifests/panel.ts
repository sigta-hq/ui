import { defineComponent } from '../schema.ts';
import { c, el } from '../examples.ts';

const kicker = (text: string) => el('span', { class: 'mb-[1.1rem] block font-mono text-[0.66rem] tracking-[0.14em] uppercase text-graphite' }, text);

export default defineComponent({
    name: 'panel',
    title: 'Panel',
    tier: 'layout',
    page: 'panel',
    svelte: 'Panel',
    description: 'Bordered surface without overflow-hidden — so overhanging annotations/badges show. Static (no hover lift); use it where the card would clip. variant="inverse" is a dark ink band.',
    aria: 'A <div>.',
    props: {
        variant: { type: 'enum', values: ['default', 'inverse'], default: 'default', description: 'default (raised paper on a hairline) or inverse (dark ink band).' },
    },
    states: {
        padding: { attribute: 'data-padding', values: ['custom'], description: 'The consumer\'s class sets a padding, which replaces the default padding entirely.' },
        layout: { attribute: 'data-layout', values: ['custom'], description: 'The consumer\'s class sets an unprefixed display (flex, grid, block…), which replaces the stacking column.' },
    },
    slots: { default: { description: 'The content; children stack as a flex column.', required: true } },
    usage: {
        livewire: `<sigta:panel class="flex items-center justify-between gap-3 p-4">
    <sigta:profile name="Ada Lovelace" meta="ada@example.com" />
    <sigta:badge>Owner</sigta:badge>
</sigta:panel>`,
        svelte: `<Panel class="flex items-center justify-between gap-3 p-4">
    <Profile name="Ada Lovelace" meta="ada@example.com" />
    <Badge>Owner</Badge>
</Panel>`,
    },
    notes: {
        shared: [
            'Default padding is fluid (24 to 40px). Pass any padding class (p-4, px-6, sm:p-5, !p-2) and it replaces the default entirely; without one, the default applies.',
            'Children stack as a flex column, so gap-* spaces them and items-* / justify-* act on that column (items-center centres them horizontally). Pass a display class (flex, grid, block) and it replaces the stack entirely, so class="flex items-center justify-between" is a row; a direction class alone (flex-row) keeps the flex and turns it. Responsive classes (sm:flex-row) build on the stack.',
        ],
        livewire: ['variant="inverse" is a dark ink band; pair it with <sigta:label variant="inverse"> and <sigta:stat tone="inverse"> so nested chrome inverts too.'],
        svelte: ['variant="inverse" is a dark ink band; pair it with <Label variant="inverse"> and <Stat tone="inverse"> so nested chrome inverts too.'],
    },
    examples: [
        {
            label: 'Bordered surface (static, no clip)',
            tree: [el('div', { class: 'grid gap-[clamp(18px,2.2vw,28px)] md:grid-cols-2' },
                c('panel', {},
                    kicker('Spor 1 · Timepris'),
                    c('heading', { level: 3, class: 'mb-4 text-[clamp(1.4rem,2.2vw,1.8rem)]' }, 'Klassisk: én lav, fast timepris'),
                    el('p', { class: 'text-[0.98rem] leading-[1.6] text-graphite' }, 'Én timepris. Ingen tillæg, ingen seniorrater, ingen overraskelser.'),
                ),
                c('panel', {},
                    c('annot', { rotate: -5, class: 'absolute -top-3 right-[8%]' }, 'overhæng vises'),
                    kicker('Spor 2 · Fast månedspris'),
                    c('heading', { level: 3, class: 'mb-4 text-[clamp(1.4rem,2.2vw,1.8rem)]' }, 'Skræddersyet software som abonnement'),
                    el('p', { class: 'text-[0.98rem] leading-[1.6] text-graphite' }, 'Panelet klipper ikke den overhængende note, fordi der ikke er overflow-hidden.'),
                ),
            )],
        },
        {
            label: 'Own layout (a display class replaces the stack)',
            tree: [el('div', { class: 'max-w-md' },
                c('panel', { class: 'flex items-center justify-between gap-3 p-4' },
                    c('profile', { name: 'Søren Frisk', meta: 'Studio lead · Copenhagen' }),
                    c('badge', {}, 'Owner'),
                ),
            )],
        },
        {
            label: 'Turned and padded on one axis',
            tree: [el('div', { class: 'max-w-md' },
                c('panel', { class: 'flex-row gap-3 px-6' }, c('badge', {}, 'Row'), c('badge', {}, 'kept flex')),
            )],
        },
        {
            label: 'Inverse (dark) surface',
            tree: [el('div', { class: 'max-w-sm' },
                c('panel', { variant: 'inverse', class: 'gap-4' },
                    el('div', {},
                        c('label', { variant: 'inverse', class: 'block' }, 'Logged this customer'),
                        el('p', { class: 'mt-1 font-head text-2xl tabular-nums' }, '42h 10m'),
                    ),
                    el('div', {},
                        c('label', { variant: 'inverse', class: 'block' }, 'Billable value'),
                        el('p', { class: 'mt-1 font-head text-2xl tabular-nums' }, '29.750 kr.'),
                    ),
                ),
            )],
        },
    ],
});
