<script lang="ts">
    /* The body of a table: a <tbody> around the Table.Row rows. Its last row draws no rule under it. */
    import type { Snippet } from 'svelte';
    import type { HTMLAttributes } from 'svelte/elements';

    interface Props extends HTMLAttributes<HTMLTableSectionElement> {
        children?: Snippet;
    }

    let { children, ...rest }: Props = $props();
</script>

<tbody data-sigta="table.rows" {...rest}>{@render children?.()}</tbody>
