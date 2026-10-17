import { defineComponent } from '../schema.ts';
import { c, el } from '../examples.ts';

export default defineComponent({
    name: 'save-state',
    title: 'Save state',
    tier: 'components',
    page: 'save-state',
    svelte: 'SaveState',
    description: 'The compass stands in for "Saving…": it draws while a save is in flight (the words stay for assistive tech, and show under reduced motion), lands with a handwritten "Saved" as the mark completes, then settles back to idle (showing the idle text, if any); a failed save — validation errors included — shakes it red.',
    aria: 'The labels sit in a role="status" polite live region; the compass is decorative.',
    props: {
        target: {
            type: 'string',
            default: null,
            description: 'Actions and/or properties on the closest Livewire component to follow (comma-separated; a dot path matches its children).',
            svelte: false,
        },
        name: { type: 'string', default: null, description: 'The name sigta-save-state events address; unnamed ones hear unnamed events.' },
        state: {
            type: 'enum',
            values: ['idle', 'dirty', 'saving', 'saved', 'failed'],
            default: 'idle',
            description: 'The state it shows (anything else reads as idle).',
            blade: { note: 'The initial state; after that it follows target and the events.' },
            svelte: { note: 'Drive it here; bind:state also follows the sigta-save-state events.' },
        },
        align: { type: 'enum', values: ['start', 'end'], default: 'start', description: 'end puts the compass on the right and right-aligns the words — for the right end of a bar.' },
        idle: { type: 'string', default: null, description: 'Text shown at rest ("All changes saved"); none by default.' },
        dirty: { type: 'string', default: 'Unsaved changes', description: 'Text while there are unsaved changes.' },
        saving: { type: 'string', default: 'Saving…', description: 'Text while saving (for assistive tech, and shown under reduced motion).' },
        saved: { type: 'string', default: 'Saved', description: 'The handwritten word once saved.' },
        failed: { type: 'string', default: 'Not saved', description: 'Text when the save failed.' },
    },
    parts: {
        loader: { description: 'The compass (inked while saving and once saved).' },
        labels: { description: 'The one grid cell every label shares.' },
        label: { description: 'One label (data-when: idle, dirty, saving, saved, failed); only the current one is visible.' },
    },
    states: {
        state: { attribute: 'data-state', values: ['idle', 'dirty', 'saving', 'saved', 'failed'], description: 'On the root; the compass shows idle, queued, working, done or failed.' },
        align: { attribute: 'data-align', values: ['start', 'end'], description: 'On the root.' },
    },
    events: {
        save: {
            description: 'Drives the save states with that name: { name, state }.',
            blade: 'sigta-save-state (window) — $sigta.save(promise, name), $sigta.saveState(state, name), window.Sigta.save(…)',
            svelte: 'sigta-save-state (window) — sigta.save(promise, name), sigta.saveState(state, name), or connectInertia({ saveState: name })',
            detail: '{ name: string | null, state: \'idle\' | \'dirty\' | \'saving\' | \'saved\' | \'failed\' }',
        },
    },
    uses: ['loader'],
    usage: {
        livewire: `<sigta:save-state target="title, body" idle="All changes saved" />
<sigta:input wire:model.live.debounce.500ms="title" />

<header class="flex items-center justify-between">
    <sigta:heading>Draft</sigta:heading>
    <sigta:save-state target="save" align="end" />   {{-- flush right, words grow leftward --}}
</header>

<sigta:save-state name="profile" />
{{-- any promise: resolve = saved, reject = failed (fetch only rejects on network errors, so throw on !response.ok) --}}
<sigta:textarea x-on:input.debounce.700ms="$sigta.save(saveProfile(), 'profile')" />`,
        svelte: `<script>
    import { Heading, SaveState, Textarea, sigta } from '@sigta-hq/svelte';

    let draft = $state('idle');
</script>

<header class="flex items-center justify-between">
    <Heading>Draft</Heading>
    <SaveState bind:state={draft} align="end" />   <!-- flush right, words grow leftward -->
</header>

<SaveState name="profile" idle="All changes saved" />
<!-- any promise: resolve = saved, reject = failed (fetch only rejects on network errors, so throw on !response.ok) -->
<Textarea oninput={() => sigta.save(saveProfile(), 'profile')} />`,
    },
    notes: {
        shared: [
            'Use it instead of any hand-written "Saving…" / "Saved" text or loading spans.',
            'It never moves: all its labels share one cell, so it is as wide as its longest label whatever it is saying and the compass stays put. Use align="end" at the right end of a bar (a justify-between header, the end of a toolbar); the default start suits a left edge. The room is reserved for the longest label, so keep your own words close in length.',
            '"Saved" holds for a moment, then it settles back to idle.',
            'Anywhere: give it a name and drive it with save(promise, name) (saving → saved / failed) or saveState(\'dirty\', name) for the unsaved-changes hint.',
        ],
        livewire: [
            'target names actions and/or properties (comma-separated, dot paths match children) on the closest component; it follows their requests by itself, and typing into a watched wire:model shows dirty until it syncs. Validation errors read as failed.',
            'Without Livewire, give it a name: $sigta.save(promise, \'profile\') or $sigta.saveState(\'dirty\', \'profile\') from Alpine.',
        ],
        svelte: [
            'There is no wire:target to follow: drive it with state (bind:state), with sigta.save(promise, name) / sigta.saveState(state, name), or from Inertia visits with connectInertia({ saveState: name }) from @sigta-hq/svelte/inertia.',
            'bind:state follows the events as well, so your code can read what it shows.',
        ],
    },
    examples: [
        {
            label: 'States',
            tree: [el('div', { class: 'flex flex-wrap items-center gap-x-10 gap-y-4' },
                c('save-state', { state: 'dirty' }),
                c('save-state', { state: 'saving' }),
                c('save-state', { state: 'saved' }),
                c('save-state', { state: 'failed' }),
                c('save-state', { idle: 'All changes saved' }),
            )],
        },
        {
            label: 'At the right end of a bar',
            tree: [el('header', { class: 'flex max-w-md items-center justify-between gap-4' },
                c('heading', { level: 3 }, 'Draft'),
                c('save-state', { align: 'end', state: 'saving', idle: 'All changes saved' }),
            )],
        },
        {
            label: 'Driven by name',
            tree: [c('save-state', { name: 'profile', idle: 'Up to date' })],
            interactions: [{ script: "window.Sigta.saveState('dirty', 'profile')" }],
        },
        {
            label: 'Your own words',
            tree: [el('div', { class: 'flex flex-wrap items-center gap-x-10 gap-y-4' },
                c('save-state', { state: 'failed', idle: 'Alt er gemt', dirty: 'Ikke gemt endnu', saving: 'Gemmer…', saved: 'Gemt', failed: 'Ikke gemt' }),
                c('save-state', { idle: 'Alt er gemt', dirty: 'Ikke gemt endnu', saving: 'Gemmer…', saved: 'Gemt', failed: 'Ikke gemt' }),
            )],
        },
    ],
});
