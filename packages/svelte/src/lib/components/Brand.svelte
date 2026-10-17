<script lang="ts">
    /* Brand — the SIGTA lockup: glyph + wordmark in currentColor, so a text
       colour on it recolours the whole mark. */
    import type { HTMLAttributes } from 'svelte/elements';

    interface Props extends HTMLAttributes<HTMLSpanElement> {
        name?: string;
        glyphOnly?: boolean;
        size?: 'sm' | 'base' | 'lg';
    }

    let { name = 'SIGTA', glyphOnly = false, size = 'base', ...rest }: Props = $props();
</script>

<span data-size={size} data-sigta="brand" {...rest}>
    <svg data-scope="brand" data-part="glyph" viewBox="0 0 40 40" fill="none" aria-hidden="true">
        <rect x="3" y="3" width="34" height="34" rx="6" stroke="currentColor" stroke-width="2.2" />
        <path d="M20 12 L11 29 M20 12 L29 29" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" />
        <path d="M13.5 26 A 13 13 0 0 0 26.5 26" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" />
        <circle cx="20" cy="12" r="2.6" fill="currentColor" />
    </svg>
    {#if !glyphOnly}
        <span data-scope="brand" data-part="name">{name}</span>
    {/if}
</span>
