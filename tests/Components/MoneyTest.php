<?php

use Illuminate\Support\Facades\Blade;

it('formats DKK minor units with a trailing symbol and Danish separators', function () {
    $html = Blade::render('<x-sigta::money :minor="123456" currency="DKK" />');

    expect($html)
        ->toContain('data-sigta="money"')
        ->toContain('data-currency="DKK"')
        ->toContain('tabular-nums')
        ->toContain('1.234,56 kr.');
});

it('formats USD with a leading symbol and US separators', function () {
    expect(Blade::render('<x-sigta::money :minor="123456" currency="USD" />'))
        ->toContain('$1,234.56');
});

it('formats EUR with a leading symbol and European separators', function () {
    expect(Blade::render('<x-sigta::money :minor="123456" currency="EUR" />'))
        ->toContain('€1.234,56');
});

it('supports whole-major display via decimals=0', function () {
    expect(Blade::render('<x-sigta::money :minor="123456" currency="USD" :decimals="0" />'))
        ->toContain('$1,235')       // 1234.56 rounds to 1,235
        ->not->toContain('.56');
});

it('renders an unknown currency code as a trailing symbol', function () {
    expect(Blade::render('<x-sigta::money :minor="5000" currency="JPY" />'))
        ->toContain('data-currency="JPY"')
        ->toContain('50.00 JPY');
});

it('appends a muted per-unit suffix', function () {
    $html = Blade::render('<x-sigta::money :minor="145000" currency="DKK" suffix="/ hour" />');

    expect($html)
        ->toContain('1.450,00 kr.')
        ->toContain('text-graphite')
        ->toContain('/ hour');
});

it('renders zero for a null amount', function () {
    expect(Blade::render('<x-sigta::money currency="USD" />'))->toContain('$0.00');
});

it('allows overriding symbol and separators', function () {
    expect(Blade::render('<x-sigta::money :minor="100000" currency="USD" symbol="US$" thousands=" " />'))
        ->toContain('US$1 000.00');
});
