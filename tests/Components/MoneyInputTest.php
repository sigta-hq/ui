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
        ->toContain('x-data="sigtaMoneyInput()"')                  // reads its start from the inputs below
        ->toMatch('/<input[^>]*value="1450.5"[^>]*data-part="amount"/s')   // 145050 minor becomes 1450.5 major
        ->toContain('type="hidden"')
        ->toContain('value="145050"');   // hidden seeded with minor
});

it('writes major units like the runtime does, dropping trailing zeros', function () {
    expect(Blade::render('<x-sigta::money-input :value="145000" />'))->toMatch('/value="1450"[^>]*data-part="amount"/s');
    expect(Blade::render('<x-sigta::money-input :value="5" />'))->toMatch('/value="0.05"[^>]*data-part="amount"/s');
    expect(Blade::render('<x-sigta::money-input />'))->toMatch('/<input[^>]*data-part="amount"/s')->not->toMatch('/value="[^"]+"[^>]*data-part="amount"/s');
});

it('emits minor units through the core parser in the runtime', function () {
    $runtime = file_get_contents(dirname(__DIR__, 2).'/resources/js/sigta.js');

    expect($runtime)
        ->toContain("Alpine.data('sigtaMoneyInput'")
        ->toContain('majorFromMinor(this.$refs.total.value)')
        ->toContain('minorFromMajor(this.major)');
});

it('renders contract parts and the currency options on the server', function () {
    $html = Blade::render('<x-sigta::money-input label="Rate" :currencies="[\'DKK\' => \'kr.\', \'EUR\' => \'EUR\']" currency="DKK" />');

    expect($html)
        ->not->toContain('class=')
        ->toMatch('/data-scope="money-input"\\s+data-part="amount"/')
        ->toContain('data-part="currency"')
        ->toContain('data-part="menu"')
        ->toMatch('/aria-selected="true"[^>]*data-part="option"\s*>\s*<span>DKK<\/span>\s*<span data-scope="money-input" data-part="label">kr.<\/span>/s')
        ->toMatch('/aria-selected="false"[^>]*data-part="option"\s*>\s*<span>EUR<\/span>\s*<\/button>/s');   // a label equal to its code is not repeated

    expect(contractCss('money-input'))
        ->toContain('focus-within:border-ink')
        ->toContain('&[aria-selected="true"] { @apply text-ink; }');
});

it('points its label at the amount field', function () {
    $html = Blade::render('<x-sigta::money-input label="Rate" />');

    preg_match('/<label[^>]*for="([^"]+)"/', $html, $label);
    expect($html)->toMatch('/id="'.preg_quote($label[1], '/').'"[^>]*data-part="amount"/s');
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
