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
