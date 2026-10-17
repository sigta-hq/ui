<script lang="ts">
    /* One cell of a tile grid: raised paper with an optional mono number on top. */
    import type { Snippet } from 'svelte';
    import type { HTMLAttributes } from 'svelte/elements';

    interface Props extends HTMLAttributes<HTMLElement> {
        num?: string | number | null;
        children?: Snippet;
    }

    let { num = null, children, ...rest }: Props = $props();
</script>

<article data-sigta="tile" {...rest}>
    {#if num !== null && num !== undefined}
        <span data-scope="tile" data-part="num">{num}</span>
    {/if}
    {@render children?.()}
</article>
