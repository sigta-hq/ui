import { defineComponent } from '../schema.ts';
import { c, el } from '../examples.ts';

const share = (props: Record<string, unknown> = {}) => c('popover', props, { trigger: [c('button', {}, 'Share ', c('icon', { name: 'chevron-down', size: 'sm' }))] },
    c('heading', { level: 3, size: 'sm' }, 'Share this draft'),
    c('text', { variant: 'muted', class: 'text-[0.9rem] mt-1.5' }, 'Anyone with the link can read, nobody can edit.'),
    el('div', { class: 'mt-3 flex gap-2' },
        c('button', { variant: 'primary', size: 'sm' }, 'Copy link'),
        c('button', { variant: 'ghost', size: 'sm' }, 'Settings'),
    ),
);
const open = { click: '[data-part="trigger"] [data-sigta="button"]' };

export default defineComponent({
    name: 'popover',
    title: 'Popover',
    tier: 'components',
    page: 'popover',
    svelte: 'Popover',
    description: 'A generic popover: the dropdown\'s placement and raised paper around free-form content, without menu semantics.',
    aria: 'The trigger wrapper has aria-haspopup="dialog" and aria-expanded; the panel is a non-modal role="dialog". Escape closes it and returns focus to the trigger; a click outside or focus leaving closes it.',
    props: {
        position: { type: 'enum', values: ['bottom-start', 'bottom-end', 'top-start', 'top-end'], default: 'bottom-start', description: 'Where the panel opens against the trigger; unknown values act as bottom-start.' },
        open: { type: 'boolean', default: false, bindable: true, blade: false, description: 'bind:open reads and sets whether it is open.' },
    },
    parts: {
        trigger: { description: 'Wraps the trigger slot; carries aria-haspopup and aria-expanded.' },
        panel: { description: 'The panel (role="dialog"), hidden until open; takes focus when clicked so a click inside keeps it open.' },
    },
    states: {
        position: { attribute: 'data-position', values: ['bottom-start', 'bottom-end', 'top-start', 'top-end'], description: 'On the root.' },
        open: { attribute: 'aria-expanded', description: 'On the trigger wrapper.' },
    },
    slots: {
        trigger: { description: 'What opens it, usually a button.', required: true },
        default: { description: 'The content.', required: true },
    },
    uses: ['dropdown', 'button'],
    notes: {
        shared: ['Use {{tag:dropdown}} for a list of actions; popover for anything else (a share box, a small form).'],
        livewire: ['The trigger goes in <x-slot:trigger> (required); the content is the default slot.'],
        svelte: ['The trigger is a snippet (required); the content is the children. bind:open to open or close it yourself.'],
    },
    examples: [
        { label: 'Free-form panel', tree: [share()] },
        { label: 'Opened', tree: [share()], interactions: [open] },
        {
            label: 'A click inside keeps it open',
            tree: [share()],
            interactions: [open, { click: '[data-part="panel"] [data-sigta="heading"]' }],
        },
        {
            label: 'Escape closes it',
            tree: [share()],
            interactions: [open, { press: 'Escape' }],
        },
        {
            label: 'Position top-end',
            tree: [el('div', { class: 'flex justify-end pt-48' }, share({ position: 'top-end' }))],
            interactions: [open],
        },
    ],
});
