<?php

use Illuminate\Support\Facades\Blade;

it('renders a board of columns and draggable cards with list semantics', function () {
    $html = Blade::render(<<<'BLADE'
        <x-sigta::kanban aria-label="Roadmap">
            <x-sigta::kanban.column id="todo" label="To do">
                <x-sigta::kanban.card id="c1">Marginalia v3</x-sigta::kanban.card>
                <x-sigta::kanban.card id="c2">Halyard OS</x-sigta::kanban.card>
            </x-sigta::kanban.column>
            <x-sigta::kanban.column id="done" label="Done">
                <x-sigta::kanban.card id="c3">Ledger rebuild</x-sigta::kanban.card>
            </x-sigta::kanban.column>
        </x-sigta::kanban>
    BLADE);

    expect($html)
        ->toContain('data-sigta="kanban"')
        ->toContain('data-sigta="kanban.column"')
        ->toContain('data-sigta="kanban.card"')
        ->toContain('role="list"')
        ->toContain('role="listitem"')
        ->toContain('aria-label="Roadmap"')   // board attribute forwarding
        ->toContain('Marginalia v3')
        ->toContain('Ledger rebuild');
});

it('marks cards draggable with keyboard-facing metadata', function () {
    $html = Blade::render('<x-sigta::kanban.card id="c1">Task</x-sigta::kanban.card>');

    expect($html)
        ->toContain('draggable="true"')
        ->toContain('aria-roledescription="draggable"')
        ->toContain('tabindex="0"')
        ->toContain('data-card-id="c1"')
        ->toContain('x-on:dragstart="start(\'c1\', $event)"');
});

it('wires the drop handler to dispatch the persistence event', function () {
    $html = Blade::render('<x-sigta::kanban><x-sigta::kanban.column id="todo" label="To do" /></x-sigta::kanban>');

    expect($html)
        ->toContain('x-data="sigtaKanban()"')     // the board is the runtime's sigtaKanban
        ->toContain('data-kanban-body')
        ->toContain('x-on:drop.prevent="drop($event, \'todo\')"')
        ->toContain('x-on:dragover.prevent="over($event, \'todo\')"')
        ->toContain('x-bind:data-over="overColumn === \'todo\' ? \'\' : null"');

    // The event name Livewire listens for, dispatched by the runtime on drop.
    expect(file_get_contents(__DIR__.'/../../packages/core/src/kanban.ts'))
        ->toContain("KANBAN_MOVE_EVENT = 'sigta-kanban-move'");
    expect(file_get_contents(__DIR__.'/../../resources/js/sigta.js'))
        ->toContain("Alpine.data('sigtaKanban'")
        ->toContain('this.$dispatch(KANBAN_MOVE_EVENT, {');
});

it('shows a count badge reflecting the number of cards', function () {
    $html = Blade::render(<<<'BLADE'
        <x-sigta::kanban.column id="doing" label="Doing">
            <x-sigta::kanban.card id="a">A</x-sigta::kanban.card>
            <x-sigta::kanban.card id="b">B</x-sigta::kanban.card>
            <x-sigta::kanban.card id="c">C</x-sigta::kanban.card>
        </x-sigta::kanban.column>
    BLADE);

    expect($html)
        ->toContain('data-sigta="badge"')
        ->toContain('Doing')
        ->toContain('>3</span>');   // three cards counted

    // Explicit count prop overrides the auto count.
    expect(Blade::render('<x-sigta::kanban.column id="x" label="Backlog" :count="12" />'))
        ->toContain('>12</span>');
});

it('forwards attributes onto the card root', function () {
    $html = Blade::render('<x-sigta::kanban.card id="c1" data-testid="card" class="ring">Task</x-sigta::kanban.card>');

    expect($html)
        ->toContain('data-testid="card"')
        ->toContain('ring');
});

it('renders a header actions slot to the left of the count, which stays pinned right', function () {
    $html = Blade::render(<<<'BLADE'
        <x-sigta::kanban.column id="todo" label="To do">
            <x-slot:actions>
                <button type="button" aria-label="Column menu">…</button>
            </x-slot:actions>
        </x-sigta::kanban.column>
    BLADE);

    expect($html)
        ->toContain('aria-label="Column menu"')   // actions slot lands in the header
        ->toContain('aria-label="To do"')         // body accessible name intact
        ->toContain('data-sigta="badge"');        // count badge still present

    // Count is pinned rightmost: the actions markup renders before the badge.
    expect(strpos($html, 'aria-label="Column menu"'))
        ->toBeLessThan(strpos($html, 'data-sigta="badge"'));
});

