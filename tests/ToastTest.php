<?php

use Livewire\Livewire;
use Sigta\Ui\Facades\Sigta;
use Sigta\Ui\Sigta as SigtaManager;
use Tests\Support\ToastHarness;

it('dispatches sigta-toast from Sigta::toast with string shorthand', function () {
    Livewire::test(ToastHarness::class)
        ->call('triggerToast', text: 'Your changes have been saved.')
        ->assertDispatched('sigta-toast', text: 'Your changes have been saved.');
});

it('dispatches sigta-toast with named parameters', function () {
    Livewire::test(ToastHarness::class)
        ->call('triggerToast', text: 'Settings updated.', heading: 'Saved', variant: 'success', duration: 3000)
        ->assertDispatched('sigta-toast', text: 'Settings updated.', heading: 'Saved', variant: 'success', duration: 3000);
});

it('dispatches sigta-toast with link payload', function () {
    $link = ['text' => 'View', 'href' => '/settings', 'navigate' => true];

    Livewire::test(ToastHarness::class)
        ->call('triggerToast', text: 'Invoice created.', variant: 'success', link: $link)
        ->assertDispatched('sigta-toast', text: 'Invoice created.', variant: 'success', link: $link);
});

it('dispatches sigta-toast with an action and a replace key', function () {
    $action = ['text' => 'Undo', 'event' => 'task-reopen', 'detail' => ['id' => 5]];

    Livewire::test(ToastHarness::class)
        ->call('triggerToast', text: 'Marked done', variant: 'success', action: $action, key: 'tasks')
        ->assertDispatched('sigta-toast', text: 'Marked done', variant: 'success', action: $action, key: 'tasks');
});

it('dispatches sigta-celebrate from Sigta::celebrate', function () {
    Livewire::test(ToastHarness::class)
        ->call('triggerCelebrate', level: 'big', text: 'Inbox zero!', target: '#inbox')
        ->assertDispatched('sigta-celebrate', level: 'big', text: 'Inbox zero!', target: '#inbox');
});

it('leaves the note out when text is false', function () {
    Livewire::test(ToastHarness::class)
        ->call('triggerCelebrate', level: 'big', text: false, target: '#done')
        ->assertDispatched('sigta-celebrate', level: 'big', text: false, target: '#done');
});

it('celebrates small by default', function () {
    Livewire::test(ToastHarness::class)
        ->call('triggerCelebrate')
        ->assertDispatched('sigta-celebrate', level: 'small');
});

it('throws when Sigta::celebrate is called outside a Livewire component', function () {
    expect(fn () => app(SigtaManager::class)->celebrate('big'))
        ->toThrow(RuntimeException::class, 'Sigta::celebrate() requires an active Livewire component');
});

it('throws when Sigta::toast is called outside a Livewire component', function () {
    expect(fn () => app(SigtaManager::class)->toast('Saved'))
        ->toThrow(RuntimeException::class, 'requires an active Livewire component');
});

it('resolves Sigta through the facade', function () {
    expect(Sigta::getFacadeRoot())->toBeInstanceOf(SigtaManager::class);
});
