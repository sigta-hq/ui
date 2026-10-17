<script lang="ts">
    /* Section: about — a device-framed portrait beside a text column
       (eyebrow, heading, paragraphs). The portrait snippet replaces the
       placeholder and its hand-note. The default paragraphs are the brand
       copy both runtimes share (@sigta-hq/contract/sections). */
    import type { Snippet } from 'svelte';
    import type { HTMLAttributes } from 'svelte/elements';
    import { sections } from '@sigta-hq/contract/sections';
    import Annot from './Annot.svelte';
    import Eyebrow from './Eyebrow.svelte';
    import Heading from './Heading.svelte';
    import { truthy } from '../runtime/sections.js';

    interface Props extends HTMLAttributes<HTMLElement> {
        eyebrow?: string | null;
        heading?: string | null;
        paragraphs?: string[];
        portrait?: Snippet;
    }

    let {
        eyebrow = 'Om Sigta · Manden bag',
        heading = 'Jeg hedder Søren, og jeg taler begge sprog.',
        paragraphs = sections.about.paragraphs,
        portrait,
        ...rest
    }: Props = $props();
</script>

<section data-sigta="section.about" {...rest}>
    <div data-scope="section.about" data-part="wrap">
        <div data-scope="section.about" data-part="grid">
            <div data-scope="section.about" data-part="portrait">
                {#if portrait}
                    {@render portrait()}
                {:else}
                    <div data-scope="section.about" data-part="frame">
                        <svg viewBox="0 0 686 800" xmlns="http://www.w3.org/2000/svg" data-scope="section.about" data-part="image" role="img" aria-label="Pladsholder for sort-hvidt portræt af Søren Risbøll Frisk">
                            <rect width="686" height="800" style="fill: var(--sigta-surface-raised)" />
                            <rect x="24" y="24" width="638" height="752" rx="8" fill="none" style="stroke: var(--sigta-line)" stroke-width="2" stroke-dasharray="10 12" />
                            <path d="M343 176 c68 -4 122 52 120 126 -2 76 -52 134 -120 134 s-118 -58 -120 -134 c-2 -74 52 -122 120 -126z" fill="none" stroke="currentColor" stroke-width="5" stroke-linecap="round" />
                            <path d="M150 800 c6 -156 88 -234 193 -236 s189 78 193 236" fill="none" stroke="currentColor" stroke-width="5" stroke-linecap="round" />
                            <path d="M262 258 c8 26 32 42 60 40 M366 296 c22 4 42 -6 52 -24" fill="none" style="stroke: var(--sigta-line)" stroke-width="4" stroke-linecap="round" />
                            <text x="343" y="716" text-anchor="middle" font-family="JetBrains Mono, monospace" font-size="22" letter-spacing="4" style="fill: var(--sigta-fg-muted)">PORTRÆT PÅ VEJ</text>
                        </svg>
                    </div>
                    <Annot rotate={-5} data-scope="section.about" data-part="note">det er mig ✎</Annot>
                {/if}
            </div>
            <div>
                {#if truthy(eyebrow)}
                    <Eyebrow data-scope="section.about" data-part="eyebrow">{eyebrow}</Eyebrow>
                {/if}
                <Heading level={2} size="xl" data-scope="section.about" data-part="heading">{heading}</Heading>
                {#each paragraphs as paragraph, index (index)}
                    <p data-scope="section.about" data-part="paragraph">{paragraph}</p>
                {/each}
            </div>
        </div>
    </div>
</section>
