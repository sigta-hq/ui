import { defineComponent } from '../schema.ts';
import { c, el } from '../examples.ts';

export default defineComponent({
    name: 'job',
    title: 'Job',
    tier: 'components',
    page: 'job',
    svelte: 'Job',
    description: 'One piece of long-running work (an import, an export, a queue run): a large compass that draws its circle as far round as value, the heading with its percentage, a mono status line (Running · <stage> · <elapsed>), and — the moment it turns done — the circle rises into the mark\'s arc with a burst.',
    aria: 'The compass is a progressbar named by the heading; the status word is a polite live region.',
    props: {
        heading: { type: 'string', default: null, description: 'What the work is ("Importing 1,204 contacts"); also names the progressbar.' },
        description: { type: 'string', default: null, description: 'A line of detail under the status.' },
        stage: { type: 'string', default: null, description: 'What is happening now, after the status word.' },
        value: { type: 'number', default: null, description: 'Progress 0–100; unset keeps the compass turning without progress. done reads 100.' },
        state: { type: 'enum', values: ['queued', 'working', 'done', 'failed'], default: 'working', description: 'queued, working (Running), done or failed; anything else reads as working.' },
        since: {
            type: 'date',
            default: null,
            description: 'When the work started (ISO-8601): an elapsed clock ticks in the status line while it is queued or working.',
            blade: { accepts: 'string|\\DateTimeInterface (Carbon)' },
        },
    },
    parts: {
        loader: { description: 'The compass.' },
        main: { description: 'The text column.' },
        header: { description: 'Heading and percentage.' },
        heading: { description: 'The heading (truncates).' },
        percent: { description: 'The percentage, in a fixed 4ch width.' },
        meta: { description: 'The status line.' },
        status: { description: 'The status word: success ink when done, danger when failed.' },
        description: { description: 'The description.' },
        actions: { description: 'The actions slot.' },
    },
    states: {
        state: { attribute: 'data-state', values: ['queued', 'working', 'done', 'failed'], description: 'On the root; the runtime bursts the compass when it turns done.' },
    },
    slots: {
        default: { description: 'Extra content under the description.' },
        actions: { description: 'Controls at the right ("Retry", "Cancel").' },
    },
    uses: ['loader', 'elapsed-clock'],
    usage: {
        livewire: `<div wire:poll.2s data-sigta-motion class="space-y-2.5">
    @foreach ($imports as $import)
        <sigta:job wire:key="import-{{ $import->id }}" :heading="$import->name" :state="$import->state"
                   :value="$import->progress" :stage="$import->stage" :since="$import->started_at" />
    @endforeach
</div>`,
        svelte: `<div data-sigta-motion class="space-y-2.5">
    {#each imports as job (job.id)}
        <Job heading={job.name} state={job.state} value={job.progress} stage={job.stage} since={job.startedAt} />
    {/each}
</div>`,
    },
    notes: {
        shared: [
            'Use it for background work and queue progress instead of a progress bar and text assembled by hand.',
            'It never moves: the compass keeps its place through every state and the percentage has a fixed width, so the heading beside it never shifts. Keep it rendered and change state; never mount it per state.',
            'Its state is plain markup, so new props move it along; in a [data-sigta-motion] list a finished job that leaves the list folds away.',
        ],
        livewire: ['Drive it with wire:poll and let the morph move it along; give each job a wire:key.'],
        svelte: ['Pass state and value from your data (a poll, a websocket, Inertia props) and key the {#each} by id.'],
    },
    examples: [
        {
            label: 'States',
            tree: [el('div', { class: 'flex max-w-xl flex-col gap-2.5' },
                c('job', { heading: 'Exporting the Q3 ledger', state: 'queued', stage: 'Waiting for a worker' }),
                c('job', { heading: 'Importing 1,204 contacts', value: 62, stage: 'Matching duplicates', description: '312 duplicates merged so far.' }),
                c('job', { heading: 'Rebuilding the search index', stage: 'Crawling pages' }),
                c('job', { heading: 'Sending the October newsletter', state: 'done', stage: '4,210 delivered' }),
                c('job', { heading: 'Syncing Dinero invoices', state: 'failed', stage: 'Step 3 of 4 · API timed out' },
                    { actions: [c('button', { size: 'sm' }, 'Retry')] }),
            )],
        },
        {
            label: 'Done reads 100%',
            tree: [el('div', { class: 'max-w-xl' }, c('job', { heading: 'Export', state: 'done', value: 80 }))],
        },
    ],
});
