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

it('lets a consumer display class replace the default stacking layout', function () {
    $default = Blade::render('<x-sigta::panel>Body</x-sigta::panel>');
    $row = Blade::render('<x-sigta::panel class="flex items-center justify-between">Body</x-sigta::panel>');
    $grid = Blade::render('<x-sigta::panel class="grid gap-1">Body</x-sigta::panel>');
    $important = Blade::render('<x-sigta::panel class="!block">Body</x-sigta::panel>');

    expect($default)->toContain('relative flex flex-col rounded-panel')
        ->and($row)->toContain('flex items-center justify-between')
        ->and($row)->not->toContain('flex-col')
        ->and($grid)->toContain('grid gap-1')
        ->and($grid)->not->toContain('flex')
        ->and($important)->not->toContain('flex');
});

it('lets a consumer direction class turn the flex without replacing it', function () {
    $html = Blade::render('<x-sigta::panel class="flex-row items-stretch">Body</x-sigta::panel>');

    expect($html)->toContain('relative flex rounded-panel')
        ->and($html)->toContain('flex-row items-stretch')
        ->and($html)->not->toContain('flex-col');
});

it('keeps the stack for responsive and unrelated flex classes', function () {
    $responsive = Blade::render('<x-sigta::panel class="gap-4 sm:flex-row md:grid">Body</x-sigta::panel>');
    $unrelated = Blade::render('<x-sigta::panel class="flex-1 flex-wrap">Body</x-sigta::panel>');

    expect($responsive)->toContain('relative flex flex-col rounded-panel')
        ->and($unrelated)->toContain('relative flex flex-col rounded-panel');
});
