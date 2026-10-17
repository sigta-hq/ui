import { defineComponent } from '../schema.ts';
import { c, el } from '../examples.ts';

export default defineComponent({
    name: 'radio',
    title: 'Radio',
    tier: 'elements',
    page: 'radio',
    svelte: 'Radio',
    description: 'A native radio drawn as a round box that fills with ink around a paper dot. Renders a <label> wrapper when label is set, the bare input otherwise; every other attribute lands on the <input>.',
    aria: 'Native <input type="radio">; wrapped in its <label> when labelled. Group radios by name (in a field or fieldset).',
    props: {
        label: { type: 'string', default: null, description: 'Label text beside the box.' },
        checked: { type: 'boolean', default: false, native: true, description: 'Chosen. Livewire: wire:model on each radio of the group, or checked.' },
        value: { type: 'string', default: null, native: true, description: 'The value this radio stands for.' },
        group: { type: 'string', default: null, bindable: true, blade: false, description: 'Svelte: bind:group={choice} on each radio of the group; the radio whose value matches is chosen.' },
    },
    parts: {
        control: { description: 'The <input> inside a labelled radio (the bare input is the root).' },
    },
    states: { checked: { attribute: 'checked', description: 'Chosen.' } },
    usage: {
        livewire: `<sigta:radio name="density" value="comfortable" label="Comfortable density" wire:model="density" />
<sigta:radio name="density" value="compact" label="Compact density" wire:model="density" />`,
        svelte: `<Radio name="density" value="comfortable" label="Comfortable density" bind:group={density} />
<Radio name="density" value="compact" label="Compact density" bind:group={density} />`,
    },
    notes: {
        shared: [
            'Use it instead of <input type="radio"> with classes. Same wrapper behavior as checkbox.',
            'For a compact strip of mutually exclusive options use segmented.',
        ],
        livewire: ['wire:model and every attribute except class land on the native <input>; class lands on the label row.'],
        svelte: ['bind:group={choice} with a value per radio; without a group, checked sets it.'],
    },
    examples: [
        {
            label: 'Basic group',
            tree: [el('div', { class: 'flex flex-col gap-3' },
                c('radio', { name: 'kit-density', value: 'comfortable', label: 'Comfortable density', checked: true }),
                c('radio', { name: 'kit-density', value: 'compact', label: 'Compact density' }),
            )],
        },
        {
            label: 'Disabled',
            tree: [el('div', { class: 'flex flex-col gap-3' },
                c('radio', { name: 'kit-locked', label: 'Locked selection', checked: true, disabled: true }),
                c('radio', { name: 'kit-locked', label: 'Unavailable option', disabled: true }),
            )],
        },
        {
            label: 'Group in a field with error',
            tree: [el('div', { class: 'max-w-sm' },
                c('field', { label: 'Billing cadence', error: 'Choose a cadence to continue.', for: 'kit-cadence-monthly' },
                    el('div', { class: 'flex flex-col gap-2' },
                        c('radio', { name: 'kit-cadence', label: 'Monthly', id: 'kit-cadence-monthly' }),
                        c('radio', { name: 'kit-cadence', label: 'Quarterly' }),
                        c('radio', { name: 'kit-cadence', label: 'Per milestone' }),
                    ),
                ),
            )],
        },
        {
            label: 'Bare and chosen by hand',
            tree: [el('div', { class: 'flex items-center gap-3' },
                c('radio', { name: 'kit-bare', value: 'a', 'aria-label': 'Option A' }),
                c('radio', { name: 'kit-bare', value: 'b', 'aria-label': 'Option B' }),
                c('radio', { name: 'kit-bare', value: 'c', 'aria-label': 'Option C', disabled: true }),
            )],
            interactions: [{ click: 'input[value="b"]' }],
        },
        {
            label: 'Bound to a value',
            conformance: 'browser',
            blade: `<div class="flex flex-col gap-3" x-data="{ density: 'compact' }">
    <x-sigta::radio name="kit-bound" value="comfortable" label="Comfortable density" x-model="density" />
    <x-sigta::radio name="kit-bound" value="compact" label="Compact density" x-model="density" />
    <p class="font-mono text-[0.72rem] text-graphite">Chosen: <span x-text="density">compact</span></p>
</div>`,
            svelte: `<script>
    import { Radio } from '@sigta-hq/svelte';

    let density = $state('compact');
</script>

<div class="flex flex-col gap-3">
    <Radio name="kit-bound" value="comfortable" label="Comfortable density" bind:group={density} />
    <Radio name="kit-bound" value="compact" label="Compact density" bind:group={density} />
    <p class="font-mono text-[0.72rem] text-graphite">Chosen: <span>{density}</span></p>
</div>`,
            interactions: [{ click: 'input[value="comfortable"]' }],
        },
    ],
});
