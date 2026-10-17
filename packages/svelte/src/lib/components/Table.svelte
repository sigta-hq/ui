<script lang="ts">
    /* Table — a brand data table: mono uppercase caption and headers,
       hairline rows, tabular numerals. Compose Table.Columns / Table.Column
       and Table.Rows / Table.Row / Table.Cell inside it. Attributes land on
       the <table>; the root is the wrapper that scrolls sideways. */
    import type { Snippet } from 'svelte';
    import type { HTMLTableAttributes } from 'svelte/elements';

    interface Props extends HTMLTableAttributes {
        caption?: string | null;
        children?: Snippet;
    }

    let { caption = null, children, ...rest }: Props = $props();
</script>

<div data-sigta="table">
    <table data-scope="table" data-part="table" {...rest}>
        {#if caption}
            <caption data-scope="table" data-part="caption">{caption}</caption>
        {/if}
        {@render children?.()}
    </table>
</div>
