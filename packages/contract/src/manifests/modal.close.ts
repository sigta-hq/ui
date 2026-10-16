import { defineComponent } from '../schema.ts';
import { c } from '../examples.ts';

export default defineComponent({
    name: 'modal.close',
    title: 'Modal Close',
    tier: 'components',
    page: 'modal',
    svelte: 'Modal.Close',
    description: 'Closes the modal with the given name (any modal when unnamed) when its content is clicked.',
    props: {
        name: { type: 'string', default: null, description: 'The modal to address.' },
    },
    slots: { default: { description: 'The clickable content, usually a button.', required: true } },
    uses: ['modal'],
    examples: [
        { label: 'Wrapping a button', tree: [c('modal.close', { name: 'kit-confirm' }, c('button', {}, 'Closes'))] },
    ],
});
