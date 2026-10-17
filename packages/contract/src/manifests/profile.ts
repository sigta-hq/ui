import { defineComponent } from '../schema.ts';
import { c, el } from '../examples.ts';
import { portrait } from './avatar.ts';

export default defineComponent({
    name: 'profile',
    title: 'Profile',
    tier: 'components',
    page: 'avatar',
    svelte: 'Profile',
    description: 'Avatar + name + optional meta line (role, place, email). reverse mirrors the layout for a right edge.',
    aria: 'The avatar is aria-hidden: the name beside it is the text.',
    props: {
        name: { type: 'string', default: null, description: 'The person\'s name (also the avatar\'s initials).' },
        meta: { type: 'string', default: null, description: 'A mono line under the name.' },
        src: { type: 'string', default: null, description: 'Portrait URL for the avatar.' },
        reverse: { type: 'boolean', default: false, description: 'Avatar on the right, text right-aligned.' },
    },
    parts: {
        text: { description: 'The name and meta column.' },
        name: { description: 'The name.' },
        meta: { description: 'The meta line (only with meta).' },
    },
    states: {
        reverse: { attribute: 'data-reverse', description: 'Mirrored.' },
    },
    uses: ['avatar'],
    usage: {
        livewire: '<sigta:profile name="Ada Lovelace" meta="Engineering · Halyard OS" />\n<sigta:profile name="Nina Vale" meta="Design" :src="$user->avatar_url" reverse />',
        svelte: '<Profile name="Ada Lovelace" meta="Engineering · Halyard OS" />\n<Profile name="Nina Vale" meta="Design" src={user.avatarUrl} reverse />',
    },
    examples: [
        {
            label: 'Profile — name + meta',
            tree: [el('div', { class: 'flex flex-col gap-5' },
                c('profile', { name: 'Søren Frisk', meta: 'Studio lead · Copenhagen' }),
                c('profile', { name: 'Ada Marsh', meta: 'Engineering · Halyard OS', src: portrait }),
                el('div', { class: 'flex justify-end' },
                    c('profile', { name: 'Nina Vale', meta: 'Design · Marginalia', reverse: true }),
                ),
            )],
        },
    ],
});
