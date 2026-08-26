<?php

use Illuminate\Support\Facades\Blade;

it('renders a self-ticking clock seeded from now by default', function () {
    $html = Blade::render('<x-sigta::elapsed-clock />');

    expect($html)
        ->toContain('data-sigta="elapsed-clock"')
        ->toContain('tabular-nums')
        ->toContain('x-text="label"')
        ->toContain('Date.now()')
        ->toContain('setInterval')
        ->toContain('>00:00:00</span>');   // SSR fallback shown before Alpine boots
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

it('forwards classes and attributes, merging the base class', function () {
    $html = Blade::render('<x-sigta::elapsed-clock class="text-lg" data-test="clock" />');

    expect($html)
        ->toContain('text-lg')
        ->toContain('tabular-nums')       // merged, not replaced
        ->toContain('data-test="clock"');
});

it('clears its interval on destroy', function () {
    expect(Blade::render('<x-sigta::elapsed-clock />'))
        ->toContain('clearInterval');
});
