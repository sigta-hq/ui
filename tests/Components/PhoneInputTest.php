<?php

use Illuminate\Support\Facades\Blade;
use Sigta\Ui\PhoneCountries;

it('renders a number field with the calling code picker inside it', function () {
    $html = Blade::render('<x-sigta::phone-input label="Mobile number" value="+45 25 04 88 63" />');

    expect($html)
        ->toContain('data-sigta="phone-input"')
        ->toContain('data-test="phone-country"')
        ->toContain('data-test="phone-number"')
        ->toContain('data-test="phone-countries"')
        ->toContain('popover="manual"')
        ->toContain('Mobile number');
});

it('ties the label to the number, not to the country button', function () {
    $html = Blade::render('<x-sigta::phone-input label="Mobile number" id="mobile" />');

    expect($html)
        ->toMatch('/<label[^>]*for="mobile"/')
        ->toMatch('/<input\s+type="tel"[^>]*id="mobile"/');
});

it('splits a typed calling code from the number and keeps how the number was written', function (string $value) {
    $html = Blade::render('<x-sigta::phone-input :value="$value" country="DK" />', ['value' => $value]);

    expect($html)
        ->toContain('data-country="SE"')
        ->toContain('data-calling-code="+46"')
        ->toMatch('/<input\s+type="tel"[^>]*value="70 412 38 95"/')
        ->toMatch('/<input type="hidden"[^>]*value="\+46 70 412 38 95"/');
})->with(['+46 70 412 38 95', '0046 70 412 38 95']);

it('gives a number without a calling code the country passed in', function () {
    $html = Blade::render('<x-sigta::phone-input value="25 04 88 63" country="DK" />');

    expect($html)
        ->toContain('data-country="DK"')
        ->toMatch('/<input type="hidden"[^>]*value="\+45 25 04 88 63"/');
});

it('starts on the first preferred country when nothing says which', function () {
    $html = Blade::render('<x-sigta::phone-input :preferred="[\'NO\', \'DK\']" />');

    expect($html)
        ->toContain('data-country="NO"')
        ->toMatch('/<input type="hidden"[^>]*value=""/');
});

it('shows a calling code that countries share as the country picked, else as its main country', function () {
    $us = Blade::render('<x-sigta::phone-input value="+1 202 555 0143" />');
    $canada = Blade::render('<x-sigta::phone-input value="+1 416 555 0143" country="CA" />');

    expect($us)->toContain('data-country="US"')
        ->and($canada)->toContain('data-country="CA"');
});

it('lists the preferred countries first and then the rest without them', function () {
    $html = Blade::render('<x-sigta::phone-input :preferred="[\'DK\', \'SE\']" />');

    preg_match('/data-countries="([^"]*)"/', $html, $match);
    $listed = json_decode(html_entity_decode($match[1]), true);
    $codes = array_column($listed, 'code');

    expect(array_slice($codes, 0, 2))->toBe(['DK', 'SE'])
        ->and(array_count_values($codes)['DK'])->toBe(1)
        ->and(count($codes))->toBe(count(PhoneCountries::all()))
        ->and($listed[1]['lastPreferred'])->toBeTrue();
});

it('names the countries in the app\'s locale', function () {
    app()->setLocale('da');

    $html = Blade::render('<x-sigta::phone-input value="+45 25 04 88 63" />');

    expect($html)->toContain('Danmark');
})->skip(! class_exists(Locale::class), 'Needs the intl extension.');

it('binds wire:model to the field\'s value, posts the value under its name, and forwards the rest onto the number', function () {
    $html = Blade::render('<x-sigta::phone-input label="Mobile" name="mobile" wire:model.live.blur="mobile" wire:key="mobile-1" aria-describedby="hint" required />');

    expect($html)
        ->toMatch('/data-sigta="phone-input"[^>]*x-modelable="value"[^>]*wire:model\.live\.blur="mobile"/')
        ->toMatch('/<input type="hidden"[^>]*x-bind:value="value"[^>]*name="mobile"/')
        ->toMatch('/data-sigta="field"[^>]*wire:key="mobile-1"/')
        ->toMatch('/<input\s+type="tel"[^>]*aria-describedby="hint"/')
        ->toMatch('/<input\s+type="tel"[^>]*required/')
        ->not->toMatch('/<input\s+type="tel"[^>]*wire:model/');
});

it('derives its id from the binding, so a morph keeps the same field', function () {
    $html = Blade::render('<x-sigta::phone-input label="Mobile" wire:model="form.mobile" />');

    expect($html)->toContain('id="sigta-phone-formmobile"');
});

it('marks an error on the number', function () {
    $html = Blade::render('<x-sigta::phone-input label="Mobile" id="mobile" error="Not valid." />');

    expect($html)
        ->toContain('border-danger')
        ->toContain('aria-invalid="true"')
        ->toContain('aria-describedby="mobile-error"')
        ->toContain('Not valid.');
});

it('renders an inline variant whose divider and chevron wait for hover or focus', function () {
    $html = Blade::render('<x-sigta::phone-input variant="inline" value="+45 25 04 88 63" />');

    expect($html)
        ->toContain('data-variant="inline"')
        ->toContain('border-transparent')
        ->toContain('group-hover/phone:border-line')
        ->toContain('group-hover/phone:opacity-100');
});

it('knows every country\'s calling code, and which country a shared code is shown as', function () {
    expect(PhoneCountries::all('en'))->toHaveKey('DK')
        ->and(PhoneCountries::all('en')['DK']['calling_code'])->toBe('+45')
        ->and(PhoneCountries::shownAs('+1'))->toBe('US')
        ->and(PhoneCountries::shownAs('+47'))->toBe('NO')
        ->and(PhoneCountries::shownAs('+46'))->toBe('SE')
        ->and(PhoneCountries::shownAs('+999'))->toBeNull();
});
