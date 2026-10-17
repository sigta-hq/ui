<script lang="ts">
    /* Timeline — an ordered list with a hairline rail down the left and an
       ink dot per Timeline.Item. */
    import type { Snippet } from 'svelte';
    import type { HTMLOlAttributes } from 'svelte/elements';

    interface Props extends HTMLOlAttributes {
        children?: Snippet;
    }

    let { children, ...rest }: Props = $props();
</script>

<ol data-sigta="timeline" {...rest}>
    {@render children?.()}
</ol>
