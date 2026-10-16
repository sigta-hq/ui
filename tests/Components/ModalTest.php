<?php

use Illuminate\Support\Facades\Blade;

it('renders a dismissible centred sheet by default', function () {
    $html = Blade::render('<x-sigta::modal name="settings" heading="Settings">Body</x-sigta::modal>');

    expect($html)
        ->toContain('data-sigta="modal"')
        ->toContain('data-scope="modal" data-part="dialog" data-size="base"')
        ->toContain('Settings')
        ->toContain('Body')
        ->toContain('aria-label="Close"');

    expect(contractCss('modal'))->toContain('max-w-lg')->toContain('rounded-frame');
});

it('takes the whole screen on a phone for the sizes that carry real work', function (string $size) {
    $html = Blade::render('<x-sigta::modal name="work" size="'.$size.'">Body</x-sigta::modal>');

    expect($html)->toContain('data-size="'.$size.'"')->toContain('data-fullscreen');

    expect(contractCss('modal'))->toContain('@apply max-md:w-full max-md:max-w-none max-md:h-dvh max-md:max-h-none max-md:m-0 max-md:rounded-none max-md:border-0;');
})->with(['lg', 'xl']);

it('keeps the small sizes a centred card on a phone', function (string $size) {
    $html = Blade::render('<x-sigta::modal name="confirm" size="'.$size.'">Delete?</x-sigta::modal>');

    expect($html)->not->toContain('data-fullscreen');
})->with(['sm', 'base']);

it('lets a small modal ask for the whole screen', function () {
    $html = Blade::render('<x-sigta::modal name="wizard" size="sm" fullscreen>Steps</x-sigta::modal>');

    // The size still decides the width it centres to above the breakpoint.
    expect($html)->toContain('data-size="sm"')->toContain('data-fullscreen');
});

it('lets a large modal stay a centred card', function () {
    $html = Blade::render('<x-sigta::modal name="preview" size="lg" :fullscreen="false">Preview</x-sigta::modal>');

    expect($html)->toContain('data-size="lg"')->not->toContain('data-fullscreen');
});

it('gives a full screen modal a column body so content can fill or scroll', function () {
    expect(contractCss('modal'))
        ->toContain('[data-fullscreen] > & { @apply max-md:flex max-md:min-h-full max-md:flex-col; }');
});

it('drops the close button when it is not dismissible', function () {
    $html = Blade::render('<x-sigta::modal name="locked" heading="Locked">Body</x-sigta::modal>');
    $locked = Blade::render('<x-sigta::modal name="locked" heading="Locked" :dismissible="false">Body</x-sigta::modal>');

    expect($html)->toContain('aria-label="Close"');
    expect($locked)->not->toContain('aria-label="Close"');
});

it('floats the close button when there is no heading row', function () {
    $html = Blade::render('<x-sigta::modal name="bare">Body</x-sigta::modal>');

    expect($html)->toContain('data-part="close"')->not->toContain('data-part="header"');
    expect(contractCss('modal'))->toContain('[data-part="body"] > & { @apply absolute right-5 top-5 z-10; }');
});

it('forwards arbitrary attributes onto the dialog', function () {
    $html = Blade::render('<x-sigta::modal name="settings" data-test="settings-dialog" class="max-w-3xl">Body</x-sigta::modal>');

    expect($html)
        ->toContain('data-test="settings-dialog"')
        ->toContain('class="max-w-3xl"');
});
