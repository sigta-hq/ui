<?php

use Illuminate\Support\Facades\Blade;

it('renders the shell grid with sidebar, bar, and main', function () {
    $html = Blade::render(<<<'BLADE'
        <x-sigta::shell>
            <x-slot:sidebar>
                <x-sigta::shell.sidebar>
                    <x-slot:brand>Plumb</x-slot:brand>
                    <x-sigta::shell.link href="#" current>Overview</x-sigta::shell.link>
                    <x-sigta::shell.link href="#">Reports</x-sigta::shell.link>
                </x-sigta::shell.sidebar>
            </x-slot:sidebar>
            <x-sigta::shell.main>
                <x-sigta::shell.bar heading="Overview" sub="Q2 2026" />
            </x-sigta::shell.main>
        </x-sigta::shell>
        BLADE);

    expect($html)
        ->toContain('data-sigta="shell"')
        ->toContain('md:grid-cols-[220px_1fr]')
        ->toContain('data-sigta="shell.sidebar"')
        ->toContain('data-sigta="shell.main"')
        ->toContain('data-sigta="shell.bar"')
        ->toContain('Plumb')
        ->toContain('Overview')
        ->toContain('Reports');
});

it('collapses the sidebar nav behind a burger toggle on mobile', function () {
    $html = Blade::render(<<<'BLADE'
        <x-sigta::shell.sidebar>
            <x-slot:brand>Plumb</x-slot:brand>
            <x-sigta::shell.link href="#">Overview</x-sigta::shell.link>
        </x-sigta::shell.sidebar>
        BLADE);

    expect($html)
        ->toContain('x-data="{ open: false }"')
        ->toContain('aria-expanded="false"')
        ->toContain('md:hidden')            // burger button is hidden on desktop
        ->toContain('@click="open = ! open"') // burger toggles the nav
        ->toContain('md:!flex')             // panel is forced visible on desktop
        ->toContain('aria-controls="');

    // The burger's aria-controls points at the id of the collapsible nav panel.
    preg_match('/aria-controls="([^"]+)"/', $html, $m);
    expect($m[1] ?? null)->not->toBeNull();
    expect($html)->toContain('id="'.$m[1].'"');
});

it('keeps the brand visible while collapsed and swaps the toggle icon', function () {
    $html = Blade::render(<<<'BLADE'
        <x-sigta::shell.sidebar>
            <x-slot:brand>Acme</x-slot:brand>
            <x-sigta::shell.link href="#">Body</x-sigta::shell.link>
        </x-sigta::shell.sidebar>
        BLADE);

    expect($html)
        ->toContain('Acme')            // brand stays in the bar even when collapsed
        ->toContain('x-show="! open"') // menu (burger) glyph when closed
        ->toContain('x-show="open"');  // close (x) glyph when open
});

it('forwards attributes onto the sidebar root', function () {
    $html = Blade::render('<x-sigta::shell.sidebar data-test="side" wire:key="side">Nav</x-sigta::shell.sidebar>');

    expect($html)
        ->toContain('data-sigta="shell.sidebar"')
        ->toContain('data-test="side"')
        ->toContain('wire:key="side"');
});

it('marks the current shell link and forwards attributes', function () {
    $plain = Blade::render('<x-sigta::shell.link href="/x">X</x-sigta::shell.link>');
    $current = Blade::render('<x-sigta::shell.link href="/x" current wire:navigate>X</x-sigta::shell.link>');

    expect($plain)
        ->toContain('data-sigta="shell.link"')
        ->toContain('href="/x"')
        ->not->toContain('aria-current');

    expect($current)
        ->toContain('aria-current="page"')
        ->toContain('bg-ink')
        ->toContain('wire:navigate');
});

it('renders an actions slot beside the burger', function () {
    $html = Blade::render(<<<'BLADE'
        <x-sigta::shell.sidebar>
            <x-slot:brand>Plumb</x-slot:brand>
            <x-slot:actions><button type="button" data-test="quick">Quick</button></x-slot:actions>
            <x-sigta::shell.link href="#">Overview</x-sigta::shell.link>
        </x-sigta::shell.sidebar>
        BLADE);

    expect($html)->toContain('data-test="quick"');

    // Actions sit in the header row, before the burger, so the pair reads as one
    // cluster at the far end rather than the action drifting toward the brand.
    expect(strpos($html, 'data-test="quick"'))
        ->toBeGreaterThan(strpos($html, 'Plumb'))
        ->toBeLessThan(strpos($html, 'Toggle navigation'));
});

it('keeps actions out of the collapsing nav panel', function () {
    $html = Blade::render(<<<'BLADE'
        <x-sigta::shell.sidebar>
            <x-slot:brand>Plumb</x-slot:brand>
            <x-slot:actions><button type="button" data-test="quick">Quick</button></x-slot:actions>
            <x-sigta::shell.link href="#">Overview</x-sigta::shell.link>
        </x-sigta::shell.sidebar>
        BLADE);

    // The panel is what x-show hides; an action placed after it would disappear
    // with the nav, which is the whole point of having this slot.
    expect(strpos($html, 'data-test="quick"'))->toBeLessThan(strpos($html, 'sigta-shell-sidebar-', 10));
});

it('renders the header row without an actions slot', function () {
    $html = Blade::render(<<<'BLADE'
        <x-sigta::shell.sidebar>
            <x-slot:brand>Plumb</x-slot:brand>
            <x-sigta::shell.link href="#">Overview</x-sigta::shell.link>
        </x-sigta::shell.sidebar>
        BLADE);

    expect($html)
        ->toContain('Plumb')
        ->toContain('Toggle navigation');
});
