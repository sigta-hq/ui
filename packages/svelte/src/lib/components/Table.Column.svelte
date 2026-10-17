<script lang="ts">
    /* A header cell: <th scope="col"> in mono uppercase graphite over a hairline. */
    import type { Snippet } from 'svelte';
    import type { HTMLThAttributes } from 'svelte/elements';

    interface Props extends HTMLThAttributes {
        children?: Snippet;
    }

    let { children, ...rest }: Props = $props();
</script>

<th data-sigta="table.column" scope="col" {...rest}>{@render children?.()}</th>
