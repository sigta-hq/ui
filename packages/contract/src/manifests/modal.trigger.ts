import { defineComponent } from '../schema.ts';
import { c } from '../examples.ts';

export default defineComponent({
    name: 'modal.trigger',
    title: 'Modal Trigger',
    tier: 'components',
    page: 'modal',
    svelte: 'Modal.Trigger',
    description: 'Opens the modal with the given name (any modal when unnamed) when its content is clicked: it dispatches a sigta-modal-show window event with { name }.',
    props: {
        name: { type: 'string', default: null, description: 'The modal to address.' },
    },
    slots: { default: { description: 'The clickable content, usually a button.', required: true } },
    uses: ['modal'],
    examples: [
        { label: 'Wrapping a button', tree: [c('modal.trigger', { name: 'kit-confirm' }, c('button', {}, 'Opens'))] },
    ],
});
