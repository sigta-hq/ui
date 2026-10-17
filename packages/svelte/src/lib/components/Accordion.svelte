<script lang="ts">
    /* A group of Accordion.Items. Its name reaches every item's <details>,
       so the browser keeps one open at a time. */
    import type { Snippet } from 'svelte';
    import type { HTMLAttributes } from 'svelte/elements';
    import { setAccordion } from '../runtime/accordion.js';

    interface Props extends HTMLAttributes<HTMLDivElement> {
        name?: string | null;
        children?: Snippet;
    }

    let { name = null, children, ...rest }: Props = $props();

    setAccordion({ get name() { return name; } });
</script>

<div data-sigta="accordion" {...rest}>
    {@render children?.()}
</div>
