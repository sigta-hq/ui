<?php

use Illuminate\Support\Facades\Blade;

it('renders with zero props: root marker and no brand, copy or links', function () {
    $html = Blade::render('<x-sigta::section.footer />');

    expect($html)
        ->toContain('data-sigta="section.footer"')
        ->not->toContain('data-sigta="brand"')
        ->not->toContain('<h4')
        ->not->toContain('<a ')
        ->not->toContain('border-dashed');
});

it('renders the brand slot, tagline and signoff', function () {
    $html = Blade::render(<<<'BLADE'
        <x-sigta::section.footer tagline="We build durable software." signoff="© 2030 Acme Inc.">
            <x-slot:brand><x-sigta::brand name="Acme" /></x-slot:brand>
        </x-sigta::section.footer>
    BLADE);

    expect($html)
        ->toContain('data-sigta="brand"')
        ->toContain('Acme')
        ->toContain('We build durable software.')
        ->toContain('© 2030 Acme Inc.');
});

it('renders custom columns from the :columns prop', function () {
    $html = Blade::render('<x-sigta::section.footer :columns="$columns" />', [
        'columns' => [
            ['heading' => 'Product', 'links' => [['label' => 'Features', 'href' => '/features']]],
        ],
    ]);

    expect($html)
        ->toContain('Product')
        ->toContain('Features')
        ->toContain('/features');
});

it('forwards arbitrary attributes', function () {
    $html = Blade::render('<x-sigta::section.footer data-test="footer" />');

    expect($html)->toContain('data-test="footer"');
});

it('gives each column its own track beside the brand', function (int $columnCount, string $gridClasses) {
    $columns = array_fill(0, $columnCount, ['heading' => 'Column', 'links' => [['label' => 'Link', 'href' => '#']]]);

    $html = Blade::render('<x-sigta::section.footer :columns="$columns" />', ['columns' => $columns]);

    expect($html)->toContain('class="grid gap-8 '.$gridClasses.'"');
})->with([
    'one column' => [1, 'sm:grid-cols-2 md:grid-cols-[1.5fr_1fr]'],
    'two columns' => [2, 'sm:grid-cols-2 md:grid-cols-[1.5fr_1fr_1fr]'],
    'three columns' => [3, 'sm:grid-cols-2 lg:grid-cols-[1.5fr_1fr_1fr_1fr]'],
]);
