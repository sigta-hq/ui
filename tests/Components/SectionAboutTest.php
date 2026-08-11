<?php

use Illuminate\Support\Facades\Blade;

it('renders with default Danish copy and a portrait placeholder', function () {
    $html = Blade::render('<x-sigta::section.about />');

    expect($html)
        ->toContain('data-sigta="section.about"')
        ->toContain('Om Sigta · Manden bag')
        ->toContain('Jeg hedder Søren, og jeg taler begge sprog.')
        ->toContain('Jeg har bygget software i 14 år')
        ->toContain('PORTRÆT PÅ VEJ')
        ->toContain('det er mig ✎')
        ->toContain('rounded-frame');
});

it('lets the heading and eyebrow props win', function () {
    $html = Blade::render('<x-sigta::section.about eyebrow="Egen kicker" heading="Et andet navn" />');

    expect($html)
        ->toContain('Egen kicker')
        ->toContain('Et andet navn')
        ->not->toContain('Jeg hedder Søren, og jeg taler begge sprog.');
});

it('renders custom paragraphs from the :paragraphs prop', function () {
    $html = Blade::render('<x-sigta::section.about :paragraphs="$p" />', ['p' => ['Første egne afsnit.', 'Andet egne afsnit.']]);

    expect($html)
        ->toContain('Første egne afsnit.')
        ->toContain('Andet egne afsnit.')
        ->not->toContain('Jeg har bygget software i 14 år');
});

it('replaces the placeholder with the portrait slot', function () {
    $html = Blade::render('<x-sigta::section.about><x-slot:portrait>EGET PORTRÆT</x-slot:portrait></x-sigta::section.about>');

    expect($html)
        ->toContain('EGET PORTRÆT')
        ->not->toContain('PORTRÆT PÅ VEJ');
});

it('forwards arbitrary attributes', function () {
    $html = Blade::render('<x-sigta::section.about data-test="about" />');

    expect($html)->toContain('data-test="about"');
});
