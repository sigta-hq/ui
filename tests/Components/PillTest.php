<?php

use Illuminate\Support\Facades\Blade;

it('renders a neutral pill with a leading dot by default', function () {
    $html = Blade::render('<x-sigta::pill>Paused</x-sigta::pill>');

    expect($html)
        ->toContain('Paused')
        ->toContain('data-sigta="pill"')
        ->toContain('data-variant="neutral"')
        ->toContain('data-scope="pill" data-part="dot" aria-hidden="true"');

    expect(contractCss('pill'))
        ->toContain('[data-variant="danger"])) { @apply bg-graphite/10 text-graphite; }')
        ->toContain('leading-none') // tight line-height keeps the label vertically centered
        ->toContain('bg-current');
});

it('renders the success variant on a success tint', function () {
    expect(Blade::render('<x-sigta::pill variant="success">Operational</x-sigta::pill>'))->toContain('data-variant="success"');
    expect(contractCss('pill'))->toContain('&[data-variant="success"] { @apply bg-success/10 text-success; }');
});

it('renders the danger variant on a danger tint', function () {
    expect(Blade::render('<x-sigta::pill variant="danger">Degraded</x-sigta::pill>'))->toContain('data-variant="danger"');
    expect(contractCss('pill'))->toContain('&[data-variant="danger"] { @apply bg-danger/10 text-danger; }');
});

it('forwards arbitrary attributes', function () {
    $html = Blade::render('<x-sigta::pill wire:key="status" data-test="pill">Live</x-sigta::pill>');

    expect($html)
        ->toContain('wire:key="status"')
        ->toContain('data-test="pill"');
});
