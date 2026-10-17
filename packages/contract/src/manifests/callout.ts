import { defineComponent } from '../schema.ts';
import { c, el } from '../examples.ts';

export default defineComponent({
    name: 'callout',
    title: 'Callout',
    tier: 'components',
    page: 'callout',
    svelte: 'Callout',
    description: 'Inline alert with a heavy left accent border; the icon and the ARIA role are picked from the variant, with an optional bold lead-in heading.',
    aria: 'role="status" (info, success) or role="alert" (danger); pass role to override. The icon is decorative.',
    props: {
        variant: { type: 'enum', values: ['info', 'success', 'danger'], default: 'info', description: 'info (ink, triangle-alert), success (circle-check) or danger (circle-alert, role="alert").' },
        icon: { type: 'string', default: null, description: 'Icon name to use instead of the variant\'s.' },
        heading: { type: 'string', default: null, description: 'A bold Fraunces lead-in before the text.' },
    },
    parts: {
        icon: { description: 'The variant icon, in the variant colour.' },
        body: { description: 'The heading and text.' },
        heading: { description: 'The bold lead-in (only with heading).' },
    },
    slots: { default: { description: 'The message.', required: true } },
    uses: ['icon'],
    usage: {
        livewire: '<sigta:callout variant="success" heading="Deploy complete.">Plumb 2.4 is live.</sigta:callout>',
        svelte: '<Callout variant="success" heading="Deploy complete.">Plumb 2.4 is live.</Callout>',
    },
    notes: {
        shared: ['Use it instead of a hand-styled <div role="alert">.'],
    },
    examples: [
        {
            label: 'Variants',
            tree: [el('div', { class: 'flex flex-col gap-4' },
                c('callout', { heading: 'Heads up.' }, 'Two seats are unassigned. Add teammates before the trial ends Friday.'),
                c('callout', { variant: 'success', heading: 'Deploy complete.' }, 'Plumb 2.4 is live for all workspaces — no migration needed.'),
                c('callout', { variant: 'danger', heading: 'Payment failed.' }, 'We couldn\'t charge the card on file. Update billing to keep the workspace open.'),
            )],
        },
        {
            label: 'Without heading & with icon override',
            tree: [el('div', { class: 'flex flex-col gap-4' },
                c('callout', {}, 'Exports run nightly at 02:00 UTC. Yesterday\'s ledger is already in your inbox.'),
                c('callout', { icon: 'info', heading: 'For the record.' }, 'The info glyph, when a triangle feels too loud.'),
            )],
        },
    ],
});
