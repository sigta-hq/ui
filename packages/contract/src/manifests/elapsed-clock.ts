import { defineComponent } from '../schema.ts';
import { c, el } from '../examples.ts';

// The clock reads "now"; freezing Date.now and waiting for the next tick makes
// what both runtimes show deterministic.
const freeze = (iso: string) => ({ script: `Date.now = () => ${Date.parse(iso)}`, wait: 1300 });

export default defineComponent({
    name: 'elapsed-clock',
    title: 'Elapsed clock',
    tier: 'elements',
    page: 'elapsed-clock',
    svelte: 'ElapsedClock',
    description: 'Live-ticking HH:MM:SS value counting up from since, in tabular numerals. Presentational — the timer\'s persistence stays in your app; it cleans up its interval on teardown.',
    aria: 'A plain <span>; the clock is its text.',
    props: {
        since: {
            type: 'date',
            default: null,
            description: 'The start instant as an ISO-8601 string; unset means now (the moment it mounts).',
            blade: { accepts: 'string|\\DateTimeInterface (Carbon)', note: 'A Carbon/DateTimeInterface is normalized to ISO-8601.' },
        },
    },
    usage: {
        livewire: '<sigta:elapsed-clock :since="$entry->started_at" class="text-lg" />',
        svelte: '<ElapsedClock since={entry.startedAt} class="text-lg" />',
    },
    notes: {
        shared: [
            'Server-rendered as 00:00:00; it ticks once a second on the client. A start in the future reads 00:00:00.',
            'For a static duration use duration.',
        ],
        livewire: ['Pass :since an ISO string or a Carbon/DateTime; omit it for "now".'],
        svelte: ['Pass since an ISO string (new Date().toISOString(), or what your backend sends).'],
    },
    examples: [
        {
            label: 'Counting up from a start instant',
            tree: [el('div', { class: 'flex flex-wrap items-start gap-10' },
                el('div', {},
                    c('label', { class: 'mb-1 block' }, 'Since page load'),
                    c('elapsed-clock', { class: 'font-head text-2xl' }),
                ),
                el('div', {},
                    c('label', { class: 'mb-1 block' }, 'Since 09:00'),
                    c('elapsed-clock', { since: '2026-01-01T09:00:00+00:00', class: 'font-head text-2xl' }),
                ),
            )],
            interactions: [freeze('2026-01-01T11:03:04+00:00')],
        },
        {
            label: 'On a dark band',
            frame: 'inverse',
            tree: [el('div', { class: 'inline-flex items-center gap-3 rounded-full bg-inverse px-5 py-2.5 font-mono text-[0.8rem]' },
                el('span', { class: 'text-inverse-fg-muted' }, 'Running'),
                c('elapsed-clock', { since: '2026-01-01T09:48:00+00:00', class: 'text-inverse-fg' }),
            )],
            interactions: [freeze('2026-01-01T10:00:00+00:00')],
        },
    ],
});
