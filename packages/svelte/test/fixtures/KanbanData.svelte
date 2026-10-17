<script lang="ts">
    /* A data-driven board: the lists move in onmove / oncolumnmove. */
    import Kanban from '../../src/lib/components/Kanban.svelte';
    import Column from '../../src/lib/components/Kanban.Column.svelte';
    import Card from '../../src/lib/components/Kanban.Card.svelte';
    import type { KanbanColumnMove, KanbanMove } from '@sigta-hq/core';

    let { keyed = true, persist = true }: { keyed?: boolean; persist?: boolean } = $props();

    let columns = $state([
        { id: 'todo', label: 'To do', cards: [{ id: 'a', title: 'A' }, { id: 'b', title: 'B' }] },
        { id: 'doing', label: 'Doing', cards: [{ id: 'c', title: 'C' }] },
        { id: 'done', label: 'Done', cards: [] as { id: string; title: string }[] },
    ]);

    function move({ card, from, to, position }: KanbanMove) {
        if (!persist) return;
        const source = columns.find((column) => column.id === from)!.cards;
        const [item] = source.splice(source.findIndex((c) => c.id === card), 1);
        columns.find((column) => column.id === to)!.cards.splice(position, 0, item);
    }

    function moveColumn({ column, position }: KanbanColumnMove) {
        if (!persist) return;
        const [item] = columns.splice(columns.findIndex((c) => c.id === column), 1);
        columns.splice(position, 0, item);
    }
</script>

<Kanban onmove={move} oncolumnmove={moveColumn}>
    {#if keyed}
        {#each columns as column (column.id)}
            <Column id={column.id} label={column.label} sortable>
                {#each column.cards as card (card.id)}
                    <Card id={card.id}>{card.title}</Card>
                {/each}
            </Column>
        {/each}
    {:else}
        {#each columns as column (column.id)}
            <Column id={column.id} label={column.label} sortable>
                {#each column.cards as card}
                    <Card id={card.id}>{card.title}</Card>
                {/each}
            </Column>
        {/each}
    {/if}
</Kanban>
