<script lang="ts">
    /* Mono uppercase form label; inverse re-colours it for a dark surface. */
    import type { Snippet } from 'svelte';
    import type { HTMLLabelAttributes } from 'svelte/elements';

    interface Props extends HTMLLabelAttributes {
        variant?: 'default' | 'inverse';
        children?: Snippet;
    }

    let { variant = 'default', children, ...rest }: Props = $props();
</script>

<label data-sigta="label" data-variant={variant} {...rest}>{@render children?.()}</label>
