import { defineComponent } from '../schema.ts';
import { c, el } from '../examples.ts';

export default defineComponent({
    name: 'field',
    title: 'Field',
    tier: 'components',
    page: 'field',
    svelte: 'Field',
    description: 'The chrome around a form control: a label, the control, then a description or an error. Every Sigta form control renders through it; wrap a custom control in it yourself.',
    aria: 'The label points at the control (for); description and error ids are referenced by the control\'s aria-describedby.',
    props: {
        label: { type: 'string', default: null, description: 'Label text above the control.' },
        description: { type: 'string', default: null, description: 'Help text below the control.' },
        error: { type: 'string', default: null, description: 'Error text below the control (replaces nothing; shown in red).' },
        for: { type: 'string', default: null, description: 'Id of the control the label names; generated when omitted.' },
    },
    parts: {
        description: { description: 'The help text.' },
        error: { description: 'The error text.' },
    },
    slots: { default: { description: 'The control.', required: true } },
    uses: ['label'],
    notes: {
        shared: [
            'With no label, description, error or attributes it renders the control alone, without a wrapper.',
            'Use it when wrapping a custom control; Sigta form controls already wrap themselves.',
        ],
    },
    examples: [
        {
            label: 'Explicit for/id wiring',
            tree: [el('div', { class: 'max-w-sm' },
                c('field', { label: 'Project name', description: 'Shown on invoices and reports.', for: 'kit-field-custom' },
                    c('input', { id: 'kit-field-custom', value: 'Marginalia v3' }),
                ),
            )],
        },
        {
            label: 'Error state',
            tree: [el('div', { class: 'max-w-sm' },
                c('field', { label: 'Workspace', error: 'Pick a workspace before continuing.', for: 'kit-field-error' },
                    c('input', { id: 'kit-field-error', placeholder: 'Marginalia' }),
                ),
            )],
        },
    ],
});
