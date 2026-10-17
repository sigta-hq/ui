<script lang="ts">
    /* Bar chart — ink bars, an optional highlighter swipe behind one bar
       (highlight) and an optional dashed trend line across the bar tops
       (trend). Optional labels, one under each bar. The geometry comes from
       @sigta-hq/core, the same numbers the Blade view prints. */
    import type { HTMLAttributes } from 'svelte/elements';
    import { barChart } from '@sigta-hq/core';

    interface Props extends HTMLAttributes<HTMLDivElement> {
        values?: number[];
        labels?: string[] | null;
        highlight?: number | null;
        trend?: boolean;
        height?: number;
        label?: string | null;
    }

    let { values = [], labels = null, highlight = null, trend = false, height = 160, label = null, ...rest }: Props = $props();

    const chart = $derived(barChart(values, { height, highlight, trend }));
</script>

<div data-sigta="chart.bars" {...rest}>
    <svg
        viewBox="0 0 {chart.width} {chart.height}"
        preserveAspectRatio="none"
        xmlns="http://www.w3.org/2000/svg"
        role={label ? 'img' : undefined}
        aria-label={label || undefined}
        aria-hidden={label ? undefined : 'true'}
        data-scope="chart.bars"
        data-part="plot"
    >
        {#if chart.swipe}
            <rect x={chart.swipe.x} y={chart.swipe.y} width={chart.swipe.width} height={chart.swipe.height} rx="2" fill="var(--sigta-accent)" opacity=".85" />
        {/if}

        {#each chart.bars as bar, i (i)}
            <rect x={bar.x} y={bar.y} width={bar.width} height={bar.height} rx="2" fill="currentColor" opacity={bar.dimmed ? '.88' : undefined} />
        {/each}

        {#if chart.trend !== ''}
            <path d={chart.trend} fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-dasharray="2 6" opacity=".7" vector-effect="non-scaling-stroke" />
        {/if}
    </svg>

    {#if labels?.length && chart.bars.length > 0}
        <div data-scope="chart.bars" data-part="labels" style="--sigta-chart-columns: {chart.bars.length}" aria-hidden="true">
            {#each labels as tick, i (i)}
                <span>{tick}</span>
            {/each}
        </div>
    {/if}
</div>
