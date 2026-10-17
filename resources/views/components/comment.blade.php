@props([
    'author' => null,      // author display name
    'meta' => null,        // secondary line (e.g. "2h ago")
    'avatar' => null,      // name/initials for a leading avatar; null → no avatar column
    'src' => null,         // avatar image src (falls back to initials from :avatar)
    'editing' => false,    // render the `edit` slot instead of the body
])

{{--
    One authored comment in a feed: author + timestamp, the body, an owner
    `actions` slot (edit / delete), and an inline `edit` slot shown when
    :editing. Persistence, validation, and who-may-edit auth stay in your
    Livewire component — this is the presentational row. Loop it for a feed and
    pair it with <sigta:composer> or a <sigta:textarea> form to add new ones.

        @foreach ($comments as $c)
            <x-sigta::comment
                :author="$c->author?->name"
                :meta="$c->created_at?->diffForHumans()"
                :editing="$editingId === $c->id"
                wire:key="comment-{{ $c->id }}"
            >
                {{ $c->body }}

                @if ($c->user_id === auth()->id())
                    <x-slot:actions>
                        <x-sigta::button variant="ghost" size="sm" wire:click="edit({{ $c->id }})" aria-label="Edit">
                            <x-sigta::icon name="pencil" size="sm" />
                        </x-sigta::button>
                    </x-slot:actions>
                @endif

                <x-slot:edit>
                    <x-sigta::textarea wire:model="editBody" rows="3" />
                    <x-sigta::button variant="primary" size="sm" wire:click="save">Save</x-sigta::button>
                </x-slot:edit>
            </x-sigta::comment>
        @endforeach
--}}
@php
$hasAvatar = $avatar !== null || $src !== null;
@endphp

<article data-sigta="comment" {{ $attributes }}>
    @if ($hasAvatar)
        <x-sigta::avatar :name="$avatar" :src="$src" size="sm" data-scope="comment" data-part="avatar" />
    @endif

    <div data-scope="comment" data-part="main">
        <div data-scope="comment" data-part="header">
            <div data-scope="comment" data-part="byline">
                @if ($author)
                    <span data-scope="comment" data-part="author">{{ $author }}</span>
                @endif
                @if ($meta)
                    <span data-scope="comment" data-part="meta">{{ $meta }}</span>
                @endif
            </div>

            @isset($actions)
                <div data-scope="comment" data-part="actions">{{ $actions }}</div>
            @endisset
        </div>

        <div data-scope="comment" data-part="content">
            @if ($editing && isset($edit))
                {{ $edit }}
            @else
                <div data-scope="comment" data-part="body">{{ $slot }}</div>
            @endif
        </div>
    </div>
</article>
