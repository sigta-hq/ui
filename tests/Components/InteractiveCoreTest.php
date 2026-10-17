<?php

use Illuminate\Support\Facades\Blade;

it('renders a dropdown with menu semantics', function () {
    $html = Blade::render(<<<'BLADE'
        <x-sigta::dropdown>
            <x-slot:trigger><x-sigta::button>Options</x-sigta::button></x-slot:trigger>
            <x-sigta::dropdown.item icon="pencil">Edit</x-sigta::dropdown.item>
            <x-sigta::dropdown.separator />
            <x-sigta::dropdown.item variant="danger">Delete</x-sigta::dropdown.item>
        </x-sigta::dropdown>
    BLADE);

    expect($html)
        ->toContain('data-sigta="dropdown"')
        ->toContain('role="menu"')
        ->toContain('role="menuitem"')
        ->toContain('aria-haspopup="menu"')
        ->toContain('role="separator"')
        ->toContain('data-sigta="dropdown.separator"')
        ->toContain('data-position="bottom-start"')
        ->toContain('data-variant="danger"')
        ->toContain('data-scope="dropdown.item" data-part="icon"')
        ->not->toContain('class=');

    // A danger item is red; focused, it takes the same ink fill as any item.
    expect(contractCss('dropdown'))
        ->toMatch('/&\[data-variant="danger"\] \{\s*@apply text-danger focus:text-surface;/')
        ->toContain('focus:bg-ink focus:text-surface');
});

it('renders dropdown items as links when href given', function () {
    $html = Blade::render('<x-sigta::dropdown><x-slot:trigger>t</x-slot:trigger><x-sigta::dropdown.item href="/settings">Settings</x-sigta::dropdown.item></x-sigta::dropdown>');

    expect($html)->toContain('<a role="menuitem"')->toContain('href="/settings"');
});

it('places the menu by position, falling back to bottom-start', function () {
    $html = Blade::render('<x-sigta::dropdown position="top-end"><x-slot:trigger>t</x-slot:trigger><x-sigta::dropdown.item>A</x-sigta::dropdown.item></x-sigta::dropdown>');

    expect($html)->toContain('data-position="top-end"')->toContain('data-part="menu"');
    expect(contractCss('dropdown'))
        ->toContain('[data-sigta="dropdown"][data-position="top-end"] > & { @apply bottom-full right-0 mb-2; }')
        ->toContain('[data-sigta="dropdown"]:not(:is([data-position="bottom-end"], [data-position="top-start"], [data-position="top-end"])) > & { @apply top-full left-0 mt-2; }');
});

it('opens the menu from the trigger with the arrow keys', function () {
    $html = Blade::render('<x-sigta::dropdown><x-slot:trigger>t</x-slot:trigger><x-sigta::dropdown.item>A</x-sigta::dropdown.item></x-sigta::dropdown>');

    expect($html)
        ->toContain('x-on:keydown.down.prevent="openAt(0)"')
        ->toContain('x-on:keydown.up.prevent="openAt(-1)"')
        ->toContain('aria-expanded="false"');
});

it('runs an item\'s own x-on:click before it closes the menu', function () {
    // An element carries an attribute once: a second x-on:click was dropped by the browser.
    $html = Blade::render('<x-sigta::dropdown><x-slot:trigger>t</x-slot:trigger><x-sigta::dropdown.item x-on:click="model = m">A</x-sigta::dropdown.item></x-sigta::dropdown>');

    expect($html)->toContain('x-on:click="model = m; close(true)"');
    expect(substr_count($html, 'x-on:click='))->toBe(2); // the item's and the trigger's
});

it('keeps a popover open when its content is clicked', function () {
    $html = Blade::render('<x-sigta::popover position="bottom-end"><x-slot:trigger>t</x-slot:trigger><p>Body</p></x-sigta::popover>');

    expect($html)
        ->toContain('data-sigta="popover"')
        ->toContain('data-position="bottom-end"')
        ->toContain('aria-haspopup="dialog"')
        ->toMatch('/role="dialog"\s+tabindex="-1"/')
        ->not->toContain('class=');
});

it('renders a modal built on native dialog', function () {
    $html = Blade::render('<x-sigta::modal name="confirm" heading="Sure?">Body</x-sigta::modal>');

    expect($html)
        ->toContain('<dialog')
        ->toContain("sigtaModal('confirm')")
        ->toContain('sigta-modal-show.window')
        ->toContain('aria-label="Close"')
        ->toContain('Body');

    // Caps at the viewport and scrolls its own body so long modals do not
    // clip on small screens.
    expect(contractCss('modal'))->toContain('max-h-[calc(100dvh-2.5rem)]')->toContain('overflow-y-auto');
});

it('keeps the dialog open across a host Livewire morph', function () {
    // showModal() sets the open state as a runtime attribute the server never
    // renders, so without wire:ignore.self a host component's morph strips it and
    // the modal closes on the first in-modal action (an autosave, a New button).
    $html = Blade::render('<x-sigta::modal name="notes">Body</x-sigta::modal>');

    expect($html)->toContain('wire:ignore.self');
});

