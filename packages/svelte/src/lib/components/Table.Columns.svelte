<script lang="ts">
    /* The header row of a table: a <thead> with one <tr> around the Table.Column cells. */
    import type { Snippet } from 'svelte';
    import type { HTMLAttributes } from 'svelte/elements';

    interface Props extends HTMLAttributes<HTMLTableSectionElement> {
        children?: Snippet;
    }

    let { children, ...rest }: Props = $props();
</script>

<thead data-sigta="table.columns" {...rest}>
    <tr>{@render children?.()}</tr>
</thead>
