<script lang="ts">
    /* One menu item: a link with href, else a button that closes its menu
       (a Dropdown, Popover or ContextMenu) once your onclick has run. */
    import type { Snippet } from 'svelte';
    import type { HTMLAttributes } from 'svelte/elements';
    import Icon from './Icon.svelte';
    import { useMenu } from '../runtime/menu.js';

    interface Props extends HTMLAttributes<HTMLElement> {
        icon?: string | null;
        variant?: 'default' | 'danger';
        kbd?: string | null;
        href?: string | null;
        children?: Snippet;
        [key: string]: unknown;
    }

    let { icon = null, variant = 'default', kbd = null, href = null, onclick, children, ...rest }: Props = $props();

    const menu = useMenu();

    const click = (event: MouseEvent & { currentTarget: EventTarget & HTMLElement }) => {
        (onclick as ((event: MouseEvent) => void) | undefined)?.(event);
        menu?.close(true);
    };
</script>

{#snippet content()}
    {#if icon}<Icon name={icon} size="sm" data-scope="dropdown.item" data-part="icon" />{/if}
    <span data-scope="dropdown.item" data-part="label">{@render children?.()}</span>
    {#if kbd}<kbd data-scope="dropdown.item" data-part="kbd">{kbd}</kbd>{/if}
{/snippet}

{#if href !== null && href !== undefined}
    <a role="menuitem" tabindex="-1" data-sigta="dropdown.item" data-variant={variant} {href} {onclick} {...rest}>{@render content()}</a>
{:else}
    <button type="button" role="menuitem" tabindex="-1" data-sigta="dropdown.item" data-variant={variant} onclick={click} {...rest}>{@render content()}</button>
{/if}
