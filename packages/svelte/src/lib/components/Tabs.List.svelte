<script lang="ts">
    /* The row of tabs (role="tablist"): the arrow keys move between tabs,
       wrapping round, Home and End jump to the ends; the tab landed on is
       selected and focused. */
    import type { Snippet } from 'svelte';
    import type { HTMLAttributes } from 'svelte/elements';
    import { useTabs } from '../runtime/tabs.js';

    interface Props extends HTMLAttributes<HTMLDivElement> {
        label?: string;
        children?: Snippet;
    }

    let { label = 'Tabs', children, ...rest }: Props = $props();

    const tabs = useTabs();

    const keydown = (event: KeyboardEvent) => {
        if (event.key === 'ArrowRight') tabs.move(1);
        else if (event.key === 'ArrowLeft') tabs.move(-1);
        else if (event.key === 'Home') tabs.moveTo(0);
        else if (event.key === 'End') tabs.moveTo(-1);
        else return;
        event.preventDefault();
    };
</script>

<!-- svelte-ignore a11y_interactive_supports_focus -->
<div role="tablist" aria-label={label} onkeydown={keydown} {...rest} data-sigta="tabs.list">
    {@render children?.()}
</div>
