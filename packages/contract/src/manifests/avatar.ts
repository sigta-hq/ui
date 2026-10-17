import { defineComponent } from '../schema.ts';
import { c, el } from '../examples.ts';

// A portrait as a data URI, percent-encoded so both runtimes write the same attribute.
export const portrait = 'data:image/svg+xml;utf8,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 80 80%27%3E%3Crect width=%2780%27 height=%2780%27 fill=%27gainsboro%27/%3E%3Ccircle cx=%2740%27 cy=%2731%27 r=%2714%27 fill=%27dimgray%27/%3E%3Cpath d=%27M12 76c4-17 16-25 28-25s24 8 28 25z%27 fill=%27dimgray%27/%3E%3C/svg%3E';

export default defineComponent({
    name: 'avatar',
    title: 'Avatar',
    tier: 'elements',
    page: 'avatar',
    svelte: 'Avatar',
    description: 'A person or team: their portrait, or mono initials from the first two words of the name when there is no src (the initials fallback). Circle or square, ink or hairline ground.',
    aria: '<img> with alt (alt ?? name) when src is set. Initials are role="img" labelled with alt ?? name, or aria-hidden without a name; passing aria-hidden leaves both to you (beside a visible name).',
    props: {
        src: { type: 'string', default: null, description: 'Image URL; without it the initials show.' },
        name: { type: 'string', default: null, description: 'The person\'s name: the initials come from its first two words, and it is the accessible name.' },
        alt: { type: 'string', default: null, description: 'Accessible name / alt text; defaults to name.' },
        shape: { type: 'enum', values: ['circle', 'square'], default: 'circle', description: 'circle, or square (rounded-base) for teams and workspaces.' },
        size: { type: 'enum', values: ['xs', 'sm', 'base', 'lg'], default: 'base', description: 'xs 24px, sm 32px, base 40px, lg 56px.' },
        tone: { type: 'enum', values: ['ink', 'line'], default: 'ink', description: 'Initials ground: ink fill, or a quiet hairline ground (line).' },
    },
    usage: {
        livewire: '<sigta:avatar name="Søren Frisk" />\n<sigta:avatar :src="$user->avatar_url" :name="$user->name" size="lg" />\n<sigta:avatar name="Marginalia Team" shape="square" tone="line" />',
        svelte: '<Avatar name="Søren Frisk" />\n<Avatar src={user.avatarUrl} name={user.name} size="lg" />\n<Avatar name="Marginalia Team" shape="square" tone="line" />',
    },
    notes: {
        shared: [
            'Use it instead of an <img class="rounded-full …">. Beside a visible name (a profile, a list row), pass aria-hidden="true" so the name is not read twice.',
        ],
    },
    examples: [
        {
            label: 'Sizes — initials fallback',
            tree: [el('div', { class: 'flex flex-wrap items-center gap-4' },
                c('avatar', { name: 'Søren Frisk', size: 'xs' }),
                c('avatar', { name: 'Søren Frisk', size: 'sm' }),
                c('avatar', { name: 'Søren Frisk' }),
                c('avatar', { name: 'Søren Frisk', size: 'lg' }),
            )],
        },
        {
            label: 'Shape & tone',
            tree: [el('div', { class: 'flex flex-wrap items-center gap-4' },
                c('avatar', { name: 'Marginalia Team' }),
                c('avatar', { name: 'Marginalia Team', tone: 'line' }),
                c('avatar', { name: 'Marginalia Team', shape: 'square' }),
                c('avatar', { name: 'Marginalia Team', shape: 'square', tone: 'line' }),
                c('avatar', { name: 'Plumb' }),
            )],
        },
        {
            label: 'With image',
            tree: [el('div', { class: 'flex flex-wrap items-center gap-4' },
                c('avatar', { src: portrait, name: 'Søren Frisk' }),
                c('avatar', { src: portrait, name: 'Søren Frisk', shape: 'square', size: 'lg' }),
            )],
        },
    ],
});
