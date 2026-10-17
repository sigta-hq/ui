import { defineComponent } from '../schema.ts';
import { c } from '../examples.ts';

export default defineComponent({
    name: 'dropdown.separator',
    title: 'Dropdown separator',
    tier: 'components',
    page: 'dropdown',
    svelte: 'Dropdown.Separator',
    description: 'A hairline <hr role="separator"> between groups of menu items.',
    aria: 'role="separator".',
    props: {},
    uses: ['dropdown'],
    examples: [
        {
            label: 'Between groups',
            tree: [c('dropdown', {}, { trigger: [c('button', {}, 'Options')] },
                c('dropdown.item', { icon: 'pencil' }, 'Edit'),
                c('dropdown.separator'),
                c('dropdown.item', { icon: 'trash-2', variant: 'danger' }, 'Delete'),
            )],
            interactions: [{ click: '[data-part="trigger"] [data-sigta="button"]' }],
        },
    ],
});
