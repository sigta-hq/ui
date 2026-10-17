<script lang="ts">
    /* Section header — the heading on the left (an eyebrow above it), a
       graphite lede on the right, baseline-aligned. The lede is the prop or,
       without one, the children. */
    import type { Snippet } from 'svelte';
    import type { HTMLAttributes } from 'svelte/elements';
    import Eyebrow from './Eyebrow.svelte';
    import Heading from './Heading.svelte';
    import Text from './Text.svelte';

    interface Props extends HTMLAttributes<HTMLDivElement> {
        heading?: string | null;
        eyebrow?: string | null;
        lede?: string | null;
        children?: Snippet;
    }

    let { heading = null, eyebrow = null, lede = null, children, ...rest }: Props = $props();
</script>

<div data-sigta="section-header" {...rest}>
    <div>
        {#if eyebrow}
            <Eyebrow data-scope="section-header" data-part="eyebrow">{eyebrow}</Eyebrow>
        {/if}
        <Heading level={2} size="xl" data-scope="section-header" data-part="heading">{heading ?? ''}</Heading>
    </div>
    {#if lede !== null && lede !== undefined}
        <Text variant="muted" data-scope="section-header" data-part="lede">{lede}</Text>
    {:else if children}
        <Text variant="muted" data-scope="section-header" data-part="lede">{@render children()}</Text>
    {/if}
</div>
