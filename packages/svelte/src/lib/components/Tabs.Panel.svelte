<script lang="ts">
    /* A tab's panel (role="tabpanel"), shown while its tab is selected. */
    import type { Snippet } from 'svelte';
    import type { HTMLAttributes } from 'svelte/elements';
    import { useTabs } from '../runtime/tabs.js';

    interface Props extends HTMLAttributes<HTMLDivElement> {
        name: string;
        children?: Snippet;
    }

    let { name, children, ...rest }: Props = $props();

    const tabs = useTabs();
</script>

<div
    role="tabpanel"
    id="sigta-panel-{name}"
    aria-labelledby="sigta-tab-{name}"
    tabindex="0"
    hidden={!tabs.isActive(name)}
    {...rest}
    data-sigta="tabs.panel"
>{@render children?.()}</div>
