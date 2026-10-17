<script lang="ts">
    /* Line chart — a smooth ink line over a faded currentColor area and three
       hairline gridlines; the latest point gets an accent halo and a dashed
       drop line. Optional tick labels underneath. The geometry comes from
       @sigta-hq/core, the same numbers the Blade view prints. */
    import type { HTMLAttributes } from 'svelte/elements';
    import { lineChart } from '@sigta-hq/core';

    interface Props extends HTMLAttributes<HTMLDivElement> {
        values?: number[];
        labels?: string[] | null;
        height?: number;
        label?: string | null;
    }

    let { values = [], labels = null, height = 200, label = null, ...rest }: Props = $props();

    const uid = $props.id();
    const gradient = `sigta-chart-${uid}`;
    const chart = $derived(lineChart(values, height));
</script>

<div data-sigta="chart.line" {...rest}>
    <svg
        viewBox="0 0 {chart.width} {chart.height}"
        preserveAspectRatio="none"
        xmlns="http://www.w3.org/2000/svg"
        role={label ? 'img' : undefined}
        aria-label={label || undefined}
        aria-hidden={label ? undefined : 'true'}
        data-scope="chart.line"
        data-part="plot"
    >
        <defs>
            <linearGradient id={gradient} x1="0" y1="0" x2="0" y2="1">
                <stop offset="0" stop-color="currentColor" stop-opacity=".14" />
                <stop offset="1" stop-color="currentColor" stop-opacity="0" />
            </linearGradient>
        </defs>

        <g stroke="var(--sigta-line)" stroke-width="1">
            {#each chart.grid as y, i (i)}
                <path d="M0 {y} H{chart.width}" />
            {/each}
        </g>

        {#if chart.area !== ''}
            <path d={chart.area} fill="url(#{gradient})" />
        {/if}
        {#if chart.line !== ''}
            <path d={chart.line} fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" vector-effect="non-scaling-stroke" />
        {/if}

        {#if chart.end}
            <line x1={chart.end.x} y1={chart.end.y} x2={chart.end.x} y2={chart.height} stroke="var(--sigta-line)" stroke-dasharray="3 4" />
            <circle cx={chart.end.x} cy={chart.end.y} r="9" fill="var(--sigta-accent)" />
            <circle cx={chart.end.x} cy={chart.end.y} r="4" fill="currentColor" />
        {/if}
    </svg>

    {#if labels?.length}
        <div data-scope="chart.line" data-part="labels" aria-hidden="true">
            {#each labels as tick, i (i)}
                <span>{tick}</span>
            {/each}
        </div>
    {/if}
</div>
