<script lang="ts">
    /* Floating bar — a dark pill pinned to the bottom centre of the viewport.
       It slides up when `show` turns true and away when it turns false; a
       first render that is already shown appears without animating. While it
       is up it publishes its footprint as --sigta-floating-space on <html>,
       which the shell's main column and the bottom toasts keep clear of. */
    import type { Snippet } from 'svelte';
    import type { HTMLAttributes } from 'svelte/elements';
    import { onMount, untrack } from 'svelte';
    import { clearFloatingSpace, publishFloatingSpace } from '@sigta-hq/core';

    interface Props extends HTMLAttributes<HTMLDivElement> {
        show?: boolean;
        label?: string | null;
        children?: Snippet;
    }

    let { show = false, label = null, children, ...rest }: Props = $props();

    // The bar's display is the runtime's from the first render on, as in
    // Blade (x-show): rendered once, then shown and hidden with the transition.
    const shownAtFirst = untrack(() => !!show);
    let visible = shownAtFirst;
    let root: HTMLDivElement | undefined = $state();
    let bar: HTMLDivElement | undefined = $state();
    let cancel: (() => void) | null = null;

    const AWAY = 'sigta-floating-bar-away';

    const publish = () => { if (root && bar) publishFloatingSpace(root, bar, visible); };

    // The same steps as Alpine's class transition: the move's start state goes
    // on with the transition, the end state a frame later, and the classes
    // come off once the transition has run.
    function move(el: HTMLDivElement, up: boolean) {
        cancel?.();
        const during = up ? 'sigta-floating-bar-enter' : 'sigta-floating-bar-leave';
        let frame = 0;
        let timer: ReturnType<typeof setTimeout> | undefined;
        if (up) el.style.removeProperty('display');
        el.classList.add(during);
        if (up) el.classList.add(AWAY);
        const finish = () => {
            if (!up) el.style.display = 'none';
            el.classList.remove(during, AWAY);
            cancel = null;
        };
        frame = requestAnimationFrame(() => {
            const duration = (parseFloat(getComputedStyle(el).transitionDuration) || 0) * 1000;
            frame = requestAnimationFrame(() => {
                el.classList.toggle(AWAY, !up);
                timer = setTimeout(finish, duration);
            });
        });
        cancel = () => {
            cancelAnimationFrame(frame);
            clearTimeout(timer);
            el.classList.remove(during, AWAY);
            cancel = null;
        };
    }

    $effect(() => {
        const next = !!show;
        untrack(() => {
            if (!bar || next === visible) return;
            visible = next;
            move(bar, next);
            publish();
        });
    });

    onMount(() => {
        const observer = new ResizeObserver(publish);
        if (bar) observer.observe(bar);
        publish();
        return () => {
            observer.disconnect();
            cancel?.();
            clearFloatingSpace();
        };
    });
</script>

<div bind:this={root} data-sigta="floating-bar" data-show={show ? 'true' : 'false'} {...rest}>
    <div
        bind:this={bar}
        style={shownAtFirst ? undefined : 'display: none'}
        role={label ? 'region' : undefined}
        aria-label={label || undefined}
        data-scope="floating-bar"
        data-part="bar"
    >
        {@render children?.()}
    </div>
</div>
