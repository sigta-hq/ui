{{-- Floating bar: a dark pill pinned to the bottom centre of the viewport that
     slides up when `show` turns true and away when it turns false. For state that
     lasts while the member works (a running timer, a bulk selection), shown over
     the page instead of taking a band of it.
     While it is up it publishes its footprint as --sigta-floating-space on <html>,
     which shell.main and the toast region add to their bottom edge, so nothing
     ends up stuck under it. --}}
@props([
    'show' => false,
    'label' => null,
])

<div
    data-sigta="floating-bar"
    data-show="{{ $show ? 'true' : 'false' }}"
    x-data="sigtaFloatingBar"
    {{ $attributes->merge(['class' => 'pointer-events-none fixed inset-x-0 bottom-0 z-[80] flex justify-center p-4']) }}
>
    {{-- Alpine owns this element's display from here on. `wire:ignore.self` keeps a
         Livewire morph from writing the server's display back over it, which would
         cut the leave transition short; the children still update. A first render
         that is already up shows without animating, since x-show only transitions
         on a change. --}}
    <div
        x-ref="bar"
        x-show="visible"
        wire:ignore.self
        @unless ($show) style="display: none" @endunless
        @if ($label) role="region" aria-label="{{ $label }}" @endif
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-6 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-6 scale-95"
        class="pointer-events-auto flex max-w-full items-center gap-3 rounded-full border border-inverse-line bg-ink py-1.5 ps-4 pe-1.5 text-inverse-fg shadow-paper"
    >
        {{ $slot }}
    </div>
</div>
