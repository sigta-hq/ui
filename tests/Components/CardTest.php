<?php

use Illuminate\Support\Facades\Blade;

it('renders body content with the hover lift by default', function () {
    $html = Blade::render('<x-sigta::card>Marginalia</x-sigta::card>');

    expect($html)
        ->toContain('Marginalia')
        ->toContain('data-sigta="card"')
        ->toContain('data-hover')
        ->toContain('data-scope="card" data-part="body">Marginalia');

    expect(contractCss('card'))
        ->toContain('@apply flex flex-col bg-surface-raised border border-line rounded-panel overflow-hidden;')
        ->toContain('&[data-hover] { @apply transition duration-200 ease-out hover:-translate-y-[3px] hover:shadow-paper; }');
});

it('disables the hover lift for static cards', function () {
    $html = Blade::render('<x-sigta::card :hover="false">Static</x-sigta::card>');

    expect($html)
        ->toContain('data-sigta="card"')
        ->not->toContain('data-hover');
});

it('renders the thumb slot above the body', function () {
    $html = Blade::render(<<<'BLADE'
        <x-sigta::card>
            <x-slot:thumb><svg aria-hidden="true"></svg></x-slot:thumb>
            Body copy
        </x-sigta::card>
    BLADE);

    expect($html)
        ->toContain('data-scope="card" data-part="thumb"><svg aria-hidden="true"></svg>')
        ->toContain('Body copy');

    expect(contractCss('card'))->toContain('[data-scope="card"][data-part="thumb"] {'."\n".'    @apply relative aspect-video bg-surface border-b border-line overflow-hidden;');

    expect(strpos($html, 'data-part="thumb"'))->toBeLessThan(strpos($html, 'Body copy'));
});

it('omits the thumb wrapper when no thumb slot is given', function () {
    $html = Blade::render('<x-sigta::card>Just body</x-sigta::card>');

    expect($html)->not->toContain('data-part="thumb"');
});

it('forwards arbitrary attributes', function () {
    $html = Blade::render('<x-sigta::card wire:key="card-1" data-test="card">Body</x-sigta::card>');

    expect($html)
        ->toContain('wire:key="card-1"')
        ->toContain('data-test="card"');
});

it('drops the body padding when the card is given its own', function () {
    $default = Blade::render('<x-sigta::card>Body</x-sigta::card>');
    $own = Blade::render('<x-sigta::card class="h-full p-5">Body</x-sigta::card>');
    $responsive = Blade::render('<x-sigta::card class="sm:!px-4">Body</x-sigta::card>');
    $unrelated = Blade::render('<x-sigta::card class="pointer-events-none place-self-center">Body</x-sigta::card>');

    expect($default)->not->toContain('data-padding')
        ->and($own)->toContain('data-padding="custom"')
        ->and($own)->toContain('p-5')
        ->and($responsive)->toContain('data-padding="custom"')
        ->and($unrelated)->not->toContain('data-padding');

    expect(contractCss('card'))->toContain('[data-sigta="card"]:not([data-padding="custom"]) > & { @apply p-[clamp(18px,2vw,26px)]; }');
});
