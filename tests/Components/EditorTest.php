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
        ->toMatch('/<div[^>]*data-sigta="editor"[^>]*data-invalid/')
        ->toContain('Required');

    expect(contractCss('editor'))->toContain('&[data-invalid] { @apply border-danger; }');
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
        ->toContain('x-show="focused || linkOpen"');

    expect(contractCss('editor'))
        ->toContain('&[data-variant="inline"] {')
        ->toContain('@apply -mx-[0.8em] border-transparent;')
        ->toContain('@apply hover:border-line;')
        ->toContain('[data-sigta="editor"][data-variant="inline"] > [data-scope="editor"][data-part="toolbar"] {');
});

it('hands its padding to the editable element so the whole box is clickable', function () {
    $html = Blade::render('<x-sigta::editor variant="inline" />');

    expect($html)
        ->toMatch('/<div[^>]*x-ref="element"[^>]*data-scope="editor" data-part="mount"/')
        ->not->toContain('data-content-class');

    // The runtime marks the editable element TipTap creates as the content part…
    expect(file_get_contents(__DIR__.'/../../packages/core/src/editor.ts'))
        ->toContain("'data-part': 'content'")
        ->toContain('attributes: { ...EDITOR_CONTENT_ATTRIBUTES }');

    // …and the contract pads it (not the mount), while it inherits the mount's height.
    expect(contractCss('editor'))
        ->toContain("[data-scope=\"editor\"][data-part=\"content\"] {\n    @apply max-w-none px-4 py-3 focus:outline-none;")
        ->toContain("[data-sigta=\"editor\"][data-variant=\"inline\"] [data-scope=\"editor\"][data-part=\"content\"] {\n    @apply px-[0.8em] py-[0.62em];");
    expect(file_get_contents(__DIR__.'/../../packages/contract/css/prose.css'))
        ->toContain('min-height: inherit;');
});

it('sizes the empty editor by rows, like a textarea', function () {
    expect(Blade::render('<x-sigta::editor />'))
        ->toContain('style="--sigta-editor-rows: 6"');

    expect(Blade::render('<x-sigta::editor variant="inline" />'))
        ->toContain('style="--sigta-editor-rows: 3"');

    expect(Blade::render('<x-sigta::editor variant="inline" rows="8" />'))
        ->toContain('style="--sigta-editor-rows: 8"');

    expect(contractCss('editor'))
        ->toContain('min-height: calc(var(--sigta-editor-rows, 6) * 1.65em + 1.5em);')
        ->toContain('min-height: calc(var(--sigta-editor-rows, 3) * 1.65em + 1.24em);');
});

it('carries the contract attributes and no design classes', function () {
    $html = Blade::render('<x-sigta::editor upload-url="/images" class="max-w-xl" />');

    expect($html)
        ->toContain('data-scope="editor" data-part="toolbar"')
        ->toContain('data-scope="editor" data-part="tool"')
        ->toContain('data-scope="editor" data-part="separator"')
        ->toContain('data-scope="editor" data-part="end"')
        ->toContain('data-scope="editor" data-part="uploading"')
        ->toContain('data-scope="editor" data-part="link"')
        ->toContain('data-scope="editor" data-part="link-input"')
        ->toContain('data-scope="editor" data-part="picker"')
        ->toContain('x-bind:aria-pressed="isActive(\'bold\')"')   // pressed tools are a state, not a class
        ->toContain('class="max-w-xl"')                            // the consumer's class lands on the root
        ->not->toContain('x-bind:class')
        ->not->toContain('rounded-base')
        ->not->toContain('bg-surface')
        ->not->toContain('animate-spin');

    expect(contractCss('editor'))
        ->toContain('&[aria-pressed="true"] { @apply bg-ink text-surface hover:bg-ink; }')
        ->toContain('& > [data-sigta="icon"] { @apply animate-spin; }');
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

it('offers a picture tool and carries the upload endpoint only when one is given', function () {
    $html = Blade::render('<x-sigta::editor upload-url="/images" />');

    expect($html)
        ->toContain('data-upload-url="/images"')
        ->toContain('data-upload-token="')
        ->toContain('aria-label="Picture"')
        ->toContain('x-ref="imageInput"')
        ->toContain('accept="image/*"')
        ->toContain('aria-label="Uploading"');

    $plain = Blade::render('<x-sigta::editor />');

    expect($plain)
        ->not->toContain('data-upload-url')
        ->not->toContain('aria-label="Picture"')
        ->not->toContain('x-ref="imageInput"');
});

it('carries the link resolver endpoint only when one is given', function () {
    expect(Blade::render('<x-sigta::editor resolve-url="/links/resolve" />'))
        ->toContain('data-resolve-url="/links/resolve"');

    expect(Blade::render('<x-sigta::editor />'))
        ->not->toContain('data-resolve-url');
});
