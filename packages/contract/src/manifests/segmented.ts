import { defineComponent } from '../schema.ts';
import { c, el } from '../examples.ts';

export default defineComponent({
    name: 'segmented',
    title: 'Segmented',
    tier: 'components',
    page: 'segmented',
    svelte: 'Segmented',
    description: 'A segmented control: a hairline strip of mono options, the chosen one filled in ink. Radio-group semantics, zero JS.',
    aria: 'A <fieldset> of native radios sharing a name; the label is its (visually hidden) <legend>.',
    props: {
        name: { type: 'string', required: true, description: 'The radios\' shared name.' },
        options: { type: 'object', default: {}, shape: 'Record<string, string> — value ⇒ label (a list uses its indexes as values)', description: 'The options, value ⇒ label.' },
        label: { type: 'string', default: null, description: 'Accessible group name (a visually hidden legend).' },
        checked: { type: 'string', default: null, bindable: true, description: 'The chosen value (compared as a string).', blade: { note: 'wire:model, or checked for the initial choice.' } },
    },
    parts: {
        legend: { description: 'The visually hidden group name.' },
        group: { description: 'The bordered strip.' },
        option: { description: 'One option\'s <label>.' },
        input: { description: 'Its visually hidden radio.' },
        label: { description: 'Its visible text; filled in ink when chosen.' },
    },
    states: { checked: { attribute: 'checked', description: 'On the chosen option\'s radio.' } },
    usage: {
        livewire: `<sigta:segmented name="range" label="Range" :options="['7d' => '7d', '30d' => '30d', 'qtd' => 'QTD']" wire:model.live="range" />`,
        svelte: `<Segmented name="range" label="Range" options={{ '7d': '7d', '30d': '30d', qtd: 'QTD' }} bind:checked={range} />`,
    },
    notes: {
        shared: ['Use it for a short range of mutually exclusive views (7d / 30d / QTD); for a longer list use {{tag:radio}} or {{tag:select}}.'],
        livewire: ['wire:model and every attribute except class land on each radio; class lands on the fieldset.'],
        svelte: ['bind:checked holds the chosen value; other attributes (onchange, disabled) land on each radio.'],
    },
    examples: [
        {
            label: 'Range picker',
            tree: [el('div', { class: 'flex items-center gap-6' },
                c('segmented', { name: 'range', label: 'Range', options: { '7d': '7d', '30d': '30d', qtd: 'QTD' }, checked: '30d' }),
                c('segmented', { name: 'density', label: 'Density', options: { comfortable: 'Comfortable', compact: 'Compact' }, checked: 'comfortable' }),
            )],
        },
        {
            label: 'Nothing chosen yet',
            tree: [c('segmented', { name: 'view', label: 'View', options: { list: 'List', board: 'Board', calendar: 'Calendar' } })],
        },
        {
            label: 'Chosen by hand',
            tree: [c('segmented', { name: 'period', label: 'Period', options: { week: 'Week', month: 'Month', year: 'Year' }, checked: 'week' })],
            interactions: [{ click: 'label:has(input[value="year"])' }],
        },
        {
            label: 'Bound to a value',
            conformance: 'browser',
            blade: `<div class="flex items-center gap-4" x-data="{ range: '30d' }">
    <x-sigta::segmented name="kit-range" label="Range" :options="['7d' => '7d', '30d' => '30d', 'qtd' => 'QTD']" checked="30d" x-model="range" />
    <span class="font-mono text-[0.72rem] text-graphite" x-text="range">30d</span>
</div>`,
            svelte: `<script>
    import { Segmented } from '@sigta-hq/svelte';

    let range = $state('30d');
</script>

<div class="flex items-center gap-4">
    <Segmented name="kit-range" label="Range" options={{ '7d': '7d', '30d': '30d', qtd: 'QTD' }} bind:checked={range} />
    <span class="font-mono text-[0.72rem] text-graphite">{range}</span>
</div>`,
            interactions: [{ click: 'label:has(input[value="qtd"])' }],
        },
    ],
});
