<script lang="ts">
    /* One timeline entry: a dot on the rail (an accent dot for the
       milestone), a mono meta line, a heading and body copy — each optional. */
    import type { Snippet } from 'svelte';
    import type { HTMLLiAttributes } from 'svelte/elements';

    interface Props extends HTMLLiAttributes {
        heading?: string | null;
        meta?: string | null;
        variant?: 'default' | 'accent';
        children?: Snippet;
    }

    let { heading = null, meta = null, variant = 'default', children, ...rest }: Props = $props();
</script>

<li data-sigta="timeline.item" data-variant={variant} {...rest}>
    <span aria-hidden="true" data-scope="timeline.item" data-part="dot"></span>

    {#if meta}
        <p data-scope="timeline.item" data-part="meta">{meta}</p>
    {/if}

    {#if heading}
        <h3 data-scope="timeline.item" data-part="heading">{heading}</h3>
    {/if}

    {#if children}
        <div data-scope="timeline.item" data-part="body">{@render children()}</div>
    {/if}
</li>
