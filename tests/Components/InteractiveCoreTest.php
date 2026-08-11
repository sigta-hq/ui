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
        ->toContain('text-danger');
});

it('renders dropdown items as links when href given', function () {
    $html = Blade::render('<x-sigta::dropdown><x-slot:trigger>t</x-slot:trigger><x-sigta::dropdown.item href="/settings">Settings</x-sigta::dropdown.item></x-sigta::dropdown>');

    expect($html)->toContain('<a role="menuitem"')->toContain('href="/settings"');
});

it('renders a modal built on native dialog', function () {
    $html = Blade::render('<x-sigta::modal name="confirm" heading="Sure?">Body</x-sigta::modal>');

    expect($html)
        ->toContain('<dialog')
        ->toContain("sigtaModal('confirm')")
        ->toContain('sigta-modal-show.window')
        ->toContain('aria-label="Close"')
        ->toContain('Body');
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
        ->toContain('aria-labelledby="sigta-tab-second"');
});

it('renders the toast region listening for sigta-toast', function () {
    $html = Blade::render('<x-sigta::toast />');

    expect($html)
        ->toContain('data-sigta="toast"')
        ->toContain('sigta-toast.window')
        ->toContain('x-for="toast in toasts"');
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
