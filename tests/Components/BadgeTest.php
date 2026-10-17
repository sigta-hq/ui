<?php

use Illuminate\Support\Facades\Blade;

it('renders an outline badge by default', function () {
    $html = Blade::render('<x-sigta::badge>Outline</x-sigta::badge>');

    expect($html)
        ->toContain('Outline')
        ->toContain('data-sigta="badge"')
        ->toContain('data-variant="outline"');

    expect(contractCss('badge'))
        ->toContain('font-mono')
        ->toContain('leading-none') // tight line-height keeps the label vertically centered
        ->toContain('rounded-full')
        ->toContain('[data-variant="accent"])) { @apply bg-transparent text-ink border-ink; }');
});

it('renders the solid variant as an ink fill', function () {
    expect(Blade::render('<x-sigta::badge variant="solid">Solid</x-sigta::badge>'))->toContain('data-variant="solid"');
    expect(contractCss('badge'))->toContain('&[data-variant="solid"] { @apply bg-ink text-surface border-ink; }');
});

it('renders the accent variant with the accent border', function () {
    expect(Blade::render('<x-sigta::badge variant="accent">Highlight</x-sigta::badge>'))->toContain('data-variant="accent"');
    expect(contractCss('badge'))->toContain('&[data-variant="accent"] { @apply bg-accent border-accent-line text-ink; }');
});

it('forwards arbitrary attributes', function () {
    $html = Blade::render('<x-sigta::badge id="state" data-test="badge" class="mt-2">New</x-sigta::badge>');

    expect($html)
        ->toContain('id="state"')
        ->toContain('data-test="badge"')
        ->toContain('mt-2');
});
