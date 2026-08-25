<?php

use Illuminate\Support\Facades\Blade;

it('renders an amount field and a currency toggle', function () {
    $html = Blade::render('<x-sigta::money-input label="Hourly rate" :value="145000" currency="DKK" />');

    expect($html)
        ->toContain('data-sigta="money-input"')
        ->toContain('data-test="money-amount"')
        ->toContain('data-test="money-currency"')
        ->toContain('data-sigta="segmented"')
        ->toContain('Hourly rate');
});

it('seeds the amount as major units and the hidden total as minor', function () {
    $html = Blade::render('<x-sigta::money-input :value="145050" currency="DKK" />');

    expect($html)
        ->toContain('major:')
        ->toContain('1450.5')            // 145050 minor → 1450.5 major
        ->toContain('type="hidden"')
        ->toContain('value="145050"');   // hidden seeded with minor
});

it('forwards wire:model onto the hidden minor input', function () {
    $html = Blade::render('<x-sigta::money-input wire:model="amountMinor" :value="1000" />');

    expect($html)->toMatch('/<input type="hidden"[^>]*wire:model="amountMinor"/');
});

it('binds the currency toggle to currencyModel when given', function () {
    $html = Blade::render('<x-sigta::money-input currency="EUR" currency-model="currency" />');

    expect($html)
        ->toContain('wire:model="currency"')
        ->toContain('data-test="money-currency"');
});

it('defaults the currency options to DKK / EUR / USD', function () {
    $html = Blade::render('<x-sigta::money-input />');

    expect($html)
        ->toContain('value="DKK"')
        ->toContain('value="EUR"')
        ->toContain('value="USD"');
});

it('accepts a custom currency map', function () {
    $html = Blade::render('<x-sigta::money-input :currencies="[\'DKK\' => \'kr.\', \'GBP\' => \'£\']" currency="GBP" />');

    expect($html)
        ->toContain('value="DKK"')
        ->toContain('value="GBP"')
        ->not->toContain('value="USD"');
});
