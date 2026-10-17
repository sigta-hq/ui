<script lang="ts">
    /* Tile grid — TileGrid.Tile cells on a hairline-gap grid. Columns
       collapse on smaller screens: 4 → 2 → 1, 3 → 1, 2 → 1. */
    import type { Snippet } from 'svelte';
    import type { HTMLAttributes } from 'svelte/elements';

    interface Props extends HTMLAttributes<HTMLDivElement> {
        cols?: 2 | 3 | 4 | number | string;
        children?: Snippet;
    }

    let { cols = 3, children, ...rest }: Props = $props();

    // A whole number, the way the Blade view casts it; any count other than 2 or 4 reads as 3.
    const count = $derived(Math.trunc(Number(cols)) || 0);
</script>

<div data-sigta="tile-grid" data-cols={count} {...rest}>{@render children?.()}</div>
