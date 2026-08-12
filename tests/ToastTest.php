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

it('throws when Sigta::toast is called outside a Livewire component', function () {
    expect(fn () => app(SigtaManager::class)->toast('Saved'))
        ->toThrow(RuntimeException::class, 'requires an active Livewire component');
});

it('resolves Sigta through the facade', function () {
    expect(Sigta::getFacadeRoot())->toBeInstanceOf(SigtaManager::class);
});
