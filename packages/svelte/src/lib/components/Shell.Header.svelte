<script lang="ts">
    /* Shell header: a full-width row above the sidebar and main, brand on the
       left, actions on the right. Below the tablet breakpoint it is the
       phone's top bar and carries the burger. The sidebar owns the drawer, so
       the burger sends sigta-shell-toggle and mirrors the sigta-shell-nav the
       sidebar reports back: the two talk through window events, wherever
       they sit (the same events as the Livewire runtime). */
    import type { Snippet } from 'svelte';
    import type { HTMLAttributes } from 'svelte/elements';
    import Icon from './Icon.svelte';
    import { translator } from '../runtime/messages.js';

    interface Props extends HTMLAttributes<HTMLElement> {
        brand?: Snippet;
        actions?: Snippet;
    }

    let { brand, actions, ...rest }: Props = $props();
    const t = translator();

    let navOpen = $state(false);

    $effect(() => {
        const mirror = (event: Event) => { navOpen = (event as CustomEvent<{ open: boolean }>).detail.open; };
        window.addEventListener('sigta-shell-nav', mirror);
        return () => window.removeEventListener('sigta-shell-nav', mirror);
    });

    const toggle = (event: MouseEvent) => {
        (event.currentTarget as HTMLElement).dispatchEvent(new CustomEvent('sigta-shell-toggle', { detail: {}, bubbles: true, composed: true, cancelable: true }));
    };
</script>

<header data-sigta="shell.header" {...rest}>
    {#if brand}
        <span data-scope="shell.header" data-part="brand">{@render brand()}</span>
    {/if}

    <div data-scope="shell.header" data-part="end">
        {@render actions?.()}

        <button
            type="button"
            aria-expanded={navOpen ? 'true' : 'false'}
            aria-label={t('Toggle navigation')}
            onclick={toggle}
            data-scope="shell.header"
            data-part="toggle"
        >
            <Icon name="menu" />
        </button>
    </div>
</header>
