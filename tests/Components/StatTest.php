<?php

use Illuminate\Support\Facades\Blade;

it('renders label, value and delta', function () {
    $html = Blade::render('<x-sigta::stat label="Active accounts" value="2,418" delta="6.2% vs last 30d" />');

    expect($html)
        ->toContain('data-sigta="stat"')
        ->toContain('Active accounts')
        ->toContain('2,418')
        ->toContain('6.2% vs last 30d')
        ->toContain('uppercase')
        ->toContain('font-head')
        ->toContain('tabular-nums')
        ->toContain('rounded-panel');
});

it('renders an upward trend in success ink with the up marker', function () {
    $html = Blade::render('<x-sigta::stat label="Net retention" value="112%" delta="1.4 pts" trend="up" />');

    expect($html)
        ->toContain('text-success')
        ->toContain('▲');
});

it('renders a downward trend in danger ink with the down marker', function () {
    $html = Blade::render('<x-sigta::stat label="Time to value" value="3.1d" delta="0.4d slower" trend="down" />');

    expect($html)
        ->toContain('text-danger')
        ->toContain('▼');
});

it('renders a neutral delta without a marker', function () {
    $html = Blade::render('<x-sigta::stat label="Seats" value="14" delta="unchanged" />');

    expect($html)
        ->toContain('text-graphite')
        ->not->toContain('▲')
        ->not->toContain('▼');
});

it('renders a sparkline slot below the value', function () {
    $html = Blade::render(<<<'BLADE'
        <x-sigta::stat label="Monthly recurring" value="$48.2k">
            <svg data-spark aria-hidden="true"></svg>
        </x-sigta::stat>
    BLADE);

    expect($html)->toContain('data-spark');
    expect(strpos($html, '$48.2k'))->toBeLessThan(strpos($html, 'data-spark'));
});

it('omits the slot wrapper when empty', function () {
    expect(Blade::render('<x-sigta::stat label="Seats" value="14" />'))
        ->not->toContain('mt-2');
});

it('forwards arbitrary attributes', function () {
    $html = Blade::render('<x-sigta::stat label="Seats" value="14" wire:key="stat-1" data-test="stat" />');

    expect($html)
        ->toContain('wire:key="stat-1"')
        ->toContain('data-test="stat"');
});

it('renders the panel variant as a boxed figure by default', function () {
    $html = Blade::render('<x-sigta::stat label="Seats" value="14" />');

    expect($html)
        ->toContain('border border-line')
        ->toContain('rounded-panel')
        ->toContain('bg-surface');
});

it('renders the inline variant as a borderless figure', function () {
    $html = Blade::render('<x-sigta::stat variant="inline" value="14 år" label="bag tastaturet" />');

    expect($html)
        ->toContain('data-sigta="stat"')
        ->toContain('14 år')
        ->toContain('bag tastaturet')
        ->toContain('font-head')
        ->toContain('text-[1.5rem]')
        ->toContain('tabular-nums')
        ->toContain('uppercase')
        ->not->toContain('border')
        ->not->toContain('rounded-panel');
});

it('renders the inline value above the label', function () {
    $html = Blade::render('<x-sigta::stat variant="inline" value="14 år" label="bag tastaturet" />');

    expect(strpos($html, '14 år'))->toBeLessThan(strpos($html, 'bag tastaturet'));
});

it('renders an inverse-tone boxed stat on a dark surface', function () {
    $html = Blade::render('<x-sigta::stat tone="inverse" label="Logged this week" value="12h 30m" />');

    expect($html)
        ->toContain('data-tone="inverse"')
        ->toContain('bg-inverse')
        ->toContain('border border-inverse-line')
        ->toContain('text-inverse-fg')          // value ink
        ->toContain('text-inverse-fg-muted')    // label ink
        ->not->toContain('text-graphite');
});

it('inverts an inline stat without drawing its own surface', function () {
    $html = Blade::render('<x-sigta::stat variant="inline" tone="inverse" label="Billable" value="kr. 4.200" />');

    expect($html)
        ->toContain('data-tone="inverse"')
        ->toContain('text-inverse-fg')
        ->toContain('text-inverse-fg-muted')
        ->not->toContain('border')
        ->not->toContain('bg-inverse');
});

it('keeps trend colors semantic under the inverse tone', function () {
    $html = Blade::render('<x-sigta::stat tone="inverse" label="Net" value="112%" delta="1.4 pts" trend="up" />');

    expect($html)
        ->toContain('text-success')
        ->toContain('▲');
});
