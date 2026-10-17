<script lang="ts">
    /* Section: pricing — a section header over tiers side by side, each a
       non-clipping panel (so a tier's hand-note can overhang it): label,
       title, a big figure (marked when asked) with its suffix, body
       paragraphs. The aside snippet fills the column beside a single tier.
       The default tiers are the brand ones both runtimes share
       (@sigta-hq/contract/sections). */
    import type { Snippet } from 'svelte';
    import type { HTMLAttributes } from 'svelte/elements';
    import { sections, type PricingTier } from '@sigta-hq/contract/sections';
    import Annot from './Annot.svelte';
    import Heading from './Heading.svelte';
    import Mark from './Mark.svelte';
    import Panel from './Panel.svelte';
    import SectionHeader from './SectionHeader.svelte';
    import { truthy } from '../runtime/sections.js';

    interface Props extends HTMLAttributes<HTMLElement> {
        heading?: string | null;
        lede?: string | null;
        tiers?: PricingTier[] | null;
        aside?: Snippet;
    }

    let {
        heading = 'To måder at samarbejde på.',
        lede = 'Én filosofi bag begge: du skal altid vide, hvad du betaler for.',
        tiers = null,
        aside,
        ...rest
    }: Props = $props();

    const list = $derived(tiers ?? sections.pricing.tiers);
</script>

<section data-sigta="section.pricing" {...rest}>
    <SectionHeader data-scope="section.pricing" data-part="header" {heading} {lede} />

    <div data-scope="section.pricing" data-part="tiers">
        {#each list as tier, index (index)}
            <Panel>
                {#if truthy(tier.annot)}
                    <Annot rotate={-5} data-scope="section.pricing" data-part="note">{tier.annot}</Annot>
                {/if}

                {#if truthy(tier.label)}
                    <span data-scope="section.pricing" data-part="label">{tier.label}</span>
                {/if}

                {#if truthy(tier.title)}
                    <Heading level={3} data-scope="section.pricing" data-part="title">{tier.title}</Heading>
                {/if}

                {#if truthy(tier.figure)}
                    <div data-scope="section.pricing" data-part="figure">
                        <span>{#if truthy(tier.mark)}<Mark>{tier.figure}</Mark>{:else}{tier.figure}{/if}</span>
                        {#if truthy(tier.figureSuffix)}
                            <small data-scope="section.pricing" data-part="suffix">{tier.figureSuffix}</small>
                        {/if}
                    </div>
                {/if}

                {#each tier.body ?? [] as paragraph, i (i)}
                    <p data-scope="section.pricing" data-part="body">{paragraph}</p>
                {/each}
            </Panel>
        {/each}

        {#if aside}
            <div data-scope="section.pricing" data-part="aside">{@render aside()}</div>
        {/if}
    </div>
</section>
