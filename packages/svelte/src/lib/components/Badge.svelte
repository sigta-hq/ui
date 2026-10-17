<script lang="ts">
    /* Badge — a small mono pill with a 1.5px border: outline, solid or accent. */
    import type { Snippet } from 'svelte';
    import type { HTMLAttributes } from 'svelte/elements';

    interface Props extends HTMLAttributes<HTMLSpanElement> {
        variant?: 'outline' | 'solid' | 'accent';
        children?: Snippet;
    }

    let { variant = 'outline', children, ...rest }: Props = $props();
</script>

<span data-sigta="badge" data-variant={variant} {...rest}>{@render children?.()}</span>
