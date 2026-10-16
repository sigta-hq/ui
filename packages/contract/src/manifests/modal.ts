import { defineComponent } from '../schema.ts';
import { c, el } from '../examples.ts';

export default defineComponent({
    name: 'modal',
    title: 'Modal',
    tier: 'components',
    page: 'modal',
    svelte: 'Modal',
    description: 'A native <dialog> on paper over a blurred ink backdrop: focus trap and Esc for free, an optional heading row with the close button, sizes, and a full-screen sheet for large modals on phones.',
    aria: 'Native <dialog> opened with showModal(); the close button is labelled "Close".',
    props: {
        name: { type: 'string', default: null, description: 'Name that triggers and window events address.' },
        size: { type: 'enum', values: ['sm', 'base', 'lg', 'xl'], default: 'base', description: 'Max width.' },
        heading: { type: 'string', default: null, description: 'Heading row text (the close button sits beside it).' },
        dismissible: { type: 'boolean', default: true, description: 'Close button and backdrop click close it.' },
        fullscreen: { type: 'boolean', default: null, description: 'Fill the screen below the tablet breakpoint; defaults to true for lg and xl.' },
        open: { type: 'boolean', default: false, bindable: true, blade: false, description: 'Svelte: bind:open to open and close it.' },
    },
    parts: {
        dialog: { description: 'The <dialog>; your attributes and class land here.' },
        body: { description: 'Padding and layout around the content.' },
        header: { description: 'The heading row.' },
        close: { description: 'The close button.' },
    },
    states: {
        size: { attribute: 'data-size', description: 'On the dialog.' },
        fullscreen: { attribute: 'data-fullscreen', description: 'On the dialog: fills the screen on phones.' },
    },
    slots: { default: { description: 'The content.', required: true } },
    events: {
        show: { description: 'Open a modal by name from anywhere.', blade: 'sigta-modal-show', svelte: false, detail: '{ name?: string }' },
        hide: { description: 'Close a modal by name from anywhere.', blade: 'sigta-modal-hide', svelte: false, detail: '{ name?: string }' },
    },
    uses: ['heading', 'icon', 'modal.trigger', 'modal.close'],
    notes: {
        shared: [
            'Open by name with <sigta:modal.trigger name="…"> / <Modal.Trigger name="…">, or a sigta-modal-show window event { name }; close with modal.close or sigta-modal-hide.',
        ],
        livewire: [
            'The dialog carries wire:ignore.self so a Livewire morph inside the modal never closes it. Open from PHP with $this->dispatch(\'sigta-modal-show\', name: \'…\').',
        ],
        svelte: ['bind:open is the idiomatic way; the window events work too, so triggers and Livewire-style events behave the same.'],
    },
    examples: [
        {
            label: 'With heading',
            tree: [
                c('modal.trigger', { name: 'kit-confirm' }, c('button', { variant: 'primary' }, 'Archive engagement')),
                c('modal', { name: 'kit-confirm', heading: 'Archive Marginalia?', size: 'sm' },
                    c('text', { variant: 'muted' }, 'It leaves the dashboard but keeps its invoices.'),
                    el('div', { class: 'mt-6 flex justify-end gap-3' },
                        c('modal.close', { name: 'kit-confirm' }, c('button', { variant: 'ghost' }, 'Cancel')),
                        c('button', { variant: 'danger' }, 'Archive'),
                    ),
                ),
            ],
        },
        {
            label: 'Opened',
            tree: [
                c('modal.trigger', { name: 'kit-open' }, c('button', { variant: 'primary' }, 'Open')),
                c('modal', { name: 'kit-open', heading: 'Rename engagement' }, c('input', { label: 'Name', value: 'Marginalia v3' })),
            ],
            interactions: [{ click: '[data-sigta="modal.trigger"] button' }],
        },
        {
            label: 'Large, no heading',
            tree: [
                c('modal.trigger', { name: 'kit-large' }, c('button', {}, 'Open workspace')),
                c('modal', { name: 'kit-large', size: 'lg' }, c('heading', { level: 3 }, 'Workspace'), c('text', {}, 'Full screen on a phone, a wide card on a desk.')),
            ],
        },
    ],
});
