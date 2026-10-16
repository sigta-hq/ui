import { defineComponent } from '../schema.ts';
import { c } from '../examples.ts';

export default defineComponent({
    name: 'autocomplete.option',
    title: 'Autocomplete option',
    tier: 'components',
    page: 'autocomplete',
    svelte: 'Autocomplete.Option',
    description: 'One option of an autocomplete. Its value defaults to its text; label is what the query box shows once it is chosen.',
    aria: 'role="option" with aria-selected.',
    props: {
        value: { type: 'string', default: null, description: 'The value chosen (defaults to the text).' },
        label: { type: 'string', default: null, description: 'Text written back into the query box (defaults to the text).' },
    },
    states: {
        active: { attribute: 'data-active', description: 'The keyboard/hover-active option.' },
        selected: { attribute: 'aria-selected', description: 'The chosen option.' },
    },
    slots: { default: { description: 'The option text.', required: true } },
    uses: ['autocomplete'],
    examples: [
        { label: 'Inside an autocomplete', tree: [c('autocomplete', { label: 'Tag' }, c('autocomplete.option', { value: 'ux' }, 'Research'), c('autocomplete.option', { value: 'eng' }, 'Engineering'))] },
    ],
});
