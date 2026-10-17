<script lang="ts">
    /* Progress — a determinate or indeterminate bar. Attributes land on the
       progressbar element itself; label adds a mono header row with the
       percentage. */
    import type { HTMLAttributes } from 'svelte/elements';
    import { phpFloat, progressPercent } from '@sigta-hq/core';

    interface Props extends HTMLAttributes<HTMLDivElement> {
        value?: number;
        max?: number;
        label?: string | null;
        indeterminate?: boolean;
    }

    let { value = 0, max = 100, label = null, indeterminate = false, id, ...rest }: Props = $props();

    const uid = $props.id();
    const trackId = $derived(id ?? `sigta-${uid}`);
    const top = $derived(Math.max(Number(max) || 0, 1e-9));
    const percent = $derived(progressPercent(value, max));
</script>

{#snippet track()}
    <div
        data-sigta="progress"
        id={trackId}
        role="progressbar"
        aria-valuemin="0"
        aria-valuemax={Number(phpFloat(top))}
        aria-valuenow={indeterminate ? undefined : value}
        aria-labelledby={label ? `${trackId}-label` : undefined}
        data-indeterminate={indeterminate ? '' : undefined}
        {...rest}
    >
        {#if indeterminate}
            <div data-scope="progress" data-part="fill"></div>
        {:else}
            <div data-scope="progress" data-part="fill" style="--sigta-progress: {phpFloat(percent)}"></div>
        {/if}
    </div>
{/snippet}

{#if label}
    <div data-scope="progress" data-part="field">
        <div data-scope="progress" data-part="header">
            <span id="{trackId}-label" data-scope="progress" data-part="label">{label}</span>
            {#if !indeterminate}
                <span data-scope="progress" data-part="value">{Math.round(percent)}%</span>
            {/if}
        </div>
        {@render track()}
    </div>
{:else}
    {@render track()}
{/if}
