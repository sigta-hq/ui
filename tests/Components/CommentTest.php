<?php

use Illuminate\Support\Facades\Blade;

it('renders author, meta and body in an article', function () {
    $html = Blade::render('<x-sigta::comment author="Ada Lovelace" meta="2h ago">Looks great.</x-sigta::comment>');

    expect($html)
        ->toContain('data-sigta="comment"')
        ->toContain('<article')
        ->toContain('Ada Lovelace')
        ->toContain('2h ago')
        ->toContain('Looks great.');
});

it('renders an actions slot for owner controls', function () {
    $html = Blade::render(<<<'BLADE'
        <x-sigta::comment author="Ada" meta="now">
            Body text
            <x-slot:actions>
                <button type="button" aria-label="Edit note">e</button>
            </x-slot:actions>
        </x-sigta::comment>
    BLADE);

    expect($html)
        ->toContain('aria-label="Edit note"')
        ->toContain('Body text');
});

it('swaps the body for the edit slot when :editing', function () {
    $html = Blade::render(<<<'BLADE'
        <x-sigta::comment :editing="true" author="Ada" meta="now">
            Original body
            <x-slot:edit>
                <textarea data-test="editor">Original body</textarea>
            </x-slot:edit>
        </x-sigta::comment>
    BLADE);

    expect($html)
        ->toContain('data-test="editor"')
        ->not->toContain('font-body text-[0.95rem]');   // body wrapper not rendered while editing
});

it('shows a leading avatar when given', function () {
    $html = Blade::render('<x-sigta::comment author="Ada Lovelace" meta="now" avatar="Ada Lovelace">Hi</x-sigta::comment>');

    expect($html)
        ->toContain('data-sigta="avatar"')
        ->toContain('AL');   // initials
});

it('omits the avatar column by default', function () {
    expect(Blade::render('<x-sigta::comment author="Ada" meta="now">Hi</x-sigta::comment>'))
        ->not->toContain('data-sigta="avatar"');
});

it('forwards wire:key and attributes onto the article', function () {
    $html = Blade::render('<x-sigta::comment author="Ada" meta="now" wire:key="comment-3" data-test="row">Hi</x-sigta::comment>');

    expect($html)
        ->toContain('wire:key="comment-3"')
        ->toContain('data-test="row"');
});
