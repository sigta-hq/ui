<script lang="ts">
    /* Tooltip — an ink bubble on hover or focus after a 150ms delay; Escape
       dismisses it. children are the trigger. */
    import type { Snippet } from 'svelte';
    import type { HTMLAttributes } from 'svelte/elements';

    interface Props extends HTMLAttributes<HTMLSpanElement> {
        text: string;
        position?: 'top' | 'bottom' | 'left' | 'right';
        children?: Snippet;
    }

    let { text, position = 'top', children, ...rest }: Props = $props();

    const uid = $props.id();
    let show = $state(false);
    let timer: ReturnType<typeof setTimeout> | undefined;

    const open = () => { timer = setTimeout(() => { show = true; }, 150); };
    const close = () => { clearTimeout(timer); show = false; };
    const escape = (event: KeyboardEvent) => { if (event.key === 'Escape') close(); };
</script>

<svelte:window onkeydown={escape} />

<!-- svelte-ignore a11y_no_static_element_interactions -->
<span data-sigta="tooltip" data-position={position} onmouseenter={open} onmouseleave={close} onfocusin={open} onfocusout={close} {...rest}>
    <span data-scope="tooltip" data-part="trigger" aria-describedby="sigta-tooltip-{uid}">{@render children?.()}</span>

    <span id="sigta-tooltip-{uid}" role="tooltip" data-scope="tooltip" data-part="bubble" hidden={!show}>{text}</span>
</span>
