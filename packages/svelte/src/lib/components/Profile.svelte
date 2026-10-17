<script lang="ts">
    /* Profile — an avatar beside a name and an optional meta line; reverse mirrors it. */
    import type { HTMLAttributes } from 'svelte/elements';
    import Avatar from './Avatar.svelte';

    interface Props extends HTMLAttributes<HTMLDivElement> {
        name?: string | null;
        meta?: string | null;
        src?: string | null;
        reverse?: boolean;
    }

    let { name = null, meta = null, src = null, reverse = false, ...rest }: Props = $props();
</script>

<div data-sigta="profile" data-reverse={reverse ? '' : undefined} {...rest}>
    <Avatar {src} {name} aria-hidden="true" />
    <div data-scope="profile" data-part="text">
        <span data-scope="profile" data-part="name">{name ?? ''}</span>
        {#if meta}
            <span data-scope="profile" data-part="meta">{meta}</span>
        {/if}
    </div>
</div>
