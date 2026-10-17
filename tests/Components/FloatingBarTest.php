<?php

use Illuminate\Support\Facades\Blade;

it('renders hidden until shown', function () {
    $html = Blade::render('<x-sigta::floating-bar>Timer</x-sigta::floating-bar>');

    expect($html)
        ->toContain('data-sigta="floating-bar"')
        ->toContain('data-show="false"')
        ->toContain('x-data="sigtaFloatingBar"')
        ->toContain('style="display: none"')
        ->toContain('Timer');
});

it('renders visible when shown, with no inline hiding to animate from', function () {
    $html = Blade::render('<x-sigta::floating-bar :show="true">Timer</x-sigta::floating-bar>');

    expect($html)
        ->toContain('data-show="true"')
        ->not->toContain('style="display: none"');
});

it('slides in and out, and keeps a morph off the animated element', function () {
    $html = Blade::render('<x-sigta::floating-bar>Timer</x-sigta::floating-bar>');

    expect($html)
        ->toContain('x-show="visible"')
        ->toContain('wire:ignore.self')
        ->toContain('x-transition:enter="sigta-floating-bar-enter"')
        ->toContain('x-transition:enter-start="sigta-floating-bar-away"')
        ->toContain('x-transition:leave="sigta-floating-bar-leave"')
        ->toContain('x-transition:leave-end="sigta-floating-bar-away"')
        ->toContain('data-scope="floating-bar"')
        ->toContain('data-part="bar"');

    expect(contractCss('floating-bar'))
        ->toMatch('/\[data-sigta="floating-bar"\] \{\s*@apply pointer-events-none fixed inset-x-0 bottom-0/')
        ->toContain('&.sigta-floating-bar-enter { @apply transition ease-out duration-300; }')
        ->toContain('&.sigta-floating-bar-leave { @apply transition ease-in duration-200; }')
        ->toContain('&.sigta-floating-bar-away { @apply opacity-0 translate-y-6 scale-95; }');
});

it('names the bar as a region when given a label', function () {
    $plain = Blade::render('<x-sigta::floating-bar>Timer</x-sigta::floating-bar>');
    $labelled = Blade::render('<x-sigta::floating-bar label="Running timer">Timer</x-sigta::floating-bar>');

    expect($plain)->not->toContain('role="region"')
        ->and($labelled)->toContain('role="region"')
        ->and($labelled)->toContain('aria-label="Running timer"');
});

it('forwards attributes onto the root', function () {
    $html = Blade::render('<x-sigta::floating-bar data-test="bar" wire:key="bar">Timer</x-sigta::floating-bar>');

    expect($html)
        ->toContain('data-test="bar"')
        ->toContain('wire:key="bar"');
});
