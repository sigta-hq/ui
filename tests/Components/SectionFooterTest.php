<?php

use Illuminate\Support\Facades\Blade;

it('renders the brand, default tagline, nav columns and signoff', function () {
    $html = Blade::render('<x-sigta::section.footer />');

    expect($html)
        ->toContain('data-sigta="section.footer"')
        ->toContain('data-sigta="brand"')
        ->toContain('Sigta er tech-partneren for virksomheder uden egen teknisk ledelse')
        ->toContain('Navigation')
        ->toContain('Kontakt')
        ->toContain('Kan du genkende det?')
        ->toContain('sf@sigta.dk')
        ->toContain('© 2026 Sigta · CVR 45530760');
});

it('lets the tagline and signoff props win', function () {
    $html = Blade::render('<x-sigta::section.footer tagline="Egen tagline." signoff="© 2030 Andet" />');

    expect($html)
        ->toContain('Egen tagline.')
        ->toContain('© 2030 Andet')
        ->not->toContain('Sigta er tech-partneren for virksomheder uden egen teknisk ledelse');
});

it('renders custom columns from the :columns prop', function () {
    $html = Blade::render('<x-sigta::section.footer :columns="$columns" />', [
        'columns' => [
            ['heading' => 'Produkt', 'links' => [['label' => 'Funktioner', 'href' => '/features']]],
        ],
    ]);

    expect($html)
        ->toContain('Produkt')
        ->toContain('Funktioner')
        ->toContain('/features')
        ->not->toContain('Navigation');
});

it('forwards arbitrary attributes', function () {
    $html = Blade::render('<x-sigta::section.footer data-test="footer" />');

    expect($html)->toContain('data-test="footer"');
});

it('gives each column its own track beside the brand', function (int $columnCount, string $gridClasses) {
    $columns = array_fill(0, $columnCount, ['heading' => 'Kolonne', 'links' => [['label' => 'Link', 'href' => '#']]]);

    $html = Blade::render('<x-sigta::section.footer :columns="$columns" />', ['columns' => $columns]);

    expect($html)->toContain('class="grid gap-8 '.$gridClasses.'"');
})->with([
    'one column' => [1, 'sm:grid-cols-2 md:grid-cols-[1.5fr_1fr]'],
    'two columns' => [2, 'sm:grid-cols-2 md:grid-cols-[1.5fr_1fr_1fr]'],
    'three columns' => [3, 'sm:grid-cols-2 lg:grid-cols-[1.5fr_1fr_1fr_1fr]'],
]);
