<script lang="ts">
    /* A tab group around a Tabs.List and its Tabs.Panels. bind:value reads
       and sets the active tab's name; `default` picks it on load (rendered
       selected on the server), else the first tab is chosen once mounted. */
    import type { Snippet } from 'svelte';
    import type { HTMLAttributes } from 'svelte/elements';
    import { onMount } from 'svelte';
    import { step } from '@sigta-hq/core';
    import { setTabs, tabsIn } from '../runtime/tabs.js';

    interface Props extends HTMLAttributes<HTMLDivElement> {
        default?: string | null;
        value?: string | null;
        children?: Snippet;
    }

    let { default: initial = null, value = $bindable(null), children, ...rest }: Props = $props();

    value ??= initial;

    let root: HTMLDivElement | undefined = $state();

    const focusTab = (tab: HTMLElement | undefined) => {
        if (!tab) return;
        value = tab.dataset.name ?? null;
        tab.focus();
    };

    setTabs({
        get active() { return value; },
        isActive: (name) => value === name,
        select: (name) => { value = name; },
        move(delta) {
            const tabs = tabsIn(root);
            const index = tabs.findIndex((tab) => tab.dataset.name === value);
            focusTab(tabs[step(index, delta, tabs.length)]);
        },
        moveTo(index) {
            const tabs = tabsIn(root);
            focusTab(tabs[index < 0 ? tabs.length + index : index]);
        },
    });

    onMount(() => {
        if (!value) value = tabsIn(root)[0]?.dataset.name ?? null;
    });
</script>

<div bind:this={root} {...rest} data-sigta="tabs">
    {@render children?.()}
</div>
