<script lang="ts">
    /* Callout — an inline note with a heavy left edge; the variant picks the
       icon and the role (danger is an alert, the rest a status). */
    import type { Snippet } from 'svelte';
    import type { HTMLAttributes } from 'svelte/elements';
    import Icon from './Icon.svelte';

    interface Props extends HTMLAttributes<HTMLDivElement> {
        variant?: 'info' | 'success' | 'danger';
        icon?: string | null;
        heading?: string | null;
        children?: Snippet;
    }

    let { variant = 'info', icon = null, heading = null, children, ...rest }: Props = $props();

    const defaults = $derived(
        variant === 'success' ? { icon: 'circle-check', role: 'status' }
            : variant === 'danger' ? { icon: 'circle-alert', role: 'alert' }
            : { icon: 'triangle-alert', role: 'status' },
    );
</script>

<div data-sigta="callout" role={defaults.role} data-variant={variant} {...rest}>
    <Icon name={icon ?? defaults.icon} data-scope="callout" data-part="icon" />
    <div data-scope="callout" data-part="body">
        {#if heading}<b data-scope="callout" data-part="heading">{heading}</b>{' '}{/if}{@render children?.()}
    </div>
</div>
