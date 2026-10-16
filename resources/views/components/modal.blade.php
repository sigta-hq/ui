@props([
    'name' => null,
    'size' => 'base',
    'heading' => null,
    'dismissible' => true,
    'fullscreen' => null,
])

@php
// On a phone the gutter and centring spend screen a working modal needs, so a
// large one takes the whole screen below the tablet breakpoint; a small one stays
// a centred card, since a confirmation is a lighter act than a workspace.
$fullscreen ??= in_array($size, ['lg', 'xl'], true);

$dialogAttributes = ['data-scope' => 'modal', 'data-part' => 'dialog', 'data-size' => $size];
if ($fullscreen) {
    $dialogAttributes['data-fullscreen'] = '';
}
@endphp

<div x-data="sigtaModal(@js($name))"
     x-on:sigta-modal-show.window="onShow($event.detail)"
     x-on:sigta-modal-hide.window="onHide($event.detail)"
     data-sigta="modal">
    {{-- wire:ignore.self by default: the open state is a runtime attribute set by
         showModal() that the server never renders, so a host Livewire morph on any
         in-modal action would otherwise strip it and close the modal on the first
         edit. It leaves only the dialog's own attributes alone; children still morph,
         and it is inert outside Livewire. --}}
    <dialog
        x-ref="dialog"
        wire:ignore.self
        @if ($dismissible) x-on:click="backdropClick($event)" @endif
        {{ $attributes->merge($dialogAttributes) }}
    >
        <div data-scope="modal" data-part="body">
            @if ($heading)
                <div data-scope="modal" data-part="header">
                    <x-sigta::heading level="2" size="lg">{{ $heading }}</x-sigta::heading>
                    @if ($dismissible)
                        <button type="button" x-on:click="hide()" aria-label="Close" data-scope="modal" data-part="close">
                            <x-sigta::icon name="x" size="sm" />
                        </button>
                    @endif
                </div>
            @elseif ($dismissible)
                <button type="button" x-on:click="hide()" aria-label="Close" data-scope="modal" data-part="close">
                    <x-sigta::icon name="x" size="sm" />
                </button>
            @endif

            {{ $slot }}
        </div>
    </dialog>
</div>
