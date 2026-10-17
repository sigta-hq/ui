<script lang="ts">
    /* One tab: selected when its name is the group's value; only the selected
       tab is in the tab order (roving tabindex). */
    import type { Snippet } from 'svelte';
    import type { HTMLButtonAttributes } from 'svelte/elements';
    import { useTabs } from '../runtime/tabs.js';

    interface Props extends HTMLButtonAttributes {
        name: string;
        children?: Snippet;
    }

    let { name, onclick, children, ...rest }: Props = $props();

    const tabs = useTabs();
    const active = $derived(tabs.isActive(name));

    const click = (event: MouseEvent & { currentTarget: EventTarget & HTMLButtonElement }) => {
        onclick?.(event);
        tabs.select(name);
    };
</script>

<button
    type="button"
    role="tab"
    data-name={name}
    id="sigta-tab-{name}"
    aria-controls="sigta-panel-{name}"
    aria-selected={active ? 'true' : 'false'}
    tabindex={active ? 0 : -1}
    onclick={click}
    {...rest}
    data-sigta="tabs.tab"
>{@render children?.()}</button>
