<?php

use Illuminate\Support\Facades\Blade;

it('renders slot content in a bordered surface', function () {
    $html = Blade::render('<x-sigta::panel>Prisindhold</x-sigta::panel>');

    expect($html)
        ->toContain('data-sigta="panel"')
        ->toContain('Prisindhold')
        ->toContain('bg-surface-raised')
        ->toContain('border border-line')
        ->toContain('rounded-panel')
        ->toContain('relative');
});

it('does not clip overhanging children', function () {
    $html = Blade::render('<x-sigta::panel>Body</x-sigta::panel>');

    expect($html)->not->toContain('overflow-hidden');
});

it('forwards arbitrary attributes', function () {
    $html = Blade::render('<x-sigta::panel wire:key="panel-1" data-test="panel">Body</x-sigta::panel>');

    expect($html)
        ->toContain('wire:key="panel-1"')
        ->toContain('data-test="panel"');
});

it('renders the default light surface with a variant marker', function () {
    expect(Blade::render('<x-sigta::panel>Body</x-sigta::panel>'))
        ->toContain('data-variant="default"')
        ->toContain('bg-surface-raised')
        ->toContain('border border-line');
});

it('renders an inverse (dark) surface', function () {
    $html = Blade::render('<x-sigta::panel variant="inverse">Dark band</x-sigta::panel>');

    expect($html)
        ->toContain('data-sigta="panel"')
        ->toContain('data-variant="inverse"')
        ->toContain('bg-inverse')
        ->toContain('text-inverse-fg')
        ->toContain('border border-inverse-line')
        ->toContain('rounded-panel')
        ->toContain('Dark band')
        ->not->toContain('bg-surface-raised');
});

it('lets a consumer padding class replace the default padding', function () {
    $default = Blade::render('<x-sigta::panel>Body</x-sigta::panel>');
    $own = Blade::render('<x-sigta::panel class="p-4">Body</x-sigta::panel>');
    $responsive = Blade::render('<x-sigta::panel class="mt-6 sm:!p-5">Body</x-sigta::panel>');
    $unrelated = Blade::render('<x-sigta::panel class="pointer-events-none place-items-center">Body</x-sigta::panel>');

    expect($default)->toContain('p-[clamp(24px,3vw,40px)]')
        ->and($own)->not->toContain('p-[clamp(24px,3vw,40px)]')
        ->and($own)->toContain('p-4')
        ->and($responsive)->not->toContain('p-[clamp(24px,3vw,40px)]')
        ->and($unrelated)->toContain('p-[clamp(24px,3vw,40px)]');
});
