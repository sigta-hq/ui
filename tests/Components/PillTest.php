<?php

use Illuminate\Support\Facades\Blade;

it('renders a neutral pill with a leading dot by default', function () {
    $html = Blade::render('<x-sigta::pill>Paused</x-sigta::pill>');

    expect($html)
        ->toContain('Paused')
        ->toContain('data-sigta="pill"')
        ->toContain('bg-graphite/10')
        ->toContain('text-graphite')
        ->toContain('leading-none') // tight line-height keeps the label vertically centered
        ->toContain('bg-current')
        ->toContain('aria-hidden="true"');
});

it('renders the success variant on a success tint', function () {
    $html = Blade::render('<x-sigta::pill variant="success">Operational</x-sigta::pill>');

    expect($html)
        ->toContain('bg-success/10')
        ->toContain('text-success');
});

it('renders the danger variant on a danger tint', function () {
    $html = Blade::render('<x-sigta::pill variant="danger">Degraded</x-sigta::pill>');

    expect($html)
        ->toContain('bg-danger/10')
        ->toContain('text-danger');
});

it('forwards arbitrary attributes', function () {
    $html = Blade::render('<x-sigta::pill wire:key="status" data-test="pill">Live</x-sigta::pill>');

    expect($html)
        ->toContain('wire:key="status"')
        ->toContain('data-test="pill"');
});
