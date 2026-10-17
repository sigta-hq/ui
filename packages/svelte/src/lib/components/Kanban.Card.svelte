<script lang="ts">
    /* A draggable kanban card (role="listitem", focusable, announced as
       draggable). It carries the `group` class, so group-hover utilities in
       its content react to hovering the card. */
    import type { Snippet } from 'svelte';
    import type { HTMLAttributes } from 'svelte/elements';
    import { onDestroy } from 'svelte';
    import { useCardCounter, useKanban } from '../runtime/kanban.svelte.js';

    interface Props extends HTMLAttributes<HTMLDivElement> {
        id?: string | null;
        children?: Snippet;
    }

    let { id = null, class: klass, children, ...rest }: Props = $props();

    const uid = $props.id();
    const cardId = $derived(id ?? `card-${uid}`);
    const board = useKanban();
    // Inside its column's counting pass the card only counts itself.
    const counter = useCardCounter();
    if (counter) {
        counter.n += 1;
        onDestroy(() => { counter.n -= 1; });
    }
</script>

<!-- svelte-ignore a11y_no_noninteractive_tabindex -->
<div
    data-sigta="kanban.card"
    data-card-id={cardId}
    role="listitem"
    draggable="true"
    tabindex="0"
    aria-roledescription="draggable"
    data-dragging={!counter && board?.draggedId === cardId ? '' : undefined}
    ondragstart={(event) => { if (!counter) board?.start(cardId, event); }}
    class={['group', klass]}
    {...rest}
>{#if !counter}{@render children?.()}{/if}</div>
