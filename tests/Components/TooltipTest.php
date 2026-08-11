<?php

use Illuminate\Support\Facades\Blade;

it('renders a tooltip bubble wired to the trigger via aria-describedby', function () {
    $html = Blade::render('<x-sigta::tooltip text="Copy to clipboard"><x-sigta::button>Copy</x-sigta::button></x-sigta::tooltip>');

    expect($html)
        ->toContain('data-sigta="tooltip"')
        ->toContain('role="tooltip"')
        ->toContain('Copy to clipboard')
        ->toContain('aria-describedby="');

    // aria-describedby matches the bubble id.
    preg_match('/aria-describedby="([^"]+)"/', $html, $m);
    expect($html)->toContain('id="'.$m[1].'"');
});

it('shows on hover/focus with a 150ms delay and hides on leave/escape', function () {
    $html = Blade::render('<x-sigta::tooltip text="Hint">Trigger</x-sigta::tooltip>');

    expect($html)
        ->toContain('x-data="{ show: false, timer: null }"')
        ->toContain('setTimeout(() => show = true, 150)')
        ->toContain('@mouseleave')
        ->toContain('@focusin')
        ->toContain('@focusout')
        ->toContain('@keydown.escape.window');
});

it('positions the bubble per the position prop', function () {
    expect(Blade::render('<x-sigta::tooltip text="Up">T</x-sigta::tooltip>'))
        ->toContain('bottom-full');

    expect(Blade::render('<x-sigta::tooltip text="Down" position="bottom">T</x-sigta::tooltip>'))
        ->toContain('top-full');

    expect(Blade::render('<x-sigta::tooltip text="Side" position="right">T</x-sigta::tooltip>'))
        ->toContain('left-full');
});

it('forwards attributes onto the wrapper', function () {
    $html = Blade::render('<x-sigta::tooltip text="Hint" data-test="tip" wire:key="tip-1">T</x-sigta::tooltip>');

    expect($html)
        ->toContain('data-test="tip"')
        ->toContain('wire:key="tip-1"');
});
