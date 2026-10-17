<?php

use Illuminate\Support\Facades\Blade;

function members(): array
{
    return [
        ['id' => 1, 'name' => 'Ada Lovelace'],
        ['id' => 2, 'name' => 'Alan Turing'],
    ];
}

it('renders an unassigned trigger and a searchable dropdown', function () {
    $html = Blade::render('<x-sigta::assignee-picker :members="$members" />', ['members' => members()]);

    expect($html)
        ->toContain('data-sigta="assignee-picker"')
        ->toContain('aria-label="Assignee"')
        ->toContain('data-part="placeholder"')       // dashed "add" affordance for no assignee
        ->toContain('data-test="assignee-dropdown"')
        ->toContain('Search people')
        ->toContain('Unassigned');

    expect(contractCss('assignee-picker'))->toContain('border-dashed');
});

it('renders contract parts instead of design classes', function () {
    $html = Blade::render('<x-sigta::assignee-picker :members="$members" size="sm" />', ['members' => members()]);

    expect($html)
        ->not->toContain('class=')
        ->toMatch('/data-scope="assignee-picker"\\s+data-part="trigger"/')
        ->toMatch('/data-part="placeholder"\\s+data-size="sm"/')   // 32px affordance at sm
        ->toContain('data-part="panel"')
        ->toContain('data-part="search"')
        ->toContain('data-part="unassign"')
        ->toContain('data-part="option"')
        ->toContain('data-part="empty"');

    expect(contractCss('assignee-picker'))
        ->toContain('&[data-size="sm"] { @apply size-8; }')
        ->toContain('@apply z-50 w-64 rounded-panel');
});

it('draws each member with the avatar initials', function () {
    $html = Blade::render('<x-sigta::assignee-picker :members="$members" />', ['members' => members()]);

    expect($html)
        ->toContain('data-sigta="avatar"')
        ->toContain('x-text="initials(m.name)"')
        ->toContain('data-size="xs"');
});

it('lands a consumer class on the trigger', function () {
    $html = Blade::render('<x-sigta::assignee-picker :members="[]" class="ml-2" />');

    expect($html)->toMatch('/<button[^>]*data-part="trigger"[^>]*class="ml-2"/s');
});

it('embeds the member list for client-side filtering', function () {
    $html = Blade::render('<x-sigta::assignee-picker :members="$members" />', ['members' => members()]);

    expect($html)
        ->toContain('Ada Lovelace')
        ->toContain('Alan Turing')
        ->toContain('get filtered()');   // client-side filter, no per-instance Livewire
});

it('shows the current assignee avatar with initials and an optional name', function () {
    $html = Blade::render('<x-sigta::assignee-picker :user="$user" :members="[]" show-name />', [
        'user' => ['id' => 1, 'name' => 'Ada Lovelace'],
    ]);

    expect($html)
        ->toContain('data-sigta="avatar"')
        ->toContain('AL')              // initials fallback
        ->toContain('Ada Lovelace');   // showName label
});

it('wires the select call to the given method and leading args', function () {
    $html = Blade::render('<x-sigta::assignee-picker :members="$members" method="assignTask" :args="[42]" />', ['members' => members()]);

    expect($html)
        ->toContain('$wire.assignTask(')
        ->toContain('42');   // leading arg spread before the user id
});

it('teleports the panel to body by default and inlines it when :inline', function () {
    $teleport = Blade::render('<x-sigta::assignee-picker :members="$members" />', ['members' => members()]);
    expect($teleport)->toContain('x-teleport="body"');

    $inline = Blade::render('<x-sigta::assignee-picker :members="$members" inline />', ['members' => members()]);
    expect($inline)
        ->not->toContain('x-teleport="body"')
        ->toContain('data-inline=""');
    expect($teleport)->not->toContain('data-inline');

    expect(contractCss('assignee-picker'))->toContain('&[data-inline] { @apply absolute left-0 top-full mt-1; }');
});

it('forwards a value-keyed wire:key onto the root, not the trigger label', function () {
    $html = Blade::render('<x-sigta::assignee-picker :members="$members" wire:key="assignee-7" />', ['members' => members()]);

    expect($html)->toContain('wire:key="assignee-7"');
});
