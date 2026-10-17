<script lang="ts">
    /* Rule — the brand's hand-drawn squiggle divider; three paths, decorative. */
    import type { HTMLAttributes } from 'svelte/elements';

    interface Props extends HTMLAttributes<HTMLDivElement> {
        variant?: number | string;
    }

    let { variant = 1, ...rest }: Props = $props();

    const paths: Record<number, string> = {
        1: 'M0 8 C140 4 260 11 420 7 S720 3 880 9 1120 6 1180 8',
        2: 'M0 7 C160 11 280 4 460 9 S760 12 920 6 1100 9 1180 7',
        3: 'M0 8 C200 4 300 11 480 7 S780 3 940 9 1120 6 1180 8',
    };

    // As the Blade view picks it: PHP's (int) cast, wrapping round the three paths.
    const d = $derived(paths[((Math.trunc(parseFloat(String(variant))) || 0) - 1) % 3 + 1] ?? paths[1]);
</script>

<div data-variant={variant} aria-hidden="true" data-sigta="rule" {...rest}>
    <svg data-scope="rule" data-part="line" viewBox="0 0 1180 14" preserveAspectRatio="none" fill="none">
        <path {d} stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
    </svg>
</div>
