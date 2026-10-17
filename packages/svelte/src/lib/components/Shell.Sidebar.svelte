<script lang="ts">
    /* Shell sidebar — a rail on desktop; below the tablet breakpoint a top bar
       whose burger slides the nav in as a drawer over the page, with a scrim.
       The open drawer is a modal dialog: focus is trapped in it and the page
       stops scrolling (the core's trap and lock), and it closes on the scrim,
       Escape, its own close button and an Inertia visit. None of that applies
       to the rail. bind:open is optional; a sigta-shell-toggle window event
       toggles it and each change goes out as sigta-shell-nav { open }, the
       same events as the Livewire runtime, so a Shell.Header anywhere talks
       to it. */
    import type { Snippet } from 'svelte';
    import type { HTMLAttributes } from 'svelte/elements';
    import { onMount, untrack } from 'svelte';
    import { lockScroll, trapFocus } from '@sigta-hq/core';
    import Icon from './Icon.svelte';
    import { translator } from '../runtime/messages.js';
    import { setShown, type ShowClasses } from '../runtime/show.js';

    interface Props extends HTMLAttributes<HTMLElement> {
        open?: boolean;
        brand?: Snippet;
        actions?: Snippet;
        children?: Snippet;
    }

    let { open = $bindable(false), brand, actions, children, 'aria-label': ariaLabel, ...rest }: Props = $props();
    const t = translator();

    const uid = $props.id();
    const panelId = `sigta-shell-sidebar-${uid}`;
    const label = $derived(ariaLabel ?? 'App navigation');
    const motion: ShowClasses = { enter: 'sigta-shell-enter', leave: 'sigta-shell-leave', away: 'sigta-shell-away' };

    // Whether it is a drawer right now (below the breakpoint); only the client knows.
    let drawer = $state(false);
    let scrim: HTMLDivElement | undefined = $state();
    let nav: HTMLDivElement | undefined = $state();

    // The scrim and nav are shown and hidden by the runtime from the first
    // render on, as x-show does in Blade.
    const shownAtFirst = untrack(() => open);
    let shown = shownAtFirst;
    let reported = shownAtFirst;

    const toggle = () => { open = !open; };
    const close = () => { open = false; };

    onMount(() => {
        const narrow = window.matchMedia('(width < 48rem)');
        drawer = narrow.matches;
        // Growing past the breakpoint turns the drawer back into the rail, so a
        // drawer left open is released rather than stranding its trap.
        const resize = (event: MediaQueryListEvent) => {
            drawer = event.matches;
            if (!event.matches) open = false;
        };
        narrow.addEventListener('change', resize);
        window.addEventListener('sigta-shell-toggle', toggle);
        document.addEventListener('inertia:navigate', close);
        return () => {
            narrow.removeEventListener('change', resize);
            window.removeEventListener('sigta-shell-toggle', toggle);
            document.removeEventListener('inertia:navigate', close);
        };
    });

    $effect(() => {
        const next = open;
        untrack(() => {
            if (!scrim || !nav || next === shown) return;
            shown = next;
            setShown(scrim, next, motion);
            setShown(nav, next, motion);
        });
    });

    // A shell header's burger lives outside this component, so it is told
    // the state rather than reading it.
    $effect(() => {
        const next = open;
        if (next === reported) return;
        reported = next;
        window.dispatchEvent(new CustomEvent('sigta-shell-nav', { detail: { open: next } }));
    });

    // The trap and scroll lock hold while it is an open drawer, never the rail.
    $effect(() => {
        if (!open || !drawer || !nav) return;
        const panel = nav;
        const unlock = lockScroll();
        let release: (() => void) | null = null;
        const timer = setTimeout(() => { release = trapFocus(panel); }, 15);
        return () => {
            clearTimeout(timer);
            unlock();
            release?.();
        };
    });

    const escape = (event: KeyboardEvent) => {
        if (event.key === 'Escape') close();
    };
</script>

<svelte:window onkeydown={escape} />

<aside data-sigta="shell.sidebar" aria-label={label} {...rest}>
    <div data-scope="shell.sidebar" data-part="top">
        {#if brand}
            <span data-scope="shell.sidebar" data-part="brand">{@render brand()}</span>
        {/if}

        <div data-scope="shell.sidebar" data-part="end">
            {@render actions?.()}

            <button
                type="button"
                aria-expanded={open ? 'true' : 'false'}
                aria-controls={panelId}
                aria-label={t('Toggle navigation')}
                onclick={toggle}
                data-scope="shell.sidebar"
                data-part="toggle"
            >
                <Icon name="menu" />
            </button>
        </div>
    </div>

    <!-- svelte-ignore a11y_click_events_have_key_events, a11y_no_static_element_interactions -->
    <div
        bind:this={scrim}
        style={shownAtFirst ? undefined : 'display: none'}
        onclick={close}
        aria-hidden="true"
        data-scope="shell.sidebar"
        data-part="scrim"
    ></div>

    <div
        bind:this={nav}
        id={panelId}
        style={shownAtFirst ? undefined : 'display: none'}
        role={drawer ? 'dialog' : undefined}
        aria-modal={drawer && open ? 'true' : undefined}
        aria-label={label}
        data-scope="shell.sidebar"
        data-part="nav"
    >
        <div data-scope="shell.sidebar" data-part="dismiss">
            <button type="button" onclick={close} aria-label={t('Close navigation')} data-scope="shell.sidebar" data-part="close">
                <Icon name="x" />
            </button>
        </div>

        {@render children?.()}
    </div>
</aside>
