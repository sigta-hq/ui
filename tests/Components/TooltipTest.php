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
        ->toContain('data-position="top"')
        ->toMatch('/data-scope="tooltip"\s+data-part="bubble"/');

    expect(Blade::render('<x-sigta::tooltip text="Down" position="bottom">T</x-sigta::tooltip>'))
        ->toContain('data-position="bottom"');

    expect(Blade::render('<x-sigta::tooltip text="Side" position="right">T</x-sigta::tooltip>'))
        ->toContain('data-position="right"');

    // Above by default (and for unknown positions), below, or beside.
    expect(contractCss('tooltip'))
        ->toContain(':not(:is([data-position="bottom"], [data-position="left"], [data-position="right"]))')
        ->toContain('@apply bottom-full left-1/2 -translate-x-1/2 mb-2;')
        ->toContain('@apply top-full left-1/2 -translate-x-1/2 mt-2;')
        ->toContain('@apply left-full top-1/2 -translate-y-1/2 ml-2;');
});

it('hides the bubble until Alpine shows it', function () {
    expect(Blade::render('<x-sigta::tooltip text="Hint">T</x-sigta::tooltip>'))
        ->toMatch('/role="tooltip"[^>]*x-show="show"[^>]*x-cloak/');
});

it('forwards attributes onto the wrapper', function () {
    $html = Blade::render('<x-sigta::tooltip text="Hint" data-test="tip" wire:key="tip-1">T</x-sigta::tooltip>');

    expect($html)
        ->toContain('data-test="tip"')
        ->toContain('wire:key="tip-1"');
});
