<script lang="ts">
    /* A kanban column: a mono header (label or your own heading, actions, the
       count badge) over a droppable body of Kanban.Cards (role="list"). The
       count defaults to the number of cards inside; sortable grows a grip
       handle that drags the column along the board. */
    import type { Snippet } from 'svelte';
    import type { HTMLAttributes } from 'svelte/elements';
    import Badge from './Badge.svelte';
    import Icon from './Icon.svelte';
    import KanbanCount from '../runtime/KanbanCount.svelte';
    import { useKanban, type CardCounter } from '../runtime/kanban.svelte.js';

    interface Props extends HTMLAttributes<HTMLDivElement> {
        label?: string | null;
        id?: string | null;
        count?: number | string | null;
        aria?: string | null;
        sortable?: boolean;
        heading?: Snippet;
        actions?: Snippet;
        children?: Snippet;
    }

    let { label = null, id = null, count = null, aria = null, sortable = false, heading, actions, children, ...rest }: Props = $props();

    const uid = $props.id();
    const columnId = $derived(id ?? `col-${uid}`);
    // The body's accessible name: the string label (or aria), never slotted markup.
    const name = $derived(aria ?? label);
    const board = useKanban();
    const counter: CardCounter = $state({ n: 0 });
</script>

<div
    data-sigta="kanban.column"
    data-column={columnId}
    data-col-dragging={sortable && board?.draggedColumn === columnId ? '' : undefined}
    {...rest}
>
    {#if count === null}<KanbanCount {counter}>{@render children?.()}</KanbanCount>{/if}
    <div data-scope="kanban.column" data-part="header">
        {#if sortable}
            <button
                type="button"
                draggable="true"
                ondragstart={(event) => board?.columnStart(columnId, event)}
                aria-label={name ? `Reorder ${name}` : 'Reorder column'}
                data-sigta="kanban.column-handle"
                data-scope="kanban.column"
                data-part="handle"
            >
                <Icon name="grip-vertical" size="xs" />
            </button>
        {/if}
        {#if heading}
            <div data-scope="kanban.column" data-part="heading">{@render heading()}</div>
        {:else}
            <span data-scope="kanban.column" data-part="label">{label}</span>
        {/if}
        <div data-scope="kanban.column" data-part="end">
            {@render actions?.()}
            <Badge>{count ?? counter.n}</Badge>
        </div>
    </div>

    <div
        role="list"
        aria-label={name ?? ''}
        data-kanban-body=""
        data-column-id={columnId}
        data-scope="kanban.column"
        data-part="body"
        data-over={board?.overColumn === columnId ? '' : undefined}
        ondragover={(event) => { event.preventDefault(); board?.over(event, columnId); }}
        ondrop={(event) => { event.preventDefault(); board?.drop(event, columnId); }}
    >
        {@render children?.()}
    </div>
</div>
