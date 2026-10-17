import { defineComponent, type Example } from '../schema.ts';
import { c, el } from '../examples.ts';

// The kit page's palette: a trigger button and five commands.
const palette = (extra: Record<string, unknown> = {}) => [
    c('modal.trigger', { name: 'command' },
        c('button', {}, 'Open command palette ', el('kbd', { class: 'font-mono text-[0.62rem] opacity-60' }, '⌘K')),
    ),
    c('command', extra,
        c('command.item', { icon: 'file-plus', kbd: '⌘N' }, 'New project'),
        c('command.item', { icon: 'pencil', kbd: '⌘E' }, 'Edit current draft'),
        c('command.item', { icon: 'chart-line' }, 'Open Plumb dashboard'),
        c('command.item', { href: '#top', icon: 'settings' }, 'Settings'),
        c('command.item', { icon: 'archive' }, 'Archive workspace'),
    ),
];

const opened = (label: string, interactions: Example['interactions']): Example => ({ label, tree: palette(), interactions });

export default defineComponent({
    name: 'command',
    title: 'Command',
    tier: 'components',
    page: 'command',
    svelte: 'Command',
    description: 'Command palette (⌘K / Ctrl-K): a dialog with a query box filtering command.items. Arrow keys move, Enter runs the active command, Escape closes. Open it with the shortcut, a modal.trigger with its name, or a sigta-modal-show window event.',
    aria: 'A native modal <dialog>; the query box is a combobox (list autocomplete, always expanded) over a listbox of options, the active one announced through aria-activedescendant.',
    props: {
        name: { type: 'string', default: 'command', description: 'Name that modal.trigger and the sigta-modal-show / sigta-modal-hide window events address.' },
        placeholder: { type: 'string', default: 'Type a command…', description: 'Query box placeholder.' },
        shortcut: { type: 'boolean', default: true, description: 'Open on ⌘K / Ctrl-K anywhere on the page.' },
        open: { type: 'boolean', default: false, bindable: true, blade: false, description: 'bind:open to open and close it.' },
    },
    parts: {
        dialog: { description: 'The <dialog>; your attributes and class land here.' },
        search: { description: 'The row holding the search icon, the query box and the esc hint.' },
        icon: { description: 'The search icon.' },
        input: { description: 'The query box (role=combobox).' },
        esc: { description: 'The "esc" key hint.' },
        list: { description: 'The listbox of commands.' },
        empty: { description: '"Nothing found" when the query filters everything out.' },
    },
    slots: { default: { description: 'The command.items.', required: true } },
    events: {
        show: { description: 'Open a palette by name from anywhere.', blade: 'sigta-modal-show', svelte: false, detail: '{ name?: string }' },
        hide: { description: 'Close a palette by name from anywhere.', blade: 'sigta-modal-hide', svelte: false, detail: '{ name?: string }' },
    },
    uses: ['command.item', 'icon', 'modal.trigger'],
    usage: {
        livewire: `<sigta:modal.trigger name="command"><sigta:button>Open command palette</sigta:button></sigta:modal.trigger>

<sigta:command>
    <sigta:command.item icon="file-plus" kbd="⌘N" wire:click="newProject">New project</sigta:command.item>
    <sigta:command.item href="/settings" icon="settings">Settings</sigta:command.item>
</sigta:command>`,
        svelte: `<Modal.Trigger name="command"><Button>Open command palette</Button></Modal.Trigger>

<Command>
    <Command.Item icon="file-plus" kbd="⌘N" onclick={newProject}>New project</Command.Item>
    <Command.Item href="/settings" icon="settings">Settings</Command.Item>
</Command>`,
    },
    notes: {
        shared: [
            'One palette per page: with shortcut on (the default) ⌘K / Ctrl-K opens it from anywhere.',
            'Open it by name like a modal: {{tag:modal.trigger}} with name="command", or a sigta-modal-show window event { name }; sigta-modal-hide closes it.',
            'The query filters the items by their text; the list keeps every item while the query is empty.',
        ],
        livewire: ['Open it from PHP with $this->dispatch(\'sigta-modal-show\', name: \'command\').'],
        svelte: ['bind:open opens and closes it; the shortcut and the window events work too.'],
    },
    examples: [
        { label: 'Palette — press ⌘K / Ctrl-K, or the trigger', tree: palette() },
        opened('Opened from the trigger', [{ click: '[data-sigta="modal.trigger"] button' }]),
        opened('Opened with Ctrl-K, moved with the arrow keys', [{ press: 'Control+k' }, { press: ['ArrowDown', 'ArrowDown', 'ArrowUp'] }]),
        opened('Typed to filter', [{ press: 'Control+k' }, { fill: ['[data-part="input"]', 'pl'] }, { press: 'ArrowDown' }]),
        opened('Nothing found', [{ press: 'Control+k' }, { fill: ['[data-part="input"]', 'zzz'] }]),
        opened('Enter runs the active command and closes', [{ press: 'Control+k' }, { press: ['ArrowDown', 'Enter'] }]),
        opened('Escape closes', [{ click: '[data-sigta="modal.trigger"] button' }, { press: ['ArrowDown', 'Escape'] }]),
        { label: 'Without the shortcut', tree: palette({ shortcut: false }), interactions: [{ press: 'Control+k' }] },
    ],
});
