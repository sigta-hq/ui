<script lang="ts">
    /* Section: hero — eyebrow, display heading (the children, else the
       heading prop as HTML, else the brand line with its mark), lede, two
       calls to action and a row of inline figures beside the visual
       (default: a hand-drawn sea chart). The default figures are the brand
       ones both runtimes share (@sigta-hq/contract/sections). */
    import type { Snippet } from 'svelte';
    import type { HTMLAttributes } from 'svelte/elements';
    import { sections, type HeroStat } from '@sigta-hq/contract/sections';
    import Button from './Button.svelte';
    import Eyebrow from './Eyebrow.svelte';
    import Heading from './Heading.svelte';
    import Mark from './Mark.svelte';
    import Stat from './Stat.svelte';
    import Text from './Text.svelte';
    import { truthy } from '../runtime/sections.js';

    interface Props extends HTMLAttributes<HTMLElement> {
        eyebrow?: string | null;
        heading?: string | null;
        lede?: string | null;
        primaryLabel?: string | null;
        primaryHref?: string | null;
        target?: string | null;
        secondaryLabel?: string | null;
        secondaryHref?: string | null;
        secondaryTarget?: string | null;
        stats?: HeroStat[] | null;
        children?: Snippet;
        visual?: Snippet;
    }

    let {
        eyebrow = 'Tech-partner · Hjælp, rådgivning og udvikling',
        heading = null,
        lede = 'Sigta er den tech-partner, du kan få: hjælp, rådgivning og udvikling til virksomheder, der ikke har deres egen tekniske ledelse. Vi taler begge sprog, forretning og teknik.',
        primaryLabel = 'Book en intro-samtale',
        primaryHref = 'https://calendly.com/sf-sigta/30min',
        target = '_blank',
        secondaryLabel = 'Se hvordan vi arbejder',
        secondaryHref = '#hjaelp',
        secondaryTarget = null,
        stats = null,
        children,
        visual,
        ...rest
    }: Props = $props();

    const figures = $derived(stats ?? sections.hero.stats);
</script>

<section data-sigta="section.hero" {...rest}>
    <div data-scope="section.hero" data-part="grid">
        <div>
            {#if truthy(eyebrow)}
                <Eyebrow data-scope="section.hero" data-part="eyebrow">{eyebrow}</Eyebrow>
            {/if}

            <Heading level={1} size="display">{#if children}{@render children()}{:else if heading !== null && heading !== undefined}{@html heading}{:else}Teknologi, <em>oversat</em> til <Mark>forretning</Mark>. Og&nbsp;tilbage&nbsp;igen.{/if}</Heading>

            {#if truthy(lede)}
                <Text variant="lede" data-scope="section.hero" data-part="lede">{lede}</Text>
            {/if}

            <div data-scope="section.hero" data-part="actions">
                {#if truthy(primaryLabel)}
                    <Button variant="cta" href={primaryHref} target={target ?? undefined}>
                        {primaryLabel} <span data-scope="section.hero" data-part="arrow" aria-hidden="true">→</span>
                    </Button>
                {/if}
                {#if truthy(secondaryLabel)}
                    <Button href={secondaryHref} target={secondaryTarget ?? undefined}>{secondaryLabel}</Button>
                {/if}
            </div>

            {#if truthy(figures)}
                <div data-scope="section.hero" data-part="stats">
                    {#each figures as stat, index (index)}
                        <Stat variant="inline" value={stat.value ?? null} label={stat.label ?? null} />
                    {/each}
                </div>
            {/if}
        </div>

        <div data-scope="section.hero" data-part="visual">
            {#if visual}
                {@render visual()}
            {:else}
                <svg viewBox="0 0 360 300" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Et håndtegnet søkort: en stiplet rute fra 'I er her' uden om forhindringerne 'dyrt' og 'teknisk gæld' til et kryds markeret 'sikker havn'.">
                    <defs>
                        <pattern id="soekort-dots" width="20" height="20" patternUnits="userSpaceOnUse">
                            <circle cx="2" cy="2" r="1" style="fill: var(--sigta-line)" />
                        </pattern>
                    </defs>
                    <circle cx="60" cy="64" r="22" fill="none" stroke="currentColor" stroke-width="2" />
                    <path d="M60 46 L65 64 L60 82 L55 64 Z" fill="currentColor" />
                    <text x="60" y="38" text-anchor="middle" font-family="JetBrains Mono, monospace" font-size="11" fill="currentColor">N</text>
                    <path d="M120 96 c10 -14 34 -12 40 2 s-4 26 -22 24 -28 -12 -18 -26z" style="fill: var(--sigta-surface-raised)" stroke="currentColor" stroke-width="2" />
                    <text x="140" y="112" text-anchor="middle" font-family="Caveat, cursive" font-size="16" style="fill: var(--sigta-fg-muted)">dyrt</text>
                    <path d="M166 212 c2 -18 28 -28 64 -28 s66 10 64 28 -28 30 -64 30 -66 -12 -64 -30z" style="fill: var(--sigta-surface-raised)" stroke="currentColor" stroke-width="2" />
                    <text x="230" y="219" text-anchor="middle" font-family="Caveat, cursive" font-size="17" style="fill: var(--sigta-fg-muted)">teknisk gæld</text>
                    <circle cx="46" cy="252" r="7" fill="currentColor" />
                    <text x="60" y="274" font-family="Caveat, cursive" font-size="19" fill="currentColor">I er her</text>
                    <path d="M46 252 C70 220 88 150 122 140 S170 168 196 158 232 108 262 92 300 74 306 62" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-dasharray="3 7" />
                    <rect x="286" y="38" width="46" height="34" rx="3" style="fill: var(--sigta-accent)" opacity=".9" transform="rotate(-5 309 55)" />
                    <path d="M300 48 L318 66 M318 48 L300 66" stroke="currentColor" stroke-width="3" stroke-linecap="round" />
                    <ellipse cx="309" cy="57" rx="26" ry="20" fill="none" style="stroke: var(--sigta-danger)" stroke-width="1.8" transform="rotate(-8 309 57)" />
                    <text x="298" y="102" text-anchor="middle" font-family="Caveat, cursive" font-size="20" fill="currentColor" transform="rotate(-4 298 102)">sikker havn</text>
                </svg>
            {/if}
        </div>
    </div>
</section>
