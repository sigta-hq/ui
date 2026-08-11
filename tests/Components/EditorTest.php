<?php

use Illuminate\Support\Facades\Blade;

it('renders the editor with a toolbar and hidden value input', function () {
    $html = Blade::render('<x-sigta::editor name="body" label="Body" :value="\'<p>Hello</p>\'" />');

    expect($html)
        ->toContain('data-sigta="editor"')
        ->toContain('sigtaEditor(')
        ->toContain('role="toolbar"')
        ->toContain('aria-label="Bold"')
        ->toContain('aria-label="Heading"')
        ->toContain('x-ref="element"')
        ->toContain('type="hidden"')
        ->toContain('name="body"')
        ->toContain('Body');
});

it('forwards wire:model and marks the error state', function () {
    $html = Blade::render('<x-sigta::editor wire:model="body" error="Required" />');

    expect($html)
        ->toContain('wire:model="body"')
        ->toContain('border-danger')
        ->toContain('Required');
});

it('wires the toolbar to the editable region', function () {
    $html = Blade::render('<x-sigta::editor id="my-editor" />');

    expect($html)
        ->toContain('aria-controls="my-editor"')
        ->toContain('id="my-editor"');
});
