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
        ->toContain('data-sigta="shell.sidebar"')
        ->toContain('data-sigta="shell.main"')
        ->toContain('data-sigta="shell.bar"')
        ->toContain('Plumb')
        ->toContain('Overview')
        ->toContain('Reports')
        ->toContain('<h3 data-scope="shell.bar" data-part="heading">Overview</h3>')
        ->toContain('<span data-scope="shell.bar" data-part="sub">Q2 2026</span>')
        ->not->toContain('class=');

    // The look lives in the contract: a 220px rail beside the main column.
    expect(contractCss('shell'))
        ->toContain('@apply grid md:grid-cols-[220px_1fr] bg-surface-raised text-ink;')
        ->toContain('&:not([data-full]) { @apply min-h-[440px] border border-line rounded-frame overflow-hidden; }');
});

it('marks a full-viewport shell', function () {
    expect(Blade::render('<x-sigta::shell full>Body</x-sigta::shell>'))->toContain('data-full');
    expect(Blade::render('<x-sigta::shell>Body</x-sigta::shell>'))->not->toContain('data-full');

    expect(contractCss('shell'))->toContain('&[data-full] { @apply min-h-screen rounded-none border-0; }');
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
        ->toContain('data-scope="shell.sidebar"'."\n".'                data-part="toggle"')
        ->toContain('x-on:click="toggle()"') // burger toggles the nav
        ->toContain('data-part="nav"')
        ->toContain('aria-controls="');

    expect(contractCss('shell'))
        // The burger is hidden on desktop; the panel is forced visible there.
        ->toMatch('/\[data-part="toggle"\] \{\s*@apply md:hidden inline-flex/')
        ->toMatch('/\[data-scope="shell\.sidebar"\]\[data-part="nav"\] \{\s*@apply flex md:!flex/');

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
        ->toContain('data-part="nav"')
        // It slides rather than appearing, through the contract's transition hooks.
        ->toContain('x-transition:enter="sigta-shell-enter"')
        ->toContain('x-transition:enter-start="sigta-shell-away"')
        ->toContain('x-transition:leave-end="sigta-shell-away"');

    expect(contractCss('shell'))
        // Off the flow and over the page, from the rail's own edge, full height.
        ->toContain('max-md:fixed max-md:inset-y-0 max-md:start-0 max-md:z-50')
        // ...and from the correct edge either way.
        ->toContain('&.sigta-shell-away { @apply max-md:ltr:-translate-x-full max-md:rtl:translate-x-full; }');
});

it('dims the page behind the drawer and closes when the scrim is tapped', function () {
    $html = Blade::render(<<<'BLADE'
        <x-sigta::shell.sidebar>
            <x-slot:brand>Acme</x-slot:brand>
            <x-sigta::shell.link href="#">Body</x-sigta::shell.link>
        </x-sigta::shell.sidebar>
        BLADE);

    expect($html)
        ->toContain('data-part="scrim"')
        ->toContain('x-on:click="close()"')
        ->toContain('aria-hidden="true"');

    // The scrim dims the page, under the drawer (z-50) and over the page.
    expect(contractCss('shell'))->toContain('@apply md:hidden fixed inset-0 z-40 bg-ink/45 backdrop-blur-[2px];');
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
        ->toContain('wire:navigate')
        ->not->toContain('class=');

    // The current link inverts to ink; the rest stay graphite.
    expect(contractCss('shell'))
        ->toContain('&[aria-current="page"] { @apply bg-ink text-surface; }')
        ->toContain('&:not([aria-current="page"]) { @apply text-graphite hover:bg-ink/5 hover:text-ink; }');
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
        ->toContain('data-sigta="shell.header"')
        ->toContain('data-test="top"')
        ->toContain('Plumb')
        ->toContain('Account');

    // The header comes first, so it sits above both columns.
    expect(strpos($html, 'shell.header'))->toBeLessThan(strpos($html, 'shell.sidebar'));

    expect(contractCss('shell'))
        ->toContain('&[data-header] { @apply grid-rows-[auto_auto_1fr] md:grid-rows-[auto_1fr]; }')
        ->toContain('@apply md:col-span-2 flex items-center gap-2 min-h-14');
});

it('leaves a shell without a header exactly as before', function () {
    $html = Blade::render('<x-sigta::shell><x-sigta::shell.main>Body</x-sigta::shell.main></x-sigta::shell>');

    expect($html)->not->toContain('data-header');

    // The pinned rows are keyed on the header, so a shell without one keeps auto rows.
    expect(contractCss('shell'))->not->toMatch('/\[data-sigta="shell"\] \{\s*@apply[^;]*grid-rows/');
});

it('drops the sidebar brand row when the shell has a header', function () {
    $html = Blade::render('<x-sigta::shell.sidebar>Nav</x-sigta::shell.sidebar>');

    expect($html)
        ->toContain('data-part="top"')
        // The header's burger opens this drawer through a window event.
        ->toContain('x-on:sigta-shell-toggle.window="toggle()"');

    expect(contractCss('shell'))
        ->toContain('[data-sigta="shell"][data-header] & { @apply hidden; }')
        ->toContain('[data-sigta="shell"][data-header] & { @apply max-md:p-0 max-md:border-b-0 md:min-h-0 md:overflow-y-auto; }');
});

it('puts the drawer burger in the header on a phone', function () {
    $html = Blade::render('<x-sigta::shell.header><x-slot:brand>Plumb</x-slot:brand></x-sigta::shell.header>');

    expect($html)
        ->toContain('aria-label="Toggle navigation"')
        ->toContain('data-scope="shell.header"'."\n".'            data-part="toggle"')
        ->toContain('$dispatch(\'sigta-shell-toggle\')')
        ->toContain('x-on:sigta-shell-nav.window="navOpen = $event.detail.open"')
        ->toContain('aria-expanded="false"');
});

it('keeps the main column clear of a visible floating bar', function () {
    $html = Blade::render('<x-sigta::shell.main>Body</x-sigta::shell.main>');

    expect($html)->toContain('data-sigta="shell.main"')->not->toContain('class=');

    expect(contractCss('shell'))->toContain('pb-[calc(1.5rem+var(--sigta-floating-space,0px))]');
});
