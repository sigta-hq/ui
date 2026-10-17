<script lang="ts">
    /* Right-click the surface (children) to open a dropdown-styled menu at the
       pointer, kept clear of the viewport edges. The `menu` snippet holds
       Dropdown.Items, which close it; arrows, Home and End move between them;
       Escape or a click outside the menu closes it. */
    import type { Snippet } from 'svelte';
    import type { HTMLAttributes } from 'svelte/elements';
    import { tick } from 'svelte';
    import { focusItem, menuKeydown, onClickOutside, setMenu } from '../runtime/menu.js';

    interface Props extends HTMLAttributes<HTMLDivElement> {
        menu?: Snippet;
        children?: Snippet;
    }

    let { menu, children, ...rest }: Props = $props();

    let open = $state(false);
    let x = $state(0);
    let y = $state(0);
    let root: HTMLDivElement | undefined = $state();
    let panel: HTMLDivElement | undefined = $state();

    async function openAt(event: MouseEvent) {
        event.preventDefault();
        x = Math.max(8, Math.min(event.clientX, window.innerWidth - 208));
        y = Math.max(8, Math.min(event.clientY, window.innerHeight - 248));
        open = true;
        await tick();
        focusItem(root, 0);
    }

    const close = () => { open = false; };

    setMenu({ close });

    $effect(() => {
        if (open) return onClickOutside(() => panel, close);
    });

    const keydown = (event: KeyboardEvent) => {
        if (event.key !== 'Escape') return;
        event.preventDefault();
        event.stopPropagation();
        close();
    };
</script>

<!-- svelte-ignore a11y_no_static_element_interactions -->
<div bind:this={root} oncontextmenu={openAt} onkeydown={keydown} {...rest} data-sigta="context-menu">
    {@render children?.()}

    <!-- svelte-ignore a11y_interactive_supports_focus -->
    <div
        bind:this={panel}
        hidden={!open}
        onkeydown={(event) => menuKeydown(event, panel)}
        style="left:{x}px;top:{y}px"
        role="menu"
        data-scope="context-menu"
        data-part="menu"
    >
        {@render menu?.()}
    </div>
</div>
