import { defineComponent } from '../schema.ts';
import { c, el } from '../examples.ts';

export default defineComponent({
    name: 'shell.main',
    title: 'Shell main',
    tier: 'layout',
    page: 'shell',
    svelte: 'Shell.Main',
    description: 'The shell\'s padded main content column: a stack of dashboard rows. Its bottom padding grows by a visible floating bar\'s footprint.',
    aria: 'A <div>; wrap your page content in <main> yourself when the shell is the page.',
    props: {},
    slots: { default: { description: 'The page: a shell.bar, then rows.', required: true } },
    usage: {
        livewire: `<sigta:shell.main>
    <sigta:shell.bar heading="Reports" />
    …
</sigta:shell.main>`,
        svelte: `<Shell.Main>
    <Shell.Bar heading="Reports" />
    …
</Shell.Main>`,
    },
    notes: {
        shared: ['Its bottom padding grows by a visible floating-bar\'s footprint (--sigta-floating-space); if your app scrolls a different element, add pb-[var(--sigta-floating-space,0px)] to it.'],
    },
    examples: [
        {
            label: 'Padded column of rows',
            tree: [
                el('div', { class: 'rounded-frame border border-line bg-surface-raised' },
                    c('shell.main', {},
                        c('shell.bar', { heading: 'Reports', sub: 'Filtered · 12 rows' }),
                        el('div', { class: 'grid gap-3 sm:grid-cols-2' },
                            c('stat', { label: 'Open', value: '12' }),
                            c('stat', { label: 'Closed', value: '48' }),
                        ),
                    ),
                ),
            ],
        },
    ],
});
