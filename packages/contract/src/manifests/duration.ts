import { defineComponent } from '../schema.ts';
import { c, el } from '../examples.ts';

export default defineComponent({
    name: 'duration',
    title: 'Duration',
    tier: 'elements',
    page: 'elapsed-clock',
    svelte: 'Duration',
    description: 'Static duration display in tabular numerals: whole minutes as a compact "1h 15m" (default) or an "HH:MM" clock. Exact hours drop the minutes ("2h"); zero renders "0m".',
    aria: 'A plain <span>; the duration is its text.',
    props: {
        minutes: { type: 'number', default: null, description: 'Whole minutes; null and negatives read as 0, fractions are truncated.' },
        format: { type: 'enum', values: ['compact', 'clock'], default: 'compact', description: 'compact (1h 15m) or clock (01:30). Unknown values fall back to compact.' },
    },
    usage: {
        livewire: `<sigta:duration :minutes="$task->estimate_minutes" />
<sigta:duration :minutes="90" format="clock" />   {{-- 01:30 --}}`,
        svelte: `<Duration minutes={task.estimateMinutes} />
<Duration minutes={90} format="clock" />   <!-- 01:30 -->`,
    },
    notes: {
        shared: [
            'For a value that counts up live use elapsed-clock.',
            'Both runtimes format with the same rules (the core\'s formatDuration), so a server render and a client render agree.',
        ],
    },
    examples: [
        {
            label: 'Compact and clock',
            tree: [el('div', { class: 'flex flex-wrap items-baseline gap-10 font-head text-2xl' },
                c('duration', { minutes: 75 }),
                c('duration', { minutes: 120 }),
                c('duration', { minutes: 45 }),
                c('duration', { minutes: 605, format: 'clock' }),
            )],
        },
        {
            label: 'Zero and unset',
            tree: [el('div', { class: 'flex flex-wrap items-baseline gap-10 font-mono text-[0.82rem]' },
                c('duration', { minutes: 0 }),
                c('duration', {}),
                c('duration', { minutes: 90, format: 'clock' }),
            )],
        },
    ],
});
