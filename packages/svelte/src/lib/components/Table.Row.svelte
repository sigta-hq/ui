<script lang="ts">
    /* One body row (<tr>) of Table.Cell cells. */
    import type { Snippet } from 'svelte';
    import type { HTMLAttributes } from 'svelte/elements';

    interface Props extends HTMLAttributes<HTMLTableRowElement> {
        children?: Snippet;
    }

    let { children, ...rest }: Props = $props();
</script>

<tr data-sigta="table.row" {...rest}>{@render children?.()}</tr>
