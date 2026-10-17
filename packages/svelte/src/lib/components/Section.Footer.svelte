<script lang="ts">
    /* Section: footer — the site footer: the brand and a tagline, columns of
       links under mono headings, a dashed signoff line. A <footer> landmark.
       The default columns are the brand ones both runtimes share
       (@sigta-hq/contract/sections). */
    import type { HTMLAttributes } from 'svelte/elements';
    import { sections, type FooterColumn } from '@sigta-hq/contract/sections';
    import Brand from './Brand.svelte';
    import { truthy } from '../runtime/sections.js';

    interface Props extends HTMLAttributes<HTMLElement> {
        tagline?: string | null;
        signoff?: string | null;
        columns?: FooterColumn[];
    }

    let {
        tagline = 'Sigta er tech-partneren for virksomheder uden egen teknisk ledelse: hjælp, rådgivning og udvikling.',
        signoff = '© 2026 Sigta · CVR 45530760',
        columns = sections.footer.columns,
        ...rest
    }: Props = $props();
</script>

<footer data-sigta="section.footer" {...rest}>
    <div data-scope="section.footer" data-part="wrap">
        <div data-scope="section.footer" data-part="grid">
            <div>
                <Brand />
                {#if truthy(tagline)}
                    <p data-scope="section.footer" data-part="tagline">{tagline}</p>
                {/if}
            </div>
            {#each columns as column, index (index)}
                <div>
                    <h4 data-scope="section.footer" data-part="heading">{column.heading}</h4>
                    <ul data-scope="section.footer" data-part="links">
                        {#each column.links as link, i (i)}
                            <li><a href={link.href} data-scope="section.footer" data-part="link">{link.label}</a></li>
                        {/each}
                    </ul>
                </div>
            {/each}
        </div>
        {#if truthy(signoff)}
            <div data-scope="section.footer" data-part="signoff">
                <span>{signoff}</span>
            </div>
        {/if}
    </div>
</footer>
