<?php

use Illuminate\Support\Facades\Blade;

it('renders a self-ticking clock seeded from now by default', function () {
    $html = Blade::render('<x-sigta::elapsed-clock />');

    expect($html)
        ->toContain('data-sigta="elapsed-clock"')
        ->toContain('x-text="label"')
        ->toContain('Date.now()')
        ->toContain('setInterval')
        ->toContain('>00:00:00</span>');   // SSR fallback shown before Alpine boots

    expect(contractCss('elapsed-clock'))->toContain('@apply tabular-nums;');
});

it('seeds the start instant from an ISO :since string', function () {
    $html = Blade::render('<x-sigta::elapsed-clock since="2026-08-25T09:00:00+00:00" />');

    expect($html)
        ->toContain('new Date(')
        ->toContain('2026-08-25T09:00:00');
});

it('accepts a DateTimeInterface for :since', function () {
    $html = Blade::render('<x-sigta::elapsed-clock :since="$since" />', [
        'since' => new DateTimeImmutable('2026-01-02T03:04:05+00:00'),
    ]);

    expect($html)->toContain('2026-01-02T03:04:05');
});

it('forwards classes and attributes onto the clock', function () {
    $html = Blade::render('<x-sigta::elapsed-clock class="text-lg" data-test="clock" />');

    expect($html)
        ->toContain('class="text-lg"')
        ->toContain('data-test="clock"');

    // The base look lives in the contract, so a consumer class adds to it.
    expect(contractCss('elapsed-clock'))->toContain('[data-sigta="elapsed-clock"]')->toContain('tabular-nums');
});

it('clears its interval on destroy', function () {
    expect(Blade::render('<x-sigta::elapsed-clock />'))
        ->toContain('clearInterval');
});
