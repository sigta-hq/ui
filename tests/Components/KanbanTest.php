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
        ->toContain('sigta-kanban-move')          // event name present for Livewire
        ->toContain('data-kanban-body')
        ->toContain('x-on:drop.prevent="drop($event, \'todo\')"')
        ->toContain('x-on:dragover.prevent="over($event, \'todo\')"');
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
