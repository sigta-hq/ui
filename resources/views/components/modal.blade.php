@props([
    'name' => null,
    'size' => 'base',
    'heading' => null,
    'dismissible' => true,
])

@php
$width = match ($size) {
    'sm' => 'max-w-sm',
    'lg' => 'max-w-2xl',
    'xl' => 'max-w-4xl',
    default => 'max-w-lg',
};
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
        {{ $attributes->merge(['class' => 'w-[calc(100vw-2.5rem)] '.$width.' max-h-[calc(100dvh-2.5rem)] overflow-y-auto overscroll-contain bg-surface-raised text-ink border border-line rounded-frame shadow-paper p-0 backdrop:bg-ink/45 backdrop:backdrop-blur-[2px] m-auto']) }}
    >
        <div class="relative p-6 md:p-8">
            @if ($heading)
                {{-- With a heading the close button shares the header row beside it. --}}
                <div class="flex items-start justify-between gap-4 mb-4">
                    <x-sigta::heading level="2" size="lg">{{ $heading }}</x-sigta::heading>
                    @if ($dismissible)
                        <button type="button" x-on:click="hide()" aria-label="Close"
                                class="ml-auto -mr-1 -mt-1 p-1.5 rounded-base text-graphite hover:text-ink hover:bg-ink/5 cursor-pointer">
                            <x-sigta::icon name="x" size="sm" />
                        </button>
                    @endif
                </div>
            @elseif ($dismissible)
                {{-- With no heading the close button floats in the corner instead of
                     reserving an empty header row, so the content starts at the top of
                     the panel with no wasted band. z-10 keeps it clickable above the
                     content; leave the top-right of the content clear for it. --}}
                <button type="button" x-on:click="hide()" aria-label="Close"
                        class="absolute right-5 top-5 z-10 p-1.5 rounded-base text-graphite hover:text-ink hover:bg-ink/5 cursor-pointer">
                    <x-sigta::icon name="x" size="sm" />
                </button>
            @endif

            {{ $slot }}
        </div>
    </dialog>
</div>
