<?php

use Illuminate\Support\Facades\Blade;

it('renders a command palette with dialog + combobox', function () {
    $html = Blade::render(<<<'BLADE'
        <x-sigta::command>
            <x-sigta::command.item icon="file-plus" kbd="⌘N">New project</x-sigta::command.item>
            <x-sigta::command.item href="/settings" icon="settings">Settings</x-sigta::command.item>
        </x-sigta::command>
    BLADE);

    expect($html)
        ->toContain('data-sigta="command"')
        ->toContain('<dialog')
        ->toContain('keydown.meta.k.window')
        ->toContain('role="combobox"')
        ->toContain('role="option"')
        ->toContain('href="/settings"')
        ->toContain('⌘N');
});

it('renders the palette and its items with contract parts', function () {
    $html = Blade::render(<<<'BLADE'
        <x-sigta::command class="max-w-2xl">
            <x-sigta::command.item icon="file-plus" kbd="⌘N" value="new">New project</x-sigta::command.item>
            <x-sigta::command.item href="/settings">Settings</x-sigta::command.item>
        </x-sigta::command>
    BLADE);

    expect($html)
        ->toMatch('/<dialog[^>]*class="max-w-2xl"/s')            // your class lands on the dialog
        ->toMatch('/data-scope="command"\s+data-part="dialog"/')
        ->toContain('data-part="search"')
        ->toContain('data-part="input"')
        ->toContain('data-part="esc"')
        ->toContain('data-part="list"')
        ->toMatch('/<button type="button"[^>]*data-value="new"[^>]*data-sigta="command.item"/s')
        ->toMatch('/<a[^>]*href="\/settings"[^>]*data-sigta="command.item"/s')
        ->toContain('data-scope="command.item" data-part="icon"')
        ->toContain('data-scope="command.item" data-part="label"')
        ->toContain('data-scope="command.item" data-part="kbd"')
        ->not->toMatch('/class="(?!max-w-2xl)/');

    expect(contractCss('command'))
        ->toContain('mt-[12vh]')
        ->toContain('data-active:bg-ink data-active:text-surface');
});

it('shows "nothing found" from the count the last query kept', function () {
    // Alpine cannot watch the options' DOM, so the line reads sigtaCombobox's matches.
    $html = Blade::render('<x-sigta::command><x-sigta::command.item>Item</x-sigta::command.item></x-sigta::command>');

    expect($html)
        ->toMatch('/<div x-cloak x-show="matches === 0"[^>]*data-part="empty">Nothing found/')
        ->not->toContain('visible().length === 0');
});

it('can disable the global shortcut', function () {
    $html = Blade::render('<x-sigta::command :shortcut="false"><x-sigta::command.item>Item</x-sigta::command.item></x-sigta::command>');

    expect($html)->not->toContain('keydown.meta.k.window');
});

it('renders a pillbox with multiselect ARIA and hidden JSON input', function () {
    $html = Blade::render(<<<'BLADE'
        <x-sigta::pillbox label="Disciplines" name="disciplines" wire:model="disciplines">
            <x-sigta::autocomplete.option value="design">Product design</x-sigta::autocomplete.option>
            <x-sigta::autocomplete.option value="saas">SaaS engineering</x-sigta::autocomplete.option>
        </x-sigta::pillbox>
    BLADE);

    expect($html)
        ->toContain('data-sigta="pillbox"')
        ->toContain('aria-multiselectable="true"')
        ->toContain('sigtaCombobox({ multiple: true })')
        ->toContain('type="hidden"')
        ->toContain('wire:model="disciplines"')
        ->toContain('Disciplines');
});

it('renders the pillbox with contract parts and a working "no matches" line', function () {
    $html = Blade::render(<<<'BLADE'
        <x-sigta::pillbox label="Tags" error="Pick one." class="max-w-md">
            <x-sigta::autocomplete.option value="a">Alpha</x-sigta::autocomplete.option>
        </x-sigta::pillbox>
    BLADE);

    expect($html)
        ->toMatch('/class="max-w-md"[^>]*data-sigta="pillbox"/s')
        ->toContain('data-part="label"')
        ->toContain('data-part="control"')
        ->toContain('data-part="chip"')
        ->toContain('data-part="remove"')
        ->toMatch('/aria-invalid="true"[^>]*data-part="input"/s')
        ->toContain('data-part="error"')
        ->toMatch('/<div x-cloak x-show="matches === 0"[^>]*data-part="empty">No matches./')
        ->not->toContain('visible().length === 0')
        ->not->toMatch('/class="(?!max-w-md)/');

    expect(contractCss('pillbox'))
        ->toContain('&:has([aria-invalid="true"])')
        ->toContain('@apply border-danger focus-within:border-ink;');
});
