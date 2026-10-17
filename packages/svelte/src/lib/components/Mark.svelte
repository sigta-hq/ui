<script lang="ts">
    /* Mark — a highlighter swipe behind a word or two. Use sparingly — one per view. */
    import type { Snippet } from 'svelte';
    import type { HTMLAttributes } from 'svelte/elements';

    interface Props extends HTMLAttributes<HTMLSpanElement> {
        children?: Snippet;
    }

    let { children, ...rest }: Props = $props();
</script>

<span data-sigta="mark" {...rest}>{@render children?.()}</span>
