<script lang="ts">
    /* Eyebrow — a mono uppercase kicker with a leading dash, above section headings. */
    import type { Snippet } from 'svelte';
    import type { HTMLAttributes } from 'svelte/elements';

    interface Props extends HTMLAttributes<HTMLParagraphElement> {
        children?: Snippet;
    }

    let { children, ...rest }: Props = $props();
</script>

<p data-sigta="eyebrow" {...rest}>{@render children?.()}</p>
