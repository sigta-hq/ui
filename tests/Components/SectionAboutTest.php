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
        ->toContain('data-scope="section.about" data-part="frame"')
        ->toMatch('/data-sigta="annot"[^>]*data-part="note"|data-part="note"[^>]*data-sigta="annot"/');

    expect(contractCss('section.about'))
        ->toContain('[data-scope="section.about"][data-part="frame"] {')
        ->toContain('@apply overflow-hidden rounded-frame border border-line bg-surface-raised shadow-paper text-ink;')
        ->toContain('@apply grid items-center gap-[clamp(32px,5vw,72px)] md:grid-cols-[0.8fr_1.2fr];');
});

it('renders the default paragraphs both runtimes share', function () {
    $html = Blade::render('<x-sigta::section.about />');

    foreach (\Sigta\Ui\Sections::get('about', 'paragraphs') as $paragraph) {
        expect($html)->toContain('<p data-scope="section.about" data-part="paragraph">'.e($paragraph).'</p>');
    }
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
