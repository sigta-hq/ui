<script lang="ts">
    /* Section: proof — a centred strip between hairlines: an inline years
       figure and the client names as mono wordmarks. */
    import type { HTMLAttributes } from 'svelte/elements';
    import Stat from './Stat.svelte';

    interface Props extends HTMLAttributes<HTMLElement> {
        yearsValue?: string | number | null;
        yearsLabel?: string | null;
        logos?: string[];
    }

    let {
        yearsValue = '14 år',
        yearsLabel = 'erfaring med software',
        logos = ['Park Styling', 'Jelling Musikfestival'],
        ...rest
    }: Props = $props();
</script>

<section data-sigta="section.proof" aria-label="Erfaring og kunder" {...rest}>
    <div data-scope="section.proof" data-part="wrap">
        <div data-scope="section.proof" data-part="strip">
            <Stat variant="inline" value={yearsValue} label={yearsLabel} data-scope="section.proof" data-part="stat" />
            {#each logos as logo, index (index)}
                <span data-scope="section.proof" data-part="logo">{logo}</span>
            {/each}
        </div>
    </div>
</section>
