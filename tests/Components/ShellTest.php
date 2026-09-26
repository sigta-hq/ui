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
        ->toContain('x-data="sigtaShellSidebar"')
        ->toContain('aria-expanded="false"')
        ->toContain('md:hidden')             // burger button is hidden on desktop
        ->toContain('x-on:click="toggle()"') // burger toggles the nav
        ->toContain('md:!flex')              // panel is forced visible on desktop
        ->toContain('aria-controls="');

    // The burger's aria-controls points at the id of the collapsible nav panel.
    preg_match('/aria-controls="([^"]+)"/', $html, $m);
    expect($m[1] ?? null)->not->toBeNull();
    expect($html)->toContain('id="'.$m[1].'"');
});

it('keeps the brand visible while the nav is closed', function () {
    $html = Blade::render(<<<'BLADE'
        <x-sigta::shell.sidebar>
            <x-slot:brand>Acme</x-slot:brand>
            <x-sigta::shell.link href="#">Body</x-sigta::shell.link>
        </x-sigta::shell.sidebar>
        BLADE);

    expect($html)->toContain('Acme');
});

it('slides the nav in as a drawer over the page on mobile', function () {
    $html = Blade::render(<<<'BLADE'
        <x-sigta::shell.sidebar>
            <x-slot:brand>Acme</x-slot:brand>
            <x-sigta::shell.link href="#">Body</x-sigta::shell.link>
        </x-sigta::shell.sidebar>
        BLADE);

    expect($html)
        // Off the flow and over the page, from the rail's own edge, full height.
        ->toContain('max-md:fixed')
        ->toContain('max-md:inset-y-0')
        ->toContain('max-md:start-0')
        ->toContain('max-md:z-50')
        // It slides rather than appearing, and from the correct edge either way.
        ->toContain('max-md:ltr:-translate-x-full')
        ->toContain('max-md:rtl:translate-x-full');
});

it('dims the page behind the drawer and closes when the scrim is tapped', function () {
    $html = Blade::render(<<<'BLADE'
        <x-sigta::shell.sidebar>
            <x-slot:brand>Acme</x-slot:brand>
            <x-sigta::shell.link href="#">Body</x-sigta::shell.link>
        </x-sigta::shell.sidebar>
        BLADE);

    expect($html)
        ->toContain('bg-ink/45')
        ->toContain('x-on:click="close()"')
        // The scrim sits under the drawer and over the page.
        ->toContain('z-40')
        ->toContain('aria-hidden="true"');
});

it('treats the open drawer as a modal dialog but never the rail', function () {
    $html = Blade::render(<<<'BLADE'
        <x-sigta::shell.sidebar>
            <x-slot:brand>Acme</x-slot:brand>
            <x-sigta::shell.link href="#">Body</x-sigta::shell.link>
        </x-sigta::shell.sidebar>
        BLADE);

    expect($html)
        // Both are bound to `drawer`, so a desktop rail is never trapped, never
        // scroll-locked, and never announced as a dialog.
        ->toContain('x-trap.noscroll="open && drawer"')
        ->toContain('x-bind:role="drawer ? \'dialog\' : null"')
        ->toContain('x-bind:aria-modal="drawer && open ? \'true\' : null"')
        ->toContain('aria-label="App navigation"');
});

it('gives the drawer its own way out, since the burger sits under the scrim', function () {
    $html = Blade::render(<<<'BLADE'
        <x-sigta::shell.sidebar>
            <x-slot:brand>Acme</x-slot:brand>
            <x-sigta::shell.link href="#">Body</x-sigta::shell.link>
        </x-sigta::shell.sidebar>
        BLADE);

    expect($html)->toContain('aria-label="Close navigation"');

    // Inside the panel, not the header row, so it rides above the scrim with it.
    preg_match('/id="(sigta-shell-sidebar-[^"]+)"/', $html, $m);
    expect(strpos($html, 'Close navigation'))->toBeGreaterThan(strpos($html, 'id="'.$m[1].'"'));
});

it('names the drawer with a consumer aria-label when one is given', function () {
    $html = Blade::render('<x-sigta::shell.sidebar aria-label="Main menu">Nav</x-sigta::shell.sidebar>');

    expect(substr_count($html, 'aria-label="Main menu"'))->toBe(2);
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

it('renders an optional header row across both columns', function () {
    $html = Blade::render(<<<'BLADE'
        <x-sigta::shell>
            <x-slot:header>
                <x-sigta::shell.header data-test="top">
                    <x-slot:brand>Plumb</x-slot:brand>
                    <x-slot:actions><button type="button">Account</button></x-slot:actions>
                </x-sigta::shell.header>
            </x-slot:header>
            <x-slot:sidebar>
                <x-sigta::shell.sidebar>
                    <x-sigta::shell.link href="#">Overview</x-sigta::shell.link>
                </x-sigta::shell.sidebar>
            </x-slot:sidebar>
            <x-sigta::shell.main>Body</x-sigta::shell.main>
        </x-sigta::shell>
        BLADE);

    expect($html)
        ->toContain('data-header')
        ->toContain('grid-rows-[auto_auto_1fr]')
        ->toContain('md:grid-rows-[auto_1fr]')
        ->toContain('data-sigta="shell.header"')
        ->toContain('md:col-span-2')
        ->toContain('data-test="top"')
        ->toContain('Plumb')
        ->toContain('Account');

    // The header comes first, so it sits above both columns.
    expect(strpos($html, 'shell.header'))->toBeLessThan(strpos($html, 'shell.sidebar'));
});

it('leaves a shell without a header exactly as before', function () {
    $html = Blade::render('<x-sigta::shell><x-sigta::shell.main>Body</x-sigta::shell.main></x-sigta::shell>');

    expect($html)
        ->not->toContain('data-header')
        ->not->toContain('grid-rows-[auto_auto_1fr]');
});

it('drops the sidebar brand row when the shell has a header', function () {
    $html = Blade::render('<x-sigta::shell.sidebar>Nav</x-sigta::shell.sidebar>');

    expect($html)
        ->toContain('group-data-[header]/shell:hidden')
        ->toContain('max-md:group-data-[header]/shell:p-0')
        // The header's burger opens this drawer through a window event.
        ->toContain('x-on:sigta-shell-toggle.window="toggle()"');
});

it('puts the drawer burger in the header on a phone', function () {
    $html = Blade::render('<x-sigta::shell.header><x-slot:brand>Plumb</x-slot:brand></x-sigta::shell.header>');

    expect($html)
        ->toContain('aria-label="Toggle navigation"')
        ->toContain('md:hidden')
        ->toContain('$dispatch(\'sigta-shell-toggle\')')
        ->toContain('x-on:sigta-shell-nav.window="navOpen = $event.detail.open"')
        ->toContain('aria-expanded="false"');
});

it('keeps the main column clear of a visible floating bar', function () {
    $html = Blade::render('<x-sigta::shell.main>Body</x-sigta::shell.main>');

    expect($html)->toContain('pb-[calc(1.5rem+var(--sigta-floating-space,0px))]');
});
