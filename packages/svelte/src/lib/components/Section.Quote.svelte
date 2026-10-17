<script lang="ts">
    /* Section: quote — a centred pull-quote in light Fraunces italic over a
       mono cite. */
    import type { HTMLAttributes } from 'svelte/elements';
    import { truthy } from '../runtime/sections.js';

    interface Props extends HTMLAttributes<HTMLElement> {
        quote?: string | null;
        cite?: string | null;
    }

    let {
        quote = 'Du behøver ikke forstå koden for at træffe gode beslutninger. Du skal bare kende én, der gør.',
        cite = 'Sådan arbejder Sigta',
        ...rest
    }: Props = $props();
</script>

<section data-sigta="section.quote" {...rest}>
    <div data-scope="section.quote" data-part="wrap">
        <blockquote data-scope="section.quote" data-part="quote">“{quote}”</blockquote>
        {#if truthy(cite)}
            <span data-scope="section.quote" data-part="cite">{cite}</span>
        {/if}
    </div>
</section>
