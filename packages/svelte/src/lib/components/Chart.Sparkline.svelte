<script lang="ts">
    /* Sparkline — a tiny smooth hand-drawn line in a 200×40 viewBox with a
       dot on the latest value. currentColor throughout; decorative unless
       given a label. The geometry comes from @sigta-hq/core, the same
       numbers the Blade view prints. */
    import type { SVGAttributes } from 'svelte/elements';
    import { sparkline } from '@sigta-hq/core';

    interface Props extends SVGAttributes<SVGSVGElement> {
        values?: number[];
        label?: string | null;
    }

    let { values = [], label = null, ...rest }: Props = $props();

    const chart = $derived(sparkline(values));
</script>

<svg
    viewBox="0 0 {chart.width} {chart.height}"
    preserveAspectRatio="none"
    xmlns="http://www.w3.org/2000/svg"
    role={label ? 'img' : undefined}
    aria-label={label || undefined}
    aria-hidden={label ? undefined : 'true'}
    data-sigta="chart.sparkline"
    {...rest}
>
    {#if chart.path !== ''}
        <path d={chart.path} fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" vector-effect="non-scaling-stroke" />
    {/if}
    {#if chart.end}
        <circle cx={chart.end.x} cy={chart.end.y} r="3.5" fill="currentColor" />
    {/if}
</svg>
