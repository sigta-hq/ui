import { defineComponent } from '../schema.ts';
import { c } from '../examples.ts';

export default defineComponent({
    name: 'command.item',
    title: 'Command item',
    tier: 'components',
    page: 'command',
    svelte: 'Command.Item',
    description: 'One command of a command palette: an optional icon, the label and a key hint. A link with href, else a button; choosing it (a click or Enter) closes the palette.',
    aria: 'role="option"; the palette\'s query box points at the active one through aria-activedescendant.',
    props: {
        icon: { type: 'string', default: null, description: 'Leading icon name.' },
        kbd: { type: 'string', default: null, description: 'Key hint on the right, e.g. "⌘N".' },
        value: { type: 'string', default: null, description: 'Written into data-value for your own handlers.' },
        href: { type: 'string', default: null, native: true, description: 'Renders an <a> instead of a <button>.' },
    },
    parts: {
        icon: { description: 'The leading icon.' },
        label: { description: 'The text.' },
        kbd: { description: 'The key hint.' },
    },
    states: {
        active: { attribute: 'data-active', description: 'The keyboard/hover-active command, filled in ink.' },
    },
    slots: { default: { description: 'The command\'s text.', required: true } },
    uses: ['command', 'icon'],
    notes: {
        shared: ['Run the command with your own click handler (or href); the palette closes on its own.'],
        livewire: ['wire:click and every other attribute land on the <a> / <button>.'],
        svelte: ['onclick and every other attribute land on the <a> / <button>.'],
    },
    examples: [
        {
            label: 'Inside a palette',
            tree: [c('command', { name: 'kit-command-items' },
                c('command.item', { icon: 'file-plus', kbd: '⌘N', value: 'new' }, 'New project'),
                c('command.item', { href: '#top', icon: 'settings' }, 'Settings'),
                c('command.item', {}, 'Sign out'),
            )],
            interactions: [{ script: "window.dispatchEvent(new CustomEvent('sigta-modal-show', { detail: { name: 'kit-command-items' } }))" }, { press: 'ArrowDown' }],
        },
    ],
});