it('derives the body aria-label from the aria prop, keeping the visible label short', function () {
    $html = Blade::render('<x-sigta::kanban.column id="wip" label="WIP" aria="Work in progress" />');

    expect($html)
        ->toContain('aria-label="Work in progress"')
        ->toContain('>WIP<');   // visible header still shows the label string
});

it('replaces the visible label with a heading slot while keeping the count and body label', function () {
    $html = Blade::render(<<<'BLADE'
        <x-sigta::kanban.column id="backlog" label="Backlog">
            <x-slot:heading>
                <input type="text" value="Backlog" aria-label="Rename column" data-testid="rename" />
            </x-slot:heading>
            <x-sigta::kanban.card id="c1">Card</x-sigta::kanban.card>
        </x-sigta::kanban.column>
    BLADE);

    expect($html)
        ->toContain('data-testid="rename"')   // custom heading control renders in place of the label
        ->not->toContain('>Backlog</span>')   // the default label span is not also rendered
        ->toContain('aria-label="Backlog"')   // body accessible name still derives from label
        ->toContain('data-sigta="badge"');    // count badge still present beside the heading
});

it('does not make columns draggable by default', function () {
    $html = Blade::render('<x-sigta::kanban.column id="todo" label="To do" />');

    expect($html)
        ->not->toContain('data-sigta="kanban.column-handle"')
        ->not->toContain('data-col-dragging');
});

it('grows a drag handle and a dragging visual when a column is sortable', function () {
    $html = Blade::render('<x-sigta::kanban.column id="todo" label="To do" sortable />');

    expect($html)
        ->toContain('data-sigta="kanban.column-handle"')                // the grip handle
        ->toContain('draggable="true"')                                 // it starts a native drag
        ->toContain('x-on:dragstart="columnStart(\'todo\', $event)"')   // wired to the board
        ->toContain('aria-label="Reorder To do"')                       // handle names the column
        ->toContain('x-bind:data-col-dragging="draggedColumn === \'todo\' ? \'\' : null"');   // fades while dragging

    expect(contractCss('kanban'))->toContain('&[data-col-dragging] { @apply opacity-40; }');
});

it('wires the board to reorder columns and dispatch the persistence event', function () {
    $html = Blade::render('<x-sigta::kanban><x-sigta::kanban.column id="todo" label="To do" sortable /></x-sigta::kanban>');

    expect($html)
        ->toContain('x-data="sigtaKanban()"')
        ->toContain('columnOver($event)')         // board handles column dragover
        ->toContain('columnDrop($event)');        // …and drop

    // The event name Livewire listens for, dispatched by the runtime on drop.
    expect(file_get_contents(__DIR__.'/../../packages/core/src/kanban.ts'))
        ->toContain("KANBAN_COLUMN_MOVE_EVENT = 'sigta-kanban-column-move'");
    expect(file_get_contents(__DIR__.'/../../resources/js/sigta.js'))
        ->toContain('this.$dispatch(KANBAN_COLUMN_MOVE_EVENT, { column: id, position });');
});

it('carries the contract attributes and no design classes', function () {
    $html = Blade::render(<<<'BLADE'
        <x-sigta::kanban>
            <x-sigta::kanban.column id="todo" label="To do" sortable>
                <x-sigta::kanban.card id="c1">Card</x-sigta::kanban.card>
            </x-sigta::kanban.column>
        </x-sigta::kanban>
    BLADE);

    expect($html)
        ->toContain('data-scope="kanban.column" data-part="header"')
        ->toContain('data-scope="kanban.column" data-part="handle"')
        ->toContain('data-scope="kanban.column" data-part="label"')
        ->toContain('data-scope="kanban.column" data-part="end"')
        ->toContain('data-scope="kanban.column" data-part="body"')
        ->toContain('class="group"')                 // a hook for group-hover in card content
        ->not->toContain('rounded-panel')
        ->not->toContain('border-dashed')
        ->not->toContain('bg-surface-raised');

    $css = contractCss('kanban');
    expect($css)
        ->toContain('[data-sigta="kanban"] {')
        ->toContain('[data-sigta="kanban.column"] {')
        ->toContain('[data-scope="kanban.column"][data-part="body"] {')
        ->toContain('&[data-over] { @apply border-ink bg-ink/5; }')     // the drop target inks
        ->toContain('[data-sigta="kanban.card"] {')
        ->toContain('&[data-dragging] { @apply opacity-40 cursor-grabbing; }');
});

it('renders the heading slot in its own part, without the label part', function () {
    $html = Blade::render(<<<'BLADE'
        <x-sigta::kanban.column id="backlog" label="Backlog">
            <x-slot:heading><span>Custom</span></x-slot:heading>
        </x-sigta::kanban.column>
    BLADE);

    expect($html)
        ->toContain('data-scope="kanban.column" data-part="heading"')
        ->not->toContain('data-part="label"');
});
