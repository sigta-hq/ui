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
        ->toContain('aria-label="Link"')
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

it('defaults to html and passes the placeholder to the runtime', function () {
    $html = Blade::render('<x-sigta::editor placeholder="Add a description" />');

    expect($html)
        ->toContain('data-format="html"')
        ->toContain('data-variant="default"')
        ->toContain('data-placeholder="Add a description"')
        ->toContain('x-data="sigtaEditor()"');
});

it('switches the runtime to markdown when asked', function () {
    $html = Blade::render('<x-sigta::editor format="markdown" :value="\'# Title\'" />');

    expect($html)
        ->toContain('data-format="markdown"')
        ->toContain('value="# Title"');
});

it('renders the inline variant borderless with a toolbar that shows only while editing', function () {
    $html = Blade::render('<x-sigta::editor variant="inline" />');

    expect($html)
        ->toContain('data-variant="inline"')
        ->toContain('border-transparent')
        ->toContain('x-show="focused || linkOpen"')
        ->not->toContain('min-h-40');
});

it('keeps the editable region out of Livewire morphs and accepts deliberate content replacement', function () {
    $html = Blade::render('<x-sigta::editor />');

    expect($html)
        ->toMatch('/<div[^>]*x-ref="element"[^>]*wire:ignore/')
        ->toContain('x-on:sigta-editor-set="setContent($event.detail)"');
});

it('ships a link tool with a keyboard shortcut', function () {
    $html = Blade::render('<x-sigta::editor />');

    expect($html)
        ->toContain('data-sigta="editor-link"')
        ->toContain('aria-label="Link address"')
        ->toContain('aria-label="Apply link"')
        ->toContain('aria-label="Open link"')
        ->toContain('aria-label="Remove link"')
        ->toContain('x-on:click="followLink($event)"')
        ->toContain('x-on:keydown.meta.k.prevent="openLink()"')
        ->toContain('x-on:keydown.ctrl.k.prevent="openLink()"');
});

it('derives a stable id from its binding so Livewire re-renders keep the mounted editor', function () {
    expect(Blade::render('<x-sigta::editor wire:model.live.debounce.500ms="description" />'))
        ->toContain('id="sigta-editor-description"')
        ->toContain('aria-controls="sigta-editor-description"');

    expect(Blade::render('<x-sigta::editor name="body" />'))->toContain('id="sigta-editor-body"');

    expect(Blade::render('<x-sigta::editor id="custom" name="body" />'))->toContain('id="custom"');
});
