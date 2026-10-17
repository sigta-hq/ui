<script lang="ts">
    /* One question: a native <details>/<summary> (keyboard and screen readers
       for free); the "+" turns into a cross while it is open. bind:open. */
    import type { Snippet } from 'svelte';
    import type { HTMLDetailsAttributes } from 'svelte/elements';
    import { useAccordion } from '../runtime/accordion.js';

    interface Props extends HTMLDetailsAttributes {
        heading: string;
        name?: string | null;
        open?: boolean;
        children?: Snippet;
    }

    let { heading, name = null, open = $bindable(false), children, ...rest }: Props = $props();

    const group = useAccordion();
    // Its own name, else the group's.
    const groupName = $derived(name ?? group?.name ?? null);
</script>

<details data-sigta="accordion.item" name={groupName || undefined} bind:open {...rest}>
    <summary data-scope="accordion.item" data-part="summary">
        {heading}
    </summary>
    <div data-scope="accordion.item" data-part="content">
        {@render children?.()}
    </div>
</details>
