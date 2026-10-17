<script lang="ts">
    /* Annot — a handwritten margin note (Caveat), decorative by default. tone
       is the API; muted is kept for back-compat (tone wins when both are given). */
    import type { Snippet } from 'svelte';
    import type { HTMLAttributes } from 'svelte/elements';

    interface Props extends HTMLAttributes<HTMLSpanElement> {
        rotate?: number | string;
        tone?: 'ink' | 'muted' | 'accent' | null;
        muted?: boolean;
        decorative?: boolean;
        children?: Snippet;
    }

    let { rotate = -6, tone = null, muted = false, decorative = true, children, ...rest }: Props = $props();

    const resolved = $derived(tone ?? (muted ? 'muted' : 'ink'));
    // PHP's (float) cast, as the Blade view prints it: a leading number, else 0.
    const angle = $derived(typeof rotate === 'number' ? rotate : parseFloat(String(rotate)) || 0);
</script>

<span data-tone={resolved} style="transform: rotate({angle}deg)" aria-hidden={decorative ? 'true' : undefined} data-sigta="annot" {...rest}>{@render children?.()}</span>
