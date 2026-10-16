<script lang="ts">
    /* The Sigta compass: the brand mark as a loader. State lives in data
       attributes, so changing the props drives it; the runtime lands a
       working compass when it turns done. */
    import type { HTMLAttributes } from 'svelte/elements';
    import { onMount } from 'svelte';
    import { ensureRuntime } from '../runtime/runtime.js';

    type State = 'idle' | 'queued' | 'working' | 'done' | 'failed';
    interface Props extends HTMLAttributes<HTMLSpanElement> {
        size?: 'xs' | 'sm' | 'base' | 'lg' | 'xl';
        variant?: 'compass' | 'sketch';
        state?: State;
        value?: number | null;
        label?: string | null;
        decorative?: boolean;
    }

    let { size = 'base', variant = 'compass', state = 'working', value = null, label = null, decorative = false, ...rest }: Props = $props();

    const sizes = ['xs', 'sm', 'base', 'lg', 'xl'];
    const states = ['idle', 'queued', 'working', 'done', 'failed'];
    const labels: Record<string, string> = { queued: 'Queued', done: 'Done', failed: 'Failed', idle: 'Ready' };

    const box = $derived(sizes.includes(size) ? size : 'base');
    const current = $derived((states.includes(state) ? state : 'working') as State);
    const kind = $derived(variant === 'sketch' ? 'sketch' : 'compass');
    const percent = $derived(value === null || value === undefined ? null : current === 'done' ? 100 : Math.min(100, Math.max(0, Number(value))));
    const name = $derived(label ?? labels[current] ?? 'Working');

    onMount(ensureRuntime);
</script>

<span
    data-sigta="loader"
    data-state={current}
    data-variant={kind}
    data-size={box}
    data-progress={percent !== null ? '' : undefined}
    style={percent !== null ? `--sigta-progress: ${Math.round(percent * 100) / 100}` : undefined}
    aria-hidden={decorative ? 'true' : undefined}
    role={decorative ? undefined : 'progressbar'}
    aria-label={decorative ? undefined : name}
    aria-valuemin={!decorative && percent !== null ? '0' : undefined}
    aria-valuemax={!decorative && percent !== null ? '100' : undefined}
    aria-valuenow={!decorative && percent !== null ? String(Math.round(percent)) : undefined}
    {...rest}
>
    <svg viewBox="0 0 40 40" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <path class="sigta-loader-frame" pathLength="100" d="M20 3H31A6 6 0 0 1 37 9V31A6 6 0 0 1 31 37H9A6 6 0 0 1 3 31V9A6 6 0 0 1 9 3Z" />
        <path class="sigta-loader-ring sigta-loader-ring-back" pathLength="100" d="M11 29A9 2.8 0 0 1 29 29" />
        <path class="sigta-loader-leg sigta-loader-needle" pathLength="100" d="M20 12V29" />
        <path class="sigta-loader-leg sigta-loader-pencil" pathLength="100" d="M20 12V29" />
        <path class="sigta-loader-ring sigta-loader-ring-front" pathLength="100" d="M29 29A9 2.8 0 0 1 11 29" />
        <path class="sigta-loader-arc" pathLength="100" d="M12.75 25.7A15.5 15.5 0 0 0 27.25 25.7" />
        <circle class="sigta-loader-pivot" cx="20" cy="12" r="2.6" fill="currentColor" stroke="none" />
    </svg>
</span>
