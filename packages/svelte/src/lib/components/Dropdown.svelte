<script lang="ts">
    /* A menu button: the `trigger` snippet opens a menu of Dropdown.Items
       (children). Click, or ArrowDown / ArrowUp on the trigger, opens it on
       the first / last item; arrows, Home and End move; Escape closes it and
       returns focus to the trigger; a click outside or focus leaving closes
       it. bind:open reads and sets it. */
    import type { Snippet } from 'svelte';
    import type { HTMLAttributes } from 'svelte/elements';
    import { tick } from 'svelte';
    import { focusItem, menuKeydown, onClickOutside, setMenu } from '../runtime/menu.js';

    interface Props extends HTMLAttributes<HTMLDivElement> {
        position?: 'bottom-start' | 'bottom-end' | 'top-start' | 'top-end';
        open?: boolean;
        trigger?: Snippet;
        children?: Snippet;
    }

    let { position = 'bottom-start', open = $bindable(false), trigger, children, ...rest }: Props = $props();

    let root: HTMLDivElement | undefined = $state();
    let triggerBox: HTMLDivElement | undefined = $state();

    async function openAt(index: number) {
        open = true;
        await tick();
        focusItem(root, index);
    }

    function close(focusTrigger = false) {
        if (!open) return;
        open = false;
        if (focusTrigger) (triggerBox?.querySelector<HTMLElement>('button, [href], input') ?? triggerBox)?.focus();
    }

    setMenu({ close });

    $effect(() => {
        if (open) return onClickOutside(() => root, () => close());
    });

    const toggle = () => (open ? close() : openAt(0));

    const keydown = (event: KeyboardEvent) => {
        if (event.key !== 'Escape') return;
        event.preventDefault();
        event.stopPropagation();
        close(true);
    };
    const focusout = (event: FocusEvent) => {
        if (!root?.contains(event.relatedTarget as Node | null)) close();
    };
    const triggerKeydown = (event: KeyboardEvent) => {
        if (event.key === 'ArrowDown') { event.preventDefault(); openAt(0); }
        else if (event.key === 'ArrowUp') { event.preventDefault(); openAt(-1); }
    };
</script>

<!-- svelte-ignore a11y_no_static_element_interactions -->
<div bind:this={root} onkeydown={keydown} onfocusout={focusout} data-position={position} {...rest} data-sigta="dropdown">
    <!-- svelte-ignore a11y_click_events_have_key_events, a11y_no_static_element_interactions -->
    <div
        bind:this={triggerBox}
        onclick={toggle}
        onkeydown={triggerKeydown}
        aria-expanded={open ? 'true' : 'false'}
        aria-haspopup="menu"
        data-scope="dropdown"
        data-part="trigger"
    >
        {@render trigger?.()}
    </div>

    <!-- svelte-ignore a11y_interactive_supports_focus -->
    <div hidden={!open} onkeydown={(event) => menuKeydown(event, root)} role="menu" data-scope="dropdown" data-part="menu">
        {@render children?.()}
    </div>
</div>
