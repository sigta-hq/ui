<?php

use Illuminate\Support\Facades\Blade;

it('formats whole minutes as a compact h/m label', function () {
    expect(Blade::render('<x-sigta::duration :minutes="75" />'))
        ->toContain('data-sigta="duration"')
        ->toContain('tabular-nums')
        ->toContain('1h 15m');
});

it('drops the minutes on an exact hour', function () {
    $html = Blade::render('<x-sigta::duration :minutes="120" />');

    expect($html)
        ->toContain('2h')
        ->not->toContain('2h 0m');
});

it('renders sub-hour, zero, and null durations', function () {
    expect(Blade::render('<x-sigta::duration :minutes="45" />'))->toContain('45m');
    expect(Blade::render('<x-sigta::duration :minutes="0" />'))->toContain('0m');
    expect(Blade::render('<x-sigta::duration />'))->toContain('0m');   // null → 0m
});

it('renders an HH:MM clock in clock format', function () {
    expect(Blade::render('<x-sigta::duration :minutes="90" format="clock" />'))->toContain('01:30');
    expect(Blade::render('<x-sigta::duration :minutes="605" format="clock" />'))->toContain('10:05');
});

it('clamps negative input to zero', function () {
    expect(Blade::render('<x-sigta::duration :minutes="-30" />'))->toContain('0m');
});

it('forwards attributes and merges the base class', function () {
    expect(Blade::render('<x-sigta::duration :minutes="60" data-test="dur" class="text-graphite" />'))
        ->toContain('data-test="dur"')
        ->toContain('text-graphite')
        ->toContain('tabular-nums');
});

it('leaks no blade comment markers before the value', function () {
    // Regression: a nested {{-- --}} in the docblock closed the outer comment
    // early and leaked its trailing --}} in front of every rendered duration.
    $html = Blade::render('<x-sigta::duration :minutes="60" />');

    expect($html)
        ->not->toContain('}}')
        ->not->toContain('--}}')
        ->toContain('>1h</span>'); // value sits flush inside the span, nothing before it
});
