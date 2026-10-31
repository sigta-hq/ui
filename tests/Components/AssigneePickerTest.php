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
        ->toContain('border-dashed')                 // dashed "add" affordance for no assignee
        ->toContain('data-test="assignee-dropdown"')
        ->toContain('Search people')
        ->toContain('Unassigned');
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

it('teleports the panel to body by default and keeps it in place as a popover when :inline', function () {
    $teleport = Blade::render('<x-sigta::assignee-picker :members="$members" />', ['members' => members()]);
    expect($teleport)
        ->toContain('x-teleport="body"')
        ->not->toContain('popover="manual"');

    // In place so it stays inside a <dialog> (not inert, not stripped by a morph),
    // and in the top layer so the dialog's overflow cannot clip or scroll it.
    $inline = Blade::render('<x-sigta::assignee-picker :members="$members" inline />', ['members' => members()]);
    expect($inline)
        ->not->toContain('x-teleport="body"')
        ->toContain('popover="manual"')
        ->toContain('showPopover()')
        ->not->toContain('absolute left-0 top-full');
});

it('fixes the panel to the trigger and follows it on scroll and resize', function () {
    $html = Blade::render('<x-sigta::assignee-picker :members="$members" inline />', ['members' => members()]);

    expect($html)
        ->toContain('position: fixed; inset: auto; margin: 0;')
        ->toContain('x-on:scroll.window.capture.passive="open && place()"')
        ->toContain('x-on:resize.window="open && place()"');
});

it('forwards a value-keyed wire:key onto the root, not the trigger label', function () {
    $html = Blade::render('<x-sigta::assignee-picker :members="$members" wire:key="assignee-7" />', ['members' => members()]);

    expect($html)->toContain('wire:key="assignee-7"');
});
