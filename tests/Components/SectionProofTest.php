<?php

use Illuminate\Support\Facades\Blade;

it('renders with zero props: the hairline strip and no stat or logos', function () {
    $html = Blade::render('<x-sigta::section.proof />');

    expect($html)
        ->toContain('data-sigta="section.proof"')
        ->toContain('border-y')
        ->not->toContain('data-sigta="stat"')
        ->not->toContain('aria-label');
});

it('renders the stat and logos from props', function () {
    $html = Blade::render('<x-sigta::section.proof years-value="20 years" years-label="in the field" :logos="$logos" aria-label="Experience and clients" />', [
        'logos' => ['Acme Inc.', 'Foobar Ltd.'],
    ]);

    expect($html)
        ->toContain('data-sigta="stat"')
        ->toContain('20 years')
        ->toContain('in the field')
        ->toContain('Acme Inc.')
        ->toContain('Foobar Ltd.')
        ->toContain('aria-label="Experience and clients"');
});

it('forwards arbitrary attributes', function () {
    $html = Blade::render('<x-sigta::section.proof data-test="proof" />');

    expect($html)->toContain('data-test="proof"');
});
