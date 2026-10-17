<script lang="ts">
    /* Section: faq — a section header over a single-open accordion (the
       name groups the native <details>). The default entries are the brand
       FAQ both runtimes share (@sigta-hq/contract/sections). */
    import type { HTMLAttributes } from 'svelte/elements';
    import { sections, type FaqItem } from '@sigta-hq/contract/sections';
    import Accordion from './Accordion.svelte';
    import AccordionItem from './Accordion.Item.svelte';
    import SectionHeader from './SectionHeader.svelte';

    interface Props extends HTMLAttributes<HTMLElement> {
        heading?: string | null;
        lede?: string | null;
        name?: string | null;
        items?: FaqItem[];
    }

    let {
        heading = 'Spørgsmål, vi ofte får.',
        lede = 'Og de ærlige svar. Mangler dit, så spørg løs på intro-samtalen.',
        name = 'faq',
        items = sections.faq.items,
        ...rest
    }: Props = $props();
</script>

<section data-sigta="section.faq" {...rest}>
    <div data-scope="section.faq" data-part="wrap">
        <SectionHeader data-scope="section.faq" data-part="header" {heading} {lede} />
        <Accordion {name} data-scope="section.faq" data-part="accordion">
            {#each items as item, index (index)}
                <AccordionItem heading={item.q}>
                    <p>{item.a}</p>
                </AccordionItem>
            {/each}
        </Accordion>
    </div>
</section>
