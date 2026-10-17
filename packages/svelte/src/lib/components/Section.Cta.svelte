<script lang="ts">
    /* Section: cta — a dark ink band with a centred heading, a muted lede and
       the inverse / ghost-inverse button pair; an accent hand-note overhangs
       the top-right corner on wider screens. */
    import type { HTMLAttributes } from 'svelte/elements';
    import Annot from './Annot.svelte';
    import Button from './Button.svelte';
    import { truthy } from '../runtime/sections.js';

    interface Props extends HTMLAttributes<HTMLElement> {
        heading?: string | null;
        lede?: string | null;
        primaryLabel?: string | null;
        primaryHref?: string | null;
        target?: string | null;
        secondaryLabel?: string | null;
        secondaryHref?: string | null;
        secondaryTarget?: string | null;
        annotation?: string | null;
    }

    let {
        heading = 'Skal vi tage en snak, før du skriver under på noget?',
        lede = 'En intro-samtale koster ingenting og forpligter ikke. Medbring gerne det tilbud, du er i tvivl om.',
        primaryLabel = 'Book en intro-samtale',
        primaryHref = 'https://calendly.com/sf-sigta/30min',
        target = null,
        secondaryLabel = 'Skriv en mail',
        secondaryHref = 'mailto:sf@sigta.dk',
        secondaryTarget = null,
        annotation = 'lad os tegne det op ↘',
        ...rest
    }: Props = $props();
</script>

<section data-sigta="section.cta" {...rest}>
    <div data-scope="section.cta" data-part="wrap">
        <div data-scope="section.cta" data-part="band">
            {#if truthy(annotation)}
                <Annot rotate={-6} tone="accent" data-scope="section.cta" data-part="note">{annotation}</Annot>
            {/if}
            <h2 data-scope="section.cta" data-part="heading">{heading}</h2>
            {#if truthy(lede)}
                <p data-scope="section.cta" data-part="lede">{lede}</p>
            {/if}
            <div data-scope="section.cta" data-part="actions">
                <Button variant="inverse" href={primaryHref} target={target ?? undefined}>{primaryLabel} <span data-scope="section.cta" data-part="arrow" aria-hidden="true">→</span></Button>
                <Button variant="ghost-inverse" href={secondaryHref} target={secondaryTarget ?? undefined}>{secondaryLabel}</Button>
            </div>
        </div>
    </div>
</section>
