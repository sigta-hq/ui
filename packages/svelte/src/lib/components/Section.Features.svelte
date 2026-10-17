<script lang="ts">
    /* Section: features — a section header over a tile grid of services
       (number, icon, title, text). An item's icon is inline <svg…> markup or
       an icon name. Without items it shows the four brand services both
       runtimes share (@sigta-hq/contract/sections). */
    import type { HTMLAttributes } from 'svelte/elements';
    import { sections, type FeatureItem } from '@sigta-hq/contract/sections';
    import Heading from './Heading.svelte';
    import Icon from './Icon.svelte';
    import SectionHeader from './SectionHeader.svelte';
    import TileGrid from './TileGrid.svelte';
    import TileGridTile from './TileGrid.Tile.svelte';
    import { truthy } from '../runtime/sections.js';

    interface Props extends HTMLAttributes<HTMLElement> {
        eyebrow?: string | null;
        heading?: string | null;
        lede?: string | null;
        cols?: 2 | 3 | 4 | number | string;
        items?: FeatureItem[] | null;
    }

    let {
        eyebrow = null,
        heading = 'Sådan hjælper vi.',
        lede = 'Fire måder at bruge en tech-partner på. De fleste samarbejder starter med én og vokser derfra.',
        cols = 4,
        items = null,
        ...rest
    }: Props = $props();

    const list = $derived(items ?? sections.features.items);
</script>

<section data-sigta="section.features" {...rest}>
    <SectionHeader data-scope="section.features" data-part="header" {eyebrow} {heading} {lede} />

    <TileGrid {cols}>
        {#each list as item, index (index)}
            <TileGridTile num={item.num ?? null} data-scope="section.features" data-part="tile">
                {#if truthy(item.icon)}
                    <span data-scope="section.features" data-part="icon" aria-hidden="true">
                        {#if String(item.icon).trim().startsWith('<svg')}{@html item.icon}{:else}<Icon name={String(item.icon)} data-scope="section.features" data-part="glyph" />{/if}
                    </span>
                {/if}
                {#if truthy(item.title)}
                    <Heading level={3} data-scope="section.features" data-part="title">{item.title}</Heading>
                {/if}
                {#if truthy(item.text)}
                    <p data-scope="section.features" data-part="text">{item.text}</p>
                {/if}
            </TileGridTile>
        {/each}
    </TileGrid>
</section>
