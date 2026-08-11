<?php

use Illuminate\Support\Facades\Blade;

it('renders a breadcrumb nav with an ordered list', function () {
    $html = Blade::render(<<<'BLADE'
        <x-sigta::breadcrumbs>
            <x-sigta::breadcrumbs.item href="/">Home</x-sigta::breadcrumbs.item>
            <x-sigta::breadcrumbs.item href="/projects">Projects</x-sigta::breadcrumbs.item>
            <x-sigta::breadcrumbs.item>Marginalia v3</x-sigta::breadcrumbs.item>
        </x-sigta::breadcrumbs>
        BLADE);

    expect($html)
        ->toContain('data-sigta="breadcrumbs"')
        ->toContain('<nav')
        ->toContain('aria-label="Breadcrumb"')
        ->toContain('<ol')
        ->toContain('Home')
        ->toContain('Marginalia v3');
});

it('marks the current item and renders it without a link', function () {
    $html = Blade::render(<<<'BLADE'
        <x-sigta::breadcrumbs>
            <x-sigta::breadcrumbs.item href="/">Home</x-sigta::breadcrumbs.item>
            <x-sigta::breadcrumbs.item>Settings</x-sigta::breadcrumbs.item>
        </x-sigta::breadcrumbs>
        BLADE);

    expect($html)
        ->toContain('aria-current="page"')
        ->toContain('href="/"');

    // The current item is a span, not an anchor.
    expect(substr_count($html, '<a '))->toBe(1);
});

it('treats an explicit current prop as the current page even with an href', function () {
    $html = Blade::render('<x-sigta::breadcrumbs><x-sigta::breadcrumbs.item href="/here" current>Here</x-sigta::breadcrumbs.item></x-sigta::breadcrumbs>');

    expect($html)
        ->toContain('aria-current="page"')
        ->not->toContain('<a ');
});

it('forwards attributes on the wrapper and items', function () {
    $html = Blade::render(<<<'BLADE'
        <x-sigta::breadcrumbs data-test="crumbs">
            <x-sigta::breadcrumbs.item href="/" wire:navigate>Home</x-sigta::breadcrumbs.item>
        </x-sigta::breadcrumbs>
        BLADE);

    expect($html)
        ->toContain('data-test="crumbs"')
        ->toContain('wire:navigate');
});
