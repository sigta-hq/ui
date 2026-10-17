<script lang="ts">
    /* Comment — one authored row in a feed: an optional avatar, the author
       with a mono timestamp line, the body, the owner's `actions` snippet and
       an inline `edit` snippet shown while editing. Presentational:
       persistence, validation and who may edit stay in your app. */
    import type { Snippet } from 'svelte';
    import type { HTMLAttributes } from 'svelte/elements';
    import Avatar from './Avatar.svelte';

    interface Props extends HTMLAttributes<HTMLElement> {
        author?: string | null;
        meta?: string | null;
        avatar?: string | null;
        src?: string | null;
        editing?: boolean;
        actions?: Snippet;
        edit?: Snippet;
        children?: Snippet;
    }

    let { author = null, meta = null, avatar = null, src = null, editing = false, actions, edit, children, ...rest }: Props = $props();

    const hasAvatar = $derived((avatar !== null && avatar !== undefined) || (src !== null && src !== undefined));
</script>

<article data-sigta="comment" {...rest}>
    {#if hasAvatar}
        <Avatar name={avatar} {src} size="sm" data-scope="comment" data-part="avatar" />
    {/if}

    <div data-scope="comment" data-part="main">
        <div data-scope="comment" data-part="header">
            <div data-scope="comment" data-part="byline">
                {#if author}
                    <span data-scope="comment" data-part="author">{author}</span>
                {/if}
                {#if meta}
                    <span data-scope="comment" data-part="meta">{meta}</span>
                {/if}
            </div>

            {#if actions}
                <div data-scope="comment" data-part="actions">{@render actions()}</div>
            {/if}
        </div>

        <div data-scope="comment" data-part="content">
            {#if editing && edit}
                {@render edit()}
            {:else}
                <div data-scope="comment" data-part="body">{@render children?.()}</div>
            {/if}
        </div>
    </div>
</article>
