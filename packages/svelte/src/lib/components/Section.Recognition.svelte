<script lang="ts">
    /* Section: recognition — a section header over a two-column tile grid of
       scene/answer cards, then a closing line and a call to action. The
       default cards are the brand ones both runtimes share
       (@sigta-hq/contract/sections). */
    import type { HTMLAttributes } from 'svelte/elements';
    import { sections, type RecognitionItem } from '@sigta-hq/contract/sections';
    import Button from './Button.svelte';
    import SectionHeader from './SectionHeader.svelte';
    import TileGrid from './TileGrid.svelte';
    import TileGridTile from './TileGrid.Tile.svelte';
    import { truthy } from '../runtime/sections.js';

    interface Props extends HTMLAttributes<HTMLElement> {
        heading?: string | null;
        lede?: string | null;
        items?: RecognitionItem[] | null;
        closingText?: string | null;
        ctaLabel?: string | null;
        ctaHref?: string | null;
        target?: string | null;
    }

    let {
        heading = 'Kan du genkende det?',
        lede = 'Fire situationer, hvor det hjælper at have en teknisk stemme på sin egen side af bordet.',
        items = null,
        closingText = 'Hvis bare ét af dem rammer, så lad os tage en kop kaffe.',
        ctaLabel = 'Book en intro-samtale',
        ctaHref = 'https://calendly.com/sf-sigta/30min',
        target = '_blank',
        ...rest
    }: Props = $props();

    const list = $derived(items ?? sections.recognition.items);
</script>

<section data-sigta="section.recognition" {...rest}>
    <SectionHeader data-scope="section.recognition" data-part="header" {heading} {lede} />

    <TileGrid cols={2}>
        {#each list as item, index (index)}
            <TileGridTile num={item.num ?? null} data-scope="section.recognition" data-part="tile">
                {#if truthy(item.scene)}
                    <p data-scope="section.recognition" data-part="scene">{item.scene}</p>
                {/if}
                {#if truthy(item.answer)}
                    <p data-scope="section.recognition" data-part="answer">{item.answer}</p>
                {/if}
            </TileGridTile>
        {/each}
    </TileGrid>

    {#if truthy(closingText) || truthy(ctaLabel)}
        <div data-scope="section.recognition" data-part="outro">
            {#if truthy(closingText)}
                <p data-scope="section.recognition" data-part="closing">{closingText}</p>
            {/if}
            {#if truthy(ctaLabel)}
                <Button href={ctaHref} target={target ?? undefined}>
                    {ctaLabel} <span data-scope="section.recognition" data-part="arrow" aria-hidden="true">→</span>
                </Button>
            {/if}
        </div>
    {/if}
</section>
