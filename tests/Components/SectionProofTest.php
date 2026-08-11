<?php

use Illuminate\Support\Facades\Blade;

it('renders the default years stat and placeholder logos between hairlines', function () {
    $html = Blade::render('<x-sigta::section.proof />');

    expect($html)
        ->toContain('data-sigta="section.proof"')
        ->toContain('14 år')
        ->toContain('erfaring med software')
        ->toContain('Park Styling')
        ->toContain('Jelling Musikfestival')
        ->toContain('data-sigta="stat"')
        ->toContain('border-y');
});

it('lets the years props win', function () {
    $html = Blade::render('<x-sigta::section.proof years-value="20 år" years-label="i marken" />');

    expect($html)
        ->toContain('20 år')
        ->toContain('i marken')
        ->not->toContain('erfaring med software');
});

it('renders custom logos from the :logos prop', function () {
    $html = Blade::render('<x-sigta::section.proof :logos="$logos" />', [
        'logos' => ['Acme A/S', 'Foobar ApS'],
    ]);

    expect($html)
        ->toContain('Acme A/S')
        ->toContain('Foobar ApS')
        ->not->toContain('Park Styling');
});

it('forwards arbitrary attributes', function () {
    $html = Blade::render('<x-sigta::section.proof data-test="proof" />');

    expect($html)->toContain('data-test="proof"');
});
