import { defineComponent } from '../schema.ts';
import { c, el } from '../examples.ts';

const drop = (names: [string, number][]) => [
    'const zone = document.querySelector(\'[data-part="zone"]\');',
    'const data = new DataTransfer();',
    ...names.map(([name, size]) => `data.items.add(new File(['${'x'.repeat(8)}'.repeat(${size / 8})], '${name}'));`),
    'zone.dispatchEvent(new DragEvent("dragenter", { bubbles: true, cancelable: true }));',
    'zone.dispatchEvent(new DragEvent("drop", { dataTransfer: data, bubbles: true, cancelable: true }));',
].join(' ');

export default defineComponent({
    name: 'file-upload',
    title: 'File upload',
    tier: 'components',
    page: 'file-upload',
    svelte: 'FileUpload',
    description: 'Dropzone file input: a dashed zone (a <label> wrapping a visually hidden native <input type="file">) that opens the picker on click and takes dropped files, then lists the picked files by name and size. Field chrome: label, description, error.',
    aria: 'A native <input type="file"> inside its <label> zone, so the picker, keyboard focus (the zone shows the ring) and the label all work natively; aria-invalid with an error, aria-describedby for description and error.',
    props: {
        label: { type: 'string', default: null, description: 'Field label.' },
        description: { type: 'string', default: null, description: 'Help text under the zone.' },
        error: { type: 'string', default: null, description: 'Error text; turns the zone red and marks the input invalid.' },
        multiple: { type: 'boolean', default: false, description: 'Accept several files.' },
        helper: { type: 'string', default: null, description: 'The line before "browse"; defaults to "Drag a file here, or" ("Drag files here, or" with multiple).' },
        files: { type: 'array', default: [], bindable: true, blade: false, shape: 'File[]', description: 'Svelte: bind:files — the picked File objects, in order (empty to clear the input).' },
    },
    parts: {
        zone: { description: 'The dashed dropzone (<label>).' },
        input: { description: 'The visually hidden native file input; your attributes land here.' },
        icon: { description: 'The upload icon.' },
        helper: { description: 'The helper line.' },
        browse: { description: 'The "browse" chip.' },
        list: { description: 'The picked files (hidden while there are none).' },
        file: { description: 'One picked file.' },
        name: { description: 'Its name.' },
        size: { description: 'Its size ("1.5 KB").' },
    },
    states: {
        dragging: { attribute: 'data-dragging', description: 'On the zone while files are dragged over it.' },
        invalid: { attribute: 'data-invalid', description: 'On the zone with an error (the input carries aria-invalid).' },
        disabled: { attribute: 'data-disabled', description: 'On the zone when the input is disabled: dimmed and inert.' },
    },
    events: {
        change: { description: 'Files were picked or dropped.', blade: 'change on the native input (wire:model uploads it)', svelte: 'onfiles', detail: 'File[]' },
    },
    uses: ['field', 'icon'],
    usage: {
        livewire: '<sigta:file-upload label="Attachment" description="PDF or PNG, up to 10 MB." accept=".pdf,.png" wire:model="attachment" />',
        svelte: `<FileUpload label="Attachment" description="PDF or PNG, up to 10 MB." accept=".pdf,.png" bind:files={attachments} error={form.errors.attachment} />
<!-- Inertia: form.attachment = attachments[0]; form.post(url) sends it as FormData -->`,
    },
    notes: {
        shared: [
            'The zone is a <label> around a visually hidden native input: clicking it opens the picker, a dropped file is assigned to the input and a change event is dispatched, and the picked files are listed by name and size. The look and the drag, has-files, error and disabled states are the same in both runtimes; what happens to the files differs.',
        ],
        livewire: [
            'Every attribute (name, accept, wire:model, …) forwards onto the native input, so Livewire\'s temporary uploads (WithFileUploads + wire:model) work untouched, dropped files included.',
        ],
        svelte: [
            'No backend protocol: the component hands you the File objects. bind:files (File[]) or onfiles(files); post them yourself — with Inertia, put them on a useForm field (form.attachment = files[0]) and form.post() sends FormData. Setting files = [] clears the input.',
            'Other attributes (name, accept, required, …) land on the native input, so it also posts with a plain <form> (enctype="multipart/form-data").',
        ],
    },
    examples: [
        {
            label: 'Single file',
            tree: [el('div', { class: 'max-w-md' }, c('file-upload', { label: 'Attachment', description: 'PDF or PNG, up to 10 MB.', accept: '.pdf,.png' }))],
        },
        {
            label: 'Multiple',
            tree: [el('div', { class: 'max-w-md' }, c('file-upload', { label: 'Documents', multiple: true }))],
        },
        {
            label: 'States',
            tree: [el('div', { class: 'max-w-md flex flex-col gap-6' },
                c('file-upload', { label: 'Disabled', disabled: true }),
                c('file-upload', { label: 'Error', error: 'File too large.' }),
            )],
        },
        {
            label: 'Dragging over the zone',
            tree: [el('div', { class: 'max-w-md' }, c('file-upload', { label: 'Documents', multiple: true }))],
            interactions: [{ script: 'document.querySelector(\'[data-part="zone"]\').dispatchEvent(new DragEvent("dragenter", { bubbles: true, cancelable: true }))' }],
        },
        {
            label: 'Dropped files are listed',
            tree: [el('div', { class: 'max-w-md' }, c('file-upload', { label: 'Documents', multiple: true }))],
            interactions: [{ script: drop([['brief.pdf', 2048], ['logo.png', 304]]) }],
        },
        {
            label: 'With an error and a dropped file',
            tree: [el('div', { class: 'max-w-md' }, c('file-upload', { label: 'Attachment', error: 'File too large.' }))],
            interactions: [{ script: drop([['scan.pdf', 1536 * 1024]]) }],
        },
    ],
});
