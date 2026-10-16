import { defineComponent } from '../schema.ts';
import { c, el } from '../examples.ts';

export default defineComponent({
    name: 'checkbox',
    title: 'Checkbox',
    tier: 'elements',
    page: 'checkbox',
    svelte: 'Checkbox',
    description: 'A native checkbox whose tick is drawn as two pen strokes. With a label it renders a mono label row; every other attribute lands on the <input>. In a motion list it is the trigger for completing a row.',
    aria: 'Native <input type="checkbox">; wrapped in its <label> when labelled.',
    props: {
        label: { type: 'string', default: null, description: 'Label text beside the box.' },
        checked: { type: 'boolean', default: false, native: true, bindable: true, description: 'Livewire: wire:model or checked; Svelte: bind:checked.' },
    },
    states: {
        checked: { attribute: 'checked', description: 'Ticked.' },
        done: { attribute: 'data-sigta-done', description: 'On a row Sigta.complete() finished: its [data-sigta-strike] text is crossed out.' },
    },
    notes: {
        shared: [
            'To complete a row with the full motion (tick, burst, strike, fold), call complete from its change handler in a [data-sigta-motion] list and mark the row\'s title with data-sigta-strike.',
        ],
        livewire: [
            '<sigta:checkbox x-on:change="$sigta.complete($el, () => $wire.complete({{ $task->id }}))" /> — inside Livewire the commit goes out at once and the row folds when the server drops it.',
        ],
        svelte: [
            '<Checkbox onchange={(e) => sigta.complete(e.currentTarget, () => remove(task))} /> with out:leave on the row: the row folds, then the item leaves the list.',
        ],
    },
    examples: [
        {
            label: 'States',
            tree: [el('div', { class: 'flex flex-col gap-3' },
                c('checkbox', { label: 'Send me the weekly digest' }),
                c('checkbox', { label: 'Invoices are approved', checked: true }),
                c('checkbox', { label: 'Archived (disabled)', disabled: true }),
                c('checkbox', { 'aria-label': 'Bare checkbox' }),
            )],
        },
        {
            label: 'Completing a row in a motion list',
            conformance: 'browser',
            blade: `<ul data-sigta-motion class="divide-y divide-line rounded-panel border border-line bg-surface max-w-md" x-data="{ items: [{ id: 1, title: 'Send the invoice' }, { id: 2, title: 'Book the kickoff' }, { id: 3, title: 'Archive Marginalia' }] }">
    <template x-for="item in items" :key="item.id">
        <li class="flex items-center gap-3 px-4 py-3">
            <x-sigta::checkbox x-bind:aria-label="'Done: ' + item.title" x-on:change="$sigta.complete($el, () => items = items.filter((i) => i.id !== item.id))" />
            <span data-sigta-strike x-text="item.title"></span>
        </li>
    </template>
</ul>`,
            svelte: `<script>
    import { Checkbox, sigta, leave } from '@sigta-hq/svelte';

    let items = $state([{ id: 1, title: 'Send the invoice' }, { id: 2, title: 'Book the kickoff' }, { id: 3, title: 'Archive Marginalia' }]);
</script>

<ul data-sigta-motion class="divide-y divide-line rounded-panel border border-line bg-surface max-w-md">
    {#each items as item (item.id)}
        <li class="flex items-center gap-3 px-4 py-3" out:leave>
            <Checkbox aria-label={'Done: ' + item.title} onchange={(event) => sigta.complete(event.currentTarget, () => (items = items.filter((i) => i.id !== item.id)))} />
            <span data-sigta-strike>{item.title}</span>
        </li>
    {/each}
</ul>`,
            interactions: [{ click: 'li:first-of-type input[type="checkbox"]', wait: 2500 }],
        },
        {
            label: 'Ticked by hand',
            tree: [c('checkbox', { label: 'Ship the invoice' })],
            interactions: [{ click: 'input[type="checkbox"]', wait: 500 }],
        },
    ],
});
