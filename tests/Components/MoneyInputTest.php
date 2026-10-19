<?php

use Illuminate\Support\Facades\Blade;

it('renders an amount field and an inline currency switcher', function () {
    $html = Blade::render('<x-sigta::money-input label="Hourly rate" :value="145000" currency="DKK" />');

    expect($html)
        ->toContain('data-sigta="money-input"')
        ->toContain('data-test="money-amount"')
        ->toContain('data-test="money-currency"')
        ->toContain('Hourly rate')
        ->not->toContain('data-sigta="segmented"'); // switcher is inline, not a separate toggle field
});

it('seeds the amount as major units and the hidden total as minor', function () {
    $html = Blade::render('<x-sigta::money-input :value="145050" currency="DKK" />');

    expect($html)
        ->toContain('major:')
        ->toContain('1450.5')            // 145050 minor becomes 1450.5 major
        ->toContain('type="hidden"')
        ->toContain('value="145050"');   // hidden seeded with minor
});

it('forwards wire:model onto the hidden minor input', function () {
    $html = Blade::render('<x-sigta::money-input wire:model="amountMinor" :value="1000" />');

    expect($html)->toMatch('/<input type="hidden"[^>]*wire:model="amountMinor"/');
});

it('binds the currency to currencyModel through a hidden input when given', function () {
    $html = Blade::render('<x-sigta::money-input currency="EUR" currency-model="currency" />');

    expect($html)
        ->toMatch('/<input type="hidden"[^>]*wire:model="currency"/')
        ->toContain('data-test="money-currency"');
});

it('defaults the currency options to DKK / EUR / USD', function () {
    $html = Blade::render('<x-sigta::money-input />');

    // The options live in the Alpine currencies map that drives the dropdown.
    expect($html)
        ->toContain('DKK')
        ->toContain('EUR')
        ->toContain('USD');
});

it('accepts a custom currency map', function () {
    $html = Blade::render('<x-sigta::money-input :currencies="[\'DKK\' => \'kr.\', \'GBP\' => \'£\']" currency="GBP" />');

    expect($html)
        ->toContain('DKK')
        ->toContain('GBP')
        ->not->toContain('USD');
});

it('renders an inline variant whose currency divider waits for hover or focus', function () {
    $html = Blade::render('<x-sigta::money-input variant="inline" wire:model="price" />');

    expect($html)
        ->toContain('data-variant="inline"')
        ->toContain('group/money')
        ->toContain('-mx-[0.8em]')
        ->toContain('border-transparent')
        ->toContain('group-hover/money:border-line')
        ->toContain('py-[0.3em]')
        ->toMatch('/<input type="hidden"[^>]*wire:model="price"/')
        ->not->toMatch('/data-sigta="field"[^>]*\svariant=/');
});

it('keeps the boxed money field as the default variant', function () {
    $html = Blade::render('<x-sigta::money-input />');

    expect($html)->toContain('bg-surface')->not->toContain('group/money');
});
