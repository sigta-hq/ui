import { defineComponent } from '../schema.ts';
import { c, el } from '../examples.ts';

export default defineComponent({
    name: 'separator',
    title: 'Separator',
    tier: 'elements',
    page: 'typography',
    svelte: 'Separator',
    description: 'Plain <hr> hairline, solid or dashed. Use inside content; use the rule for decorative dividers.',
    aria: 'Native <hr> (role separator).',
    props: {
        variant: { type: 'enum', values: ['solid', 'dashed'], default: 'solid', description: 'Line style.' },
    },
    notes: {
        livewire: ['Use it inside content; for a decorative divider between sections use <sigta:rule>.'],
        svelte: ['Use it inside content; for a decorative divider between sections use <Rule>.'],
    },
    examples: [
        {
            label: 'Solid and dashed',
            tree: [el('div', { class: 'flex flex-col gap-6' },
                c('separator', {}),
                c('separator', { variant: 'dashed' }),
            )],
        },
    ],
});
