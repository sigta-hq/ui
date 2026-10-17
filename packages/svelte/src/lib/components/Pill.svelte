<script lang="ts">
    /* Pill — a status pill: a leading dot on a tint of the status colour. */
    import type { Snippet } from 'svelte';
    import type { HTMLAttributes } from 'svelte/elements';

    interface Props extends HTMLAttributes<HTMLSpanElement> {
        variant?: 'neutral' | 'success' | 'danger';
        children?: Snippet;
    }

    let { variant = 'neutral', children, ...rest }: Props = $props();
</script>

<span data-sigta="pill" data-variant={variant} {...rest}><span data-scope="pill" data-part="dot" aria-hidden="true"></span>{@render children?.()}</span>