it('floats the close button with no header band when a modal has no heading', function () {
    $withHeading = Blade::render('<x-sigta::modal name="a" heading="Sure?">Body</x-sigta::modal>');
    $noHeading = Blade::render('<x-sigta::modal name="b">Body</x-sigta::modal>');

    // A headed modal keeps the close button in a header row.
    expect($withHeading)->toContain('aria-label="Close"')->toContain('data-part="header"');

    // A heading-less modal still offers a close button, but floats it in the
    // corner with no reserved header row, so the content sits at the top of
    // the panel with no wasted band.
    expect($noHeading)
        ->toContain('aria-label="Close"')
        ->not->toContain('data-part="header"');

    expect(contractCss('modal'))->toContain('@apply absolute right-5 top-5 z-10;');
});

it('renders no close button when a heading-less modal is not dismissible', function () {
    $html = Blade::render('<x-sigta::modal name="c" :dismissible="false">Body</x-sigta::modal>');

    expect($html)->not->toContain('aria-label="Close"');
});

it('renders modal trigger dispatching the named event', function () {
    $html = Blade::render('<x-sigta::modal.trigger name="confirm"><button>Open</button></x-sigta::modal.trigger>');

    expect($html)->toContain('sigta-modal-show')->toContain("name: 'confirm'");
});

it('renders tabs with APG roles and ids', function () {
    $html = Blade::render(<<<'BLADE'
        <x-sigta::tabs default="first">
            <x-sigta::tabs.list label="Demo">
                <x-sigta::tabs.tab name="first">First</x-sigta::tabs.tab>
                <x-sigta::tabs.tab name="second">Second</x-sigta::tabs.tab>
            </x-sigta::tabs.list>
            <x-sigta::tabs.panel name="first">One</x-sigta::tabs.panel>
            <x-sigta::tabs.panel name="second">Two</x-sigta::tabs.panel>
        </x-sigta::tabs>
    BLADE);

    expect($html)
        ->toContain('role="tablist"')
        ->toContain('role="tab"')
        ->toContain('role="tabpanel"')
        ->toContain('id="sigta-tab-first"')
        ->toContain('aria-controls="sigta-panel-first"')
        ->toContain('aria-labelledby="sigta-tab-second"')
        ->toContain('x-on:keydown.home.prevent="moveTo(0)"')
        ->toContain('x-on:keydown.end.prevent="moveTo(-1)"')
        ->not->toContain('class=');

    // The default tab renders selected and its panel shown before Alpine starts.
    expect($html)
        ->toMatch('/data-name="first"[^>]*aria-selected="true"[^>]*tabindex="0"/')
        ->toMatch('/data-name="second"[^>]*aria-selected="false"[^>]*tabindex="-1"/')
        ->toMatch('/id="sigta-panel-second"[^>]*x-cloak/')
        ->not->toMatch('/id="sigta-panel-first"[^>]*x-cloak/');

    expect(contractCss('tabs'))
        ->toMatch('/&\[aria-selected="true"\] \{\s*@apply text-ink border-ink;/')
        ->toMatch('/&:not\(\[aria-selected="true"\]\) \{\s*@apply text-graphite border-transparent hover:text-ink;/');
});

it('leaves every tab unselected on the server without a default', function () {
    $html = Blade::render('<x-sigta::tabs><x-sigta::tabs.list><x-sigta::tabs.tab name="a">A</x-sigta::tabs.tab></x-sigta::tabs.list><x-sigta::tabs.panel name="a">One</x-sigta::tabs.panel></x-sigta::tabs>');

    expect($html)->toContain('aria-selected="false"')->toMatch('/id="sigta-panel-a"[^>]*x-cloak/');
});

it('renders the toast region listening for sigta-toast', function () {
    $html = Blade::render('<x-sigta::toast />');

    expect($html)
        ->toContain('data-sigta="toast"')
        ->toContain('sigta-toast.window')
        ->toContain('x-for="toast in toasts"')
        ->toContain('x-bind:data-variant="toast.variant"')
        ->toContain("toast.variant === 'warning'")
        ->toContain('toast.link.href');

    expect(contractCss('toast'))->toContain('&[data-variant="warning"] { @apply border-warning; }');
});

it('renders segmented control as a radio group', function () {
    $html = Blade::render('<x-sigta::segmented name="range" label="Range" :options="[\'7d\' => \'7d\', \'30d\' => \'30d\']" checked="30d" />');

    expect($html)
        ->toContain('data-sigta="segmented"')
        ->toContain('type="radio"')
        ->toContain('name="range"')
        ->toContain('checked');
});

it('forwards wire:model on segmented radios', function () {
    $html = Blade::render('<x-sigta::segmented name="r" :options="[\'a\' => \'A\']" wire:model="range" />');

    expect($html)->toContain('wire:model="range"');
});

it('fills the chosen segment in ink through the contract', function () {
    $html = Blade::render('<x-sigta::segmented name="r" label="Range" :options="[\'a\' => \'A\', \'b\' => \'B\']" checked="b" class="mt-2" />');

    expect($html)
        ->toMatch('/<fieldset class="mt-2" data-sigta="segmented">/')
        ->toContain('data-scope="segmented" data-part="legend"')
        ->toMatch('/value="b"\s+checked/')
        ->toContain('data-scope="segmented" data-part="label"');

    expect(contractCss('segmented'))
        ->toContain('[data-scope="segmented"][data-part="input"]:checked ~ [data-scope="segmented"][data-part="label"] {')
        ->toContain('@apply bg-ink text-surface hover:text-surface;');
});
