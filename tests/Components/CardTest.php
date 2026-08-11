<?php

use Illuminate\Support\Facades\Blade;

it('renders body content with the hover lift by default', function () {
    $html = Blade::render('<x-sigta::card>Marginalia</x-sigta::card>');

    expect($html)
        ->toContain('Marginalia')
        ->toContain('data-sigta="card"')
        ->toContain('bg-surface-raised')
        ->toContain('rounded-panel')
        ->toContain('overflow-hidden')
        ->toContain('hover:-translate-y-[3px]')
        ->toContain('hover:shadow-paper');
});

it('disables the hover lift for static cards', function () {
    $html = Blade::render('<x-sigta::card :hover="false">Static</x-sigta::card>');

    expect($html)
        ->not->toContain('hover:-translate-y-[3px]')
        ->not->toContain('hover:shadow-paper');
});

it('renders the thumb slot above the body', function () {
    $html = Blade::render(<<<'BLADE'
        <x-sigta::card>
            <x-slot:thumb><svg aria-hidden="true"></svg></x-slot:thumb>
            Body copy
        </x-sigta::card>
    BLADE);

    expect($html)
        ->toContain('aspect-video')
        ->toContain('bg-surface')
        ->toContain('border-b border-line')
        ->toContain('Body copy');

    expect(strpos($html, 'aspect-video'))->toBeLessThan(strpos($html, 'Body copy'));
});

it('omits the thumb wrapper when no thumb slot is given', function () {
    $html = Blade::render('<x-sigta::card>Just body</x-sigta::card>');

    expect($html)->not->toContain('aspect-video');
});

it('forwards arbitrary attributes', function () {
    $html = Blade::render('<x-sigta::card wire:key="card-1" data-test="card">Body</x-sigta::card>');

    expect($html)
        ->toContain('wire:key="card-1"')
        ->toContain('data-test="card"');
});
