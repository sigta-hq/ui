<script lang="ts">
    /* Skeleton — a pulsing placeholder in the shape of what is loading. Size it with classes. */
    import type { HTMLAttributes } from 'svelte/elements';

    interface Props extends HTMLAttributes<HTMLDivElement> {
        variant?: 'line' | 'heading' | 'block' | 'circle';
    }

    let { variant = 'line', ...rest }: Props = $props();
</script>

<div data-sigta="skeleton" aria-hidden="true" data-variant={variant} {...rest}></div>
