<script lang="ts">
    /* The pinned Lucide set both runtimes share, at the brand stroke weight. */
    import type { SVGAttributes } from 'svelte/elements';
    import { icons } from '@sigta-hq/contract/icons';

    interface Props extends SVGAttributes<SVGSVGElement> {
        name: string;
        size?: 'xs' | 'sm' | 'base' | 'lg';
    }

    let { name, size = 'base', ...rest }: Props = $props();

    const markup = $derived(icons[name] ?? '');
</script>

<svg data-sigta="icon" data-size={size} xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" aria-hidden="true" {...rest}>{@html markup}</svg>
