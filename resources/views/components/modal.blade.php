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
    <dialog
        x-ref="dialog"
        @if ($dismissible) x-on:click="backdropClick($event)" @endif
        {{ $attributes->merge(['class' => 'w-[calc(100vw-2.5rem)] '.$width.' max-h-[calc(100dvh-2.5rem)] overflow-y-auto overscroll-contain bg-surface-raised text-ink border border-line rounded-frame shadow-paper p-0 backdrop:bg-ink/45 backdrop:backdrop-blur-[2px] m-auto']) }}
    >
        <div class="p-6 md:p-8">
            @if ($heading || $dismissible)
                <div class="flex items-start justify-between gap-4 mb-4">
                    @if ($heading)
                        <x-sigta::heading level="2" size="lg">{{ $heading }}</x-sigta::heading>
                    @endif
                    @if ($dismissible)
                        <button type="button" x-on:click="hide()" aria-label="Close"
                                class="ml-auto -mr-1 -mt-1 p-1.5 rounded-base text-graphite hover:text-ink hover:bg-ink/5 cursor-pointer">
                            <x-sigta::icon name="x" size="sm" />
                        </button>
                    @endif
                </div>
            @endif

            {{ $slot }}
        </div>
    </dialog>
</div>
