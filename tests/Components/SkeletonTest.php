<?php

use Illuminate\Support\Facades\Blade;

it('renders a pulsing line placeholder by default', function () {
    $html = Blade::render('<x-sigta::skeleton />');

    expect($html)
        ->toContain('data-sigta="skeleton"')
        ->toContain('bg-line/60')
        ->toContain('animate-pulse')
        ->toContain('h-4')
        ->toContain('rounded-base')
        ->toContain('aria-hidden="true"');
});

it('renders the heading variant', function () {
    $html = Blade::render('<x-sigta::skeleton variant="heading" />');

    expect($html)
        ->toContain('h-8')
        ->toContain('w-2/3');
});

it('renders the block variant', function () {
    expect(Blade::render('<x-sigta::skeleton variant="block" />'))->toContain('aspect-video');
});

it('renders the circle variant', function () {
    $html = Blade::render('<x-sigta::skeleton variant="circle" />');

    expect($html)
        ->toContain('size-10')
        ->toContain('rounded-full')
        ->not->toContain('rounded-base');
});

it('merges custom sizing classes and forwards attributes', function () {
    $html = Blade::render('<x-sigta::skeleton class="w-24 h-3" data-test="skeleton" />');

    expect($html)
        ->toContain('w-24 h-3')
        ->toContain('animate-pulse')
        ->toContain('data-test="skeleton"');
});
