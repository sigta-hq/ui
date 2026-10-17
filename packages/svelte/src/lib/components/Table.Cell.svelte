<script lang="ts">
    /* A body cell (<td>) over a hairline, in tabular numerals. */
    import type { Snippet } from 'svelte';
    import type { HTMLTdAttributes } from 'svelte/elements';

    interface Props extends HTMLTdAttributes {
        children?: Snippet;
    }

    let { children, ...rest }: Props = $props();
</script>

<td data-sigta="table.cell" {...rest}>{@render children?.()}</td>
