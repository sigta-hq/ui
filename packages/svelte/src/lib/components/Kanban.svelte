<script lang="ts">
    /* Kanban — a native HTML5 drag-and-drop board of Kanban.Columns. Dragging
       a card between (or within) columns calls onmove { card, from, to,
       position }; dragging a sortable column's handle calls oncolumnmove
       { column, position }. Both also dispatch the sigta-kanban-move /
       sigta-kanban-column-move DOM events, as the Livewire runtime does.
       Render the columns and cards from your own lists, keyed by id, and move
       the item in them in the callback: the board re-renders from your data. */
    import type { Snippet } from 'svelte';
    import type { HTMLAttributes } from 'svelte/elements';
    import type { KanbanColumnMove, KanbanMove } from '@sigta-hq/core';
    import { KanbanBoard } from '../runtime/kanban.svelte.js';

    interface Props extends HTMLAttributes<HTMLDivElement> {
        onmove?: (move: KanbanMove) => void;
        oncolumnmove?: (move: KanbanColumnMove) => void;
        children?: Snippet;
    }

    let { onmove, oncolumnmove, children, ...rest }: Props = $props();

    const board = new KanbanBoard(() => ({ onmove, oncolumnmove }));
    let root: HTMLDivElement | undefined = $state();
    $effect(() => {
        board.root = root ?? null;
    });
</script>

<div
    bind:this={root}
    data-sigta="kanban"
    ondragend={() => { board.end(); board.columnEnd(); }}
    ondragover={(event) => board.columnOver(event)}
    ondrop={(event) => board.columnDrop(event)}
    role="presentation"
    {...rest}
>
    {@render children?.()}
</div>
