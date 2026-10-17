<script lang="ts">
    /* Card — a raised surface with an optional thumb above a padded body;
       lifts on hover unless hover={false}. A padding class on the card
       replaces the body's own padding instead of stacking with it. */
    import type { Snippet } from 'svelte';
    import type { HTMLAttributes } from 'svelte/elements';
    import { ownsPadding } from '@sigta-hq/core';

    interface Props extends HTMLAttributes<HTMLDivElement> {
        hover?: boolean;
        thumb?: Snippet;
        children?: Snippet;
    }

    let { hover = true, thumb, children, class: klass, ...rest }: Props = $props();
</script>

<div data-sigta="card" data-hover={hover ? '' : undefined} data-padding={ownsPadding(klass) ? 'custom' : undefined} class={klass} {...rest}>
    {#if thumb}
        <div data-scope="card" data-part="thumb">{@render thumb()}</div>
    {/if}
    <div data-scope="card" data-part="body">{@render children?.()}</div>
</div>
