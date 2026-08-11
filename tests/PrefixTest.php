<?php

use Illuminate\Support\Facades\Blade;

it('compiles <sigta:button> identically to <x-sigta::button>', function () {
    $prefixed = Blade::render('<sigta:button variant="primary">Save</sigta:button>');
    $canonical = Blade::render('<x-sigta::button variant="primary">Save</x-sigta::button>');

    expect($prefixed)->toBe($canonical)
        ->and($prefixed)->toContain('data-sigta="button"')
        ->and($prefixed)->toContain('Save');
});

it('compiles dotted subcomponents', function () {
    $html = Blade::render(<<<'BLADE'
        <sigta:dropdown>
            <x-slot:trigger><sigta:button>Menu</sigta:button></x-slot:trigger>
            <sigta:dropdown.item>Edit</sigta:dropdown.item>
        </sigta:dropdown>
    BLADE);

    expect($html)
        ->toContain('data-sigta="dropdown"')
        ->toContain('role="menuitem"')
        ->toContain('Edit');
});

it('compiles self-closing prefixed tags', function () {
    $prefixed = Blade::render('<sigta:icon name="arrow-right" />');
    $canonical = Blade::render('<x-sigta::icon name="arrow-right" />');

    expect($prefixed)->toBe($canonical)->and($prefixed)->toContain('<svg');
});

it('leaves x-slot alone', function () {
    $html = Blade::render(<<<'BLADE'
        <sigta:card>
            <x-slot:thumb><div>thumb</div></x-slot:thumb>
            Body
        </sigta:card>
    BLADE);

    expect($html)->toContain('thumb')->toContain('Body');
});
