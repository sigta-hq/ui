<?php

use Illuminate\Support\Facades\Blade;

// A theme restyles a variant by its data attributes, inside its own
// [data-sigta-theme] scope, so every variant and size has to say what it is in
// the markup, not only through utility classes.

it('marks a button with its variant and size', function () {
    expect(Blade::render('<x-sigta::button variant="cta" size="sm">Go</x-sigta::button>'))
        ->toContain('data-variant="cta"')
        ->toContain('data-size="sm"');

    expect(Blade::render('<x-sigta::button href="/x">Go</x-sigta::button>'))
        ->toContain('data-variant="secondary"')
        ->toContain('data-size="base"');
});

it('marks badges, pills, callouts and stats with their variant', function (string $tag, string $expected) {
    expect(Blade::render($tag))->toContain($expected);
})->with([
    'badge' => ['<x-sigta::badge variant="accent">New</x-sigta::badge>', 'data-variant="accent"'],
    'pill' => ['<x-sigta::pill variant="success">Live</x-sigta::pill>', 'data-variant="success"'],
    'callout' => ['<x-sigta::callout variant="danger">Failed</x-sigta::callout>', 'data-variant="danger"'],
    'stat' => ['<x-sigta::stat variant="inline" value="26" />', 'data-variant="inline"'],
]);

it('marks headings with their size and text with its variant', function () {
    expect(Blade::render('<x-sigta::heading level="1" size="display">Hi</x-sigta::heading>'))
        ->toContain('data-sigta="heading"')
        ->toContain('data-size="display"');

    expect(Blade::render('<x-sigta::heading level="3">Hi</x-sigta::heading>'))
        ->toContain('data-size="base"');

    expect(Blade::render('<x-sigta::text variant="lede">Hi</x-sigta::text>'))
        ->toContain('data-sigta="text"')
        ->toContain('data-variant="lede"');
});

it('marks only a hoverable card as such', function () {
    expect(Blade::render('<x-sigta::card>Body</x-sigta::card>'))->toContain('data-hover');
    expect(Blade::render('<x-sigta::card :hover="false">Body</x-sigta::card>'))->not->toContain('data-hover');
});
