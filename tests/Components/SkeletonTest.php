<?php

use Illuminate\Support\Facades\Blade;

it('renders a pulsing line placeholder by default', function () {
    $html = Blade::render('<x-sigta::skeleton />');

    expect($html)
        ->toContain('data-sigta="skeleton"')
        ->toContain('data-variant="line"')
        ->toContain('aria-hidden="true"');

    expect(contractCss('skeleton'))
        ->toContain('@apply bg-line/60 animate-pulse;')
        ->toContain('[data-variant="circle"])) { @apply h-4 rounded-base; }');
});

it('renders the heading variant', function () {
    expect(Blade::render('<x-sigta::skeleton variant="heading" />'))->toContain('data-variant="heading"');
    expect(contractCss('skeleton'))->toContain('&[data-variant="heading"] { @apply h-8 w-2/3 rounded-base; }');
});

it('renders the block variant', function () {
    expect(Blade::render('<x-sigta::skeleton variant="block" />'))->toContain('data-variant="block"');
    expect(contractCss('skeleton'))->toContain('&[data-variant="block"] { @apply aspect-video rounded-base; }');
});

it('renders the circle variant', function () {
    expect(Blade::render('<x-sigta::skeleton variant="circle" />'))->toContain('data-variant="circle"');
    expect(contractCss('skeleton'))->toContain('&[data-variant="circle"] { @apply size-10 rounded-full; }');
});

it('merges custom sizing classes and forwards attributes', function () {
    $html = Blade::render('<x-sigta::skeleton class="w-24 h-3" data-test="skeleton" />');

    expect($html)
        ->toContain('w-24 h-3')
        ->toContain('data-sigta="skeleton"')
        ->toContain('data-test="skeleton"');
});
