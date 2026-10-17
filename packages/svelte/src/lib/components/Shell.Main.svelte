<script lang="ts">
    /* The shell's padded main column. Its bottom padding grows by a visible
       floating bar's footprint (--sigta-floating-space). */
    import type { Snippet } from 'svelte';
    import type { HTMLAttributes } from 'svelte/elements';

    interface Props extends HTMLAttributes<HTMLDivElement> {
        children?: Snippet;
    }

    let { children, ...rest }: Props = $props();
</script>

<div data-sigta="shell.main" {...rest}>
    {@render children?.()}
</div>
