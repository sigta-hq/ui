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
        open: { type: 'boolean', default: false, bindable: true, blade: false, description: 'bind:open to open and close it.' },
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
    usage: {
        livewire: `<sigta:modal.trigger name="settings"><sigta:button>Settings</sigta:button></sigta:modal.trigger>
<sigta:modal name="settings" heading="Settings">…</sigta:modal>`,
        svelte: `<Modal.Trigger name="settings"><Button>Settings</Button></Modal.Trigger>
<Modal name="settings" heading="Settings">…</Modal>
<!-- or: <Modal bind:open heading="Settings">…</Modal> -->`,
    },
    notes: {
        shared: [
            'Use it instead of custom dialog markup.',
            'Open it by name: wrap the opener in {{tag:modal.trigger}} with the same name, or dispatch a sigta-modal-show window event { name }; close it with {{tag:modal.close}} or a sigta-modal-hide event.',
            'Full screen on a phone: below the tablet breakpoint an lg or xl modal fills the screen edge to edge (no gutter, border or rounding) while sm and base stay centred cards, because a workspace needs the room and a confirmation does not. Pass fullscreen to force it on a small modal, or set it to false to keep a large one centred. Above the breakpoint every size is unchanged.',
            'A full-screen modal lays its body out as a column with a full-height minimum, so a child marked flex-1 fills the screen exactly and taller content scrolls the dialog as usual.',
            'With a heading the close button sits in the heading row; with no heading it floats in the top-right corner and no header band is reserved, so keep the top-right of your content clear for it.',
        ],
        livewire: [
            'It keeps its open state across a host Livewire re-render: the dialog renders wire:ignore.self, so a modal that stays open while its contents autosave or run New / Delete actions does not close on the morph (its children still morph). Do not add wire:ignore.self yourself.',
            'Open it from PHP with $this->dispatch(\'sigta-modal-show\', name: \'…\'); sigta-modal-hide closes it. To keep a large modal centred on a phone: :fullscreen="false".',
        ],
        svelte: ['bind:open is the idiomatic way to open and close it; {{tag:modal.trigger}} / {{tag:modal.close}} and the sigta-modal-show / sigta-modal-hide window events work too. To keep a large modal centred on a phone: fullscreen={false}.'],
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
