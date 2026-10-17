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
        ->toContain('data-scope="section.proof" data-part="strip"');

    expect(contractCss('section.proof'))
        ->toContain('@apply flex flex-wrap items-center justify-center gap-[clamp(20px,4vw,56px)] border-y border-line py-[clamp(24px,3vw,40px)];');
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

it('labels the strip, and lets a consumer label win without a duplicate attribute', function () {
    expect(Blade::render('<x-sigta::section.proof />'))->toContain('aria-label="Erfaring og kunder"');

    $html = Blade::render('<x-sigta::section.proof aria-label="Kunder" />');
    expect($html)->toContain('aria-label="Kunder"')->not->toContain('Erfaring og kunder');
});

it('forwards arbitrary attributes', function () {
    $html = Blade::render('<x-sigta::section.proof data-test="proof" />');

    expect($html)->toContain('data-test="proof"');
});
