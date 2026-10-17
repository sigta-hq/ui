<?php

use Illuminate\Support\Facades\Blade;

it('renders slot content in a bordered surface', function () {
    $html = Blade::render('<x-sigta::panel>Prisindhold</x-sigta::panel>');

    expect($html)
        ->toContain('data-sigta="panel"')
        ->toContain('Prisindhold');

    expect(contractCss('panel'))
        ->toContain('@apply relative rounded-panel;')
        ->toContain('&:not([data-variant="inverse"]) { @apply bg-surface-raised border border-line; }');
});

it('does not clip overhanging children', function () {
    expect(contractCss('panel'))->not->toContain('overflow-hidden');
});

it('forwards arbitrary attributes', function () {
    $html = Blade::render('<x-sigta::panel wire:key="panel-1" data-test="panel">Body</x-sigta::panel>');

    expect($html)
        ->toContain('wire:key="panel-1"')
        ->toContain('data-test="panel"');
});

it('renders the default light surface with a variant marker', function () {
    expect(Blade::render('<x-sigta::panel>Body</x-sigta::panel>'))
        ->toContain('data-variant="default"');
});

it('renders an inverse (dark) surface', function () {
    $html = Blade::render('<x-sigta::panel variant="inverse">Dark band</x-sigta::panel>');

    expect($html)
        ->toContain('data-sigta="panel"')
        ->toContain('data-variant="inverse"')
        ->toContain('Dark band');

    expect(contractCss('panel'))->toContain('&[data-variant="inverse"] { @apply bg-inverse text-inverse-fg border border-inverse-line; }');
});

it('lets a consumer padding class replace the default padding', function () {
    $default = Blade::render('<x-sigta::panel>Body</x-sigta::panel>');
    $own = Blade::render('<x-sigta::panel class="p-4">Body</x-sigta::panel>');
    $responsive = Blade::render('<x-sigta::panel class="mt-6 sm:!p-5">Body</x-sigta::panel>');
    $unrelated = Blade::render('<x-sigta::panel class="pointer-events-none place-items-center">Body</x-sigta::panel>');

    expect($default)->not->toContain('data-padding')
        ->and($own)->toContain('data-padding="custom"')
        ->and($own)->toContain('p-4')
        ->and($responsive)->toContain('data-padding="custom"')
        ->and($unrelated)->not->toContain('data-padding');

    expect(contractCss('panel'))->toContain('&:not([data-padding="custom"]) { @apply p-[clamp(24px,3vw,40px)]; }');
});

it('lets a consumer display class replace the default stacking layout', function () {
    $default = Blade::render('<x-sigta::panel>Body</x-sigta::panel>');
    $row = Blade::render('<x-sigta::panel class="flex items-center justify-between">Body</x-sigta::panel>');
    $grid = Blade::render('<x-sigta::panel class="grid gap-1">Body</x-sigta::panel>');
    $important = Blade::render('<x-sigta::panel class="!block">Body</x-sigta::panel>');

    expect($default)->not->toContain('data-layout')
        ->and($row)->toContain('data-layout="custom"')
        ->and($row)->toContain('flex items-center justify-between')
        ->and($grid)->toContain('data-layout="custom"')
        ->and($grid)->toContain('grid gap-1')
        ->and($important)->toContain('data-layout="custom"');

    expect(contractCss('panel'))->toContain('&:not([data-layout="custom"]) { @apply flex flex-col; }');
});

it('lets a consumer direction class turn the flex without replacing it', function () {
    $html = Blade::render('<x-sigta::panel class="flex-row items-stretch">Body</x-sigta::panel>');

    // The column comes from the component layer, so the consumer's flex-row outranks it.
    expect($html)->not->toContain('data-layout')
        ->and($html)->toContain('flex-row items-stretch');
});

it('keeps the stack for responsive and unrelated flex classes', function () {
    $responsive = Blade::render('<x-sigta::panel class="gap-4 sm:flex-row md:grid">Body</x-sigta::panel>');
    $unrelated = Blade::render('<x-sigta::panel class="flex-1 flex-wrap">Body</x-sigta::panel>');

    expect($responsive)->not->toContain('data-layout')
        ->and($unrelated)->not->toContain('data-layout');
});
