import { defineComponent } from '../schema.ts';
import { c, el } from '../examples.ts';

export default defineComponent({
    name: 'composer',
    title: 'Composer',
    tier: 'components',
    page: 'composer',
    svelte: 'Composer',
    description: 'Chat composer: an auto-growing textarea (up to six rows) between a leading slot (attach, by default) and a send button. Enter sends, Shift+Enter inserts a newline; sending hands the trimmed text over and clears the field.',
    aria: 'A native <textarea> labelled "Message" (override with aria-label); the buttons are labelled "Attach file" and "Send", and Send is disabled while the text is blank.',
    props: {
        placeholder: { type: 'string', default: 'Write a message…', description: 'Textarea placeholder.' },
        name: { type: 'string', default: null, description: 'Form name of the textarea.' },
        value: { type: 'string', default: null, native: true, bindable: true, description: 'The text. Livewire: wire:model (lands on the textarea); Svelte: bind:value.' },
    },
    parts: {
        leading: { description: 'Holds the leading control (the attach button, or your leading slot).' },
        input: { description: 'The textarea; your attributes land here.' },
        send: { description: 'Holds the send button.' },
    },
    slots: {
        leading: { description: 'Replaces the attach button (a mention or emoji button, …).' },
    },
    events: {
        submit: { description: 'A message was sent (Enter or the send button); the field clears.', blade: 'sigta-composer-submit', svelte: 'onsubmit', detail: '{ text: string }' },
    },
    uses: ['button', 'icon'],
    usage: {
        livewire: '<sigta:composer placeholder="Message the studio…" x-on:sigta-composer-submit="$wire.send($event.detail.text)" />',
        svelte: '<Composer placeholder="Message the studio…" onsubmit={(text) => send(text)} />',
    },
    notes: {
        shared: [
            'Sending dispatches a bubbling sigta-composer-submit event { text } from the textarea (the window hears it too) and clears the field.',
            'The textarea grows with its text up to six rows, then scrolls.',
        ],
        livewire: [
            'Attributes (name, wire:model, id, aria-*, x-on:sigta-composer-submit) forward onto the native textarea, so a listener on the component hears every send.',
            'Replace the attach button with <x-slot:leading>…</x-slot:leading>.',
        ],
        svelte: [
            'onsubmit(text) on every send (it receives the text itself; the DOM event still carries { text }); bind:value for the draft. Other attributes land on the textarea.',
            'Replace the attach button with {#snippet leading()}…{/snippet}.',
        ],
    },
    examples: [
        {
            label: 'Default',
            tree: [el('div', { class: 'max-w-xl' }, c('composer', { name: 'message', placeholder: 'Message the studio…' }))],
        },
        {
            label: 'Custom leading slot',
            tree: [el('div', { class: 'max-w-xl' },
                c('composer', { placeholder: 'Add a comment…' },
                    { leading: [c('button', { variant: 'ghost', 'aria-label': 'Mention someone' }, c('icon', { name: 'at-sign', size: 'sm' }))] },
                ),
            )],
        },
        {
            label: 'Typing enables Send; the field grows with the text',
            tree: [el('div', { class: 'max-w-xl' }, c('composer', { placeholder: 'Message the studio…' }))],
            interactions: [{ fill: ['[data-part="input"]', 'First line\nSecond line\nThird line'] }],
        },
        {
            label: 'Grows to six rows, then scrolls',
            tree: [el('div', { class: 'max-w-xl' }, c('composer', { placeholder: 'Message the studio…' }))],
            interactions: [{ fill: ['[data-part="input"]', '1\n2\n3\n4\n5\n6\n7\n8\n9'] }],
        },
        {
            label: 'Enter sends and clears the field',
            conformance: 'browser',
            blade: `<div class="max-w-xl" x-data="{ log: [] }" x-on:sigta-composer-submit="log.push($event.detail.text)">
    <x-sigta::composer name="message" placeholder="Message the studio…" />
    <ul class="mt-4 flex flex-col gap-1.5 font-mono text-[0.72rem] text-graphite">
        <template x-for="(line, i) in log" :key="i">
            <li class="border-b border-line pb-1.5">sent → <span class="text-ink" x-text="line"></span></li>
        </template>
    </ul>
</div>`,
            svelte: `<script>
    import { Composer } from '@sigta-hq/svelte';

    let log = $state([]);
</script>

<div class="max-w-xl">
    <Composer name="message" placeholder="Message the studio…" onsubmit={(text) => log.push(text)} />
    <ul class="mt-4 flex flex-col gap-1.5 font-mono text-[0.72rem] text-graphite">
        {#each log as line, i (i)}
            <li class="border-b border-line pb-1.5">sent → <span class="text-ink">{line}</span></li>
        {/each}
    </ul>
</div>`,
            interactions: [{ fill: ['[data-part="input"]', '  Ship it  '] }, { press: 'Enter' }],
        },
        {
            label: 'The send button sends too',
            conformance: 'browser',
            blade: `<div class="max-w-xl" x-data="{ sent: '' }">
    <x-sigta::composer placeholder="Add a note…" x-on:sigta-composer-submit="sent = $event.detail.text" />
    <p class="mt-3 font-mono text-[0.72rem] text-graphite" x-text="sent"></p>
</div>`,
            svelte: `<script>
    import { Composer } from '@sigta-hq/svelte';

    let sent = $state('');
</script>

<div class="max-w-xl">
    <Composer placeholder="Add a note…" onsubmit={(text) => (sent = text)} />
    <p class="mt-3 font-mono text-[0.72rem] text-graphite">{sent}</p>
</div>`,
            interactions: [{ fill: ['[data-part="input"]', 'Looks good'] }, { click: '[aria-label="Send"]' }],
        },
    ],
});
