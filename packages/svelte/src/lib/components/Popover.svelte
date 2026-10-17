<script lang="ts">
    /* The dropdown's placement around free-form content (role="dialog"). The
       panel takes focus when clicked, so clicking inside keeps it open; a
       click outside, focus leaving or Escape (focus back to the trigger)
       closes it. bind:open reads and sets it. */
    import type { Snippet } from 'svelte';
    import type { HTMLAttributes } from 'svelte/elements';
    import { tick } from 'svelte';
    import { focusItem, onClickOutside, setMenu } from '../runtime/menu.js';

    interface Props extends HTMLAttributes<HTMLDivElement> {
        position?: 'bottom-start' | 'bottom-end' | 'top-start' | 'top-end';
        open?: boolean;
        trigger?: Snippet;
        children?: Snippet;
    }

    let { position = 'bottom-start', open = $bindable(false), trigger, children, ...rest }: Props = $props();

    let root: HTMLDivElement | undefined = $state();
    let triggerBox: HTMLDivElement | undefined = $state();

    function close(focusTrigger = false) {
        if (!open) return;
        open = false;
        if (focusTrigger) (triggerBox?.querySelector<HTMLElement>('button, [href], input') ?? triggerBox)?.focus();
    }

    // Menu items inside (a Dropdown.Item) close it, as they close a dropdown.
    setMenu({ close });

    $effect(() => {
        if (open) return onClickOutside(() => root, () => close());
    });

    async function toggle() {
        if (open) return close();
        open = true;
        await tick();
        focusItem(root, 0);
    }

    const keydown = (event: KeyboardEvent) => {
        if (event.key !== 'Escape') return;
        event.preventDefault();
        event.stopPropagation();
        close(true);
    };
    const focusout = (event: FocusEvent) => {
        if (!root?.contains(event.relatedTarget as Node | null)) close();
    };
</script>

<!-- svelte-ignore a11y_no_static_element_interactions -->
<div bind:this={root} onkeydown={keydown} onfocusout={focusout} data-position={position} {...rest} data-sigta="popover">
    <!-- svelte-ignore a11y_click_events_have_key_events, a11y_no_static_element_interactions -->
    <div
        bind:this={triggerBox}
        onclick={toggle}
        aria-expanded={open ? 'true' : 'false'}
        aria-haspopup="dialog"
        data-scope="popover"
        data-part="trigger"
    >
        {@render trigger?.()}
    </div>

    <div hidden={!open} role="dialog" tabindex="-1" data-scope="popover" data-part="panel">
        {@render children?.()}
    </div>
</div>
