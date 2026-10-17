import { defineComponent } from '../schema.ts';
import { c, el } from '../examples.ts';

const action = (label: string, icon: string) => c('button', { variant: 'ghost', size: 'sm', 'aria-label': label }, c('icon', { name: icon, size: 'sm' }));

export default defineComponent({
    name: 'comment',
    title: 'Comment',
    tier: 'components',
    page: 'comment',
    svelte: 'Comment',
    description: 'One authored comment in a feed: an optional avatar, the author with a mono timestamp line, the body, an owner actions slot (edit / delete) and an inline edit slot shown while editing. Presentational — persistence, validation and who-may-edit auth stay in your app.',
    aria: 'An <article>; the avatar is labelled with the name it shows.',
    props: {
        author: { type: 'string', default: null, description: 'Author display name.' },
        meta: { type: 'string', default: null, description: 'Secondary mono line beside the author, usually the timestamp ("2h ago").' },
        avatar: { type: 'string', default: null, description: 'Name (or initials) for a leading avatar; unset (with no src) = no avatar column.' },
        src: { type: 'string', default: null, description: 'Avatar image URL (falls back to initials from avatar).' },
        editing: { type: 'boolean', default: false, description: 'Render the edit slot instead of the body.' },
    },
    parts: {
        avatar: { description: 'The avatar.' },
        main: { description: 'The column beside the avatar.' },
        header: { description: 'Byline and actions, on one baseline.' },
        byline: { description: 'Author and meta.' },
        author: { description: 'The author (truncates).' },
        meta: { description: 'The timestamp line.' },
        actions: { description: 'The actions slot.' },
        content: { description: 'Holds the body, or the edit slot while editing.' },
        body: { description: 'The comment text.' },
    },
    slots: {
        default: { description: 'The comment body.', required: true },
        actions: { description: 'Owner controls (edit / delete), at the right of the byline. Only rendered when given.' },
        edit: { description: 'The inline editor, shown instead of the body while editing.' },
    },
    uses: ['avatar'],
    usage: {
        livewire: `<x-sigta::comment :author="$c->author?->name" :meta="$c->created_at?->diffForHumans()" :editing="$editingId === $c->id" wire:key="comment-{{ $c->id }}">
    {{ $c->body }}
    @if ($c->user_id === auth()->id())
        <x-slot:actions><sigta:button variant="ghost" size="sm" wire:click="edit({{ $c->id }})"><sigta:icon name="pencil" size="sm" /></sigta:button></x-slot:actions>
    @endif
</x-sigta::comment>`,
        svelte: `{#each comments as c (c.id)}
    <Comment author={c.author?.name} meta={c.createdAgo} editing={editingId === c.id} actions={c.userId === me.id ? ownerActions : undefined}>
        {c.body}
        {#snippet edit()}
            <Textarea bind:value={draft} rows={3} />
        {/snippet}
    </Comment>
{/each}

{#snippet ownerActions()}
    <Button variant="ghost" size="sm" aria-label="Edit" onclick={() => startEditing(c)}><Icon name="pencil" size="sm" /></Button>
{/snippet}`,
    },
    notes: {
        shared: [
            'Presentational: persistence, validation and who-may-edit auth stay in your app. Loop it for a feed; pair it with composer or a textarea form to add new ones.',
            'Pass meta a ready string ("2h ago", a date) — the component does not format time.',
        ],
        livewire: ['Give each comment a wire:key. Wrap the actions slot in @if for the owner only: an empty <x-slot:actions> still renders the actions column.'],
        svelte: ['Pass the actions snippet only to the owner (actions={mine ? ownerActions : undefined}): a given snippet renders the actions column. Key the {#each} by id.'],
    },
    examples: [
        {
            label: 'Comment feed with owner actions',
            tree: [el('div', { class: 'max-w-xl' },
                c('comment', { author: 'Ada Lovelace', meta: '2h ago', avatar: 'Ada Lovelace' },
                    'Shipping the analytical-engine notes today — the append-only ledger idea holds up well.'),
                c('comment', { author: 'Alan Turing', meta: '1h ago', avatar: 'Alan Turing' },
                    { actions: [action('Edit note', 'pencil'), action('Delete note', 'trash-2')] },
                    'Agreed. Let\'s add a decidability test before we merge.'),
            )],
        },
        {
            label: 'Editing state (inline editor slot)',
            tree: [el('div', { class: 'max-w-xl' },
                c('comment', { editing: true, author: 'Grace Hopper', meta: 'just now', avatar: 'Grace Hopper' },
                    {
                        edit: [
                            c('textarea', { rows: 3, 'aria-label': 'Edit note', value: 'The original body text.' }),
                            el('div', { class: 'mt-2 flex gap-2' },
                                c('button', { variant: 'primary', size: 'sm' }, 'Save'),
                                c('button', { variant: 'ghost', size: 'sm' }, 'Cancel'),
                            ),
                        ],
                    },
                    'The original body text.'),
            )],
        },
        {
            label: 'Without an avatar',
            tree: [el('div', { class: 'max-w-xl' },
                c('comment', { author: 'Margaret Hamilton', meta: '14 Oct 2026' }, 'The priority display saved the landing — keep it.'),
                c('comment', { author: 'Katherine Johnson', meta: '15 Oct 2026' }, 'Numbers check out.'),
            )],
        },
    ],
});
