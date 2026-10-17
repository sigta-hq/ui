<script lang="ts">
    /* The sticky site header: brand, links (children) and actions. Below the
       collapse breakpoint (md or lg) the links fold behind a burger into a
       panel under the bar; Escape folds it away. With no actions snippet, a
       ctaLabel renders the call-to-action button (target forwards onto it). */
    import type { Snippet } from 'svelte';
    import type { HTMLAttributes } from 'svelte/elements';
    import Button from './Button.svelte';
    import Icon from './Icon.svelte';

    interface Props extends HTMLAttributes<HTMLElement> {
        ctaLabel?: string | null;
        ctaHref?: string | null;
        target?: string | null;
        collapse?: 'md' | 'lg';
        brand?: Snippet;
        actions?: Snippet;
        children?: Snippet;
    }

    let { ctaLabel = null, ctaHref = null, target = null, collapse = 'md', brand, actions, children, ...rest }: Props = $props();

    const uid = $props.id();
    const panelId = `sigta-navbar-panel-${uid}`;
    let open = $state(false);

    const escape = (event: KeyboardEvent) => {
        if (event.key === 'Escape') open = false;
    };
</script>

<svelte:window onkeydown={escape} />

<header data-sigta="navbar" data-collapse={collapse} {...rest}>
    <div data-scope="navbar" data-part="bar">
        {#if brand}
            <div data-scope="navbar" data-part="brand">{@render brand()}</div>
        {/if}

        <nav data-scope="navbar" data-part="links" aria-label="Primary">
            {@render children?.()}
        </nav>

        <div data-scope="navbar" data-part="end">
            {#if actions}
                <div data-scope="navbar" data-part="actions">{@render actions()}</div>
            {:else if ctaLabel}
                {#if ctaHref !== null && ctaHref !== undefined}
                    <Button variant="cta" size="sm" href={ctaHref} target={target ?? undefined}>{ctaLabel}</Button>
                {:else}
                    <Button variant="cta" size="sm">{ctaLabel}</Button>
                {/if}
            {/if}

            <button
                type="button"
                aria-expanded={open ? 'true' : 'false'}
                aria-controls={panelId}
                aria-label="Menu"
                onclick={() => (open = !open)}
                data-scope="navbar"
                data-part="toggle"
            >
                <span hidden={open}><Icon name="menu" /></span>
                <span hidden={!open}><Icon name="x" /></span>
            </button>
        </div>
    </div>

    <nav id={panelId} hidden={!open} aria-label="Primary" data-scope="navbar" data-part="panel">
        {@render children?.()}
    </nav>
</header>
