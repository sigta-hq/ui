<script lang="ts">
    /* Text — body copy; lede, muted, caption and hand variants. */
    import type { Snippet } from 'svelte';
    import type { HTMLAttributes } from 'svelte/elements';

    interface Props extends HTMLAttributes<HTMLParagraphElement> {
        variant?: 'body' | 'lede' | 'muted' | 'caption' | 'hand';
        children?: Snippet;
    }

    let { variant = 'body', children, ...rest }: Props = $props();
</script>

<p data-sigta="text" data-variant={variant} {...rest}>{@render children?.()}</p>
