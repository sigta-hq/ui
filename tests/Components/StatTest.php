<?php

use Illuminate\Support\Facades\Blade;

it('renders label, value and delta', function () {
    $html = Blade::render('<x-sigta::stat label="Active accounts" value="2,418" delta="6.2% vs last 30d" />');

    expect($html)
        ->toContain('data-sigta="stat"')
        ->toContain('data-variant="panel"')
        ->toContain('<span data-scope="stat" data-part="label">Active accounts</span>')
        ->toContain('<span data-scope="stat" data-part="value">2,418</span>')
        ->toContain('6.2% vs last 30d');

    expect(contractCss('stat'))
        ->toMatch('/\[data-scope="stat"\]\[data-part="label"\] \{\s*@apply font-mono[^;]*uppercase/')
        ->toMatch('/\[data-scope="stat"\]\[data-part="value"\] \{\s*@apply font-head[^;]*tabular-nums/')
        ->toMatch('/&:not\(\[data-variant="inline"\]\) \{\s*@apply rounded-panel/');
});

it('renders an upward trend in success ink with the up marker', function () {
    $html = Blade::render('<x-sigta::stat label="Net retention" value="112%" delta="1.4 pts" trend="up" />');

    expect($html)
        ->toMatch('/data-part="delta"\s+data-trend="up"/')
        ->toContain('▲');
    expect(contractCss('stat'))->toContain('&[data-trend="up"] { @apply text-success; }');
});

it('renders a downward trend in danger ink with the down marker', function () {
    $html = Blade::render('<x-sigta::stat label="Time to value" value="3.1d" delta="0.4d slower" trend="down" />');

    expect($html)
        ->toMatch('/data-part="delta"\s+data-trend="down"/')
        ->toContain('▼');
    expect(contractCss('stat'))->toContain('&[data-trend="down"] { @apply text-danger; }');
});

it('renders a neutral delta without a marker', function () {
    $html = Blade::render('<x-sigta::stat label="Seats" value="14" delta="unchanged" />');

    expect($html)
        ->toContain('data-part="delta"')
        ->not->toContain('data-trend')
        ->not->toContain('▲')
        ->not->toContain('▼');
    expect(contractCss('stat'))->toMatch('/&:not\(:is\(\[data-trend="up"\], \[data-trend="down"\]\)\) \{\s*@apply text-graphite;/');
});

it('renders a sparkline slot below the value', function () {
    $html = Blade::render(<<<'BLADE'
        <x-sigta::stat label="Monthly recurring" value="$48.2k">
            <svg data-spark aria-hidden="true"></svg>
        </x-sigta::stat>
    BLADE);

    expect($html)->toContain('data-spark')->toContain('data-part="content"');
    expect(strpos($html, '$48.2k'))->toBeLessThan(strpos($html, 'data-spark'));
});

it('omits the slot wrapper when empty', function () {
    expect(Blade::render('<x-sigta::stat label="Seats" value="14" />'))
        ->not->toContain('data-part="content"');
});

it('forwards arbitrary attributes', function () {
    $html = Blade::render('<x-sigta::stat label="Seats" value="14" wire:key="stat-1" data-test="stat" />');

    expect($html)
        ->toContain('wire:key="stat-1"')
        ->toContain('data-test="stat"');
});

it('renders the panel variant as a boxed figure by default', function () {
    $html = Blade::render('<x-sigta::stat label="Seats" value="14" />');

    expect($html)->toContain('data-variant="panel"')->toContain('data-tone="default"');
    expect(contractCss('stat'))->toMatch('/&:not\(\[data-variant="inline"\]\) \{\s*@apply rounded-panel p-\[18px\] bg-surface border border-line;/');
});

it('renders the inline variant as a borderless figure', function () {
    $html = Blade::render('<x-sigta::stat variant="inline" value="14 år" label="bag tastaturet" />');

    expect($html)
        ->toContain('data-sigta="stat"')
        ->toContain('data-variant="inline"')
        ->toContain('14 år')
        ->toContain('bag tastaturet');

    // Only the panel variant draws a surface; inline sets type sizes alone.
    $css = contractCss('stat');
    expect($css)
        ->toContain('[data-sigta="stat"][data-variant="inline"] > & { @apply text-[1.5rem]; }')
        ->toContain('[data-sigta="stat"][data-variant="inline"] > & { @apply text-[0.72rem] mt-[0.2rem]; }')
        ->not->toMatch('/\[data-sigta="stat"\]\[data-variant="inline"\][^{]*\{[^}]*border/');
});

it('renders the inline value above the label', function () {
    $html = Blade::render('<x-sigta::stat variant="inline" value="14 år" label="bag tastaturet" />');

    expect(strpos($html, '14 år'))->toBeLessThan(strpos($html, 'bag tastaturet'));
});

it('renders an inverse-tone boxed stat on a dark surface', function () {
    $html = Blade::render('<x-sigta::stat tone="inverse" label="Logged this week" value="12h 30m" />');

    expect($html)->toContain('data-tone="inverse"')->toContain('data-variant="panel"');

    expect(contractCss('stat'))
        ->toContain('&[data-tone="inverse"] { @apply bg-inverse border-inverse-line; }')
        ->toContain('[data-sigta="stat"][data-tone="inverse"] > & { @apply text-inverse-fg; }')          // value ink
        ->toContain('[data-sigta="stat"][data-tone="inverse"] > & { @apply text-inverse-fg-muted; }');   // label ink
});

it('inverts an inline stat without drawing its own surface', function () {
    $html = Blade::render('<x-sigta::stat variant="inline" tone="inverse" label="Billable" value="kr. 4.200" />');

    expect($html)
        ->toContain('data-tone="inverse"')
        ->toContain('data-variant="inline"');

    // The inverse surface hangs off the panel variant only.
    expect(contractCss('stat'))->toMatch('/&:not\(\[data-variant="inline"\]\) \{[^}]*&\[data-tone="inverse"\] \{ @apply bg-inverse/s');
});

it('keeps trend colors semantic under the inverse tone', function () {
    $html = Blade::render('<x-sigta::stat tone="inverse" label="Net" value="112%" delta="1.4 pts" trend="up" />');

    expect($html)
        ->toContain('data-trend="up"')
        ->toContain('▲');

    // Only the trendless delta re-inks for the inverse tone.
    expect(contractCss('stat'))->toMatch('/&:not\(:is\(\[data-trend="up"\], \[data-trend="down"\]\)\) \{\s*@apply text-graphite;\s*\[data-sigta="stat"\]\[data-tone="inverse"\] > & \{ @apply text-inverse-fg-muted; \}/');
});
