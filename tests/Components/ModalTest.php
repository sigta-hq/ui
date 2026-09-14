<?php

use Illuminate\Support\Facades\Blade;

it('renders a dismissible centred sheet by default', function () {
    $html = Blade::render('<x-sigta::modal name="settings" heading="Settings">Body</x-sigta::modal>');

    expect($html)
        ->toContain('data-sigta="modal"')
        ->toContain('Settings')
        ->toContain('Body')
        ->toContain('max-w-lg')
        ->toContain('rounded-frame')
        ->toContain('aria-label="Close"');
});

it('takes the whole screen on a phone for the sizes that carry real work', function (string $size) {
    $html = Blade::render('<x-sigta::modal name="work" size="'.$size.'">Body</x-sigta::modal>');

    expect($html)
        ->toContain('max-md:w-full')
        ->toContain('max-md:max-w-none')
        ->toContain('max-md:h-dvh')
        ->toContain('max-md:max-h-none')
        ->toContain('max-md:m-0')
        ->toContain('max-md:rounded-none')
        ->toContain('max-md:border-0');
})->with(['lg', 'xl']);

it('keeps the small sizes a centred card on a phone', function (string $size) {
    $html = Blade::render('<x-sigta::modal name="confirm" size="'.$size.'">Delete?</x-sigta::modal>');

    expect($html)
        ->not->toContain('max-md:w-full')
        ->not->toContain('max-md:h-dvh')
        ->not->toContain('max-md:rounded-none');
})->with(['sm', 'base']);

it('lets a small modal ask for the whole screen', function () {
    $html = Blade::render('<x-sigta::modal name="wizard" size="sm" fullscreen>Steps</x-sigta::modal>');

    expect($html)
        ->toContain('max-md:h-dvh')
        ->toContain('max-md:rounded-none')
        // The size still decides the width it centres to above the breakpoint.
        ->toContain('max-w-sm');
});

it('lets a large modal stay a centred card', function () {
    $html = Blade::render('<x-sigta::modal name="preview" size="lg" :fullscreen="false">Preview</x-sigta::modal>');

    expect($html)
        ->not->toContain('max-md:h-dvh')
        ->not->toContain('max-md:rounded-none')
        ->toContain('max-w-2xl');
});

it('gives a full screen modal a column body so content can fill or scroll', function () {
    $html = Blade::render('<x-sigta::modal name="notes" size="xl">Panes</x-sigta::modal>');

    expect($html)
        ->toContain('max-md:flex')
        ->toContain('max-md:min-h-full')
        ->toContain('max-md:flex-col');
});

it('leaves a centred modal body a plain block', function () {
    $html = Blade::render('<x-sigta::modal name="confirm">Delete?</x-sigta::modal>');

    expect($html)->not->toContain('max-md:min-h-full');
});

it('drops the close button when it is not dismissible', function () {
    $html = Blade::render('<x-sigta::modal name="locked" heading="Locked">Body</x-sigta::modal>');
    $locked = Blade::render('<x-sigta::modal name="locked" heading="Locked" :dismissible="false">Body</x-sigta::modal>');

    expect($html)->toContain('aria-label="Close"');
    expect($locked)->not->toContain('aria-label="Close"');
});

it('forwards arbitrary attributes onto the dialog', function () {
    $html = Blade::render('<x-sigta::modal name="settings" data-test="settings-dialog" class="max-w-3xl">Body</x-sigta::modal>');

    expect($html)
        ->toContain('data-test="settings-dialog"')
        ->toContain('max-w-3xl');
});
